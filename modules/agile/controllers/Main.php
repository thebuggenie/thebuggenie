<?php

    namespace thebuggenie\modules\agile\controllers;

    use thebuggenie\core\framework,
        thebuggenie\modules\agile\entities,
        thebuggenie\core\helpers;

    /**
     * Actions for the agile module
     *
     * @Routes(name_prefix="agile_", url_prefix="/:project_key/agile")
     */
    class Main extends helpers\ProjectActions
    {

        /**
         * Action for marking a milestone as completed, optionally moving issues across to a new milestone
         *
         * @Route(url="/boards/:board_id/milestone/:milestone_id/markfinished")
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runMarkMilestoneFinished(framework\Request $request)
        {
            try
            {
                if (!($this->getUser()->canManageProject($this->selected_project) || $this->getUser()->canManageProjectReleases($this->selected_project)))
                {
                    throw new \Exception($this->getI18n()->__("You don't have access to modify milestones"));
                }
                $return_options = array('finished' => 'ok');
                $board = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);
                $milestone = \thebuggenie\core\entities\Milestone::getB2DBTable()->selectById($request['milestone_id']);
                $reached_date = mktime(23, 59, 59, framework\Context::getRequest()->getParameter('milestone_finish_reached_month'), framework\Context::getRequest()->getParameter('milestone_finish_reached_day'), framework\Context::getRequest()->getParameter('milestone_finish_reached_year'));
                $milestone->setReachedDate($reached_date);
                $milestone->setReached();
                $milestone->setClosed(true);
                $milestone->save();
                if ($request->hasParameter('unresolved_issues_action'))
                {
                    switch ($request['unresolved_issues_action'])
                    {
                        case 'backlog':
                            \thebuggenie\core\entities\tables\Issues::getTable()->reAssignIssuesByMilestoneIds($milestone->getID(), null, 0);
                            break;
                        case 'reassign':
                            $new_milestone = \thebuggenie\core\entities\Milestone::getB2DBTable()->selectById($request['assign_issues_milestone_id']);
                            $return_options['new_milestone_id'] = $new_milestone->getID();
                            break;
                        case 'addnew':
                            $new_milestone = $this->_saveMilestoneDetails($request);
                            $return_options['component'] = $this->getComponentHTML('milestonebox', array('milestone' => $new_milestone, 'board' => $board));
                            $return_options['new_milestone_id'] = $new_milestone->getID();
                            break;
                    }
                    if (isset($new_milestone) && $new_milestone instanceof \thebuggenie\core\entities\Milestone)
                    {
                        \thebuggenie\core\entities\tables\Issues::getTable()->reAssignIssuesByMilestoneIds($milestone->getID(), $new_milestone->getID());
                    }
                }

                return $this->renderJSON($return_options);
            }
            catch (\Exception $e)
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => $e->getMessage()));
            }
        }

        /**
         * The agile boards list
         *
         * @Route
         *
         * @param framework\Request $request
         */
        public function runIndex(framework\Request $request)
        {
            $boards = entities\tables\AgileBoards::getTable()->getAvailableProjectBoards($this->getUser()->getID(), $this->selected_project->getID());
            $project_boards = array();
            $user_boards = array();
            foreach ($boards as $board)
            {
                if ($board->isPrivate())
                    $user_boards[$board->getID()] = $board;
                else
                    $project_boards[$board->getID()] = $board;
            }
            $this->project_boards = $project_boards;
            $this->user_boards = $user_boards;
        }

        /**
         * The project planning page
         *
         * @Route(url="/boards/:board_id")
         *
         * @param framework\Request $request
         */
        public function runBoard(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('agile_board'));
            $this->board = ($request['board_id']) ? entities\tables\AgileBoards::getTable()->selectById($request['board_id']) : new entities\AgileBoard();
            
            if (!$this->board instanceof entities\AgileBoard) {
                return $this->return404();
            }

            if ($request->isDelete())
            {
                $board_id = $this->board->getID();
                $this->board->delete();
                return $this->renderJSON(array('message' => $this->getI18n()->__('The board has been deleted'), 'board_id' => $board_id));
            }
            elseif ($request->isPost())
            {
                $this->board->setName($request['name']);
                $this->board->setDescription($request['description']);
                $this->board->setType($request['type']);
                $this->board->setProject($this->selected_project);
                $this->board->setIsPrivate($request['is_private']);
                $this->board->setUser(framework\Context::getUser());
                $this->board->setEpicIssuetype($request['epic_issuetype_id']);
                $this->board->setTaskIssuetype($request['task_issuetype_id']);
                list($type, $id) = explode('_', $request['backlog_search']);
                if ($type == 'predefined')
                {
                    $this->board->setAutogeneratedSearch($id);
                }
                else
                {
                    $this->board->setBacklogSearch($id);
                }
                $this->board->setUseSwimlanes((bool) $request['use_swimlane']);
                if ($this->board->usesSwimlanes())
                {
                    $details = $request['swimlane_'.$request['swimlane'].'_details'];
                    $this->board->setSwimlaneType($request['swimlane']);
                    $this->board->setSwimlaneIdentifier($request['swimlane_'.$request['swimlane'].'_identifier']);
                    if (isset($details[$this->board->getSwimlaneIdentifier()]))
                    {
                        $this->board->setSwimlaneFieldValues(explode(',', $details[$this->board->getSwimlaneIdentifier()]));
                    }
                }
                else
                {
                    $this->board->clearSwimlaneType();
                    $this->board->clearSwimlaneIdentifier();
                    $this->board->clearSwimlaneFieldValues();
                }
                $this->board->save();

                return $this->renderJSON(array('component' => $this->getComponentHTML('agile/boardbox', array('board' => $this->board)), 'id' => $this->board->getID(), 'private' => $this->board->isPrivate(), 'backlog_search' => $this->board->getBacklogSearchIdentifier(), 'saved' => 'ok'));
            }
        }

        /**
         * Whiteboard column edit
         *
         * @Route(url="/boards/:board_id/whiteboard/column/*")
         *
         * @param framework\Request $request
         */
        public function runWhiteboardColumn(framework\Request $request)
        {
            $board = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);
            $column = entities\BoardColumn::getB2DBTable()->selectById($request['column_id']);
            if (!$column instanceof entities\BoardColumn)
            {
                $column = new entities\BoardColumn();
                $column->setBoard($board);
            }

            $column_id = $column->getColumnOrRandomID();

            return $this->renderJSON(array('component' => $this->getComponentHTML('agile/editboardcolumn', compact('column', 'column_id')), 'status_element_id' => 'boardcolumn_'. $column_id .'_status'));
        }

        /**
         * The project board whiteboard page
         *
         * @Route(url="/boards/:board_id/whiteboard/issues/*")
         * @CsrfProtected
         *
         * @param framework\Request $request
         */
        public function runWhiteboardIssues(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('agile_board'));
            $this->board = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);

            $this->forward403unless($this->board instanceof entities\AgileBoard);

            try
            {
                if ($request->isPost())
                {
                    $issue = \thebuggenie\core\entities\tables\Issues::getTable()->selectById((int) $request['issue_id']);
                    $column = entities\BoardColumn::getB2DBTable()->selectById((int) $request['column_id']);
                    $milestone = \thebuggenie\core\entities\Milestone::getB2DBTable()->selectById((int) $request['milestone_id']);

                    $swimlane = null;
                    if ($request['swimlane_identifier'])
                    {
                        foreach ($column->getBoard()->getMilestoneSwimlanes($milestone) as $swimlane)
                        {
                            if ($swimlane->getIdentifier() == $request['swimlane_identifier']) break;
                        }
                    }

                    if ($request->hasParameter('transition_id'))
                    {
                        $transitions = array(\thebuggenie\core\entities\tables\WorkflowTransitions::getTable()->selectById((int) $request['transition_id']));

                        if ($transitions[0]->hasTemplate())
                        {
                            return $this->renderJSON(array('component' => $this->getComponentHTML('main/issue_workflow_transition', compact('issue')), 'transition_id' => $transitions[0]->getID()));
                        }

                        $transitions[0]->transitionIssueToOutgoingStepWithoutRequest($issue);
                    }
                    else
                    {
                        list ($status_ids, $transitions, $rule_status_valid) = $issue->getAvailableWorkflowStatusIDsAndTransitions();
                        $available_statuses = array_intersect($status_ids, $column->getStatusIds());

                        if ($rule_status_valid && count($available_statuses) == 1 && count($transitions[reset($available_statuses)]) == 1 && $transitions[reset($available_statuses)][0]->hasTemplate())
                        {
                            return $this->renderJSON(array('component' => $this->getComponentHTML('main/issue_workflow_transition', compact('issue')), 'transition_id' => $transitions[reset($available_statuses)][0]->getID()));
                        }

                        if (empty($available_statuses))
                        {
                            $this->getResponse()->setHttpStatus(400);
                            return $this->renderJSON(array('error' => $this->getI18n()->__('There are no valid transitions to any states in this column')));
                        }

                        if (count($available_statuses) > 1 || (count($available_statuses) == 1 && count($transitions[reset($available_statuses)]) > 1))
                            return $this->renderJSON(array('component' => $this->getComponentHTML('agile/whiteboardtransitionselector', array('issue' => $issue, 'transitions' => $transitions, 'statuses' => $available_statuses, 'new_column' => $column, 'board' => $column->getBoard(), 'swimlane_identifier' => $request['swimlane_identifier']))));

                        $transitions[reset($available_statuses)][0]->transitionIssueToOutgoingStepWithoutRequest($issue);
                    }

                    return $this->renderJSON(array('transition' => 'ok', 'issue' => $this->getComponentHTML('agile/whiteboardissue', array('issue' => $issue, 'column' => $column, 'swimlane' => $swimlane))));
                }
                else
                {
                    $milestone = \thebuggenie\core\entities\tables\Milestones::getTable()->selectById((int) $request['milestone_id']);
                    return $this->renderJSON(array('component' => $this->getComponentHTML('agile/whiteboardcontent', array('board' => $this->board, 'milestone' => $milestone)), 'swimlanes' => $this->board->usesSwimlanes() ? 1 : 0));
                }
            }
            catch (\Exception $e)
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => $e->getMessage()));
            }
        }

        /**
         * Get milestone status for a board
         *
         * @Route(url="/milestonestatus")
         *
         * @param framework\Request $request
         */
        public function runWhiteboardMilestoneStatus(framework\Request $request)
        {
            $milestone = \thebuggenie\core\entities\tables\Milestones::getTable()->selectById((int) $request['milestone_id']);
            return $this->renderJSON(array('content' => $this->getComponentHTML('project/milestonevirtualstatusdetails', array('milestone' => $milestone))));
        }

        /**
         * The project board whiteboard page
         *
         * @Route(url="/boards/:board_id/whiteboard")
         *
         * @param framework\Request $request
         */
        public function runWhiteboard(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('agile_board'));
            $this->board = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);

            $this->forward403unless($this->board instanceof entities\AgileBoard);

            try
            {
                if ($request->isPost())
                {
                    $columns = $request['columns'];
                    $saved_columns = array();
                    $cc = 1;
                    if (is_array($columns))
                    {
                        foreach ($columns as $details)
                        {
                            if ($details['column_id'])
                            {
                                $column = entities\BoardColumn::getB2DBTable()->selectById($details['column_id']);
                            }
                            else
                            {
                                $column = new entities\BoardColumn();
                                $column->setBoard($this->board);
                            }
                            if (!$column instanceof entities\BoardColumn)
                            {
                                throw new \Exception($this->getI18n()->__('There was an error trying to save column %column', array('%column' => $details['column_id'])));
                            }
                            $column->setName($details['name']);
                            $column->setSortOrder($details['sort_order']);
                            if (array_key_exists('min_workitems', $details)) $column->setMinWorkitems($details['min_workitems']);
                            if (array_key_exists('max_workitems', $details)) $column->setMaxWorkitems($details['max_workitems']);
                            $column->setStatusIds(explode(',', $details['status_ids']));
                            $column->save();
                            $saved_columns[$column->getID()] = $column->getID();
                            $cc++;
                        }
                    }
                    foreach ($this->board->getColumns() as $column)
                    {
                        if (!array_key_exists($column->getID(), $saved_columns))
                        {
                            $column->delete();
                        }
                    }
                    return $this->renderJSON(array('forward' => $this->getRouting()->generate('agile_whiteboard', array('project_key' => $this->board->getProject()->getKey(), 'board_id' => $this->board->getID()))));
                }
            }
            catch (\Exception $e)
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => $e->getMessage()));
            }

            $this->selected_milestone = $this->board->getDefaultSelectedMilestone();
        }

        /**
         * Issue retriever for the project planning page
         *
         * @Route(url="/boards/:board_id/retrieveissue/:mode")
         *
         * @param framework\Request $request
         */
        public function runRetrieveIssue(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_planning'));
            $board = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);
            $issue = \thebuggenie\core\entities\Issue::getB2DBTable()->selectById($request['issue_id']);

            $this->forward403unless($issue instanceof \thebuggenie\core\entities\Issue && $issue->hasAccess());

            $text = array('child_issue' => 0, 'issue_details' => $issue->toJSON());

            if ($request['mode'] == 'whiteboard')
            {
                $text['swimlane_type'] = $board->getSwimlaneType();

                if ($board->getSwimlaneType() == $request['swimlane_type'])
                {
                    if ($issue->getMilestone() instanceof \thebuggenie\core\entities\Milestone && $issue->getMilestone()->getID() == $request['milestone_id'])
                    {
                        foreach ($board->getMilestoneSwimlanes($issue->getMilestone()) as $swimlane)
                        {
                            if ($swimlane->getBoard()->usesSwimlanes()
                                && $swimlane->hasIdentifiables()
                                && $swimlane->getBoard()->getSwimlaneType() == entities\AgileBoard::SWIMLANES_ISSUES
                                && $swimlane->getIdentifierIssue()->getID() == $issue->getID())
                            {
                                $text['swimlane_identifier'] = $swimlane->getIdentifier();
                                $text['column_id'] = $request['column_id'];
                                $component = $this->getComponentHTML('agile/boardswimlane', compact('swimlane'));
                                break;
                            }

                            $issue_in_swimlane = false;

                            foreach ($swimlane->getIssues() as $swimlane_issue)
                            {
                                if ($swimlane_issue->getID() == $issue->getID())
                                {
                                    $issue_in_swimlane = true;
                                    break;
                                }
                            }

                            if (! $issue_in_swimlane) continue;

                            foreach ($swimlane->getBoard()->getColumns() as $column)
                            {
                                if (! $column->hasIssue($issue)) continue;

                                if ($issue->isChildIssue())
                                {
                                    foreach ($issue->getParentIssues() as $parent)
                                    {
                                        if ($parent->getIssueType()->getID() == $board->getEpicIssuetypeID()) continue;

                                        $text['child_issue'] = 1;
                                    }
                                }

                                $text['swimlane_identifier'] = $swimlane->getIdentifier();
                                $text['column_id'] = $column->getID();
                                $component = $this->getComponentHTML('agile/whiteboardissue', compact('issue', 'column', 'swimlane'));
                                break 2;
                            }
                        }
                    }
                }
            }
            else
            {
                if ($issue->isChildIssue())
                {
                    foreach ($issue->getParentIssues() as $parent)
                    {
                        if ($parent->getIssueType()->getID() == $board->getEpicIssuetypeID()) continue;

                        return $this->renderJSON(array('child_issue' => 1, 'issue_details' => array('milestone' => array('id' => -1))));
                    }
                }
                elseif ($issue->getIssueType()->getID() == $board->getEpicIssuetypeID())
                {
                    return $this->renderJSON(array('child_issue' => 0, 'epic' => 1, 'component' => $this->getComponentHTML('agile/milestoneepic', array('epic' => $issue, 'board' => $board)), 'issue_details' => $issue->toJSON()));
                }

                $component = $this->getComponentHTML('agile/milestoneissue', compact('issue', 'board'));
            }

            $text['component'] = isset($component) ? $component : '';

            return $this->renderJSON($text);
        }

        /**
         * Retrieves a list of all releases on a board
         *
         * @Route(url="/boards/:board_id/getreleases")
         *
         * @param framework\Request $request
         */
        public function runGetReleases(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_planning'));
            $board = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);

            return $this->renderComponent('agile/releasestrip', compact('board'));
        }

        /**
         * Retrieves a list of all epics on a board
         *
         * @Route(url="/boards/:board_id/getepics")
         *
         * @param framework\Request $request
         */
        public function runGetEpics(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_planning'));
            $board = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);

            return $this->renderComponent('agile/epicstrip', compact('board'));
        }

        /**
         * Adds an epic
         *
         * @Route(url="/boards/:board_id/addepic")
         *
         * @param framework\Request $request
         */
        public function runAddEpic(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_planning'));
            $board = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);

            try
            {
                $title = trim($request['title']);
                $shortname = trim($request['shortname']);
                if (!$title)
                    throw new \Exception($this->getI18n()->__('You have to provide a title'));
                if (!$shortname)
                    throw new \Exception($this->getI18n()->__('You have to provide a label'));

                $issue = new \thebuggenie\core\entities\Issue();
                $issue->setTitle($title);
                $issue->setShortname($shortname);
                $issue->setIssuetype($board->getEpicIssuetypeID());
                $issue->setProject($board->getProject());
                $issue->setPostedBy($this->getUser());
                $issue->save();

                return $this->renderJSON(array('issue_details' => $issue->toJSON()));
            }
            catch (\Exception $e)
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => $e->getMessage()));
            }
        }

        /**
         * Retrieving or sorting milestone issues
         *
         * @Route(url="/boards/:board_id/milestone/:milestone_id/issues")
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runMilestoneIssues(framework\Request $request)
        {
            try
            {
                switch (true)
                {
                    case $request->isPost():
                        $issue_table = \thebuggenie\core\entities\tables\Issues::getTable();
                        $orders = array_keys($request["issue_ids"] ?: array());
                        foreach ($request["issue_ids"] ?: array() as $issue_id)
                        {
                            $issue_table->setOrderByIssueId(array_pop($orders), $issue_id);
                        }
                        return $this->renderJSON(array('sorted' => 'ok'));
                    default:
                        if ($request->getParameter('milestone_id'))
                            $milestone = \thebuggenie\core\entities\tables\Milestones::getTable()->selectById($request['milestone_id']);

                        $board = ($request['board_id']) ? entities\tables\AgileBoards::getTable()->selectById($request['board_id']) : new entities\AgileBoard();
                        $component = (isset($milestone) && $milestone instanceof \thebuggenie\core\entities\Milestone) ? 'milestoneissues' : 'backlog';
                        
                        return $this->renderJSON(array('content' => $this->getComponentHTML("agile/{$component}", compact('milestone', 'board'))));
                }
            }
            catch (\Exception $e)
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => $e->getMessage()));
            }
        }

        protected function _saveMilestoneDetails(framework\Request $request, $milestone = null)
        {
            if (!$request['name'])
                throw new \Exception($this->getI18n()->__('You must provide a valid milestone name'));

            if ($milestone === null) $milestone = new \thebuggenie\core\entities\Milestone();
            $milestone->setName($request['name']);
            $milestone->setProject($this->selected_project);
            $milestone->setStarting((bool) $request['is_starting']);
            $milestone->setScheduled((bool) $request['is_scheduled']);
            $milestone->setDescription($request['description']);
            $milestone->setVisibleRoadmap($request['visibility_roadmap']);
            $milestone->setVisibleIssues($request['visibility_issues']);
            $milestone->setType($request->getParameter('milestone_type', \thebuggenie\core\entities\Milestone::TYPE_REGULAR));
            if ($request->hasParameter('sch_month') && $request->hasParameter('sch_day') && $request->hasParameter('sch_year'))
            {
                $scheduled_date = mktime(23, 59, 59, framework\Context::getRequest()->getParameter('sch_month'), framework\Context::getRequest()->getParameter('sch_day'), framework\Context::getRequest()->getParameter('sch_year'));
                $milestone->setScheduledDate($scheduled_date);
            }
            else
                $milestone->setScheduledDate(0);

            if ($request->hasParameter('starting_month') && $request->hasParameter('starting_day') && $request->hasParameter('starting_year'))
            {
                $starting_date = mktime(0, 0, 1, framework\Context::getRequest()->getParameter('starting_month'), framework\Context::getRequest()->getParameter('starting_day'), framework\Context::getRequest()->getParameter('starting_year'));
                $milestone->setStartingDate($starting_date);
            }
            else
                $milestone->setStartingDate(0);

            $milestone->save();
        }

        /**
         * Assign a user story to a milestone id
         *
         * @Route(url="/assign/issue/milestone/:milestone_id")
         *
         * @param framework\Request $request
         */
        public function runAssignMilestone(framework\Request $request)
        {
            $this->forward403if(framework\Context::getCurrentProject()->isArchived());
            $this->forward403unless($this->_checkProjectPageAccess('project_scrum') && framework\Context::getUser()->canAssignScrumUserStories($this->selected_project));
            
            try
            {
                $issue = \thebuggenie\core\entities\Issue::getB2DBTable()->selectById((int) $request['issue_id']);
                $milestone = \thebuggenie\core\entities\tables\Milestones::getTable()->selectById($request['milestone_id']);

                if (!$issue instanceof \thebuggenie\core\entities\Issue)
                    throw new \Exception($this->getI18n ()->__('This is not a valid issue'));

                $issue->setMilestone($milestone);
                $issue->save();
                foreach ($issue->getChildIssues() as $child_issue)
                {
                    $child_issue->setMilestone($milestone);
                    $child_issue->save();
                }
                $new_issues = ($milestone instanceof \thebuggenie\core\entities\Milestone) ? $milestone->countIssues() : 0;
                $new_e_points = ($milestone instanceof \thebuggenie\core\entities\Milestone) ? $milestone->getPointsEstimated() : 0;
                $new_e_hours = ($milestone instanceof \thebuggenie\core\entities\Milestone) ? $milestone->getHoursEstimated() : 0;
                return $this->renderJSON(array('issue_id' => $issue->getID(), 'issues' => $new_issues, 'points' => $new_e_points, 'hours' => $new_e_hours));
            }
            catch (\Exception $e)
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => $e->getMessage()));
            }
        }

        /**
         * Assign a user story to a release
         *
         * @Route(url="/assign/issue/release/:release_id")
         *
         * @param framework\Request $request
         */
        public function runAssignRelease(framework\Request $request)
        {
            try
            {
                $issue = \thebuggenie\core\entities\Issue::getB2DBTable()->selectById((int) $request['issue_id']);
                $release = \thebuggenie\core\entities\tables\Builds::getTable()->selectById((int) $request['release_id']);

                $issue->addAffectedBuild($release);

                return $this->renderJSON(array('issue_id' => $issue->getID(), 'release_id' => $release->getID(), 'closed_pct' => $release->getPercentComplete()));
            }
            catch (\Exception $e)
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => framework\Context::getI18n()->__('An error occured when trying to assign the issue to the release')));
            }
        }

        /**
         * Assign an issue to an epic
         *
         * @Route(url="/assign/issue/epic/:epic_id")
         *
         * @param framework\Request $request
         */
        public function runAssignEpic(framework\Request $request)
        {
            try
            {
                $epic = \thebuggenie\core\entities\Issue::getB2DBTable()->selectById((int) $request['epic_id']);
                $issue = \thebuggenie\core\entities\Issue::getB2DBTable()->selectById((int) $request['issue_id']);

                $epic->addChildIssue($issue, true);

                return $this->renderJSON(array('issue_id' => $issue->getID(), 'epic_id' => $epic->getID(), 'closed_pct' => $epic->getEstimatedPercentCompleted(), 'num_child_issues' => $epic->countChildIssues(), 'estimate' => \thebuggenie\core\entities\Issue::getFormattedTime($epic->getEstimatedTime()), 'text_color' => $epic->getAgileTextColor()));
            }
            catch (\Exception $e)
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => framework\Context::getI18n()->__('An error occured when trying to assign the issue to the epic')));
            }
        }

        /**
         * Milestone actions
         *
         * @Route(url="/milestone/:milestone_id/*")
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runMilestone(framework\Request $request)
        {
            $milestone_id = ($request['milestone_id']) ? $request['milestone_id'] : null;
            $milestone = new \thebuggenie\core\entities\Milestone($milestone_id);

            try
            {
                if (!$this->getUser()->canManageProject($this->selected_project) || !$this->getUser()->canManageProjectReleases($this->selected_project))
                    throw new \Exception($this->getI18n()->__("You don't have access to modify milestones"));

                switch (true)
                {
                    case $request->isDelete():
                        $milestone->delete();

                        $no_milestone = new \thebuggenie\core\entities\Milestone(0);
                        $no_milestone->setProject($milestone->getProject());
                        return $this->renderJSON(array('issue_count' => $no_milestone->countIssues(), 'hours' => $no_milestone->getHoursEstimated(), 'points' => $no_milestone->getPointsEstimated()));
                    case $request->isPost():
                        $this->_saveMilestoneDetails($request, $milestone);
                        $board = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);

                        if ($request->hasParameter('issues') && $request['include_selected_issues'])
                            \thebuggenie\core\entities\tables\Issues::getTable()->assignMilestoneIDbyIssueIDs($milestone->getID(), $request['issues']);

                        $message = framework\Context::getI18n()->__('Milestone saved');
                        return $this->renderJSON(array('message' => $message, 'component' => $this->getComponentHTML('agile/milestonebox', array('milestone' => $milestone, 'board' => $board)), 'milestone_id' => $milestone->getID()));
                    default:
                        return $this->renderJSON(array('content' => framework\Action::returnComponentHTML('agile/milestonebox', array('milestone' => $milestone)), 'milestone_id' => $milestone->getID(), 'milestone_name' => $milestone->getName(), 'milestone_order' => array_keys($milestone->getProject()->getMilestonesForRoadmap())));
                }
            }
            catch (\Exception $e)
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => $e->getMessage()));
            }
        }

        /**
         * Poller for the planning page
         *
         * @Route(url="/boards/:board_id/poll/:mode")
         *
         * @param framework\Request $request
         */
        public function runPoll(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectPageAccess('project_planning'));
            $last_refreshed = $request['last_refreshed'];
            $board = entities\tables\AgileBoards::getTable()->selectById($request['board_id']);
            $search_object = $board->getBacklogSearchObject();
            if ($search_object instanceof \thebuggenie\core\entities\SavedSearch) 
            {
                $search_object->setFilter('last_updated', \thebuggenie\core\entities\SearchFilter::createFilter('last_updated', array('o' => \b2db\Criteria::DB_GREATER_THAN_EQUAL, 'v' => $last_refreshed - 2)));
            }

            if ($request['mode'] == 'whiteboard')
            {
                $milestone_id = $request['milestone_id'];
                $ids = \thebuggenie\core\entities\tables\Issues::getTable()->getUpdatedIssueIDsByTimestampAndProjectIDAndMilestoneID($last_refreshed - 2, $this->selected_project->getID(), $milestone_id);
            }
            else
            {
                $ids = \thebuggenie\core\entities\tables\Issues::getTable()->getUpdatedIssueIDsByTimestampAndProjectIDAndIssuetypeID($last_refreshed - 2, $this->selected_project->getID());
                $epic_ids = ($board->getEpicIssuetypeID()) ? \thebuggenie\core\entities\tables\Issues::getTable()->getUpdatedIssueIDsByTimestampAndProjectIDAndIssuetypeID($last_refreshed - 2, $this->selected_project->getID(), $board->getEpicIssuetypeID()) : array();
            }

            $backlog_ids = array();
            if ($search_object instanceof \thebuggenie\core\entities\SavedSearch) 
            {
                foreach ($search_object->getIssues() as $backlog_issue)
                {
                    foreach ($ids as $id_issue) {
                        if ($id_issue['issue_id'] == $backlog_issue->getID()) continue 2;
                    }

                    $backlog_ids[] = array('issue_id' => $backlog_issue->getID(), 'last_updated' => $backlog_issue->getLastUpdatedTime());
                }
            }

            return $this->renderJSON(compact('ids', 'backlog_ids', 'epic_ids', 'milestone_id'));
        }

    }

