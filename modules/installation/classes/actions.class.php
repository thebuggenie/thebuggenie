<?php

	class installationActions extends TBGAction
	{
		
		/**
		 * Sample docblock used to test docblock retrieval
		 */
		protected $_sampleproperty;

		public function preExecute(TBGRequest $request, $action)
		{
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
		}

		/**
		 * Runs the installation action
		 * 
		 * @param TBGRequest $request The request object
		 * 
		 * @return null
		 */
		public function runInstallIntro(TBGRequest $request)
		{
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
			
			if (($step = $request['step']) && $step >= 1 && $step <= 6)
			{
				if ($step >= 5)
				{
					$scope = new TBGScope(1);
					TBGContext::setScope($scope);
				}
				return $this->redirect('installStep'.$step);
			}
		}
		
		/**
		 * Runs the action for the first step of the installation
		 * 
		 * @param TBGRequest $request The request object
		 * 
		 * @return null
		 */
		public function runInstallStep1(TBGRequest $request)
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
			if (version_compare($this->pcre_ver, '7.99', 'le'))
			{
				$this->pcre_ok = false;
				$this->all_well = false;
			}			
			if (file_exists(THEBUGGENIE_CORE_PATH . 'b2db_bootstrap.inc.php') && !is_writable(THEBUGGENIE_CORE_PATH . 'b2db_bootstrap.inc.php'))
			{
				$this->b2db_param_file_ok = false;
				$this->all_well = false;
			}
			if (!file_exists(THEBUGGENIE_CORE_PATH . 'b2db_bootstrap.inc.php') && !is_writable(THEBUGGENIE_CORE_PATH))
			{
				$this->b2db_param_folder_ok = false;
				$this->all_well = false;
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
			
			if (!file_exists(THEBUGGENIE_CORE_PATH . 'cache') && is_writable(THEBUGGENIE_CORE_PATH)) mkdir(THEBUGGENIE_CORE_PATH . 'cache');
			if (!file_exists(THEBUGGENIE_CORE_PATH . 'cache' . DS . 'B2DB') && is_writable(THEBUGGENIE_CORE_PATH . 'cache')) mkdir(THEBUGGENIE_CORE_PATH . 'cache' . DS . 'B2DB');
			
			if (!file_exists(THEBUGGENIE_CORE_PATH . 'cache') || !file_exists(THEBUGGENIE_CORE_PATH . 'cache' . DS . 'B2DB') || !is_writable(THEBUGGENIE_CORE_PATH . 'cache' . DS) || !is_writable(THEBUGGENIE_CORE_PATH . 'cache' . DS .'B2DB' . DS))
			{
				$this->cache_folder_perm_ok = false;
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
			
			$reflection = new ReflectionProperty(get_class($this), '_sampleproperty');
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
		 * @param TBGRequest $request The request object
		 * 
		 * @return null
		 */
		public function runInstallStep2(TBGRequest $request)
		{
			$this->preloaded = false;
			$this->selected_connection_detail = 'custom';
			
			if (!$this->error)
			{
				try
				{
					\b2db\Core::initialize(THEBUGGENIE_CORE_PATH . 'b2db_bootstrap.inc.php');
				}
				catch (Exception $e)
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
		 * @param TBGRequest $request The request object
		 * 
		 * @return null
		 */
		public function runInstallStep3(TBGRequest $request)
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
							throw new Exception('You must provide a valid DSN');
					}
					else
					{
						if ($this->db_type = $request['db_type'])
						{
							\b2db\Core::setDBtype($this->db_type);
							if ($this->db_hostname = $request['db_hostname'])
								\b2db\Core::setHost($this->db_hostname);
							else
								throw new Exception('You must provide a database hostname');

							if ($this->db_port = $request['db_port'])
								\b2db\Core::setPort($this->db_port);

							if ($this->db_databasename = $request['db_name'])
								\b2db\Core::setDBname($this->db_databasename);
							else
								throw new Exception('You must provide a database to use');
						}
						else
						{
							throw new Exception('You must provide a database type');
						}
					}
					
					\b2db\Core::initialize(THEBUGGENIE_CORE_PATH . 'b2db_bootstrap.inc.php');
					\b2db\Core::doConnect();
					
					if (\b2db\Core::getDBname() == '')
						throw new Exception('You must provide a database to use');

					\b2db\Core::saveConnectionParameters(THEBUGGENIE_CORE_PATH . 'b2db_bootstrap.inc.php');
				}
				else
				{
					throw new Exception('You must provide a database username');
				}
				
				// Add table classes to classpath 
				$tables_path = THEBUGGENIE_CORE_PATH . 'classes' . DS . 'B2DB' . DS;
				TBGContext::addAutoloaderClassPath($tables_path);
				$tables_path_handle = opendir($tables_path);
				$tables_created = array();
				while ($table_class_file = readdir($tables_path_handle))
				{
					if (($tablename = mb_substr($table_class_file, 0, mb_strpos($table_class_file, '.'))) != '') 
					{
						\b2db\Core::getTable($tablename)->create();
						\b2db\Core::getTable($tablename)->createIndexes();
						$tables_created[] = $tablename;
					}
				}
				sort($tables_created);
				$this->tables_created = $tables_created;
				
				//TBGScope::setupInitialScope();
				
			}
			catch (Exception $e)
			{
				throw $e;
				$this->error = $e->getMessage();
			}
		}
		
		/**
		 * Runs the action for the fourth step of the installation
		 * where it loads fixtures and saves settings for url
		 * 
		 * @param TBGRequest $request The request object
		 * 
		 * @return null
		 */
		public function runInstallStep4(TBGRequest $request)
		{
			try
			{
				TBGLogging::log('Initializing language support');
				TBGContext::reinitializeI18n('en_US');

				TBGLogging::log('Loading fixtures for default scope');
				$scope = new TBGScope();
				$scope->addHostname('*');
				$scope->setName('The default scope');
				$scope->setEnabled(true);
				TBGContext::setScope($scope);
				$scope->save();
				
				TBGLogging::log('Setting up default users and groups');
				TBGSettings::saveSetting('language', 'en_US', 'core', 1);

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
				}
			}
			catch (Exception $e)
			{
				$this->error = $e->getMessage();
				throw $e;
			}
		}
		
		/**
		 * Runs the action for the fifth step of the installation
		 * where it enables modules on demand
		 * 
		 * @param TBGRequest $request The request object
		 * 
		 * @return null
		 */
		public function runInstallStep5(TBGRequest $request)
		{
			$this->sample_data = false;
			try
			{
				if ($request->hasParameter('modules'))
				{
					foreach ($request->getParameter('modules', array()) as $module => $install)
					{
						if ((bool) $install && file_exists(THEBUGGENIE_MODULES_PATH . $module . DS . 'module'))
						{
							TBGModule::installModule($module);
						}
					}
				}
				elseif ($request->hasParameter('sample_data'))
				{
					$this->sample_data = true;
				}
			}
			catch (Exception $e)
			{
				throw $e;
				$this->error = $e->getMessage();
			}
		}
		
		/**
		 * Runs the action for the sixth step of the installation
		 * where it finalizes the installation
		 * 
		 * @param TBGRequest $request The request object
		 * 
		 * @return null
		 */
		public function runInstallStep6(TBGRequest $request)
		{
			if (file_put_contents(THEBUGGENIE_PATH . 'installed', TBGSettings::getMajorVer() . '.' . TBGSettings::getMinorVer() . ', installed ' . date('d.m.Y H:i')) === false)
			{
				$this->error = "Couldn't write to the main directory. Please create the file " . THEBUGGENIE_PATH . "installed manually, with the following content: \n3.0, installed " . date('d.m.Y H:i');
			}
			if (file_exists(THEBUGGENIE_PATH . 'upgrade') && !unlink(THEBUGGENIE_PATH . 'upgrade'))
			{
				$this->error = "Couldn't remove the file " . THEBUGGENIE_PATH . "upgrade. Please remove this file manually.";
			}
		}

		protected function _upgradeFrom3dot0()
		{
			// Add new tables
			TBGScopeHostnamesTable::getTable()->create();
			
			// Add classpath for existing old tables used for upgrade
			TBGContext::addAutoloaderClassPath(THEBUGGENIE_MODULES_PATH . 'installation' . DS . 'classes' . DS . 'upgrade_3.0');
			
			// Upgrade old tables
			TBGScopesTable::getTable()->upgrade(TBGScopesTable3dot0::getTable());
			TBGIssueFieldsTable::getTable()->upgrade(TBGIssueFieldsTable3dot0::getTable());

			// Upgrade all modules
			foreach (TBGContext::getModules() as $module)
			{
				if (method_exists($module, 'upgradeFrom3dot0'))
				{
					$module->upgradeFrom3dot0();
				}
			}
			
			// Start a transaction to preserve the upgrade path
			$transaction = \b2db\Core::startTransaction();
			
			// Add votes to feature requests for default issue type scheme
			$its = new TBGIssuetypeScheme(1);
			foreach (TBGIssuetype::getAll() as $fr)
			{
				if ($fr instanceof TBGIssuetype)
				{
					if (in_array($fr->getKey(), array('featurerequest', 'bugreport', 'enhancement')))
					{
						$its->setFieldAvailableForIssuetype($fr, 'votes');
					}
				}
			}
			
			$ut = TBGUsersTable::getTable();
			$crit = $ut->getCriteria();
			$crit->addUpdate(TBGUsersTable::PRIVATE_EMAIL, true);
			$ut->doUpdate($crit);
			
			// Add default gravatar setting
			TBGSettings::saveSetting(TBGSettings::SETTING_ENABLE_GRAVATARS, 1);
			
			$trans_crit = TBGWorkflowTransitionsTable::getTable()->getCriteria();
			$trans_crit->addWhere(TBGWorkflowTransitionsTable::NAME, 'Request more information');
			$trans_crit->addWhere(TBGWorkflowTransitionsTable::WORKFLOW_ID, 1);
			$trans_row = TBGWorkflowTransitionsTable::getTable()->doSelectOne($trans_crit);
			if ($trans_row)
			{
				$transition = new TBGWorkflowTransition($trans_row->get(TBGWorkflowTransitionsTable::ID), $trans_row);
				$transition->setTemplate('main/updateissueproperties');
				$transition->save();
			}

			// End transaction and finalize upgrade
			$transaction->commitAndEnd();
			$this->upgrade_complete = true;
		}

		protected function _upgradeFrom3dot1()
		{
			// Add classpath for existing old tables used for upgrade
			TBGContext::addAutoloaderClassPath(THEBUGGENIE_MODULES_PATH . 'installation' . DS . 'classes' . DS . 'upgrade_3.1');
			TBGContext::addAutoloaderClassPath(THEBUGGENIE_MODULES_PATH . 'mailing' . DS . 'classes' . DS . 'B2DB');
			TBGContext::addAutoloaderClassPath(THEBUGGENIE_MODULES_PATH . 'mailing' . DS . 'classes');
			TBGContext::addAutoloaderClassPath(THEBUGGENIE_MODULES_PATH . 'publish' . DS . 'classes' . DS . 'B2DB');
			TBGContext::addAutoloaderClassPath(THEBUGGENIE_MODULES_PATH . 'publish' . DS . 'classes');

			// Upgrade existing tables
			TBGProjectsTable::getTable()->upgrade(TBGProjectsTable3dot1::getTable());
			TBGUsersTable::getTable()->upgrade(TBGUsersTable3dot1::getTable());
			TBGIssuesTable::getTable()->upgrade(TBGIssuesTable3dot1::getTable());
			TBGIssueTypesTable::getTable()->upgrade(TBGIssueTypesTable3dot1::getTable());
			TBGListTypesTable::getTable()->upgrade(TBGListTypesTable3dot1::getTable());
			TBGEditionsTable::getTable()->upgrade(TBGEditionsTable3dot1::getTable());
			TBGBuildsTable::getTable()->upgrade(TBGBuildsTable3dot1::getTable());
			TBGCommentsTable::getTable()->upgrade(TBGCommentsTable3dot1::getTable());
			TBGComponentsTable::getTable()->upgrade(TBGComponentsTable3dot1::getTable());
			TBGCustomFieldsTable::getTable()->upgrade(TBGCustomFieldsTable3dot1::getTable());
			TBGCustomFieldOptionsTable::getTable()->upgrade(TBGCustomFieldOptionsTable3dot1::getTable());
			
			// Create new tables
			TBGDashboardViewsTable::getTable()->create();
			TBGOpenIdAccountsTable::getTable()->create();
			TBGProjectAssignedUsersTable::getTable()->create();
			TBGProjectAssignedTeamsTable::getTable()->create();
			TBGEditionAssignedUsersTable::getTable()->create();
			TBGEditionAssignedTeamsTable::getTable()->create();
			TBGComponentAssignedUsersTable::getTable()->create();
			TBGComponentAssignedTeamsTable::getTable()->create();
			TBGRolePermissionsTable::getTable()->create();

			// Create new module tables
			TBGIncomingEmailAccountTable::getTable()->create();
			
			// Add new indexes
			TBGArticlesTable::getTable()->createIndexes();
			TBGCommentsTable::getTable()->createIndexes();
			TBGIssueAffectsBuildTable::getTable()->createIndexes();
			TBGIssueAffectsComponentTable::getTable()->createIndexes();
			TBGIssueAffectsEditionTable::getTable()->createIndexes();
			TBGIssueFieldsTable::getTable()->createIndexes();
			TBGIssueFilesTable::getTable()->createIndexes();
			TBGIssuesTable::getTable()->createIndexes();
			TBGIssuetypeSchemesTable::getTable()->createIndexes();
			TBGPermissionsTable::getTable()->createIndexes();
			TBGProjectsTable::getTable()->createIndexes();
			TBGSettingsTable::getTable()->createIndexes();
			TBGTeamMembersTable::getTable()->createIndexes();
			TBGUserIssuesTable::getTable()->createIndexes();
			TBGUsersTable::getTable()->createIndexes();

			foreach (TBGScope::getAll() as $scope)
			{
				TBGRole::loadFixtures($scope);
				foreach (TBGDatatype::getTypes() as $type => $class)
				{
					TBGContext::setPermission('set_datatype_'.$type, 0, 'core', 0, 0, 0, true, $scope->getID());
				}
			}

			TBGWorkflowTransitionsTable::getTable()->upgradeFrom3dot1();
			TBGSettings::saveSetting(TBGSettings::SETTING_ICONSET, TBGSettings::get(TBGSettings::SETTING_THEME_NAME));
			TBGContext::setPermission('readarticle', 0, 'core', 0, 0, 0, true);
			
			foreach (TBGProject::getAll() as $project)
			{
				TBGDashboardViewsTable::getTable()->setDefaultViews($project->getID(), TBGDashboardViewsTable::TYPE_PROJECT);
				if (!$project->getKey())
				{
					$project->setName($project->getName());
				}
			}
			
			if (TBGContext::getRequest()->getParameter('fix_my_timestamps' == '1'))
			{
				$this->_fixTimestamps();
			}
			
			$this->upgrade_complete = true;
		}

		private function _fixTimestamps()
		{
			// Unlimited execution time
			set_time_limit(0);
			
			foreach (TBGScope::getAll() as $scope)
			{
				TBGContext::setScope($scope);
				
				// The first job is to work out the offsets that need applying
				$offsets = array('system', 'users');
				$offsets['users'] = array();
				
				$offsets['system'] == (int) TBGSettings::getGMToffset() * 3600;
				
				foreach (TBGUser::getAll() as $user)
				{
					// Don't record an offset if the user's timezone is set to 0, use the system one instead
					if ($user->getTimezone() != '0' && $user->getTimezone() != 'sys')
					{
						$offsets['users']['uid_'.$user->getID()] = (int) $user->getTimezone() * 3600;
					}
				}
				
				// Now go through every thing which requires updating
				
				if (TBGContext::isModuleLoaded('publish'))
				{
					// ARTICLE HISTORY
					$this->_fixUserDependentTimezone($offsets, TBGArticleHistoryTable::getTable(), TBGArticleHistoryTable::AUTHOR, TBGArticleHistoryTable::DATE);
					
					// ARTICLES
					$this->_fixUserDependentTimezone($offsets, TBGArticlesTable::getTable(), TBGArticlesTable::AUTHOR, TBGArticlesTable::DATE);
				}

				// BUILDS
				$this->_fixNonUserDependentTimezone($offsets, TBGBuildsTable::getTable(), TBGBuildsTable::RELEASE_DATE, TBGBuildsTable::RELEASED);

				
				// COMMENTS		
				$this->_fixUserDependentTimezone($offsets, TBGCommentsTable::getTable(), array('a' => TBGCommentsTable::POSTED_BY, 'b' => TBGCommentsTable::UPDATED_BY), array('a' => TBGCommentsTable::POSTED, 'b' => TBGCommentsTable::UPDATED));
				
				// EDITIONS
				$this->_fixNonUserDependentTimezone($offsets, TBGEditionsTable::getTable(), TBGEditionsTable::RELEASE_DATE, TBGEditionsTable::RELEASED);

								
				// FILES
				$this->_fixUserDependentTimezone($offsets, TBGFilesTable::getTable(), TBGFilesTable::UID, TBGFilesTable::UPLOADED_AT);
				
				// ISSUES
				// This is a bit more complex so do this manually - we have to poke around with the issue log
				$table = TBGIssuesTable::getTable();
				$crit = $table->getCriteria();
				$crit->addWhere(TBGIssuesTable::SCOPE, $scope->getID());
				
				$res = $table->doSelect($crit);
				
				while ($row = $res->getNextRow())
				{
					$issue_object = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID), $row);
					$issue_log = $issue_object->getLogEntries();
					
					array_reverse($issue_log);
					
					$updated_by = null;
					$assigned_by = null;
	
					foreach($issue_log as $entry)
					{
						if ($updated_by == null)
						{
							if ($entry['change_type'] == TBGLogTable::LOG_ISSUE_ASSIGNED)
							{
								$assigned_by = $entry['user_id'];
							}
						}
					}
					
					reset($issue_log);
					array_reverse($issue_log);
					
					end($issue_log); // Set array to the end
					
					$assigned_by = $issue_log['user_id'];
					
					if (array_key_exists('uid_'.$row->get(TBGIssuesTable::POSTED_BY), $offsets['users']))
					{
						$offset = $offsets['users']['uid_'.$row->get(TBGIssuesTable::POSTED_BY)];
					}
					else
					{
						$offset = $offsets['system'];
					}
					
					if (array_key_exists('uid_'.$updated_by, $offsets['users']))
					{
						$offset2 = $offsets['users']['uid_'.$updated_by];
					}
					else
					{
						$offset2 = $offsets['system'];
					}
					
					if ($assigned_by !== null && array_key_exists('uid_'.$assigned_by, $offsets['users']))
					{
						$offset3 = $offsets['users']['uid_'.$assigned_by];
					}
					else
					{
						$offset3 = $offsets['system'];
					}

					unset($issue_log);
					unset($issue_object);
					unset($updated_by);
					unset($assigned_by);
	
					$crit2 = $table->getCriteria();
					$crit2->addUpdate(TBGIssuesTable::POSTED, (int) $row->get(TBGIssuesTable::POSTED) + $offset);

					if (isset($offset2))
					{
						$crit2->addUpdate(TBGIssuesTable::LAST_UPDATED, (int) $row->get(TBGIssuesTable::LAST_UPDATED) + $offset2);
						unset($offset2);
					}
					
					if (isset($offset3))
					{
						$crit2->addUpdate(TBGIssuesTable::BEING_WORKED_ON_BY_USER_SINCE, (int) $row->get(TBGIssuesTable::BEING_WORKED_ON_BY_USER_SINCE) + $offset3);
						unset($offset3);
					}
					
					$crit2->addWhere(TBGIssuesTable::ID, $row->get(TBGIssuesTable::ID));
					$table->doUpdate($crit2);
				}
				
				// LOG
				$this->_fixUserDependentTimezone($offsets, TBGLogTable::getTable(), TBGLogTable::UID, TBGLogTable::TIME);
				
				// MILESTONES
				// The conditions are a bit different here so do it manually
				$table = TBGMilestonesTable::getTable();
				$crit = $table->getCriteria();
				$crit->addWhere(TBGMilestonesTable::SCOPE, $scope->getID());
				
				$res = $table->doSelect($crit);
				
				while ($row = $res->getNextRow())
				{
					$offset = $offsets['system'];
	
					$crit2 = $table->getCriteria();
					
					$added = 0;
					
					if ($row->get(TBGMilestonesTable::REACHED) > 0)
					{
						$crit2->addUpdate(TBGMilestonesTable::REACHED, (int) $row->get(TBGMilestonesTable::REACHED) + $offset);
						$added = 1;
					}

					if ($row->get(TBGMilestonesTable::SCHEDULED) > 0)
					{
						$crit2->addUpdate(TBGMilestonesTable::SCHEDULED, (int) $row->get(TBGMilestonesTable::SCHEDULED) + $offset);
						$added = 1;
					}
					
					if ($row->get(TBGMilestonesTable::STARTING) > 0)
					{
						$crit2->addUpdate(TBGMilestonesTable::STARTING, (int) $row->get(TBGMilestonesTable::STARTING) + $offset);
						$added = 1;
					}
					
					// Only do something if at least one call to addUpdate is done
					if ($added == 1)
					{
						$crit2->addWhere(TBGMilestonesTable::ID, $row->get(TBGMilestonesTable::ID));
						$table->doUpdate($crit2);
					}
				}
				
				// PROJECTS
				$this->_fixNonUserDependentTimezone($offsets, TBGProjectsTable::getTable(), TBGProjectsTable::RELEASE_DATE, TBGProjectsTable::RELEASED);
				
				// VCS INTEGRATION
				if (TBGContext::isModuleLoaded('vcs_integration'))
				{
					$this->fixUserDependentTimezone($offsets, TBGVCSIntegrationTable::getTable(), TBGVCSIntegrationTable::AUTHOR, TBGVCSIntegrationTable::DATE);
				}
			}
		}

		private function _fixUserDependentTimezone($offsets, \b2db\Table $table, $author_field, $correctionfield, $testfield = null)
		{
			$crit = $table->getCriteria();
			$crit->addWhere($table::SCOPE, $scope->getID());
			
			$res = $table->doSelect($crit);
			
			while ($row = $res->getNextRow())
			{				
				if ($testfield !== null)
				{
					if ($row->get($testfield) != 1)
					{
						continue;
					}
				}
				
				if (!is_array($correctionfield))
				{
					$correctionfield = array('a' => $correctionfield);
				}
				
				if (!is_array($author_field))
				{
					$author_field = array('a' => $author_field);
				}
				
				$crit2 = $table->getCriteria();
			
				$added = 0;
			
				foreach ($author_field as $key => $field)
				{
					if (array_key_exists('uid_'.$row->get($field, $offsets['users'])))
					{
						$offset = $offsets['users']['uid_'.$row->get($field)];
					}
					else
					{
						$offset = $offsets['system'];
					}

					// If the timestamp is 0, don't correct as it is unset
					if ($row->get($correctionfield[$key]) == 0)
					{
						continue;
					}

					$crit2->addUpdate($correctionfield[$key], (int) $row->get($correctionfield[$key]) + $offset);
					$added = 1; // Mark that we have actually added something
				}
				
				// Don't update if no addUpdate calls made
				if ($added == 1)
				{
					$crit2->addWhere($table::ID, $row->get($table::ID));
					$table->doUpdate($crit2);
				}
			}
		}

		private function _fixNonUserDependentTimezone($offsets, \b2db\Table $table, $correctionfield, $testfield = null)
		{
			$crit = $table->getCriteria();
			$crit->addWhere($table::SCOPE, $scope->getID());
			
			$res = $table->doSelect($crit);
			
			while ($row = $res->getNextRow())
			{
				if ($testfield !== null)
				{
					if ($row->get($testfield) != 1)
					{
						continue;
					}
				}
				
				$offset = $offsets['system'];

				$crit2 = $table->getCriteria();
				
				if (!is_array($correctionfield))
				{
					$correctionfield = array($correctionfield);
				}
				
				$added = 0;
				
				foreach ($correctionfield as $field)
				{
					// If the timestamp is 0, don't correct as it is unset
					if ($row->get($field) == 0)
					{
						continue;
					}

					$crit2->addUpdate($field, (int) $row->get($field) + $offset);
					$added = 1; // Mark that we have actually added something
				}
				
				// Don't update if no addUpdate calls made
				if ($added == 1)
				{
					$crit2->addWhere($table::ID, $row->get($table::ID));
					$table->doUpdate($crit2);
				}
			}
		}


		public function runUpgrade(TBGRequest $request)
		{
			$version_info = explode(',', file_get_contents(THEBUGGENIE_PATH . 'installed'));
			$this->current_version = $version_info[0];
			$this->upgrade_available = ($this->current_version != '3.2');
			
			if ($this->upgrade_available)
			{
				$scope = new TBGScope();
				$scope->setID(1);
				$scope->setEnabled();
				TBGContext::setScope($scope);
			}
			$this->upgrade_complete = false;

			if ($this->upgrade_available && $request->isPost())
			{
				$this->upgrade_complete = false;
				switch ($this->current_version)
				{
					case '3.0':
						$this->_upgradeFrom3dot0();
					case '3.1':
						$this->_upgradeFrom3dot1();
				}
				
				if ($this->upgrade_complete)
				{
					$existing_installed_content = file_get_contents(THEBUGGENIE_PATH . 'installed');
					file_put_contents(THEBUGGENIE_PATH . 'installed', TBGSettings::getVersion(false, false) . ', upgraded ' . date('d.m.Y H:i') . "\n" . $existing_installed_content);
					unlink(THEBUGGENIE_PATH . 'upgrade');
					$this->current_version = '3.2';
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
				$this->forward(TBGContext::getRouting()->generate('home'));
			}
		}

	}
