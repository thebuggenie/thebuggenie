<?php

	class mailnotificationActions extends BUGSaction
	{

		/**
		 * Forgotten password logic
		 *
		 * @param BUGSrequest $request
		 */
		public function runForgot(BUGSrequest $request)
		{
			try
			{
				if (BUGScontext::getRequest()->getMethod() == BUGSrequest::POST)
				{
					$username = BUGScontext::getRequest()->getParameter('forgot_password_username');
					if (!empty($username))
					{
						if (($user = BUGSuser::getByUsername($username)) instanceof BUGSuser)
						{
							if ($user->getEmail())
							{
								BUGScontext::getModule('mailnotification')->sendForgottenPasswordEmail($user);
								BUGScontext::setMessage('forgot_success', BUGScontext::getI18n()->__('Please use the link in the email you received'));
								$this->forward(BUGScontext::getRouting()->generate('login'));
							}
							else
							{
								throw new Exception(BUGScontext::getI18n()->__('Cannot find an email address for this user'));
							}
						}
						else
						{
							throw new Exception(BUGScontext::getI18n()->__('This username does not exist'));
						}
					}
					else
					{
						throw new Exception(BUGScontext::getI18n()->__('Please enter a username'));
					}
				}
			}
			catch (Exception $e)
			{
				BUGScontext::setMessage('forgot_error', $e->getMessage());
				$this->forward(BUGScontext::getRouting()->generate('login'));
			}
		}

		/**
		 * Send a test email
		 *
		 * @param BUGSrequest $request
		 */
		public function runTestEmail(BUGSrequest $request)
		{
			if ($email_to = $request->getParameter('test_email_to'))
			{
				if (BUGScontext::getModule('mailnotification')->sendTestEmail($email_to))
				{
					BUGScontext::setMessage('module_message', BUGScontext::getI18n()->__('The email was successfully accepted for delivery'));
				}
				else
				{
					BUGScontext::setMessage('module_error', BUGScontext::getI18n()->__('The email was not sent'));
					BUGScontext::setMessage('module_error_details', BUGSlogging::getMessagesForCategory('mailnotification', BUGSlogging::LEVEL_NOTICE));
				}
			}
			else
			{
				BUGScontext::setMessage('module_error', BUGScontext::getI18n()->__('Please specify an email address'));
			}
			$this->forward(BUGScontext::getRouting()->generate('configure_module', array('config_module' => 'mailnotification')));
		}

	}