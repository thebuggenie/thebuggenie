<?php 

	/**
	 * Main action components
	 */
	class mailingActionComponents extends TBGActionComponent
	{

		public function componentForgotPasswordPane()
		{
			$this->forgottenintro = TBGArticlesTable::getTable()->getArticleByName('ForgottenPasswordIntro');
		}

		public function componentForgotPasswordLink()
		{
		}			
		
		public function componentSettings()
		{
		}

		public function componentAccountSettings()
		{
		}
		
		public function componentConfigCreateuserEmail()
		{
		}

		public function componentEditIncomingEmailAccount()
		{
			$this->project = TBGContext::getCurrentProject();
		}
		
	}

