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
	}
