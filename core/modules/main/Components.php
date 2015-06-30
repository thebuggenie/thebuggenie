<?php

    namespace thebuggenie\core\modules\main;

    use thebuggenie\core\framework,
        thebuggenie\core\entities,
        thebuggenie\core\entities\tables;

    /**
     * Main action components
     *
     * @property entities\Issue $issue The issue
     *
     */
    class Components extends framework\ActionComponent
    {

        public function componentUserdropdown()
        {
            framework\Logging::log('user dropdown component');
            $this->rnd_no = rand();
            try
            {
                if (!$this->user instanceof entities\User)
                {
                    framework\Logging::log('loading user object in dropdown');
                    if (is_numeric($this->user))
                    {
                        $this->user = tables\Users::getTable()->getByUserId($this->user);
                    }
                    else
                    {
                        $this->user = tables\Users::getTable()->getByUsername($this->user);
                    }
                    framework\Logging::log('done (loading user object in dropdown)');
                }
            }
            catch (\Exception $e)
            {

            }
            $this->show_avatar = (isset($this->show_avatar)) ? $this->show_avatar : true;
            framework\Logging::log('done (user dropdown component)');
        }

        public function componentUserdropdown_Inline()
        {
            $this->componentUserdropdown();
        }

        public function componentClientusers()
        {
            try
            {
                if (!$this->client instanceof entities\Client)
                {
                    framework\Logging::log('loading user object in dropdown');
                    $this->client = entities\Client::getB2DBTable()->selectById($this->client);
                    framework\Logging::log('done (loading user object in dropdown)');
                }
                $this->clientusers = $this->client->getMembers();
            }
            catch (\Exception $e)
            {

            }
        }

        public function componentArchivedProjects()
        {
            if (!isset($this->target))
            {
                $this->projects = entities\Project::getAllRootProjects(true);
                $this->project_count = count($this->projects);
            }
            elseif ($this->target == 'team')
            {
                $this->team = entities\Team::getB2DBTable()->selectById($this->id);
                $projects = array();
                foreach (entities\Project::getAllByOwner($this->team) as $project)
                {
                    $projects[$project->getID()] = $project;
                }
                foreach (entities\Project::getAllByLeader($this->team) as $project)
                {
                    $projects[$project->getID()] = $project;
                }
                foreach (entities\Project::getAllByQaResponsible($this->team) as $project)
                {
                    $projects[$project->getID()] = $project;
                }
                foreach ($this->team->getAssociatedProjects() as $project_id => $project)
                {
                    $projects[$project_id] = $project;
                }

                $final_projects = array();

                foreach ($projects as $project)
                {
                    if ($project->isArchived()): $final_projects[] = $project;
                    endif;
                }

                $this->projects = $final_projects;
            }
            elseif ($this->target == 'client')
            {
                $this->client = entities\Client::getB2DBTable()->selectById($this->id);
                $projects = entities\Project::getAllByClientID($this->client->getID());

                $final_projects = array();

                foreach ($projects as $project)
                {
                    if (!$project->isArchived()): $final_projects[] = $project;
                    endif;
                }

                $this->projects = $final_projects;
            }
            elseif ($this->target == 'project')
            {
                $this->parent = entities\Project::getB2DBTable()->selectById($this->id);
                $this->projects = $this->parent->getChildren(true);
                ;
            }

            $this->project_count = count($this->projects);
        }

        public function componentTeamdropdown()
        {
            framework\Logging::log('team dropdown component');
            $this->rnd_no = rand();
            try
            {
                $this->team = (isset($this->team)) ? $this->team : null;
                if (!$this->team instanceof entities\Team)
                {
                    framework\Logging::log('loading team object in dropdown');
                    $this->team = entities\Team::getB2DBTable()->selectById($this->team);
                    framework\Logging::log('done (loading team object in dropdown)');
                }
            }
            catch (\Exception $e)
            {

            }
            framework\Logging::log('done (team dropdown component)');
        }

        public function componentIdentifiableselector()
        {
            $this->include_teams = (isset($this->include_teams)) ? $this->include_teams : false;
            $this->include_clients = (isset($this->include_clients)) ? $this->include_clients : false;
            $this->include_users = (isset($this->include_users)) ? $this->include_users : true;
            $this->callback = (isset($this->callback)) ? $this->callback : null;
            $this->allow_clear = (isset($this->allow_clear)) ? $this->allow_clear : true;
        }

        public function componentIdentifiableselectorresults()
        {
            $this->include_teams = (framework\Context::getRequest()->hasParameter('include_teams')) ? framework\Context::getRequest()->getParameter('include_teams') : false;
            $this->include_clients = (framework\Context::getRequest()->hasParameter('include_clients')) ? framework\Context::getRequest()->getParameter('include_clients') : false;
        }

        public function componentMyfriends()
        {
            $this->friends = framework\Context::getUser()->getFriends();
        }

        protected function setupVariables()
        {
            $i18n = framework\Context::getI18n();
            if ($this->issue instanceof entities\Issue)
            {
                $this->project = $this->issue->getProject();
                $this->statuses = ($this->project->isFreelancingAllowed()) ? entities\Status::getAll() : $this->issue->getAvailableStatuses();
                $this->issuetypes = $this->project->getIssuetypeScheme()->getIssuetypes();
                $fields_list = array();
                $fields_list['category'] = array('title' => $i18n->__('Category'), 'choices' => array(), 'visible' => $this->issue->isCategoryVisible(), 'changed' => $this->issue->isCategoryChanged(), 'merged' => $this->issue->isCategoryMerged(), 'name' => (($this->issue->getCategory() instanceof entities\Category) ? $this->issue->getCategory()->getName() : ''), 'name_visible' => (bool) ($this->issue->getCategory() instanceof entities\Category), 'noname_visible' => (bool) (!$this->issue->getCategory() instanceof entities\Category), 'icon' => false, 'change_tip' => $i18n->__('Click to change category'), 'change_header' => $i18n->__('Change category'), 'clear' => $i18n->__('Clear the category'), 'select' => $i18n->__('%clear_the_category or click to select a new category', array('%clear_the_category' => '')));
                if ($this->issue->isUpdateable() && $this->issue->canEditCategory())
                    $fields_list['category']['choices'] = entities\Category::getAll();
                $fields_list['resolution'] = array('title' => $i18n->__('Resolution'), 'choices' => array(), 'visible' => $this->issue->isResolutionVisible(), 'changed' => $this->issue->isResolutionChanged(), 'merged' => $this->issue->isResolutionMerged(), 'name' => (($this->issue->getResolution() instanceof entities\Resolution) ? $this->issue->getResolution()->getName() : ''), 'name_visible' => (bool) ($this->issue->getResolution() instanceof entities\Resolution), 'noname_visible' => (bool) (!$this->issue->getResolution() instanceof entities\Resolution), 'icon' => false, 'change_tip' => $i18n->__('Click to change resolution'), 'change_header' => $i18n->__('Change resolution'), 'clear' => $i18n->__('Clear the resolution'), 'select' => $i18n->__('%clear_the_resolution or click to select a new resolution', array('%clear_the_resolution' => '')));
                if ($this->issue->isUpdateable() && $this->issue->canEditResolution())
                    $fields_list['resolution']['choices'] = entities\Resolution::getAll();
                $fields_list['priority'] = array('title' => $i18n->__('Priority'), 'choices' => array(), 'visible' => $this->issue->isPriorityVisible(), 'changed' => $this->issue->isPriorityChanged(), 'merged' => $this->issue->isPriorityMerged(), 'name' => (($this->issue->getPriority() instanceof entities\Priority) ? $this->issue->getPriority()->getName() : ''), 'name_visible' => (bool) ($this->issue->getPriority() instanceof entities\Priority), 'noname_visible' => (bool) (!$this->issue->getPriority() instanceof entities\Priority), 'icon' => false, 'change_tip' => $i18n->__('Click to change priority'), 'change_header' => $i18n->__('Change priority'), 'clear' => $i18n->__('Clear the priority'), 'select' => $i18n->__('%clear_the_priority or click to select a new priority', array('%clear_the_priority' => '')));
                if ($this->issue->isUpdateable() && $this->issue->canEditPriority())
                    $fields_list['priority']['choices'] = entities\Priority::getAll();
                $fields_list['reproducability'] = array('title' => $i18n->__('Reproducability'), 'choices' => array(), 'visible' => $this->issue->isReproducabilityVisible(), 'changed' => $this->issue->isReproducabilityChanged(), 'merged' => $this->issue->isReproducabilityMerged(), 'name' => (($this->issue->getReproducability() instanceof entities\Reproducability) ? $this->issue->getReproducability()->getName() : ''), 'name_visible' => (bool) ($this->issue->getReproducability() instanceof entities\Reproducability), 'noname_visible' => (bool) (!$this->issue->getReproducability() instanceof entities\Reproducability), 'icon' => false, 'change_tip' => $i18n->__('Click to change reproducability'), 'change_header' => $i18n->__('Change reproducability'), 'clear' => $i18n->__('Clear the reproducability'), 'select' => $i18n->__('%clear_the_reproducability or click to select a new reproducability', array('%clear_the_reproducability' => '')));
                if ($this->issue->isUpdateable() && $this->issue->canEditReproducability())
                    $fields_list['reproducability']['choices'] = entities\Reproducability::getAll();
                $fields_list['severity'] = array('title' => $i18n->__('Severity'), 'choices' => array(), 'visible' => $this->issue->isSeverityVisible(), 'changed' => $this->issue->isSeverityChanged(), 'merged' => $this->issue->isSeverityMerged(), 'name' => (($this->issue->getSeverity() instanceof entities\Severity) ? $this->issue->getSeverity()->getName() : ''), 'name_visible' => (bool) ($this->issue->getSeverity() instanceof entities\Severity), 'noname_visible' => (bool) (!$this->issue->getSeverity() instanceof entities\Severity), 'icon' => false, 'change_tip' => $i18n->__('Click to change severity'), 'change_header' => $i18n->__('Change severity'), 'clear' => $i18n->__('Clear the severity'), 'select' => $i18n->__('%clear_the_severity or click to select a new severity', array('%clear_the_severity' => '')));
                if ($this->issue->isUpdateable() && $this->issue->canEditSeverity())
                    $fields_list['severity']['choices'] = entities\Severity::getAll();
                $fields_list['milestone'] = array('title' => $i18n->__('Targetted for'), 'choices' => array(), 'visible' => $this->issue->isMilestoneVisible(), 'changed' => $this->issue->isMilestoneChanged(), 'merged' => $this->issue->isMilestoneMerged(), 'name' => (($this->issue->getMilestone() instanceof entities\Milestone) ? $this->issue->getMilestone()->getName() : ''), 'name_visible' => (bool) ($this->issue->getMilestone() instanceof entities\Milestone), 'noname_visible' => (bool) (!$this->issue->getMilestone() instanceof entities\Milestone), 'icon' => true, 'icon_name' => 'icon_milestones.png', 'change_tip' => $i18n->__('Click to change which milestone this issue is targetted for'), 'change_header' => $i18n->__('Set issue target / milestone'), 'clear' => $i18n->__('Set as not targetted'), 'select' => $i18n->__('%set_as_not_targetted or click to set a new target milestone', array('%set_as_not_targetted' => '')), 'url' => true, 'current_url' => (($this->issue->getMilestone() instanceof entities\Milestone) ? $this->getRouting()->generate('project_roadmap', array('project_key' => $this->issue->getProject()->getKey())).'#roadmap_milestone_'.$this->issue->getMilestone()->getID() : ''));
                if ($this->issue->isUpdateable() && $this->issue->canEditMilestone())
                    $fields_list['milestone']['choices'] = $this->project->getMilestonesForIssues();

                $customfields_list = array();
                foreach (entities\CustomDatatype::getAll() as $key => $customdatatype)
                {
                    $customvalue = $this->issue->getCustomField($key);
                    $changed_methodname = "isCustomfield{$key}Changed";
                    $merged_methodname = "isCustomfield{$key}Merged";
                    $customfields_list[$key] = array('type' => $customdatatype->getType(),
                        'title' => $i18n->__($customdatatype->getDescription()),
                        'visible' => $this->issue->isFieldVisible($key),
                        'editable' => $customdatatype->isEditable(),
                        'changed' => $this->issue->$changed_methodname(),
                        'merged' => $this->issue->$merged_methodname(),
                        'change_tip' => $i18n->__($customdatatype->getInstructions()),
                        'change_header' => $i18n->__($customdatatype->getDescription()),
                        'clear' => $i18n->__('Clear this field'),
                        'select' => $i18n->__('%clear_this_field or click to set a new value', array('%clear_this_field' => '')));

                    if ($customdatatype->getType() == entities\CustomDatatype::CALCULATED_FIELD)
                    {
                        $result = $this->issue->getCustomField($key);
                        $customfields_list[$key]['name'] = $result;
                        $customfields_list[$key]['name_visible'] = !is_null($result);
                        $customfields_list[$key]['noname_visible'] = is_null($result);
                    }
                    elseif ($customdatatype->hasCustomOptions())
                    {
                        $customfields_list[$key]['name'] = ($customvalue instanceof entities\CustomDatatypeOption) ? $customvalue->getName() : '';
                        $customfields_list[$key]['name_visible'] = (bool) ($customvalue instanceof entities\CustomDatatypeOption);
                        $customfields_list[$key]['noname_visible'] = (bool) (!$customvalue instanceof entities\CustomDatatypeOption);
                        $customfields_list[$key]['choices'] = $customdatatype->getOptions();
                    }
                    elseif ($customdatatype->hasPredefinedOptions())
                    {
                        $customfields_list[$key]['name'] = ($customvalue instanceof entities\common\Identifiable) ? $customvalue->getName() : '';
                        $customfields_list[$key]['name_visible'] = (bool) ($customvalue instanceof entities\common\Identifiable);
                        $customfields_list[$key]['noname_visible'] = (bool) (!$customvalue instanceof entities\common\Identifiable);
                        $customfields_list[$key]['choices'] = $customdatatype->getOptions();
                    }
                    else
                    {
                        $customfields_list[$key]['name'] = $customvalue;
                        $customfields_list[$key]['name_visible'] = (bool) ($customvalue != '');
                        $customfields_list[$key]['noname_visible'] = (bool) ($customvalue == '');
                    }
                }
                $this->customfields_list = $customfields_list;
                $this->editions = ($this->issue->getProject()->isEditionsEnabled()) ? $this->issue->getEditions() : array();
                $this->components = ($this->issue->getProject()->isComponentsEnabled()) ? $this->issue->getComponents() : array();
                $this->builds = ($this->issue->getProject()->isBuildsEnabled()) ? $this->issue->getBuilds() : array();
                $this->affected_count = count($this->editions) + count($this->components) + count($this->builds);
            }
            else
            {
                $fields_list = array();
                $fields_list['category'] = array();
                $fields_list['category']['choices'] = entities\Category::getAll();
                $fields_list['resolution'] = array();
                $fields_list['resolution']['choices'] = entities\Resolution::getAll();
                $fields_list['priority'] = array();
                $fields_list['priority']['choices'] = entities\Priority::getAll();
                $fields_list['reproducability'] = array();
                $fields_list['reproducability']['choices'] = entities\Reproducability::getAll();
                $fields_list['severity'] = array();
                $fields_list['severity']['choices'] = entities\Severity::getAll();
                $fields_list['milestone'] = array();
                $fields_list['milestone']['choices'] = $this->project->getMilestonesForIssues();
            }

            $this->fields_list = $fields_list;
            if (isset($this->transition) && $this->transition->hasAction(entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE))
            {
                $available_assignees = array();
                foreach (framework\Context::getUser()->getTeams() as $team)
                {
                    foreach ($team->getMembers() as $user)
                    {
                        $available_assignees[$user->getID()] = $user->getNameWithUsername();
                    }
                }
                foreach (framework\Context::getUser()->getFriends() as $user)
                {
                    $available_assignees[$user->getID()] = $user->getNameWithUsername();
                }
                $this->available_assignees = $available_assignees;
            }
        }

        public function componentIssuedetailslistEditable()
        {
            $this->setupVariables();
        }

        public function componentIssuemaincustomfields()
        {
            $this->setupVariables();
        }

        public function componentHideableInfoBox()
        {
            $this->show_box = framework\Settings::isInfoBoxVisible($this->key);
        }

        public function componentHideableInfoBoxModal()
        {
            if (!isset($this->options))
                $this->options = array();
            if (!isset($this->button_label))
                $this->button_label = $this->getI18n()->__('Hide');
            $this->show_box = framework\Settings::isInfoBoxVisible($this->key);
        }

        public function componentUploader()
        {
            switch ($this->mode)
            {
                case 'issue':
                    $this->issue = entities\Issue::getB2DBTable()->selectById($this->issue_id);
                    break;
                case 'article':
                    $this->article = \thebuggenie\modules\publish\entities\Article::getByName($this->article_name);
                    break;
                default:
                    // @todo: dispatch a framework\Event that allows us to retrieve the
                    // necessary variables from anyone catching it
                    break;
            }
        }

        public function componentDynamicUploader()
        {
            switch (true)
            {
                case isset($this->issue):
                    $this->target = $this->issue;
                    $this->existing_files = $this->issue->getFiles();
                    break;
                case isset($this->article):
                    $this->target = $this->article;
                    $this->existing_files = $this->article->getFiles();
                    break;
                default:
                    // @todo: dispatch a framework\Event that allows us to retrieve the
                    // necessary variables from anyone catching it
                    break;
            }
        }

        public function componentStandarduploader()
        {
            switch ($this->mode)
            {
                case 'issue':
                    $this->form_action = make_url('issue_upload', array('issue_id' => $this->issue->getID()));
                    $this->poller_url = make_url('issue_upload_status', array('issue_id' => $this->issue->getID()));
                    $this->existing_files = $this->issue->getFiles();
                    break;
                case 'article':
                    $this->form_action = make_url('article_upload', array('article_name' => $this->article->getName()));
                    $this->poller_url = make_url('article_upload_status', array('article_name' => $this->article->getName()));
                    $this->existing_files = $this->article->getFiles();
                    break;
                default:
                    // @todo: dispatch a framework\Event that allows us to retrieve the
                    // necessary variables from anyone catching it
                    break;
            }
        }

        public function componentAttachedfile()
        {
            if ($this->mode == 'issue' && !isset($this->issue))
            {
                $this->issue = entities\Issue::getB2DBTable()->selectById($this->issue_id);
            }
            elseif ($this->mode == 'article' && !isset($this->article))
            {
                $this->article = \thebuggenie\modules\publish\entities\Article::getByName($this->article_name);
            }
            $this->file_id = $this->file->getID();
        }

        public function componentUpdateissueproperties()
        {
            $this->issue = $this->issue ? : null;
            $this->setupVariables();
        }

        public function componentRelateissue()
        {

        }

        public function componentNotifications()
        {
            $offset = $this->offset ?: 0;
            $notifications = $this->getUser()->getNotifications();
            $this->notifications = ($offset < count($notifications)) ? array_slice($notifications, $offset, 25) : array();
            $this->num_unread = $this->getUser()->getNumberOfUnreadNotifications();
            $this->num_read = $this->getUser()->getNumberOfReadNotifications();
        }

        public function componentFindduplicateissues()
        {
            $this->setupVariables();
        }

        public function componentFindrelatedissues()
        {

        }

        public function componentLogitem()
        {
            if ($this->log_action['target_type'] == 1)
            {
                try
                {
                    $this->issue = entities\Issue::getB2DBTable()->selectById($this->log_action['target']);
                }
                catch (\Exception $e)
                {

                }
            }
        }

        public function componentCommentitem()
        {
            if ($this->comment->getTargetType() == 1)
            {
                try
                {
                    $this->issue = entities\Issue::getB2DBTable()->selectById($this->comment->getTargetID());
                }
                catch (\Exception $e)
                {

                }
            }
        }

        public function componentUsercard()
        {
            $this->rnd_no = rand();
            $this->issues = $this->user->getIssues();
        }

        public function componentIssueaffected()
        {
            $this->editions = ($this->issue->getProject()->isEditionsEnabled()) ? $this->issue->getEditions() : array();
            $this->components = ($this->issue->getProject()->isComponentsEnabled()) ? $this->issue->getComponents() : array();
            $this->builds = ($this->issue->getProject()->isBuildsEnabled()) ? $this->issue->getBuilds() : array();
            $this->statuses = entities\Status::getAll();
            $this->count = count($this->editions) + count($this->components) + count($this->builds);
        }

        public function componentRelatedissues()
        {
            $child_issues = array();
            foreach ($this->issue->getChildIssues() as $child_issue)
            {
                if ($child_issue->hasAccess())
                    $child_issues[] = $child_issue;
            }
            $this->child_issues = $child_issues;
        }

        public function componentDuplicateissues()
        {
            $this->duplicate_issues = $this->issue->getDuplicateIssues();
        }

        public function componentLoginpopup()
        {
            if (framework\Context::getRequest()->getParameter('redirect') == true)
                $this->mandatory = true;
        }

        public function componentLogin()
        {
            $this->selected_tab = isset($this->section) ? $this->section : 'login';
            $this->options = $this->getParameterHolder();

            if (framework\Context::hasMessage('login_referer')):
                $this->referer = htmlentities(framework\Context::getMessage('login_referer'), ENT_COMPAT, framework\Context::getI18n()->getCharset());
            elseif (array_key_exists('HTTP_REFERER', $_SERVER)):
                $this->referer = htmlentities($_SERVER['HTTP_REFERER'], ENT_COMPAT, framework\Context::getI18n()->getCharset());
            else:
                $this->referer = framework\Context::getRouting()->generate('dashboard');
            endif;

            try
            {
                $this->loginintro = null;
                $this->registrationintro = null;
                $this->loginintro = \thebuggenie\modules\publish\entities\tables\Articles::getTable()->getArticleByName('LoginIntro');
                $this->registrationintro = \thebuggenie\modules\publish\entities\tables\Articles::getTable()->getArticleByName('RegistrationIntro');
            }
            catch (\Exception $e)
            {

            }

            if (framework\Settings::isLoginRequired())
            {
                framework\Context::getResponse()->deleteCookie('tbg3_username');
                framework\Context::getResponse()->deleteCookie('tbg3_password');
                $this->error = framework\Context::geti18n()->__('You need to log in to access this site');
            }
            elseif (!framework\Context::getUser()->isAuthenticated())
            {
                $this->error = framework\Context::geti18n()->__('Please log in');
            }
            else
            {
                //$this->error = framework\Context::geti18n()->__('Please log in');
            }
        }

        public function componentOpenidButtons()
        {
            $this->openidintro = \thebuggenie\modules\publish\entities\tables\Articles::getTable()->getArticleByName('OpenidIntro');
        }

        public function componentLoginRegister()
        {

        }

        public function componentCaptcha()
        {

        }

        public function componentIssueadditem()
        {
            $project = $this->issue->getProject();
            $this->editions = $project->getEditions();
            $this->components = $project->getComponents();
            $this->builds = $project->getBuilds();
        }

        public function componentDashboardview()
        {
            if ($this->view->hasJS())
            {
                foreach ($this->view->getJS() as $js)
                    $this->getResponse()->addJavascript($js);
            }
        }

        public function componentDashboardConfig()
        {
            $this->views = entities\DashboardView::getAvailableViews($this->target_type);
            $this->dashboardViews = entities\DashboardView::getViews($this->tid, $this->target_type);
        }

        protected function _setupReportIssueProperties()
        {
            $this->locked_issuetype = $this->locked_issuetype ? : null;
            $this->selected_issuetype = $this->selected_issuetype ? : null;
            $this->selected_edition = $this->selected_edition ? : null;
            $this->selected_build = $this->selected_build ? : null;
            $this->selected_milestone = $this->selected_milestone ? : null;
            $this->parent_issue = $this->parent_issue ? : null;
            $this->selected_component = $this->selected_component ? : null;
            $this->selected_category = $this->selected_category ? : null;
            $this->selected_status = $this->selected_status ? : null;
            $this->selected_resolution = $this->selected_resolution ? : null;
            $this->selected_priority = $this->selected_priority ? : null;
            $this->selected_reproducability = $this->selected_reproducability ? : null;
            $this->selected_severity = $this->selected_severity ? : null;
            $this->selected_estimated_time = $this->selected_estimated_time ? : null;
            $this->selected_spent_time = $this->selected_spent_time ? : null;
            $this->selected_percent_complete = $this->selected_percent_complete ? : null;
            $this->selected_pain_bug_type = $this->selected_pain_bug_type ? : null;
            $this->selected_pain_likelihood = $this->selected_pain_likelihood ? : null;
            $this->selected_pain_effect = $this->selected_pain_effect ? : null;
            $selected_customdatatype = $this->selected_customdatatype ? : array();
            foreach (entities\CustomDatatype::getAll() as $customdatatype)
            {
                $selected_customdatatype[$customdatatype->getKey()] = isset($selected_customdatatype[$customdatatype->getKey()]) ? $selected_customdatatype[$customdatatype->getKey()] : null;
            }
            $this->selected_customdatatype = $selected_customdatatype;
            $this->issuetype_id = $this->issuetype_id ? : null;
            $this->issue = $this->issue ? : null;
            $this->categories = entities\Category::getAll();
            $this->severities = entities\Severity::getAll();
            $this->priorities = entities\Priority::getAll();
            $this->reproducabilities = entities\Reproducability::getAll();
            $this->resolutions = entities\Resolution::getAll();
            $this->statuses = entities\Status::getAll();
            $this->milestones = framework\Context::getCurrentProject()->getMilestonesForIssues();
        }

        public function componentReportIssue()
        {
            $introarticle = \thebuggenie\modules\publish\entities\tables\Articles::getTable()->getArticleByName(ucfirst(framework\Context::getCurrentProject()->getKey()) . ':ReportIssueIntro');
            $this->introarticle = ($introarticle instanceof \thebuggenie\modules\publish\entities\Article) ? $introarticle : \thebuggenie\modules\publish\entities\tables\Articles::getTable()->getArticleByName('ReportIssueIntro');
            $reporthelparticle = \thebuggenie\modules\publish\entities\tables\Articles::getTable()->getArticleByName(ucfirst(framework\Context::getCurrentProject()->getKey()) . ':ReportIssueHelp');
            $this->reporthelparticle = ($reporthelparticle instanceof \thebuggenie\modules\publish\entities\Article) ? $reporthelparticle : \thebuggenie\modules\publish\entities\tables\Articles::getTable()->getArticleByName('ReportIssueHelp');
            $this->uniqid = framework\Context::getRequest()->getParameter('uniqid', uniqid());
            $this->_setupReportIssueProperties();
            $dummyissue = new entities\Issue();
            $dummyissue->setProject(framework\Context::getCurrentProject());
            $this->canupload = (framework\Settings::isUploadsEnabled() && $dummyissue->canAttachFiles());
        }

        public function componentReportIssueContainer()
        {

        }

        public function componentConfirmUsername()
        {

        }

        public function componentOpenID()
        {

        }

        public function componentMoveIssue()
        {

        }

        public function componentIssuePermissions()
        {
            $al_items = $this->issue->getAccessList();

            foreach ($al_items as $k => $item)
            {
                if ($item['target'] instanceof entities\User && $item['target']->getID() == $this->getUser()->getID())
                {
                    unset($al_items[$k]);
                }
            }

            $this->al_items = $al_items;
        }

        public function componentIssueSubscribers()
        {
            $this->users = $this->issue->getSubscribers();
        }

        public function componentIssueSpenttimes()
        {

        }

        public function componentIssueSpenttime()
        {
            $this->entry = tables\IssueSpentTimes::getTable()->selectById($this->entry_id);
        }

        public function componentDashboardLayoutStandard()
        {

        }

        public function componentDashboardViewRecentComments()
        {
            $this->comments = entities\Comment::getRecentCommentsByAuthor($this->getUser()->getID());
        }

        public function componentDashboardViewLoggedActions()
        {
            $this->actions = $this->getUser()->getLatestActions();
        }

        public function componentDashboardViewUserProjects()
        {
            $routing = $this->getRouting();
            $i18n = $this->getI18n();
            $links = array(
                array('url' => $routing->generate('project_open_issues', array('project_key' => '%project_key%')), 'text' => $i18n->__('Issues')),
                array('url' => $routing->generate('project_roadmap', array('project_key' => '%project_key%')), 'text' => $i18n->__('Roadmap')),
            );
            $event = \thebuggenie\core\framework\Event::createNew('core', 'main\Components::DashboardViewUserProjects::links', null, array(), $links);
            $event->trigger();
            $this->links = $event->getReturnList();
        }

        public function componentDashboardViewUserMilestones()
        {

        }

        public function componentIssueEstimator()
        {
            switch ($this->field)
            {
                case 'estimated_time':
                    $this->months = $this->issue->getEstimatedMonths();
                    $this->weeks = $this->issue->getEstimatedWeeks();
                    $this->days = $this->issue->getEstimatedDays();
                    $this->hours = $this->issue->getEstimatedHours();
                    $this->points = $this->issue->getEstimatedPoints();
                    break;
                case 'spent_time':
                    $this->months = 0;
                    $this->weeks = 0;
                    $this->days = 0;
                    $this->hours = 0;
                    $this->points = 0;
                    break;
            }
            $this->project_key = $this->issue->getProject()->getKey();
            $this->issue_id = $this->issue->getID();
        }

        public function componentAddDashboardView()
        {
            $request = framework\Context::getRequest();
            $this->dashboard = entities\Dashboard::getB2DBTable()->selectById($request['dashboard_id']);
            $this->column = $request['column'];
            $this->views = entities\DashboardView::getAvailableViews($this->dashboard->getType());
            $this->savedsearches = tables\SavedSearches::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(framework\Context::getUser()->getID(), ($this->dashboard->getProject() instanceof entities\Project) ? $this->dashboard->getProject()->getID() : 0);
        }

    }
