<?php

	class mailnotificationActions extends BUGSaction
	{

		/**
		 * Forgotten password logic
		 *
		 * @param BUGSrequest $request
		 */
		public function runForgot($request)
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


	}