<?php

	class installationActions extends TBGAction
	{
		
		/**
		 * Sample docblock used to test docblock retrieval
		 */
		protected $_sampleproperty;
		
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
			
			if (($step = $request->getParameter('step')) && $step >= 1 && $step <= 6)
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
			$this->all_well = false;
			$this->base_folder_perm_ok = true;
			$this->cache_folder_perm_ok = true;
			$this->thebuggenie_folder_perm_ok = true;
			$this->b2db_param_file_ok = true;
			$this->b2db_param_folder_ok = true;
			$this->pdo_ok = true;
			$this->mysql_ok = true;
			$this->pgsql_ok = true;
			$this->gd_ok = true;
			$this->php_ok = true;
			$this->docblock_ok = false;
			$this->php_ver = PHP_VERSION;
			
			if (version_compare($this->php_ver, '5.3.0', 'lt'))
			{
				$this->php_ok = false;
				$this->all_well = false;
			}
			if (file_exists(TBGContext::getIncludePath() . 'core/b2db_bootstrap.inc.php') && !is_writable(TBGContext::getIncludePath() . 'core/b2db_bootstrap.inc.php'))
			{
				$this->b2db_param_file_ok = false;
				$this->all_well = false;
			}
			if (!file_exists(TBGContext::getIncludePath() . 'core/b2db_bootstrap.inc.php') && !is_writable(TBGContext::getIncludePath() . 'core/'))
			{
				$this->b2db_param_folder_ok = false;
				$this->all_well = false;
			}			
			if (!is_writable(TBGContext::getIncludePath()))
			{
				$this->base_folder_perm_ok = false;
				$this->all_well = false;
			}
			if (!is_writable(TBGContext::getIncludePath() . 'core/cache/') || !is_writable(TBGContext::getIncludePath() . 'core/cache/B2DB/'))
			{
				$this->cache_folder_perm_ok = false;
				$this->all_well = false;
			}
			if (!is_writable(TBGContext::getIncludePath() . THEBUGGENIE_PUBLIC_PATH . '/'))
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
			
			$reflection = new ReflectionProperty(get_class($this), '_sampleproperty');
			$docblock = $reflection->getDocComment();
			if ($docblock)
			{
				$this->docblock_ok = true;
				$this->all_well = true;
			}
			
			$this->all_well = $this->all_well & ($this->mysql_ok | $this->pgsql_ok);
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
					BaseB2DB::initialize(TBGContext::getIncludePath() . 'core/b2db_bootstrap.inc.php');
				}
				catch (Exception $e)
				{
				}
				if (class_exists('B2DB'))
				{
					$this->preloaded = true;
					$this->username = B2DB::getUname();
					$this->password = B2DB::getPasswd();
					$this->dsn = B2DB::getDSN();
					$this->hostname = B2DB::getHost();
					$this->port = B2DB::getPort();
					$this->b2db_dbtype = B2DB::getDBtype();
					$this->db_name = B2DB::getDBname();
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
			$this->selected_connection_detail = $request->getParameter('connection_type');
			try
			{
				if ($this->username = $request->getParameter('db_username'))
				{
					BaseB2DB::setUname($this->username);
					BaseB2DB::setTablePrefix($request->getParameter('db_prefix'));
					if ($this->password = $request->getParameter('db_password'))
					{
						BaseB2DB::setPasswd($this->password);
					}

					if ($this->selected_connection_detail == 'dsn')
					{
						if (($this->dsn = $request->getParameter('db_dsn')) != '')
						{
							BaseB2DB::setDSN($this->dsn);
						}
						else
						{
							throw new Exception('You must provide a valid DSN');
						}
					}
					else
					{
						if ($this->db_type = $request->getParameter('db_type'))
						{
							BaseB2DB::setDBtype($this->db_type);
							if ($this->db_hostname = $request->getParameter('db_hostname'))
							{
								BaseB2DB::setHost($this->db_hostname);
							}
							else
							{
								throw new Exception('You must provide a database hostname');
							}

							if ($this->db_port = $request->getParameter('db_port'))
							{
								BaseB2DB::setPort($this->db_port);
							}

							if ($this->db_databasename = $request->getParameter('db_name'))
							{
								BaseB2DB::setDBname($this->db_databasename);
							}
							else
							{
								throw new Exception('You must provide a database to use');
							}
						}
						else
						{
							throw new Exception('You must provide a database type');
						}
					}
					
					BaseB2DB::initialize(TBGContext::getIncludePath() . 'core/b2db_bootstrap.inc.php');
					$engine_path = BaseB2DB::getEngineClassPath();
					if ($engine_path !== null)
					{
						TBGContext::addClasspath($engine_path);
					}
					else
					{
						throw new Exception("Cannot initialize the B2DB engine");
					}
					B2DB::doConnect();
					
					if (B2DB::getDBname() == '')
					{
						throw new Exception('You must provide a database to use');
					}
					B2DB::saveConnectionParameters(TBGContext::getIncludePath() . 'core/b2db_bootstrap.inc.php');
					
				}
				else
				{
					throw new Exception('You must provide a database username');
				}
				
				// Add table classes to classpath 
				$tables_path = THEBUGGENIE_PATH . 'core/classes/B2DB/';
				TBGContext::addClasspath($tables_path);
				$tables_path_handle = opendir($tables_path);
				$tables_created = array();
				while ($table_class_file = readdir($tables_path_handle))
				{
					if (($tablename = substr($table_class_file, 0, strpos($table_class_file, '.'))) != '') 
					{
						B2DB::getTable($tablename)->create();
						$tables_created[] = $tablename;
					}
				}
				sort($tables_created);
				$this->tables_created = $tables_created;
				
				//TBGScope::setupInitialScope();
				
			}
			catch (Exception $e)
			{
				//throw $e;
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
				$scope->setHostname($request->getParameter('url_host'));
				$scope->setName('The default scope');
				$scope->setEnabled(true);
				TBGContext::setScope($scope);
				$scope->save();
				
				TBGLogging::log('Setting up default users and groups');
				TBGSettings::saveSetting('language', 'en_US', 'core', 1);
				TBGSettings::saveSetting('url_subdir', $request->getParameter('url_subdir'), 'core', 1);

				$this->htaccess_error = false;
				$this->htaccess_ok = (bool) $request->getParameter('apache_autosetup');

				if ($request->getParameter('apache_autosetup'))
				{
					if (!is_writable(TBGContext::getIncludePath() . THEBUGGENIE_PUBLIC_PATH . '/') || (file_exists(TBGContext::getIncludePath() . THEBUGGENIE_PUBLIC_PATH . '/.htaccess') && !is_writable(TBGContext::getIncludePath() . THEBUGGENIE_PUBLIC_PATH . '/.htaccess')))
					{
						$this->htaccess_error = 'Permission denied when trying to save the [main folder]/thebuggenie/.htaccess';
					}
					else
					{
						$content = str_replace('###PUT URL SUBDIRECTORY HERE###', $request->getParameter('url_subdir'), file_get_contents(TBGContext::getIncludePath() . THEBUGGENIE_PUBLIC_PATH . '/htaccess.template'));
						file_put_contents(TBGContext::getIncludePath() . THEBUGGENIE_PUBLIC_PATH . '/.htaccess', $content);
						if (file_get_contents(TBGContext::getIncludePath() . THEBUGGENIE_PUBLIC_PATH . '/.htaccess') != $content)
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
						if ((bool) $install && file_exists(TBGContext::getIncludePath() . "modules/{$module}/module"))
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
			if (file_put_contents(TBGContext::getIncludePath() . 'installed', '3.0, installed ' . date('d.m.Y H:i')) === false)
			{
				$this->error = "Couldn't write to the main directory";
			}
		}
		
	}
