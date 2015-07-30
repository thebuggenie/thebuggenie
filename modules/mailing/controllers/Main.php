<?php

    namespace thebuggenie\modules\mailing\controllers;

    use thebuggenie\core\framework,
        thebuggenie\modules\mailing\entities;

    class Main extends framework\Action
    {

        /**
         * Forgotten password logic (AJAX call)
         *
         * @Route(url="/mailing/forgot")
         * @AnonymousRoute
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runForgot(framework\Request $request)
        {
            $i18n = framework\Context::getI18n();

            try
            {
                $username = str_replace('%2E', '.', $request['forgot_password_username']);
                if (!empty($username))
                {
                    if (($user = \thebuggenie\core\entities\User::getByUsername($username)) instanceof \thebuggenie\core\entities\User)
                    {
                        if ($user->isActivated() && $user->isEnabled() && !$user->isDeleted())
                        {
                            if ($user->getEmail())
                            {
                                framework\Context::getModule('mailing')->sendForgottenPasswordEmail($user);
                                return $this->renderJSON(array('message' => $i18n->__('Please use the link in the email you received')));
                            }
                            else
                            {
                                throw new \Exception($i18n->__('Cannot find an email address for this user'));
                            }
                        }
                        else
                        {
                            throw new \Exception($i18n->__('Forbidden for this username, please contact your administrator'));
                        }
                    }
                    else
                    {
                        throw new \Exception($i18n->__('This username does not exist'));
                    }
                }
                else
                {
                    throw new \Exception($i18n->__('Please enter an username'));
                }
            }
            catch (\Exception $e)
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => $e->getMessage()));
            }
        }

        /**
         * Send a test email
         *
         * @Route(url="/mailing/test")
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runTestEmail(framework\Request $request)
        {
            if ($email_to = $request['test_email_to'])
            {
                try
                {
                    if (framework\Context::getModule('mailing')->sendTestEmail($email_to))
                    {
                        framework\Context::setMessage('module_message', framework\Context::getI18n()->__('The email was successfully accepted for delivery'));
                    }
                    else
                    {
                        framework\Context::setMessage('module_error', framework\Context::getI18n()->__('The email was not sent'));
                        framework\Context::setMessage('module_error_details', framework\Logging::getMessagesForCategory('mailing', framework\Logging::LEVEL_NOTICE));
                    }
                }
                catch (\Exception $e)
                {
                    framework\Context::setMessage('module_error', framework\Context::getI18n()->__('The email was not sent'));
                    framework\Context::setMessage('module_error_details', $e->getMessage());
                }
            }
            else
            {
                framework\Context::setMessage('module_error', framework\Context::getI18n()->__('Please specify an email address'));
            }
            $this->forward(framework\Context::getRouting()->generate('configure_module', array('config_module' => 'mailing')));
        }

        /**
         * Save incoming email account
         *
         * @Route(url="/mailing/:project_key/incoming_account/*", name="save_incoming_account")
         * @param \thebuggenie\core\framework\Request $request
         * @return type
         */
        public function runSaveIncomingAccount(framework\Request $request)
        {
            $project = null;
            if ($project_key = $request['project_key'])
            {
                try
                {
                    $project = \thebuggenie\core\entities\Project::getByKey($project_key);
                }
                catch (\Exception $e)
                {

                }
            }
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                try
                {
                    $account_id = $request['account_id'];
                    $account = ($account_id) ? new \thebuggenie\modules\mailing\entities\IncomingEmailAccount($account_id) : new \thebuggenie\modules\mailing\entities\IncomingEmailAccount();
                    $account->setIssuetype((integer) $request['issuetype']);
                    $account->setProject($project);
                    $account->setPort((integer) $request['port']);
                    $account->setName($request['name']);
                    $account->setFoldername($request['folder']);
                    $account->setKeepEmails($request['keepemail']);
                    $account->setServer($request['servername']);
                    $account->setUsername($request['username']);
                    $account->setPassword($request['password']);
                    $account->setSSL((boolean) $request['ssl']);
                    $account->setIgnoreCertificateValidation((boolean) $request['ignore_certificate_validation']);
                    $account->setUsePlaintextAuthentication((boolean) $request['plaintext_authentication']);
                    $account->setServerType((integer) $request['account_type']);
                    $account->save();

                    if (!$account_id)
                    {
                        return $this->renderComponent('mailing/incomingemailaccount', array('project' => $project, 'account' => $account));
                    }
                    else
                    {
                        return $this->renderJSON(array('name' => $account->getName()));
                    }
                }
                catch (\Exception $e)
                {
                    $this->getResponse()->setHttpStatus(400);
                    return $this->renderJSON(array('error' => $this->getI18n()->__('This is not a valid mailing account')));
                }
            }
            else
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => $this->getI18n()->__('This is not a valid project')));
            }
        }

        /**
         * Check incoming email accounts for incoming emails
         *
         * @Route(url="/mailing/incoming_account/:account_id/check", name="check_account")
         * @param \thebuggenie\core\framework\Request $request
         * @return type
         * @throws \Exception
         */
        public function runCheckIncomingAccount(framework\Request $request)
        {
            framework\Context::loadLibrary('common');
            if ($account_id = $request['account_id'])
            {
                try
                {
                    $account = new \thebuggenie\modules\mailing\entities\IncomingEmailAccount($account_id);
                    try
                    {
                        if (!function_exists('imap_open'))
                        {
                            throw new \Exception($this->getI18n()->__('The php imap extension is not installed'));
                        }
                        framework\Context::getModule('mailing')->processIncomingEmailAccount($account);
                    }
                    catch (\Exception $e)
                    {
                        $this->getResponse()->setHttpStatus(400);
                        return $this->renderJSON(array('error' => $e->getMessage()));
                    }

                    return $this->renderJSON(array('account_id' => $account->getID(), 'time' => tbg_formatTime($account->getTimeLastFetched(), 6), 'count' => $account->getNumberOfEmailsLastFetched()));
                }
                catch (\Exception $e)
                {
                    $this->getResponse()->setHttpStatus(400);
                    return $this->renderJSON(array('error' => $this->getI18n()->__('This is not a valid mailing account')));
                }
            }
            else
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => $this->getI18n()->__('This is not a valid mailing account')));
            }
        }

        /**
         * Delete an incoming email account
         *
         * @Route(url="/mailing/incoming_account/:account_id/delete", name="delete_account")
         * @param \thebuggenie\core\framework\Request $request
         * @return type
         */
        public function runDeleteIncomingAccount(framework\Request $request)
        {
            if ($account_id = $request['account_id'])
            {
                try
                {
                    $account = new \thebuggenie\modules\mailing\entities\IncomingEmailAccount($account_id);
                    $account->delete();

                    return $this->renderJSON(array('message' => $this->getI18n()->__('Incoming email account deleted')));
                }
                catch (\Exception $e)
                {
                    $this->getResponse()->setHttpStatus(400);
                    return $this->renderJSON(array('error' => $this->getI18n()->__('This is not a valid mailing account')));
                }
            }
            else
            {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('error' => $this->getI18n()->__('This is not a valid mailing account')));
            }
        }

        /**
         * Save project settings
         *
         * @Route(url="/configure/project/:project_id/mailing", name="configure_settings")
         * @Parameters(config_module="core", section=15)
         * @param \thebuggenie\core\framework\Request $request
         * @return type
         */
        public function runConfigureProjectSettings(framework\Request $request)
        {
            $this->forward403unless($request->isPost());

            if ($this->access_level != framework\Settings::ACCESS_FULL)
            {
                $project_id = $request['project_id'];

                if (trim($request['mailing_from_address']) != '')
                {
                    if (filter_var(trim($request['mailing_from_address']), FILTER_VALIDATE_EMAIL) !== false)
                    {
                        framework\Context::getModule('mailing')->saveSetting(Mailing::SETTING_PROJECT_FROM_ADDRESS . $project_id, trim(mb_strtolower($request->getParameter('mailing_from_address'))));
                        if (trim($request['mailing_from_name']) !== '')
                        {
                            framework\Context::getModule('mailing')->saveSetting(Mailing::SETTING_PROJECT_FROM_NAME . $project_id, trim($request->getParameter('mailing_from_name')));
                        }
                        else
                        {
                            framework\Context::getModule('mailing')->deleteSetting(Mailing::SETTING_PROJECT_FROM_NAME . $project_id);
                        }
                    }
                    else
                    {
                        $this->getResponse()->setHttpStatus(400);
                        return $this->renderJSON(array('message' => framework\Context::getI18n()->__('Please enter a valid email address')));
                    }
                }
                elseif (trim($request['mailing_from_address']) == '')
                {
                    framework\Context::getModule('mailing')->deleteSetting(Mailing::SETTING_PROJECT_FROM_ADDRESS . $project_id);
                    framework\Context::getModule('mailing')->deleteSetting(Mailing::SETTING_PROJECT_FROM_NAME . $project_id);
                }

                return $this->renderJSON(array('failed' => false, 'message' => framework\Context::getI18n()->__('Settings saved')));
            }
            else
            {
                $this->forward403();
            }
        }

    }
