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
					$this->user = TBGFactory::userLab($this->user);
					TBGLogging::log('done (loading user object in dropdown)');
				}
			}
			catch (Exception $e) 
			{ 
			}
			TBGLogging::log('done (user dropdown component)');
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
					$this->team = TBGFactory::teamLab($this->team);
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
			$this->allow_clear = (isset($this->allow_clear)) ? $this->allow_clear : true;
		}
		
		public function componentIdentifiableselectorresults()
		{
			$this->include_teams = (isset($include_teams)) ? $include_teams : false;
		}
		
		public function componentMyfriends()
		{
			$this->friends = TBGContext::getUser()->getFriends();
		}

		protected function setupVariables()
		{
			$i18n = TBGContext::getI18n();
			$this->statuses = TBGStatus::getAll();
			$this->issuetypes = TBGIssuetype::getAllApplicableToProject($this->issue->getProject()->getID());
			$fields_list = array();
			$fields_list['category'] = array('title' => $i18n->__('Category'), 'visible' => $this->issue->isCategoryVisible(), 'changed' => $this->issue->isCategoryChanged(), 'merged' => $this->issue->isCategoryMerged(), 'name' => (($this->issue->getCategory() instanceof TBGCategory) ? $this->issue->getCategory()->getName() : ''), 'name_visible' => (bool) ($this->issue->getCategory() instanceof TBGCategory), 'noname_visible' => (bool) (!$this->issue->getCategory() instanceof TBGCategory), 'icon' => false, 'change_tip' => $i18n->__('Click to change category'), 'change_header' => $i18n->__('Change category'), 'choices' => TBGCategory::getAll(), 'clear' => $i18n->__('Clear the category'), 'select' => $i18n->__('%clear_the_category% or click to select a new category', array('%clear_the_category%' => '')));
			$fields_list['resolution'] = array('title' => $i18n->__('Resolution'), 'visible' => $this->issue->isResolutionVisible(), 'changed' => $this->issue->isResolutionChanged(), 'merged' => $this->issue->isResolutionMerged(), 'name' => (($this->issue->getResolution() instanceof TBGResolution) ? $this->issue->getResolution()->getName() : ''), 'name_visible' => (bool) ($this->issue->getResolution() instanceof TBGResolution), 'noname_visible' => (bool) (!$this->issue->getResolution() instanceof TBGResolution), 'icon' => false, 'change_tip' => $i18n->__('Click to change resolution'), 'change_header' => $i18n->__('Change resolution'), 'choices' => TBGResolution::getAll(), 'clear' => $i18n->__('Clear the resolution'), 'select' => $i18n->__('%clear_the_resolution% or click to select a new resolution', array('%clear_the_resolution%' => '')));
			$fields_list['priority'] = array('title' => $i18n->__('Priority'), 'visible' => $this->issue->isPriorityVisible(), 'changed' => $this->issue->isPriorityChanged(), 'merged' => $this->issue->isPriorityMerged(), 'name' => (($this->issue->getPriority() instanceof TBGPriority) ? $this->issue->getPriority()->getName() : ''), 'name_visible' => (bool) ($this->issue->getPriority() instanceof TBGPriority), 'noname_visible' => (bool) (!$this->issue->getPriority() instanceof TBGPriority), 'icon' => false, 'change_tip' => $i18n->__('Click to change priority'), 'change_header' => $i18n->__('Change priority'), 'choices' => TBGPriority::getAll(), 'clear' => $i18n->__('Clear the priority'), 'select' => $i18n->__('%clear_the_priority% or click to select a new priority', array('%clear_the_priority%' => '')));
			$fields_list['reproducability'] = array('title' => $i18n->__('Reproducability'), 'visible' => $this->issue->isReproducabilityVisible(), 'changed' => $this->issue->isReproducabilityChanged(), 'merged' => $this->issue->isReproducabilityMerged(), 'name' => (($this->issue->getReproducability() instanceof TBGReproducability) ? $this->issue->getReproducability()->getName() : ''), 'name_visible' => (bool) ($this->issue->getReproducability() instanceof TBGReproducability), 'noname_visible' => (bool) (!$this->issue->getReproducability() instanceof TBGReproducability), 'icon' => false, 'change_tip' => $i18n->__('Click to change reproducability'), 'change_header' => $i18n->__('Change reproducability'), 'choices' => TBGReproducability::getAll(), 'clear' => $i18n->__('Clear the reproducability'), 'select' => $i18n->__('%clear_the_reproducability% or click to select a new reproducability', array('%clear_the_reproducability%' => '')));
			$fields_list['severity'] = array('title' => $i18n->__('Severity'), 'visible' => $this->issue->isSeverityVisible(), 'changed' => $this->issue->isSeverityChanged(), 'merged' => $this->issue->isSeverityMerged(), 'name' => (($this->issue->getSeverity() instanceof TBGSeverity) ? $this->issue->getSeverity()->getName() : ''), 'name_visible' => (bool) ($this->issue->getSeverity() instanceof TBGSeverity), 'noname_visible' => (bool) (!$this->issue->getSeverity() instanceof TBGSeverity), 'icon' => false, 'change_tip' => $i18n->__('Click to change severity'), 'change_header' => $i18n->__('Change severity'), 'choices' => TBGSeverity::getAll(), 'clear' => $i18n->__('Clear the severity'), 'select' => $i18n->__('%clear_the_severity% or click to select a new severity', array('%clear_the_severity%' => '')));
			$fields_list['milestone'] = array('title' => $i18n->__('Targetted for'), 'visible' => $this->issue->isMilestoneVisible(), 'changed' => $this->issue->isMilestoneChanged(), 'merged' => $this->issue->isMilestoneMerged(), 'name' => (($this->issue->getMilestone() instanceof TBGMilestone) ? $this->issue->getMilestone()->getName() : ''), 'name_visible' => (bool) ($this->issue->getMilestone() instanceof TBGMilestone), 'noname_visible' => (bool) (!$this->issue->getMilestone() instanceof TBGMilestone), 'icon' => true, 'icon_name' => 'icon_milestones.png', 'change_tip' => $i18n->__('Click to change which milestone this issue is targetted for'), 'change_header' => $i18n->__('Set issue target / milestone'), 'choices' => $this->issue->getProject()->getAllMilestones(), 'clear' => $i18n->__('Set as not targetted'), 'select' => $i18n->__('%set_as_not_targetted% or click to set a new target milestone', array('%set_as_not_targetted%' => '')));

			$customfields_list = array();
			foreach (TBGCustomDatatype::getAll() as $key => $customdatatype)
			{
				$changed_methodname = "isCustomfield{$key}Changed";
				$merged_methodname = "isCustomfield{$key}Merged";
				$customfields_list[$key] = array('type' => $customdatatype->getType(),
												'title' => $i18n->__($customdatatype->getDescription()),
												'visible' => $this->issue->getIssuetype()->isFieldVisible($customdatatype->getKey()),
												'changed' => $this->issue->$changed_methodname(),
												'merged' => $this->issue->$merged_methodname(),
												'change_tip' => $i18n->__($customdatatype->getInstructions()),
												'change_header' => $i18n->__($customdatatype->getDescription()),
												'clear' => $i18n->__('Clear this field'),
												'select' => $i18n->__('%clear_this_field% or click to set a new value', array('%clear_this_field%' => '')));
				
				if ($customdatatype->hasCustomOptions())
				{
					$customfields_list[$key]['name'] = (($this->issue->getCustomField($key) instanceof TBGCustomDatatypeOption) ? $this->issue->getCustomField($key)->getName() : '');
					$customfields_list[$key]['name_visible'] = (bool) ($this->issue->getCustomField($key) instanceof TBGCustomDatatypeOption);
					$customfields_list[$key]['noname_visible'] = (bool) (!$this->issue->getCustomField($key) instanceof TBGCustomDatatypeOption);
					$customfields_list[$key]['choices'] = $customdatatype->getOptions();
				}
				else
				{
					$customfields_list[$key]['name'] = $this->issue->getCustomField($key);
					$customfields_list[$key]['name_visible'] = (bool) ($this->issue->getCustomField($key) != '');
					$customfields_list[$key]['noname_visible'] = (bool) ($this->issue->getCustomField($key) == '');
				}
			}

			$this->fields_list = $fields_list;
			$this->customfields_list = $customfields_list;
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
			}
		}

		public function componentAttachedfile()
		{
			if ($this->mode == 'issue' && !isset($this->issue))
			{
				$this->issue = TBGFactory::TBGIssueLab($this->issue_id);
			}
			$this->file_id = $this->file->getID();
		}

		public function componentCloseissue()
		{
			$this->setupVariables();
		}
		
		public function componentMarkasduplicate()
		{
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
					$this->issue = TBGFactory::TBGIssueLab($this->log_action['target']);
				}
				catch (Exception $e) {}
			}
		}

		public function componentUsercard()
		{
			$this->rnd_no = rand();
		}

	}
