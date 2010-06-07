<?php

	class mailingActions extends TBGAction
	{

		/**
		 * Forgotten password logic
		 *
		 * @param TBGRequest $request
		 */
		public function runForgot(TBGRequest $request)
		{
			try
			{
				if (TBGContext::getRequest()->getMethod() == TBGRequest::POST)
				{
					$username = TBGContext::getRequest()->getParameter('forgot_password_username');
					if (!empty($username))
					{
						if (($user = TBGUser::getByUsername($username)) instanceof TBGUser)
						{
							if ($user->getEmail())
							{
								TBGMailing::getModule()->sendForgottenPasswordEmail($user);
								TBGContext::setMessage('forgot_success', TBGContext::getI18n()->__('Please use the link in the email you received'));
								$this->forward(TBGContext::getRouting()->generate('login'));
							}
							else
							{
								throw new Exception(TBGContext::getI18n()->__('Cannot find an email address for this user'));
							}
						}
						else
						{
							throw new Exception(TBGContext::getI18n()->__('This username does not exist'));
						}
					}
					else
					{
						throw new Exception(TBGContext::getI18n()->__('Please enter a username'));
					}
				}
			}
			catch (Exception $e)
			{
				TBGContext::setMessage('forgot_error', $e->getMessage());
				$this->forward(TBGContext::getRouting()->generate('login'));
			}
		}

		/**
		 * Send a test email
		 *
		 * @param TBGRequest $request
		 */
		public function runTestEmail(TBGRequest $request)
		{
			if ($email_to = $request->getParameter('test_email_to'))
			{
				try
				{
					if (TBGMailing::getModule()->sendTestEmail($email_to))
					{
						TBGContext::setMessage('module_message', TBGContext::getI18n()->__('The email was successfully accepted for delivery'));
					}
					else
					{
						TBGContext::setMessage('module_error', TBGContext::getI18n()->__('The email was not sent'));
						TBGContext::setMessage('module_error_details', TBGLogging::getMessagesForCategory('mailing', TBGLogging::LEVEL_NOTICE));
					}
				}
				catch (Exception $e)
				{
					TBGContext::setMessage('module_error', $e->getMessage());
				}
			}
			else
			{
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Please specify an email address'));
			}
			$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'mailing')));
		}

	}