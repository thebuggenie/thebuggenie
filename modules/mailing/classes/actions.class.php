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
				$username = str_replace('%2E', '.', $request['forgot_password_username']);
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
					throw new Exception($i18n->__('Please enter an username'));
				}
			}
			catch (Exception $e)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $e->getMessage()));
			}
		}

		/**
		 * Send a test email
		 *
		 * @param TBGRequest $request
		 */
		public function runTestEmail(TBGRequest $request)
		{
			if ($email_to = $request['test_email_to'])
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
					TBGContext::setMessage('module_error', TBGContext::getI18n()->__('The email was not sent'));
					TBGContext::setMessage('module_error_details', $e->getMessage());
				}
			}
			else
			{
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Please specify an email address'));
			}
			$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'mailing')));
		}
		
		public function runSaveIncomingAccount(TBGRequest $request)
		{
			$project = null;
			if ($project_key = $request['project_key'])
			{
				try
				{
					$project = TBGProject::getByKey($project_key);
				}
				catch (Exception $e) {}
			}
			if ($project instanceof TBGProject)
			{
				$account_id = $request['account_id'];
				$account = new TBGIncomingEmailAccount($account_id);
				$account->setIssuetype((integer) $request['issuetype']);
				$account->setProject($project);
				$account->setPort((integer) $request['port']);
				$account->setName($request['name']);
				$account->setServer($request['servername']);
				$account->setUsername($request['username']);
				$account->setPassword($request['password']);
				$account->setSSL((boolean) $request['ssl']);
				$account->setServerType((integer) $request['account_type']);
				$account->save();
				
				if (!$account_id)
				{
					return $this->renderTemplate('mailing/incomingemailaccount', array('project' => $project, 'account' => $account));
				}
				else
				{
					return $this->renderJSON(array('name' => $account->getName()));
				}
			}
		}
		
		public function runCheckIncomingAccount(TBGRequest $request)
		{
			TBGContext::loadLibrary('common');
			if ($account_id = $request['account_id'])
			{
				$account = new TBGIncomingEmailAccount($account_id);
				TBGContext::getModule('mailing')->processIncomingEmailAccount($account);
				
				return $this->renderJSON(array('account_id' => $account->getID(), 'time' => tbg_formatTime($account->getTimeLastFetched(), 6), 'count' => $account->getNumberOfEmailsLastFetched()));
			}
		}
		
		public function runDeleteIncomingAccount(TBGRequest $request)
		{
			if ($account_id = $request['account_id'])
			{
				$account = new TBGIncomingEmailAccount($account_id);
				$account->delete();
				
				return $this->renderText('ok');
			}
		}

	}