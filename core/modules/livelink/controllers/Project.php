<?php

    namespace thebuggenie\core\modules\livelink\controllers;

    use thebuggenie\core\entities\Branch;
    use thebuggenie\core\entities\tables\Branches;
    use thebuggenie\core\entities\tables\Commits,
        thebuggenie\core\framework,
        thebuggenie\core\helpers\ProjectActions,
        thebuggenie\core\modules\livelink\Livelink;
    use thebuggenie\core\entities\tables\IssueCommits;
    use thebuggenie\core\entities\tables\Issues;

    /**
     * Main controller for the livelink module
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

                $commit = Commits::getTable()->getCommitByRef($from_commit_ref, $this->selected_project);
            }
            $commits = $branch->getCommits($commit);

            return $this->renderJSON(['content' => $this->getComponentHTML('livelink/projectcommitsbox', ['commits' => $commits, 'selected_project' => $this->selected_project, 'branch' => $branch])]);
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

                $commit = Commits::getTable()->getCommitByRef($from_commit_ref, $this->selected_project);
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

