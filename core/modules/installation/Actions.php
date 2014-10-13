<?php

    namespace thebuggenie\core\modules\installation;

    class Actions extends \TBGAction
    {

        /**
         * Sample docblock used to test docblock retrieval
         */
        protected $_sampleproperty;

        public function preExecute(\TBGRequest $request, $action)
        {
            $this->getResponse()->setDecoration(\TBGResponse::DECORATE_NONE);
        }

        /**
         * Runs the installation action
         *
         * @param \TBGRequest $request The request object
         *
         * @return null
         */
        public function runInstallIntro(\TBGRequest $request)
        {
            $this->getResponse()->setDecoration(\TBGResponse::DECORATE_NONE);

            if (($step = $request['step']) && $step >= 1 && $step <= 6)
            {
                if ($step >= 5)
                {
                    $scope = new \TBGScope(1);
                    \TBGContext::setScope($scope);
                }
                return $this->redirect('installStep' . $step);
            }
        }

        /**
         * Runs the action for the first step of the installation
         *
         * @param \TBGRequest $request The request object
         *
         * @return null
         */
        public function runInstallStep1(\TBGRequest $request)
        {
            $this->all_well = true;
            $this->base_folder_perm_ok = true;
            $this->cache_folder_perm_ok = true;
            $this->thebuggenie_folder_perm_ok = true;
            $this->b2db_param_file_ok = true;
            $this->b2db_param_folder_ok = true;
            $this->pdo_ok = true;
            $this->mysql_ok = true;
            $this->pgsql_ok = true;
            $this->gd_ok = true;
            $this->mb_ok = true;
            $this->php_ok = true;
            $this->pcre_ok = true;
            $this->docblock_ok = false;
            $this->php_ver = PHP_VERSION;
            $this->pcre_ver = PCRE_VERSION;

            if (version_compare($this->php_ver, '5.3.0', 'lt'))
            {
                $this->php_ok = false;
                $this->all_well = false;
            }
            if (version_compare($this->pcre_ver, '7', 'le'))
            {
                $this->pcre_ok = false;
                $this->all_well = false;
            }
            if (file_exists(THEBUGGENIE_CONFIGURATION_PATH . 'b2db.yml') && !is_writable(THEBUGGENIE_CONFIGURATION_PATH . 'b2db.yml'))
            {
                $this->b2db_param_file_ok = false;
                $this->all_well = false;
            }
            elseif (!file_exists(THEBUGGENIE_CONFIGURATION_PATH . 'b2db.yml') && !is_writable(THEBUGGENIE_CONFIGURATION_PATH))
            {
                $this->b2db_param_folder_ok = false;
                $this->all_well = $this->b2db_param_file_ok;
            }
            if (!is_writable(THEBUGGENIE_PATH))
            {
                $this->base_folder_perm_ok = false;
                $this->all_well = false;
            }

            if (!is_writable(THEBUGGENIE_PATH))
            {
                $this->base_folder_perm_ok = false;
                $this->all_well = false;
            }

            if (!is_writable(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DS))
            {
                $this->thebuggenie_folder_perm_ok = false;
                $this->all_well = false;
            }
            if (!class_exists('PDO'))
            {
                $this->pdo_ok = false;
                $this->all_well = false;
            }
            if (!extension_loaded('pdo_mysql'))
            {
                $this->mysql_ok = false;
            }
            if (!extension_loaded('pdo_pgsql'))
            {
                $this->pgsql_ok = false;
            }
            if (!extension_loaded('gd'))
            {
                $this->gd_ok = false;
            }
            if (!extension_loaded('mbstring'))
            {
                $this->mb_ok = false;
                $this->all_well = false;
            }

            $reflection = new \ReflectionProperty(get_class($this), '_sampleproperty');
            $docblock = $reflection->getDocComment();
            if ($docblock)
            {
                $this->docblock_ok = true;
            }
            else
            {
                $this->all_well = false;
            }

            if (!$this->mysql_ok && !$this->pgsql_ok)
            {
                $this->all_well = false;
            }
        }

        /**
         * Runs the action for the second step of the installation
         * where you enter database information
         *
         * @param \TBGRequest $request The request object
         *
         * @return null
         */
        public function runInstallStep2(\TBGRequest $request)
        {
            $this->preloaded = false;
            $this->selected_connection_detail = 'custom';

            if (!$this->error)
            {
                try
                {
                    $b2db_filename = \THEBUGGENIE_CONFIGURATION_PATH . "b2db.yml";
                    if (file_exists($b2db_filename))
                    {
                        $b2db_config = \Spyc::YAMLLoad($b2db_filename, true);
                        \b2db\Core::initialize($b2db_config);
                    }
                }
                catch (\Exception $e)
                {

                }
                if (\b2db\Core::isInitialized())
                {
                    $this->preloaded = true;
                    $this->username = \b2db\Core::getUname();
                    $this->password = \b2db\Core::getPasswd();
                    $this->dsn = \b2db\Core::getDSN();
                    $this->hostname = \b2db\Core::getHost();
                    $this->port = \b2db\Core::getPort();
                    $this->b2db_dbtype = \b2db\Core::getDBtype();
                    $this->db_name = \b2db\Core::getDBname();
                }
            }
        }

        /**
         * Runs the action for the third step of the installation
         * where it tests the connection, sets up the database and the initial scope
         *
         * @param \TBGRequest $request The request object
         *
         * @return null
         */
        public function runInstallStep3(\TBGRequest $request)
        {
            $this->selected_connection_detail = $request['connection_type'];
            try
            {
                if ($this->username = $request['db_username'])
                {
                    \b2db\Core::setUname($this->username);
                    \b2db\Core::setTablePrefix($request['db_prefix']);
                    if ($this->password = $request->getRawParameter('db_password'))
                        \b2db\Core::setPasswd($this->password);

                    if ($this->selected_connection_detail == 'dsn')
                    {
                        if (($this->dsn = $request['db_dsn']) != '')
                            \b2db\Core::setDSN($this->dsn);
                        else
                            throw new \Exception('You must provide a valid DSN');
                    }
                    else
                    {
                        if ($this->db_type = $request['db_type'])
                        {
                            \b2db\Core::setDBtype($this->db_type);
                            if ($this->db_hostname = $request['db_hostname'])
                                \b2db\Core::setHost($this->db_hostname);
                            else
                                throw new \Exception('You must provide a database hostname');

                            if ($this->db_port = $request['db_port'])
                                \b2db\Core::setPort($this->db_port);

                            if ($this->db_databasename = $request['db_name'])
                                \b2db\Core::setDBname($this->db_databasename);
                            else
                                throw new \Exception('You must provide a database to use');
                        }
                        else
                        {
                            throw new \Exception('You must provide a database type');
                        }
                    }

                    try
                    {
                        \b2db\Core::doConnect();
                    }
                    catch (\b2db\Exception $e)
                    {
                        throw new \Exception('There was an error connecting to the database: '.$e->getMessage());
                    }

                    if (\b2db\Core::getDBname() == '')
                        throw new \Exception('You must provide a database to use');

                    \b2db\Core::saveConnectionParameters(\THEBUGGENIE_CONFIGURATION_PATH . "b2db.yml");
                }
                else
                {
                    throw new \Exception('You must provide a database username');
                }

                // Create legacy tables
                $tables_path = THEBUGGENIE_CORE_PATH . 'classes' . DS . 'B2DB' . DS;
                foreach (scandir($tables_path) as $tablefile)
                {
                    if (in_array($tablefile, array('.', '..')))
                        continue;

                    if (($tablename = mb_substr($tablefile, 0, mb_strpos($tablefile, '.'))) != '')
                    {
                        \b2db\Core::getTable($tablename)->create();
                        \b2db\Core::getTable($tablename)->createIndexes();
                        $tables_created[] = $tablename;
                    }
                }

                // Create v4 tables
                $b2db_entities_path = THEBUGGENIE_CORE_PATH . 'entities' . DS . 'b2db' . DS;
                foreach (scandir($b2db_entities_path) as $tablefile)
                {
                    if (in_array($tablefile, array('.', '..')))
                        continue;

                    if (($tablename = mb_substr($tablefile, 0, mb_strpos($tablefile, '.'))) != '')
                    {
                        $tablename = "\\thebuggenie\\core\\entities\\b2db\\{$tablename}";
                        \b2db\Core::getTable($tablename)->create();
                        \b2db\Core::getTable($tablename)->createIndexes();
                        $tables_created[] = $tablename;
                    }
                }
                sort($tables_created);
                $this->tables_created = $tables_created;
            }
            catch (\Exception $e)
            {
                $this->error = $e->getMessage();
            }
            $server_type = strtolower(trim($_SERVER['SERVER_SOFTWARE']));
            switch (true)
            {
                case (stripos($server_type, 'apache') !== false):
                    $this->server_type = 'apache';
                    break;
                case (stripos($server_type, 'nginx') !== false):
                    $this->server_type = 'nginx';
                    break;
                case (stripos($server_type, 'iis') !== false):
                    $this->server_type = 'iis';
                    break;
                default:
                    $this->server_type = 'unknown';
            }
            $dirname = dirname($_SERVER['PHP_SELF']);
            if (mb_stristr(PHP_OS, 'WIN'))
            {
                $dirname = str_replace("\\", "/", $dirname); /* Windows adds a \ to the URL which we don't want */
            }

            $this->dirname = ($dirname != '/') ? $dirname . '/' : $dirname;
        }

        /**
         * Runs the action for the fourth step of the installation
         * where it loads fixtures and saves settings for url
         *
         * @param \TBGRequest $request The request object
         *
         * @return null
         */
        public function runInstallStep4(\TBGRequest $request)
        {
            try
            {
                \TBGLogging::log('Initializing language support');
                \TBGContext::reinitializeI18n('en_US');

                \TBGLogging::log('Loading fixtures for default scope');
                $scope = new \TBGScope();
                $scope->addHostname('*');
                $scope->setName('The default scope');
                $scope->setEnabled(true);
                \TBGContext::setScope($scope);
                $scope->save();

                \TBGSettings::saveSetting('language', 'en_US', 'core', 1);

                \TBGModule::installModule('publish');
                \TBGModule::installModule('mailing');
                \TBGModule::installModule('vcs_integration');

                $this->htaccess_error = false;
                $this->htaccess_ok = (bool) $request['apache_autosetup'];

                if ($request['apache_autosetup'])
                {
                    if (!is_writable(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/') || (file_exists(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.htaccess') && !is_writable(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.htaccess')))
                    {
                        $this->htaccess_error = 'Permission denied when trying to save the [main folder]/' . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.htaccess';
                    }
                    else
                    {
                        $content = str_replace('###PUT URL SUBDIRECTORY HERE###', $request['url_subdir'], file_get_contents(THEBUGGENIE_CORE_PATH . '/templates/htaccess.template'));
                        file_put_contents(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.htaccess', $content);
                        if (file_get_contents(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.htaccess') != $content)
                        {
                            $this->htaccess_error = true;
                        }
                    }
                    if (!is_writable(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/') || (file_exists(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.user.ini') && !is_writable(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.user.ini')))
					{
						$this->htaccess_error = 'Permission denied when trying to save the [main folder]/' . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.user.ini';
					}
					else
					{
						$content = file_get_contents(THEBUGGENIE_CORE_PATH . '/templates/user.ini.template');
						file_put_contents(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.user.ini', $content);
						if (file_get_contents(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.user.ini') != $content)
						{
							$this->htaccess_error = true;
						}
					}
                }
            }
            catch (\Exception $e)
            {
                $this->error = $e->getMessage();
                throw $e;
            }
        }

        /**
         * Runs the action for the fifth step of the installation
         * where it enables modules on demand
         *
         * @param \TBGRequest $request The request object
         *
         * @return null
         */
        public function runInstallStep5(\TBGRequest $request)
        {
            try
            {
                $password = trim($request['password']);
                if ($password !== trim($request['password_repeat']))
                    throw new \Exception("Passwords don't match");

                $this->password = $password;
                $user = \TBGUsersTable::getTable()->getByUsername('administrator');
                $username = trim(strtolower($request['username']));
                if ($username) $user->setUsername($username);
                $user->setRealname($request['name']);
                $user->setPassword($request['password']);
                $user->setEmail($request['email']);
                $user->save();

                $this->user = $user;
            }
            catch (\Exception $e)
            {
                $this->error = $e->getMessage();
            }
        }

        /**
         * Runs the action for the sixth step of the installation
         * where it finalizes the installation
         *
         * @param \TBGRequest $request The request object
         *
         * @return null
         */
        public function runInstallStep6(\TBGRequest $request)
        {
            $installed_string = \TBGSettings::getMajorVer() . '.' . \TBGSettings::getMinorVer() . ', installed ' . date('d.m.Y H:i');

            if (file_put_contents(THEBUGGENIE_PATH . 'installed', $installed_string) === false)
            {
                $this->error = "Couldn't write to the main directory. Please create the file " . THEBUGGENIE_PATH . "installed manually, with the following content: \n" . $installed_string;
            }
            if (file_exists(THEBUGGENIE_PATH . 'upgrade') && !unlink(THEBUGGENIE_PATH . 'upgrade'))
            {
                $this->error = "Couldn't remove the file " . THEBUGGENIE_PATH . "upgrade. Please remove this file manually.";
            }
            \TBGContext::clearRoutingCache();
        }

        protected function _upgradeFrom3dot0()
        {
            // Add new tables
            \TBGScopeHostnamesTable::getTable()->create();

            // Add classpath for existing old tables used for upgrade
            \TBGContext::addAutoloaderClassPath(THEBUGGENIE_MODULES_PATH . 'installation' . DS . 'classes' . DS . 'upgrade_3.0');

            // Upgrade old tables
            \TBGScopesTable::getTable()->upgrade(\TBGScopesTable3dot0::getTable());
            \TBGIssueFieldsTable::getTable()->upgrade(\TBGIssueFieldsTable3dot0::getTable());

            // Upgrade all modules
            foreach (\TBGContext::getModules() as $module)
            {
                if (method_exists($module, 'upgradeFrom3dot0'))
                {
                    $module->upgradeFrom3dot0();
                }
            }

            // Start a transaction to preserve the upgrade path
            $transaction = \b2db\Core::startTransaction();

            // Add votes to feature requests for default issue type scheme
            $its = new \TBGIssuetypeScheme(1);
            foreach (\TBGIssuetype::getAll() as $fr)
            {
                if ($fr instanceof \TBGIssuetype)
                {
                    if (in_array($fr->getKey(), array('featurerequest', 'bugreport', 'enhancement')))
                    {
                        $its->setFieldAvailableForIssuetype($fr, 'votes');
                    }
                }
            }

            $ut = \TBGUsersTable::getTable();
            $crit = $ut->getCriteria();
            $crit->addUpdate(\TBGUsersTable::PRIVATE_EMAIL, true);
            $ut->doUpdate($crit);

            // Add default gravatar setting
            \TBGSettings::saveSetting(\TBGSettings::SETTING_ENABLE_GRAVATARS, 1);

            $trans_crit = \TBGWorkflowTransitionsTable::getTable()->getCriteria();
            $trans_crit->addWhere(\TBGWorkflowTransitionsTable::NAME, 'Request more information');
            $trans_crit->addWhere(\TBGWorkflowTransitionsTable::WORKFLOW_ID, 1);
            $trans_row = \TBGWorkflowTransitionsTable::getTable()->doSelectOne($trans_crit);
            if ($trans_row)
            {
                $transition = new \TBGWorkflowTransition($trans_row->get(\TBGWorkflowTransitionsTable::ID), $trans_row);
                $transition->setTemplate('main/updateissueproperties');
                $transition->save();
            }

            // End transaction and finalize upgrade
            $transaction->commitAndEnd();
            $this->upgrade_complete = true;
        }

        protected function _upgradeFrom3dot2(\TBGRequest $request)
        {
            set_time_limit(0);

            \TBGMilestonesTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGMilestone::getB2DBTable());
            \TBGProjectsTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGProjectsTable::getTable());
            \TBGLogTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGLogTable::getTable());
            \TBGUsersTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGUsersTable::getTable());
            \TBGIssuesTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGIssuesTable::getTable());
            \TBGWorkflowsTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGWorkflowsTable::getTable());
            \TBGIssueSpentTimesTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGIssueSpentTimesTable::getTable());
            \TBGCommentsTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGCommentsTable::getTable());
            \TBGSavedSearchesTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGSavedSearchesTable::getTable());
            \TBGSettingsTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGSettingsTable::getTable());
            \TBGNotificationsTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGNotificationsTable::getTable());
            \TBGPermissionsTable::getTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGPermissionsTable::getTable());
            \thebuggenie\core\entities\Dashboard::getB2DBTable()->create();
            \thebuggenie\core\entities\DashboardView::getB2DBTable()->upgrade(\thebuggenie\core\modules\installation\upgrade_32\TBGDashboardViewsTable::getTable());
            \thebuggenie\core\entities\ApplicationPassword::getB2DBTable()->create();
            \thebuggenie\core\entities\NotificationSetting::getB2DBTable()->create();
            \thebuggenie\core\entities\AgileBoard::getB2DBTable()->create();

            $transaction = \b2db\Core::startTransaction();

            // Upgrade user passwords
            switch ($request['upgrade_passwords'])
            {
                case 'manual':
                    $password = $request['manul_password'];
                    foreach (\TBGUsersTable::getTable()->selectAll() as $user)
                    {
                        $user->setPassword($password);
                        $user->save();
                    }
                    break;
                case 'auto':
                    $field = ($request['upgrade_passwords_pick'] == 'username') ? 'username' : 'email';
                    foreach (\TBGUsersTable::getTable()->selectAll() as $user)
                    {
                        if ($field == 'username' && trim($user->getUsername()))
                        {
                            $user->setPassword(trim($user->getUsername()));
                            $user->save();
                        }
                        elseif ($field == 'email' && trim($user->getEmail()))
                        {
                            $user->setPassword(trim($user->getEmail()));
                            $user->save();
                        }
                    }
                    break;
            }

            $adminuser = \TBGContext::factory()->TBGUser(1);
            $adminuser->setPassword($request['admin_password']);
            $adminuser->save();

            // Add new settings
            \TBGSettings::saveSetting(\TBGSettings::SETTING_SERVER_TIMEZONE, 'core', date_default_timezone_get(), 0, 1);

            foreach ($request->getParameter('status') as $scope_id => $status_id)
            {
                $scope = \TBGScopesTable::getTable()->selectById((int) $scope_id);
                if ($scope instanceof \TBGScope)
                {
                    $epic = new \TBGIssuetype();
                    $epic->setName('Epic');
                    $epic->setIcon('epic');
                    $epic->setDescription('Issue type suited for entering epics');
                    $epic->setScope($scope_id);
                    $epic->save();
                    \TBGSettings::saveSetting('issuetype_epic', $epic->getID(), 'core', $scope_id);

                    foreach (\TBGWorkflowsTable::getTable()->getAll((int) $scope_id) as $workflow)
                    {
                        $transition = new \TBGWorkflowTransition();
                        $steps = $workflow->getSteps();
                        $step = array_shift($steps);
                        $step->setLinkedStatusID((int) $status_id);
                        $step->save();
                        $transition->setOutgoingStep($step);
                        $transition->setName('Issue created');
                        $transition->setWorkflow($workflow);
                        $transition->setScope($scope);
                        $transition->setDescription('This is the initial transition for issues using this workflow');
                        $transition->save();
                        $workflow->setInitialTransition($transition);
                        $workflow->save();
                    }
                    \TBGActivityType::loadFixtures($scope);
                }
            }
            $transaction->commitAndEnd();

            \TBGContext::finishUpgrading();
            foreach (\TBGContext::getModules() as $module)
            {
                $module->upgrade();
            }

            $this->upgrade_complete = true;
        }

        public function runUpgrade(\TBGRequest $request)
        {
            $version_info = explode(',', file_get_contents(THEBUGGENIE_PATH . 'installed'));
            $this->current_version = $version_info[0];
            $this->upgrade_available = ($this->current_version != '4.0');

            if ($this->upgrade_available)
            {
                $scope = new \TBGScope();
                $scope->setID(1);
                $scope->setEnabled();
                \TBGContext::setScope($scope);

                $this->statuses = \TBGListTypesTable::getTable()->getStatusListForUpgrade();
                $this->adminusername = \thebuggenie\core\modules\installation\upgrade_32\TBGUsersTable::getTable()->getAdminUsername();
            }
            $this->upgrade_complete = false;

            if ($this->upgrade_available && $request->isPost())
            {
                $this->upgrade_complete = false;
                $this->_upgradeFrom3dot2($request);

                if ($this->upgrade_complete)
                {
                    $existing_installed_content = file_get_contents(THEBUGGENIE_PATH . 'installed');
                    file_put_contents(THEBUGGENIE_PATH . 'installed', \TBGSettings::getVersion(false, false) . ', upgraded ' . date('d.m.Y H:i') . "\n" . $existing_installed_content);
                    $prev_error_reportiong_level = error_reporting(0);
                    unlink(THEBUGGENIE_PATH . 'upgrade');
                    error_reporting($prev_error_reportiong_level);
                    if (file_exists(THEBUGGENIE_PATH . 'upgrade'))
                        $this->upgrade_file_failed = true;
                    $this->current_version = \TBGSettings::getVersion(false, false);
                    $this->upgrade_available = false;
                }
            }
            elseif ($this->upgrade_available)
            {
                $this->permissions_ok = false;
                if (is_writable(THEBUGGENIE_PATH . 'installed') && is_writable(THEBUGGENIE_PATH . 'upgrade'))
                {
                    $this->permissions_ok = true;
                }
            }
            elseif ($this->upgrade_complete)
            {
                $this->forward(\TBGContext::getRouting()->generate('home'));
            }
        }

    }
