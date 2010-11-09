<?php

	class mailingActions extends TBGAction
	{

		/**
		 * Forgotten password logic (AJAX call)
		 *
		 * @param TBGRequest $request
		 */
		public function runForgot(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			
			try
			{
				$username = $request->getParameter('forgot_password_username');
				if (!empty($username))
				{
					if (($user = TBGUser::getByUsername($username)) instanceof TBGUser)
					{
						if($user->isActivated() && $user->isEnabled() && !$user->isDeleted())
						{
							if ($user->getEmail())
							{
								TBGMailing::getModule()->sendForgottenPasswordEmail($user);
								return $this->renderJSON(array('message' => $i18n->__('Please use the link in the email you received')));
							}
							else
							{
								throw new Exception($i18n->__('Cannot find an email address for this user'));
							}
						}
						else
						{
							throw new Exception($i18n->__('Forbidden for this username, please contact your administrator'));
						}
					}
					else
					{
						throw new Exception($i18n->__('This username does not exist'));
					}
				}
				else
				{
					throw new Exception($i18n->__('Please enter a username'));
				}
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}

		/**
		 * Reset user password
		 * 
		 * @param TBGRequest $request
		 */
		public function runResetPassword(TBGRequest $request)
		{
			$user = TBGUser::getByUsername($request->getParameter('user'));
			$id = $request->getParameter('id');
			$this->forward403unless($user instanceof TBGUser && $id == $user->getHashPassword(), 'Invalid password reset request');
			$this->forward(TBGContext::getRouting()->generate('login_section', array('section' => 'forgot', 'user' => $user->getUsername(), 'id' => $id, 'reset' => true)));
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