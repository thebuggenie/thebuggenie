<?php 

	/**
	 * Main action components
	 */
	class mailingActionComponents extends TBGActionComponent
	{

		public function componentForgotPasswordPane()
		{
		}		

		public function componentForgotPasswordTab()
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
			
			$issues_settings[TBGMailing::NOTIFY_ISSUE_POSTED_UPDATED] = $i18n->__('Notify me when an issue I posted gets updated or created');
			$issues_settings[TBGMailing::NOTIFY_ISSUE_ONCE] = $i18n->__('Only notify me once per issue until I open the issue');
			$issues_settings[TBGMailing::NOTIFY_ISSUE_ASSIGNED_UPDATED] = $i18n->__("Notify me when an issue I'm assigned to gets updated or created");
			$issues_settings[TBGMailing::NOTIFY_ISSUE_UPDATED_SELF] = $i18n->__('Notify me when I update or create an issue');
			$issues_settings[TBGMailing::NOTIFY_ISSUE_TEAMASSIGNED_UPDATED] = $i18n->__("Notify me when an issue assigned to one of my teams is updated or created");
			$issues_settings[TBGMailing::NOTIFY_ISSUE_RELATED_PROJECT_TEAMASSIGNED] = $i18n->__("Notify me when an issue assigned to one of my team projects is updated or created");
			$issues_settings[TBGMailing::NOTIFY_ISSUE_PROJECT_ASSIGNED] = $i18n->__("Notify me when an issue assigned to one of my projects is updated or created");
			$issues_settings[TBGMailing::NOTIFY_ISSUE_COMMENTED_ON] = $i18n->__("Notify me when an issue I commented on gets updated");

			$this->general_settings = $general_settings;
			$this->issues_settings = $issues_settings;

			$this->uid = TBGContext::getUser()->getID();
		}
		
		public function componentEditIncomingEmailAccount()
		{
			$this->project = TBGContext::getCurrentProject();
		}
		
	}

