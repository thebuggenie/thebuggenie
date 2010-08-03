<?php

	class TBGVCSIntegration extends TBGModule 
	{
		
		protected $_module_version = '1.0';

		/**
		 * Return an instance of this module
		 *
		 * @return TBGVCSIntegration
		 */
		public static function getModule()
		{
			return TBGContext::getModule('vcs_integration');
		}

		protected function _initialize(TBGI18n $i18n)
		{
			$this->setLongName($i18n->__('VCS Integration'));
			$this->setConfigTitle($i18n->__('VCS Integration'));
			$this->setDescription($i18n->__('Allows details from source code checkins to be displayed in The Bug Genie'));
			$this->setConfigDescription($i18n->__('Configure repository settings for source code integration'));
			$this->setHasConfigSettings();
		}
		
		protected function _install($scope)
		{
			$this->enableListenerSaved('core', 'project_menustrip_item_links');
			$this->enableListenerSaved('core', 'viewissue_tabs');
			$this->enableListenerSaved('core', 'viewissue_tab_panes_back');
		}
		
		protected function _addAvailableListeners()
		{
			$this->addAvailableListener('core', 'project_menustrip_item_links', 'listen_projectMenustripLinks', 'Project menustrip links');
			$this->addAvailableListener('core', 'viewissue_tabs', 'listen_viewissue_tab', 'Tab to view commit details when viewing issues');
			$this->addAvailableListener('core', 'viewissue_tab_panes_back', 'listen_viewissue_panel', 'Commit details shown when viewing issues');
		}

		protected function _addAvailableRoutes()
		{
		}

		protected function _uninstall()
		{
			if (TBGContext::getScope()->getID() == 1)
			{
				TBGVCSIntegrationTable::getTable()->drop();
			}
			parent::_uninstall();
		}

		public function getRoute()
		{
			return TBGContext::getRouting()->generate('vcs_integration');
		}

		public function hasProjectAwareRoute()
		{
			return false;
		}

		public function postConfigSettings(TBGRequest $request)
		{
			$settings = array('use_web_interface', 'vcs_passkey');
			foreach ($settings as $setting)
			{
				if ($request->hasParameter($setting))
				{
					$this->saveSetting($setting, $request->getParameter($setting));
				}
			}
			
			foreach (TBGProject::getAll() as $aProduct)
			{
				if ($request->hasParameter('web_path_' . $aProduct->getID()))
				{
					$this->saveSetting('web_path_' . $aProduct->getID(), $request->getParameter('web_path_' . $aProduct->getID()));
				}
				if ($request->hasParameter('web_type_' . $aProduct->getID()))
				{
					$this->saveSetting('web_type_' . $aProduct->getID(), $request->getParameter('web_type_' . $aProduct->getID()));
				}
				if ($request->hasParameter('web_repo_' . $aProduct->getID()))
				{
					$this->saveSetting('web_repo_' . $aProduct->getID(), $request->getParameter('web_repo_' . $aProduct->getID()));
				}
			}
		}
		
		public function isUsingHTTPMethod()
		{
			if ($this->getSetting('use_web_interface') == 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public function listen_projectMenustripLinks(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('vcs_integration/projectmenustriplinks', array('project' => $event->getSubject(), 'module' => $this));
		}
		
		public function listen_viewissue_tab(TBGEvent $event)
		{
			$count = TBGVCSIntegrationTable::getTable()->getNumberOfCommitsByIssue($event->getSubject()->getId());
			TBGActionComponent::includeTemplate('vcs_integration/viewissue_tab', array('count' => $count));
		}
		
		public function listen_viewissue_panel(TBGEvent $event)
		{
			$web_path = $this->getSetting('web_path_' . $event->getSubject()->getProject()->getID());
			$web_repo = $this->getSetting('web_repo_' . $event->getSubject()->getProject()->getID());

			$data = TBGVCSIntegrationTable::getTable()->getCommitsByIssue($event->getSubject()->getId());
			
			if (!is_array($data))
			{
				TBGActionComponent::includeTemplate('vcs_integration/viewissue_commits_top', array('items' => false));
			}
			else
			{
				TBGActionComponent::includeTemplate('vcs_integration/viewissue_commits_top', array('items' => true));
				
				/* Now produce each box */
				foreach ($data as $revno => $entry)
				{
					$revision = $revno;
					/* Build correct URLs */
					switch ($this->getSetting('web_type_' . $event->getSubject()->getProject()->getID()))
					{
						case 'viewvc':
							$link_rev = $web_path . '/' . '?root=' . $web_repo . '&amp;view=rev&amp;revision=' . $revision;
							break;
						case 'viewvc_repo':
							$link_rev = $web_path . '/' . '?view=rev&amp;revision=' . $revision;
							break;
						case 'websvn':
							$link_rev = $web_path . '/revision.php?repname=' . $web_repo . '&amp;isdir=1&amp;rev=' . $revision;
							break;
						case 'websvn_mv':
							$link_rev = $web_path . '/' . '?repname=' . $web_repo . '&amp;op=log&isdir=1&amp;rev=' . $revision;
							break;
						case 'loggerhead':
							$link_rev = $web_path . '/' . $web_repo . '/revision/' . $revision;
							break;
						case 'gitweb':
							$link_rev = $web_path . '/' . '?p=' . $web_repo . ';a=commitdiff;h=' . $revision;
							break;
						case 'cgit':
							$link_rev = $web_path . '/' . $web_repo . '/commit/?id=' . $revision;
							break;
						case 'hgweb':
							$link_rev = $web_path . '/' . $web_repo . '/rev/' . $revision;
							break;
						case 'github':
							$link_rev = 'http://github.com/' . $web_repo . '/commit/' . $revision;
							break;
					}
					
					/* Now we have everything, render the template */
					include_template('vcs_integration/commitbox', array("projectId" => $event->getSubject()->getProject()->getID(), "id" => $entry[0][0], "revision" => $revision, "author" => $entry[0][1], "date" => $entry[0][2], "log" => $entry[0][3], "files" => $entry[1]));
				}
				
				TBGActionComponent::includeTemplate('vcs_integration/viewissue_commits_bottom');
			}
		}
	}
