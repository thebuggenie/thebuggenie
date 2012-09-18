<?php 

	/**
	 * Main action components
	 */
	class mainActionComponents extends TBGActionComponent
	{
		
		public function componentUserdropdown()
		{
			TBGLogging::log('user dropdown component');
			$this->rnd_no = rand();
			try
			{
				if (!$this->user instanceof TBGUser)
				{
					TBGLogging::log('loading user object in dropdown');
					$this->user = TBGContext::factory()->TBGUser($this->user);
					TBGLogging::log('done (loading user object in dropdown)');
				}
			}
			catch (Exception $e) 
			{ 
			}
			$this->show_avatar = (isset($this->show_avatar)) ? $this->show_avatar : true;
			TBGLogging::log('done (user dropdown component)');
		}
		
		public function componentClientusers()
		{
			try
			{
				if (!$this->client instanceof TBGClient)
				{
					TBGLogging::log('loading user object in dropdown');
					$this->client = TBGContext::factory()->TBGClient($this->client);
					TBGLogging::log('done (loading user object in dropdown)');
				}
				$this->clientusers = $this->client->getMembers();
			}
			catch (Exception $e) 
			{
			}
		}
		
		public function componentArchivedProjects()
		{
			if (!isset($this->target))
			{
				$this->projects = TBGProject::getAllRootProjects(true);
				$this->project_count = count($this->projects);
			}
			elseif ($this->target == 'team')
			{
				$this->team = TBGContext::factory()->TBGTeam($this->id);
				$projects = array();
				foreach (TBGProject::getAllByOwner($this->team) as $project) {
					$projects[$project->getID()] = $project;
				}
				foreach (TBGProject::getAllByLeader($this->team) as $project) {
					$projects[$project->getID()] = $project;
				}
				foreach (TBGProject::getAllByQaResponsible($this->team) as $project) {
					$projects[$project->getID()] = $project;
				}
				foreach ($this->team->getAssociatedProjects() as $project_id => $project) {
					$projects[$project_id] = $project;
				}
				
				$final_projects = array();
				
				foreach ($projects as $project)
				{
					if ($project->isArchived()): $final_projects[] = $project; endif;
				}
				
				$this->projects = $final_projects;
			}
			elseif ($this->target == 'client')
			{
				$this->client = TBGContext::factory()->TBGClient($this->id);
				$projects = TBGProject::getAllByClientID($this->client->getID());
				
				$final_projects = array();
				
				foreach ($projects as $project)
				{
					if (!$project->isArchived()): $final_projects[] = $project; endif;
				}
				
				$this->projects = $final_projects;
			}
			elseif ($this->target == 'project')
			{
				$this->parent = TBGContext::factory()->TBGProject($this->id);
				$this->projects = $this->parent->getChildren(true);;
			}
			
			$this->project_count = count($this->projects);
		}
		
		public function componentTeamdropdown()
		{
			TBGLogging::log('team dropdown component');
			$this->rnd_no = rand();
			try
			{
				$this->team = (isset($this->team)) ? $this->team : null;
				if (!$this->team instanceof TBGTeam)
				{
					TBGLogging::log('loading team object in dropdown');
					$this->team = TBGContext::factory()->TBGTeam($this->team);
					TBGLogging::log('done (loading team object in dropdown)');
				}
			}
			catch (Exception $e) 
			{ 
			}
			TBGLogging::log('done (team dropdown component)');
		}
		
		public function componentIdentifiableselector()
		{
			$this->include_teams = (isset($this->include_teams)) ? $this->include_teams : false;
			$this->include_users = (isset($this->include_users)) ? $this->include_users : true;
			$this->callback = (isset($this->callback)) ? $this->callback : null;
			$this->allow_clear = (isset($this->allow_clear)) ? $this->allow_clear : true;
		}
		
		public function componentIdentifiableselectorresults()
		{
			$this->include_teams = (TBGContext::getRequest()->hasParameter('include_teams')) ? TBGContext::getRequest()->getParameter('include_teams') : false;
		}
		
		public function componentMyfriends()
		{
			$this->friends = TBGContext::getUser()->getFriends();
		}

		protected function setupVariables()
		{
			$i18n = TBGContext::getI18n();
			$this->statuses = TBGStatus::getAll();
			if ($this->issue instanceof TBGIssue)
			{
				$this->project = $this->issue->getProject();
				$this->issuetypes = $this->project->getIssuetypeScheme()->getIssuetypes();
				$fields_list = array();
				$fields_list['category'] = array('title' => $i18n->__('Category'), 'choices' => array(), 'visible' => $this->issue->isCategoryVisible(), 'changed' => $this->issue->isCategoryChanged(), 'merged' => $this->issue->isCategoryMerged(), 'name' => (($this->issue->getCategory() instanceof TBGCategory) ? $this->issue->getCategory()->getName() : ''), 'name_visible' => (bool) ($this->issue->getCategory() instanceof TBGCategory), 'noname_visible' => (bool) (!$this->issue->getCategory() instanceof TBGCategory), 'icon' => false, 'change_tip' => $i18n->__('Click to change category'), 'change_header' => $i18n->__('Change category'), 'clear' => $i18n->__('Clear the category'), 'select' => $i18n->__('%clear_the_category% or click to select a new category', array('%clear_the_category%' => '')));
				if ($this->issue->isUpdateable() && $this->issue->canEditCategory()) $fields_list['category']['choices'] = TBGCategory::getAll();
				$fields_list['resolution'] = array('title' => $i18n->__('Resolution'), 'choices' => array(), 'visible' => $this->issue->isResolutionVisible(), 'changed' => $this->issue->isResolutionChanged(), 'merged' => $this->issue->isResolutionMerged(), 'name' => (($this->issue->getResolution() instanceof TBGResolution) ? $this->issue->getResolution()->getName() : ''), 'name_visible' => (bool) ($this->issue->getResolution() instanceof TBGResolution), 'noname_visible' => (bool) (!$this->issue->getResolution() instanceof TBGResolution), 'icon' => false, 'change_tip' => $i18n->__('Click to change resolution'), 'change_header' => $i18n->__('Change resolution'), 'clear' => $i18n->__('Clear the resolution'), 'select' => $i18n->__('%clear_the_resolution% or click to select a new resolution', array('%clear_the_resolution%' => '')));
				if ($this->issue->isUpdateable() && $this->issue->canEditResolution()) $fields_list['resolution']['choices'] = TBGResolution::getAll();
				$fields_list['priority'] = array('title' => $i18n->__('Priority'), 'choices' => array(), 'visible' => $this->issue->isPriorityVisible(), 'changed' => $this->issue->isPriorityChanged(), 'merged' => $this->issue->isPriorityMerged(), 'name' => (($this->issue->getPriority() instanceof TBGPriority) ? $this->issue->getPriority()->getName() : ''), 'name_visible' => (bool) ($this->issue->getPriority() instanceof TBGPriority), 'noname_visible' => (bool) (!$this->issue->getPriority() instanceof TBGPriority), 'icon' => false, 'change_tip' => $i18n->__('Click to change priority'), 'change_header' => $i18n->__('Change priority'), 'clear' => $i18n->__('Clear the priority'), 'select' => $i18n->__('%clear_the_priority% or click to select a new priority', array('%clear_the_priority%' => '')));
				if ($this->issue->isUpdateable() && $this->issue->canEditPriority()) $fields_list['priority']['choices'] = TBGPriority::getAll();
				$fields_list['reproducability'] = array('title' => $i18n->__('Reproducability'), 'choices' => array(), 'visible' => $this->issue->isReproducabilityVisible(), 'changed' => $this->issue->isReproducabilityChanged(), 'merged' => $this->issue->isReproducabilityMerged(), 'name' => (($this->issue->getReproducability() instanceof TBGReproducability) ? $this->issue->getReproducability()->getName() : ''), 'name_visible' => (bool) ($this->issue->getReproducability() instanceof TBGReproducability), 'noname_visible' => (bool) (!$this->issue->getReproducability() instanceof TBGReproducability), 'icon' => false, 'change_tip' => $i18n->__('Click to change reproducability'), 'change_header' => $i18n->__('Change reproducability'), 'clear' => $i18n->__('Clear the reproducability'), 'select' => $i18n->__('%clear_the_reproducability% or click to select a new reproducability', array('%clear_the_reproducability%' => '')));
				if ($this->issue->isUpdateable() && $this->issue->canEditReproducability()) $fields_list['reproducability']['choices'] = TBGReproducability::getAll();
				$fields_list['severity'] = array('title' => $i18n->__('Severity'), 'choices' => array(), 'visible' => $this->issue->isSeverityVisible(), 'changed' => $this->issue->isSeverityChanged(), 'merged' => $this->issue->isSeverityMerged(), 'name' => (($this->issue->getSeverity() instanceof TBGSeverity) ? $this->issue->getSeverity()->getName() : ''), 'name_visible' => (bool) ($this->issue->getSeverity() instanceof TBGSeverity), 'noname_visible' => (bool) (!$this->issue->getSeverity() instanceof TBGSeverity), 'icon' => false, 'change_tip' => $i18n->__('Click to change severity'), 'change_header' => $i18n->__('Change severity'), 'clear' => $i18n->__('Clear the severity'), 'select' => $i18n->__('%clear_the_severity% or click to select a new severity', array('%clear_the_severity%' => '')));
				if ($this->issue->isUpdateable() && $this->issue->canEditSeverity()) $fields_list['severity']['choices'] = TBGSeverity::getAll();
				$fields_list['milestone'] = array('title' => $i18n->__('Targetted for'), 'choices' => array(), 'visible' => $this->issue->isMilestoneVisible(), 'changed' => $this->issue->isMilestoneChanged(), 'merged' => $this->issue->isMilestoneMerged(), 'name' => (($this->issue->getMilestone() instanceof TBGMilestone) ? $this->issue->getMilestone()->getName() : ''), 'name_visible' => (bool) ($this->issue->getMilestone() instanceof TBGMilestone), 'noname_visible' => (bool) (!$this->issue->getMilestone() instanceof TBGMilestone), 'icon' => true, 'icon_name' => 'icon_milestones.png', 'change_tip' => $i18n->__('Click to change which milestone this issue is targetted for'), 'change_header' => $i18n->__('Set issue target / milestone'), 'clear' => $i18n->__('Set as not targetted'), 'select' => $i18n->__('%set_as_not_targetted% or click to set a new target milestone', array('%set_as_not_targetted%' => '')), 'url' => true, 'current_url' => (($this->issue->getMilestone() instanceof TBGMilestone) ? $this->getRouting()->generate('project_milestone_details', array('project_key' => $this->issue->getProject()->getKey(), 'milestone_id' => $this->issue->getMilestone()->getID())) : ''));
				if ($this->issue->isUpdateable() && $this->issue->canEditMilestone()) $fields_list['milestone']['choices'] = $this->project->getMilestones();

				$customfields_list = array();
				foreach (TBGCustomDatatype::getAll() as $key => $customdatatype)
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
													'select' => $i18n->__('%clear_this_field% or click to set a new value', array('%clear_this_field%' => '')));

					if ($customdatatype->getType() == TBGCustomDatatype::CALCULATED_FIELD)
					{
						$result = $this->issue->getCustomField($key);
						$customfields_list[$key]['name'] = $result;
						$customfields_list[$key]['name_visible'] = !is_null($result);
						$customfields_list[$key]['noname_visible'] = is_null($result);
					}
					elseif ($customdatatype->hasCustomOptions())
					{
						$customfields_list[$key]['name'] = ($customvalue instanceof TBGCustomDatatypeOption) ? $customvalue->getName() : '';
						$customfields_list[$key]['name_visible'] = (bool) ($customvalue instanceof TBGCustomDatatypeOption);
						$customfields_list[$key]['noname_visible'] = (bool) (!$customvalue instanceof TBGCustomDatatypeOption);
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
			}
			else
			{
				$fields_list = array();
				$fields_list['category'] = array();
				$fields_list['category']['choices'] = TBGCategory::getAll();
				$fields_list['resolution'] = array();
				$fields_list['resolution']['choices'] = TBGResolution::getAll();
				$fields_list['priority'] = array();
				$fields_list['priority']['choices'] = TBGPriority::getAll();
				$fields_list['reproducability'] = array();
				$fields_list['reproducability']['choices'] = TBGReproducability::getAll();
				$fields_list['severity'] = array();
				$fields_list['severity']['choices'] = TBGSeverity::getAll();
				$fields_list['milestone'] = array();
				$fields_list['milestone']['choices'] = $this->project->getMilestones();
			}

			$this->fields_list = $fields_list;
			if (isset($this->transition) && $this->transition->hasAction(TBGWorkflowTransitionAction::ACTION_ASSIGN_ISSUE))
			{
				$available_assignees = array();
				foreach (TBGContext::getUser()->getTeams() as $team)
				{
					foreach ($team->getMembers() as $user)
					{
						$available_assignees[$user->getID()] = $user->getNameWithUsername();
					}
				}
				foreach (TBGContext::getUser()->getFriends() as $user)
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
			$this->show_box = TBGSettings::isInfoBoxVisible($this->key);
		}

		public function componentUploader()
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
					// @todo: dispatch a TBGEvent that allows us to retrieve the
					// necessary variables from anyone catching it
					break;
			}
		}

		public function componentAttachedfile()
		{
			if ($this->mode == 'issue' && !isset($this->issue))
			{
				$this->issue = TBGContext::factory()->TBGIssue($this->issue_id);
			}
			elseif ($this->mode == 'article' && !isset($this->article))
			{
				$this->article = TBGWikiArticle::getByName($this->article_name);
			}
			$this->file_id = $this->file->getID();
		}

		public function componentUpdateissueproperties()
		{
			$this->issue = $this->issue ?: null;
			$this->setupVariables();
		}
		
		public function componentRelateissue()
		{
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
					$this->issue = TBGContext::factory()->TBGIssue($this->log_action['target']);
				}
				catch (Exception $e) {}
			}
		}

		public function componentCommentitem()
		{
			if ($this->comment->getTargetType() == 1)
			{
				try
				{
					$this->issue = TBGContext::factory()->TBGIssue($this->comment->getTargetID());
				}
				catch (Exception $e) {}
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
			$this->statuses = TBGStatus::getAll();
			$this->count = count($this->editions) + count($this->components) + count($this->builds);
		}

		public function componentRelatedissues()
		{
			$parent_issues = array();
			$child_issues = array();
			foreach ($this->issue->getParentIssues() as $parent_issue)
			{
				if ($parent_issue->hasAccess()) $parent_issues[] = $parent_issue;
			}
			foreach ($this->issue->getChildIssues() as $child_issue)
			{
				if ($child_issue->hasAccess()) $child_issues[] = $child_issue;
			}
			$this->parent_issues = $parent_issues;
			$this->child_issues = $child_issues;
		}

		public function componentLoginpopup()
		{
			if (TBGContext::getRequest()->getParameter('redirect') == true)
				$this->mandatory = true;
		}

		public function componentLogin()
		{
			$this->selected_tab = isset($this->section) ? $this->section : 'login';
			$this->options = $this->getParameterHolder();
			
			if (array_key_exists('HTTP_REFERER', $_SERVER)):
				$this->referer = $_SERVER['HTTP_REFERER'];
			elseif (TBGContext::hasMessage('login_referer')):
				$this->referer = TBGContext::getMessage('login_referer');
			else:
				$this->referer = TBGContext::getRouting()->generate('dashboard');
			endif;
			
			try
			{
				$this->article = null;
				$this->article = PublishFactory::articleName('LoginIntro');
			}
			catch (Exception $e) {}

			if (TBGSettings::isLoginRequired())
			{
				TBGContext::getResponse()->deleteCookie('tbg3_username');
				TBGContext::getResponse()->deleteCookie('tbg3_password');
				$this->error = TBGContext::geti18n()->__('You need to log in to access this site');
			}
			elseif (!TBGContext::getUser()->isAuthenticated())
			{
				$this->error = TBGContext::geti18n()->__('Please log in');
			}
			else
			{
			//$this->error = TBGContext::geti18n()->__('Please log in');
			}
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
				$js = $this->view->getJS();
				$jss = (is_array($js)) ? $js : array($js);
				foreach ($jss as $js)
					$this->getResponse()->addJavascript($js, false);
			}
		}
		
		public function componentDashboardConfig()
		{	
			$this->views = TBGDashboardView::getAvailableViews($this->target_type);
			$this->dashboardViews = TBGDashboardView::getViews($this->tid, $this->target_type);
		}

		protected function _setupReportIssueProperties()
		{
			$this->selected_issuetype = $this->selected_issuetype ?: null;
			$this->selected_edition = $this->selected_edition ?: null;
			$this->selected_build = $this->selected_build ?: null;
			$this->selected_milestone = $this->selected_milestone ?: null;
			$this->parent_issue = $this->parent_issue ?: null;
			$this->selected_component = $this->selected_component ?: null;
			$this->selected_category = $this->selected_category ?: null;
			$this->selected_status = $this->selected_status ?: null;
			$this->selected_resolution = $this->selected_resolution ?: null;
			$this->selected_priority = $this->selected_priority ?: null;
			$this->selected_reproducability = $this->selected_reproducability ?: null;
			$this->selected_severity = $this->selected_severity ?: null;
			$this->selected_estimated_time = $this->selected_estimated_time ?: null;
			$this->selected_spent_time = $this->selected_spent_time ?: null;
			$this->selected_percent_complete = $this->selected_percent_complete ?: null;
			$this->selected_pain_bug_type = $this->selected_pain_bug_type ?: null;
			$this->selected_pain_likelihood = $this->selected_pain_likelihood ?: null;
			$this->selected_pain_effect = $this->selected_pain_effect ?: null;
			$selected_customdatatype = $this->selected_customdatatype ?: array();
			foreach (TBGCustomDatatype::getAll() as $customdatatype)
			{
				$selected_customdatatype[$customdatatype->getKey()] = isset($selected_customdatatype[$customdatatype->getKey()]) ? $selected_customdatatype[$customdatatype->getKey()] : null;
			}
			$this->selected_customdatatype = $selected_customdatatype;
			$this->issuetype_id = $this->issuetype_id ?: null;
			$this->issue = $this->issue ?: null;
			$this->categories = TBGCategory::getAll();
			$this->severities = TBGSeverity::getAll();
			$this->priorities = TBGPriority::getAll();
			$this->reproducabilities = TBGReproducability::getAll();
			$this->resolutions = TBGResolution::getAll();
			$this->statuses = TBGStatus::getAll();
			$this->milestones = TBGContext::getCurrentProject()->getMilestones();
		}

		public function componentReportIssue()
		{
			$this->uniqid = TBGContext::getRequest()->getParameter('uniqid', uniqid());
			$this->_setupReportIssueProperties();
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
				if ($item['target'] instanceof TBGUser && $item['target']->getID() == $this->getUser()->getID())
				{
					unset($al_items[$k]);
				}
			}
			
			$this->al_items = $al_items;
		}

		public function componentDashboardViewRecentComments()
		{
			$this->comments = TBGComment::getRecentCommentsByAuthor($this->getUser()->getID());
		}

		public function componentDashboardViewLoggedActions()
		{
			$this->actions = $this->getUser()->getLatestActions();
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
					$this->months = $this->issue->getSpentMonths();
					$this->weeks = $this->issue->getSpentWeeks();
					$this->days = $this->issue->getSpentDays();
					$this->hours = $this->issue->getSpentHours();
					$this->points = $this->issue->getSpentPoints();
					break;
			}
		}

	}
