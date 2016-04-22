<?php

    namespace thebuggenie\core\helpers;

    use thebuggenie\core\framework,
        thebuggenie\core\entities;

    /**
     * actions for the project module
     */
    class ProjectActions extends framework\Action
    {
        /**
         * The currently selected project
         *
         * @var entities\Project
         * @access protected
         * @property $selected_project
         */

        /**
         * Pre-execute function
         *
         * @param framework\Request $request
         * @param string $action
         */
        public function preExecute(framework\Request $request, $action)
        {
            try
            {
                if ($project_id = $request['project_id'])
                    $this->selected_project = entities\Project::getB2DBTable()->selectById($project_id);
                elseif ($project_key = $request['project_key'])
                    $this->selected_project = entities\Project::getByKey($project_key);
            }
            catch (\Exception $e) { }

            if (!$this->selected_project instanceof entities\Project)
                return $this->return404(framework\Context::getI18n()->__('This project does not exist'));

            framework\Context::setCurrentProject($this->selected_project);
            $this->project_key = $this->selected_project->getKey();
        }

        protected function _checkProjectPageAccess($page)
        {
            return framework\Context::getUser()->hasProjectPageAccess($page, $this->selected_project);
        }

        public function runListIssues(framework\Request $request)
        {
            $filters = array('project_id' => array('v' => $this->selected_project->getID(), 'o' => '='));
            $filter_state = $request->getParameter('state', 'open');
            $filter_issuetype = $request->getParameter('issuetype', 'all');
            $filter_assigned_to = $request->getParameter('assigned_to', 'all');
            $filter_relation = $request->getParameter('relation');

            if (mb_strtolower($filter_state) != 'all')
            {
                $filters['state'] = array('o' => '=', 'v' => '');
                if (mb_strtolower($filter_state) == 'open')
                    $filters['state']['v'] = entities\Issue::STATE_OPEN;
                elseif (mb_strtolower($filter_state) == 'closed')
                    $filters['state']['v'] = entities\Issue::STATE_CLOSED;
            }

            if (mb_strtolower($filter_issuetype) != 'all')
            {
                $issuetype = entities\Issuetype::getByKeyish($filter_issuetype);
                if ($issuetype instanceof entities\Issuetype)
                {
                    $filters['issuetype'] = array('o' => '=', 'v' => $issuetype->getID());
                }
            }

            if (mb_strtolower($filter_assigned_to) != 'all')
            {
                $user_id = 0;
                switch (mb_strtolower($filter_assigned_to))
                {
                    case 'me':
                        $user_id = framework\Context::getUser()->getID();
                        break;
                    case 'none':
                        $user_id = 0;
                        break;
                    default:
                        try
                        {
                            $user = entities\User::findUser(mb_strtolower($filter_assigned_to));
                            if ($user instanceof entities\User)
                                $user_id = $user->getID();
                        }
                        catch (\Exception $e)
                        {

                        }
                        break;
                }

                $filters['assignee_user'] = array('o' => '=', 'v' => $user_id);
            }

            if (is_numeric($filter_relation) && in_array((string) $filter_relation, array('4', '3', '2', '1', '0')))
            {
                $filters['relation'] = array('o' => '=', 'v' => $filter_relation);
            }

            $filters = array_map(function ($key, $options)
            {
                return \thebuggenie\core\entities\SearchFilter::createFilter($key, $options);
            }, array_keys($filters), $filters);

            list ($this->issues, $this->count) = entities\Issue::findIssues($filters, 50);
            $this->return_issues = array();
        }

        public function runListWorkflowTransitions(framework\Request $request)
        {
            $i18n = framework\Context::getI18n();
            $issue = entities\Issue::getIssueFromLink($request['issue_no']);
            if ($issue->getProject()->getID() != $this->selected_project->getID())
            {
                throw new \Exception($i18n->__('This issue is not valid for this project'));
            }
            $transitions = array();
            foreach ($issue->getAvailableWorkflowTransitions() as $transition)
            {
                if (!$transition instanceof entities\WorkflowTransition)
                    continue;
                $details = array('name' => $transition->getName(), 'description' => $transition->getDescription(), 'template' => $transition->getTemplate());
                if ($details['template'])
                {
                    $details['post_validation'] = array();
                    foreach ($transition->getPostValidationRules() as $rule)
                    {
                        $details['post_validation'][] = array('name' => $rule->getRule(), 'values' => $rule->getRuleValueAsJoinedString());
                    }
                }
                $transitions[] = $details;
            }
            $this->transitions = $transitions;
        }

        /**
         * View an issue
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runViewIssue(framework\Request $request)
        {
            framework\Logging::log('Loading issue');

            $issue = $this->_getIssueFromRequest($request);

            if ($issue instanceof entities\Issue)
            {
                if (!array_key_exists('viewissue_list', $_SESSION))
                {
                    $_SESSION['viewissue_list'] = array();
                }

                $k = array_search($issue->getID(), $_SESSION['viewissue_list']);
                if ($k !== false)
                    unset($_SESSION['viewissue_list'][$k]);

                array_push($_SESSION['viewissue_list'], $issue->getID());

                if (count($_SESSION['viewissue_list']) > 10)
                    array_shift($_SESSION['viewissue_list']);

                $this->getUser()->markNotificationsRead('issue', $issue->getID());

                framework\Context::getUser()->setNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_ITEM_ONCE . '_issue_' . $issue->getID(), false)->save();

                \thebuggenie\core\framework\Event::createNew('core', 'viewissue', $issue)->trigger();
            }

            $message = framework\Context::getMessageAndClear('issue_saved');
            $uploaded = framework\Context::getMessageAndClear('issue_file_uploaded');

            if ($request->isPost() && $issue instanceof entities\Issue && $request->hasParameter('issue_action'))
            {
                if ($request['issue_action'] == 'save')
                {
                    if (!$issue->hasMergeErrors())
                    {
                        try
                        {
                            $issue->getWorkflow()->moveIssueToMatchingWorkflowStep($issue);
                            // Currently if category is changed we want to regenerate permissions since category is used for granting user access.
                            if ($issue->isCategoryChanged())
                            {
                                framework\Event::listen('core', 'thebuggenie\core\entities\Issue::save_pre_notifications', array($this, 'listen_issueCreate'));
                            }
                            $issue->save();
                            framework\Context::setMessage('issue_saved', true);
                            $this->forward(framework\Context::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
                        }
                        catch (\thebuggenie\core\exceptions\WorkflowException $e)
                        {
                            $this->error = $e->getMessage();
                            $this->workflow_error = true;
                        }
                        catch (\Exception $e)
                        {
                            $this->error = $e->getMessage();
                        }
                    }
                    else
                    {
                        $this->issue_unsaved = true;
                    }
                }
            }
            elseif (framework\Context::hasMessage('issue_deleted_shown') && (is_null($issue) || ($issue instanceof entities\Issue && $issue->isDeleted())))
            {
                $request_referer = ($request['referer'] ?: isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);

                if ($request_referer)
                {
                    return $this->forward($request_referer);
                }
            }
            elseif (framework\Context::hasMessage('issue_deleted'))
            {
                $this->issue_deleted = framework\Context::getMessageAndClear('issue_deleted');
                framework\Context::setMessage('issue_deleted_shown', true);
            }
            elseif ($message == true)
            {
                $this->issue_saved = true;
            }
            elseif ($uploaded == true)
            {
                $this->issue_file_uploaded = true;
            }
            elseif (framework\Context::hasMessage('issue_error'))
            {
                $this->error = framework\Context::getMessageAndClear('issue_error');
            }
            elseif (framework\Context::hasMessage('issue_message'))
            {
                $this->issue_message = framework\Context::getMessageAndClear('issue_message');
            }

            $this->issue = $issue;
            $event = \thebuggenie\core\framework\Event::createNew('core', 'viewissue', $issue)->trigger();
            $this->listenViewIssuePostError($event);
        }

        public function runUpdateIssueDetails(framework\Request $request)
        {
            $this->forward403if(framework\Context::getCurrentProject()->isArchived());
            $this->error = false;
            try
            {
                $i18n = framework\Context::getI18n();
                $issue = entities\Issue::getIssueFromLink($request['issue_no']);
                if ($issue->getProject()->getID() != $this->selected_project->getID())
                {
                    throw new \Exception($i18n->__('This issue is not valid for this project'));
                }
                if (!$issue instanceof entities\Issue)
                {
                    throw new \Exception($i18n->__('Cannot find this issue'));
                }

                $workflow_transition = null;
                if ($passed_transition = $request['workflow_transition'])
                {
                    //echo "looking for transition ";
                    $key = str_replace(' ', '', mb_strtolower($passed_transition));
                    //echo $key . "\n";
                    foreach ($issue->getAvailableWorkflowTransitions() as $transition)
                    {
                        //echo str_replace(' ', '', mb_strtolower($transition->getName())) . "?";
                        if (mb_strpos(str_replace(' ', '', mb_strtolower($transition->getName())), $key) !== false)
                        {
                            $workflow_transition = $transition;
                            //echo "found transition " . $transition->getID();
                            break;
                        }
                        //echo "no";
                    }

                    if (!$workflow_transition instanceof entities\WorkflowTransition)
                        throw new \Exception("This transition ({$key}) is not valid");
                }
                $fields = $request->getRawParameter('fields', array());
                $return_values = array();
                if ($workflow_transition instanceof entities\WorkflowTransition)
                {
                    foreach ($fields as $field_key => $field_value)
                    {
                        $classname = "\\thebuggenie\\core\\entities\\" . ucfirst($field_key);
                        $method = "set" . ucfirst($field_key);
                        $choices = $classname::getAll();
                        $found = false;
                        foreach ($choices as $choice_key => $choice)
                        {
                            if (mb_strpos(str_replace(' ', '', mb_strtolower($choice->getName())), str_replace(' ', '', mb_strtolower($field_value))) !== false)
                            {
                                $request->setParameter($field_key . '_id', $choice->getId());
                                break;
                            }
                        }
                    }
                    $request->setParameter('comment_body', $request['message']);
                    $return_values['applied_transition'] = $workflow_transition->getName();
                    if ($workflow_transition->validateFromRequest($request))
                    {
                        $retval = $workflow_transition->transitionIssueToOutgoingStepFromRequest($issue, $request);
                        $return_values['transition_ok'] = ($retval === false) ? false : true;
                    }
                    else
                    {
                        $return_values['transition_ok'] = false;
                        $return_values['message'] = "Please pass all information required for this transition";
                    }
                }
                elseif ($issue->isUpdateable())
                {
                    foreach ($fields as $field_key => $field_value)
                    {
                        try
                        {
                            if (in_array($field_key, array_merge(array('title', 'state'), entities\Datatype::getAvailableFields(true))))
                            {
                                switch ($field_key)
                                {
                                    case 'state':
                                        $issue->setState(($field_value == 'open') ? entities\Issue::STATE_OPEN : entities\Issue::STATE_CLOSED);
                                        break;
                                    case 'title':
                                        if ($field_value != '')
                                            $issue->setTitle($field_value);
                                        else
                                            throw new \Exception($i18n->__('Invalid title'));
                                        break;
                                    case 'shortname':
                                    case 'description':
                                    case 'reproduction_steps':
                                        $method = "set" . ucfirst($field_key);
                                        $issue->$method($field_value);
                                        break;
                                    case 'status':
                                    case 'resolution':
                                    case 'reproducability':
                                    case 'priority':
                                    case 'severity':
                                    case 'category':
                                        $classname = "\\thebuggenie\\core\\entities\\" . ucfirst($field_key);
                                        $method = "set" . ucfirst($field_key);
                                        $choices = $classname::getAll();
                                        $found = false;
                                        foreach ($choices as $choice_key => $choice)
                                        {
                                            if (str_replace(' ', '', mb_strtolower($choice->getName())) == str_replace(' ', '', mb_strtolower($field_value)))
                                            {
                                                $issue->$method($choice);
                                                $found = true;
                                            }
                                        }
                                        if (!$found)
                                        {
                                            throw new \Exception('Could not find this value');
                                        }
                                        break;
                                    case 'percent_complete':
                                        $issue->setPercentCompleted($field_value);
                                        break;
                                    case 'owner':
                                    case 'assignee':
                                        $set_method = "set" . ucfirst($field_key);
                                        $unset_method = "un{$set_method}";
                                        switch (mb_strtolower($field_value))
                                        {
                                            case 'me':
                                                $issue->$set_method(framework\Context::getUser());
                                                break;
                                            case 'none':
                                                $issue->$unset_method();
                                                break;
                                            default:
                                                try
                                                {
                                                    $user = entities\User::findUser(mb_strtolower($field_value));
                                                    if ($user instanceof entities\User)
                                                        $issue->$set_method($user);
                                                }
                                                catch (\Exception $e)
                                                {
                                                    throw new \Exception('No such user found');
                                                }
                                                break;
                                        }
                                        break;
                                    case 'estimated_time':
                                    case 'spent_time':
                                        $set_method = "set" . ucfirst(str_replace('_', '', $field_key));
                                        $issue->$set_method($field_value);
                                        break;
                                    case 'milestone':
                                        $found = false;
                                        foreach ($this->selected_project->getMilestones() as $milestone)
                                        {
                                            if (str_replace(' ', '', mb_strtolower($milestone->getName())) == str_replace(' ', '', mb_strtolower($field_value)))
                                            {
                                                $issue->setMilestone($milestone->getID());
                                                $found = true;
                                            }
                                        }
                                        if (!$found)
                                        {
                                            throw new \Exception('Could not find this milestone');
                                        }
                                        break;
                                    default:
                                        throw new \Exception($i18n->__('Invalid field'));
                                }
                            }
                            $return_values[$field_key] = array('success' => true);
                        }
                        catch (\Exception $e)
                        {
                            $return_values[$field_key] = array('success' => false, 'error' => $e->getMessage());
                        }
                    }
                }

                if (!$workflow_transition instanceof entities\WorkflowTransition)
                    $issue->getWorkflow()->moveIssueToMatchingWorkflowStep($issue);

                if (!array_key_exists('transition_ok', $return_values) || $return_values['transition_ok'])
                {
                    $comment = new entities\Comment();
                    $comment->setContent($request->getParameter('message', null, false));
                    $comment->setPostedBy(framework\Context::getUser()->getID());
                    $comment->setTargetID($issue->getID());
                    $comment->setTargetType(entities\Comment::TYPE_ISSUE);
                    $comment->setModuleName('core');
                    $comment->setIsPublic(true);
                    $comment->setSystemComment(false);
                    $comment->save();
                    $issue->setSaveComment($comment);
                    $issue->save();
                }

                $this->return_values = $return_values;
            }
            catch (\Exception $e)
            {
                //$this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
            }
        }

        protected function _getIssueFromRequest(framework\Request $request)
        {
            $issue = null;
            if ($issue_no = framework\Context::getRequest()->getParameter('issue_no'))
            {
                $issue = entities\Issue::getIssueFromLink($issue_no);
                if ($issue instanceof entities\Issue)
                {
                    if (!$this->selected_project instanceof entities\Project || $issue->getProjectID() != $this->selected_project->getID())
                    {
                        $issue = null;
                    }
                }
                else
                {
                    framework\Logging::log("Issue no [$issue_no] not a valid issue no", 'main', framework\Logging::LEVEL_WARNING_RISK);
                }
            }
            framework\Logging::log('done (Loading issue)');
            if ($issue instanceof entities\Issue && (!$issue->hasAccess() || $issue->isDeleted()))
                $issue = null;

            return $issue;
        }

        /**
         * Go to the next/previous open issue
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runNavigateIssue(framework\Request $request)
        {
            $issue = $this->_getIssueFromRequest($request);

            if (!$issue instanceof entities\Issue)
            {
                $this->getResponse()->setTemplate('viewissue');
                return;
            }

            do
            {
                if ($issue->getMilestone() instanceof entities\Milestone) {
                    if ($request['direction'] == 'next') {
                        $found_issue = tables\Issues::getTable()->getNextIssueFromIssueMilestoneOrderAndMilestoneID($issue->getMilestoneOrder(), $issue->getMilestone()->getID(), $request['mode'] == 'open');
                    } else {
                        $found_issue = tables\Issues::getTable()->getPreviousIssueFromIssueMilestoneOrderAndMilestoneID($issue->getMilestoneOrder(), $issue->getMilestone()->getID(), $request['mode'] == 'open');
                    }
                } else {
                    if ($request['direction'] == 'next') {
                        $found_issue = tables\Issues::getTable()->getNextIssueFromIssueIDAndProjectID($issue->getID(), $issue->getProject()->getID(), $request['mode'] == 'open');
                    } else {
                        $found_issue = tables\Issues::getTable()->getPreviousIssueFromIssueIDAndProjectID($issue->getID(), $issue->getProject()->getID(), $request['mode'] == 'open');
                    }
                }
                if (is_null($found_issue))
                    break;
            }
            while ($found_issue instanceof entities\Issue && !$found_issue->hasAccess());

            if ($found_issue instanceof entities\Issue)
            {
                $this->forward(framework\Context::getRouting()->generate('viewissue', array('project_key' => $found_issue->getProject()->getKey(), 'issue_no' => $found_issue->getFormattedIssueNo())));
            }
            else
            {
                framework\Context::setMessage('issue_message', $this->getI18n()->__('There are no more issues in that direction.'));
                $this->forward(framework\Context::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
            }
        }

        public function listenViewIssuePostError(\thebuggenie\core\framework\Event $event)
        {
            if (framework\Context::hasMessage('comment_error'))
            {
                $this->comment_error = true;
                $this->error = framework\Context::getMessageAndClear('comment_error');
                $this->comment_error_body = framework\Context::getMessageAndClear('comment_error_body');
            }
        }


    }