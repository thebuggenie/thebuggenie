<?php 

	/**
	 * Main action components
	 */
	class mailingActionComponents extends TBGActionComponent
	{
		
		public function componentForgotPasswordBlock()
		{
		}

		public function componentSettings()
		{
		}

		public function componentAccountSettings()
		{
			$i18n = TBGContext::getI18n();
			$general_settings = array();
			$issues_settings = array();
			$general_settings['notify_add_friend'] = $i18n->__('Notify me when someone adds me as their friend');

			$issues_settings['notify_issue_change'] = $i18n->__('Notify me when an issue I posted gets updated');
			$issues_settings['notify_issue_comment'] = $i18n->__('Notify me when someone comments on an issue I posted');

			$this->general_settings = $general_settings;
			$this->issues_settings = $issues_settings;

			$this->uid = TBGContext::getUser()->getID();
		}
		
	}

?>