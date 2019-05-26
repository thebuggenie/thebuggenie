<?php

    namespace thebuggenie\core\modules\livelink\controllers;

    use thebuggenie\core\entities\Branch;
    use thebuggenie\core\entities\Comment;
    use thebuggenie\core\entities\Commit;
    use thebuggenie\core\entities\tables\Branches;
    use thebuggenie\core\entities\tables\Comments;
    use thebuggenie\core\entities\tables\Commits,
        thebuggenie\core\framework,
        thebuggenie\core\helpers\ProjectActions,
        thebuggenie\core\modules\livelink\Livelink;
    use thebuggenie\core\entities\tables\IssueCommits;
    use thebuggenie\core\entities\tables\Issues;

    /**
     * Main controller for the livelink module
     *
     * @property Commit $commit
     * @property Branch $branch
     */
    class Project extends ProjectActions
    {

        /**
         * @return Livelink
         */
        protected function getModule()
        {
            return framework\Context::getModule('livelink');
        }

        /**
         * @Route(name="livelink_project_commits_post", url="/:project_key/commits", methods="POST")
         *
         * @param framework\Request $request
         * @return bool
         */
        public function runPostProjectCommits(framework\Request $request)
        {
            $branch = Branches::getTable()->getByBranchNameAndProject($request['branch'], $this->selected_project);
            if (!$branch instanceof Branch) {
                $this->return404('Invalid branch');
            }

            $commit = null;
            if ($request->hasParameter('from_commit')) {
                $from_commit_ref = trim($request['from']);
                if (strlen($from_commit_ref) < 7) {
                    $this->return404('Invalid commit ref');
                }

                $commit = Commits::getTable()->getCommitByHash($from_commit_ref, $this->selected_project);
            }
            $commits = $branch->getCommits($commit);
            $commit_ids = array_reduce($commits, function ($ids, $commit) {
                $ids[] = $commit->getID();

                return $ids;
            }, []);
            $branch_points = Branches::getTable()->getByCommitsAndProject($commit_ids, $this->selected_project);
            Comments::getTable()->preloadCommentCounts(Comment::TYPE_COMMIT, $commit_ids);

            return $this->renderJSON(['content' => $this->getComponentHTML('livelink/projectcommitsbox', ['commits' => $commits, 'branches' => $branch_points, 'selected_project' => $this->selected_project, 'branch' => $branch])]);
        }

        /**
         * @Route(name="livelink_project_commits", url="/:project_key/commits", methods="GET")
         *
         * @param framework\Request $request
         * @return bool
         */
        public function runProjectCommits(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_commits'));

            $branches = Branches::getTable()->getByProject($this->selected_project);
            $this->branches = $branches;
            $this->is_importing = $this->getModule()->isProjectImportInProgress($this->selected_project);
        }

        /**
         * @Route(name="livelink_project_commit_import", url="/:project_key/commits/:commit_hash/import/*", methods="GET")
         *
         * @param framework\Request $request
         * @return bool
         */
        public function runProjectImportCommit(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_commits'));

            $this->commit = Commits::getTable()->getCommitByHash($request['commit_hash'], $this->selected_project);
            if (!$this->commit instanceof Commit || $this->commit->isImported()) {
                $this->return404('Invalid commit');
            }

            if ($request->hasParameter('branch')) {
                $this->branch = Branches::getTable()->getByBranchNameAndProject($request['branch'], $this->selected_project);
                if (!$this->branch instanceof Branch) {
                    $this->return404('Invalid branch');
                }
            }

            $connector = $this->getModule()->getConnectorModule($this->getModule()->getProjectConnector($this->selected_project));
            $connector->importSingleCommit($this->selected_project, $this->commit);

            $options = ['project_key' => $this->selected_project->getKey(), 'commit_hash' => $this->commit->getRevision()];
            if (isset($this->branch)) {
                $options['branch'] = $this->branch->getName();
            }

            $this->forward($this->getRouting()->generate('livelink_project_commit', $options));
        }

        /**
         * @Route(name="livelink_project_commit", url="/:project_key/commits/:commit_hash/*", methods="GET")
         *
         * @param framework\Request $request
         * @return bool
         */
        public function runProjectCommit(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_commits'));

            if ($request->hasParameter('branch')) {
                $this->branch = Branches::getTable()->getByBranchNameAndProject($request['branch'], $this->selected_project);
                if (!$this->branch instanceof Branch) {
                    $this->return404('Invalid branch');
                }
            }

            $this->commit = Commits::getTable()->getCommitByHash($request['commit_hash'], $this->selected_project);
            if (!$this->commit instanceof Commit) {
                $this->return404('Invalid commit');
            }

            $this->is_importing = $this->getModule()->isProjectImportInProgress($this->selected_project);
        }

        /**
         * @Route(name="livelink_project_commits_more", url="/:project_key/commits/more")
         *
         * @param framework\Request $request
         * @return bool
         */
        public function runProjectCommitsMore(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_commits') || $request->isPost());

            $branch = Branches::getTable()->getByBranchNameAndProject($request['branch'], $this->selected_project);
            if (!$branch instanceof Branch) {
                $this->return404('Invalid branch');
            }

            $commit = null;
            if ($request->hasParameter('from_commit')) {
                $from_commit_ref = trim($request['from']);
                if (strlen($from_commit_ref) < 7) {
                    $this->return404('Invalid commit ref');
                }

                $commit = Commits::getTable()->getCommitByHash($from_commit_ref, $this->selected_project);
            }
            $this->commits = $branch->getCommits($commit);

            if (count($this->commits)) {
                $last_commit_hash = array_shift(array_values($this->commits))->getShortRevision();
            } else {
                $last_commit_hash = $from_commit_ref ?? '';
            }

            return $this->renderJSON(array('content' => $this->getComponentHTML('livelink/projectcommits', ['commits' => $this->commits, 'selected_project' => $this->selected_project]), 'last_commit' => $last_commit_hash));
        }

        /**
         * @Route(name="livelink_project_issue_commits_more", url="/:project_key/issues/:issue_no/commits/more", methods="POST")
         *
         * @param framework\Request $request
         * @return bool
         */
        public function runProjectIssueCommitsMore(framework\Request $request)
        {
            $issue = Issues::getTable()->getByProjectIDAndIssueNo($this->selected_project->getID(), $request['issue_no']);
            $links = IssueCommits::getTable()->getByIssueID($issue->getID(), $request->getParameter('limit', 0), $request->getParameter('offset', 0));

            return $this->renderJSON(array('content' => $this->getComponentHTML('livelink/issuecommits', ["projectId" => $this->selected_project->getID(), "links" => $links])));
        }

    }

