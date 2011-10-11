<?php

	/**
	 * CLI command class, main -> install
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, main -> install
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliMainInstall extends TBGCliCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'install';
			$this->_description = "Run the installation routine";
			$this->addOptionalArgument('accept_license', 'Set to "yes" to auto-accept license');
			$this->addOptionalArgument('url_subdir', 'Specify URL subdirectory');
			$this->addOptionalArgument('use_existing_db_info', 'Set to "yes" to use existing db information if available');
			$this->addOptionalArgument('enable_all_modules', 'Set to "yes" to install all modules');
			$this->addOptionalArgument('setup_htaccess', 'Set to "yes" to autoconfigure .htaccess file');
		}

		public function do_execute()
		{
			if (file_exists(THEBUGGENIE_PATH . 'installed'))
			{
				$this->cliEcho("The Bug Genie seems to already be installed.\n", 'red', 'bold');
				$this->cliEcho('Please remove the file ');
				$this->cliEcho(THEBUGGENIE_PATH.'installed', 'white', 'bold');
				$this->cliEcho(' and try again.');
				$this->cliEcho("\n");
				return;
			}
			$this->cliEcho("\nWelcome to the \"The Bug Genie\" installation wizard!\n", 'white', 'bold');
			$this->cliEcho("This wizard will take you through the installation of The Bug Genie.\nRemember that you can also install The Bug Genie from your web-browser.\n");
			$this->cliEcho("Simply point your web-browser to the The Bug Genie subdirectory on your web server,\nand the installation will start.\n\n");
			$this->cliEcho("Press ENTER to continue with the installation: ");
			$this->pressEnterToContinue();
			
			$this->cliEcho("\n");
			$this->cliEcho("How to support future development\n", 'green', 'bold');
			$this->cliEcho("Even though this software has been provided to you free of charge,\ndeveloping it would not have been possible without support from our users.\n");
			$this->cliEcho("By making a donation, or buying a support contract you can help us continue development.\n\n");
			$this->cliEcho("If this software is valuable to you - please consider supporting it.\n\n");
			$this->cliEcho("More information about supporting The Bug Genie's development can be found here:\n");
			$this->cliEcho("http://www.thebuggenie.com/giving_back.php\n\n", 'blue', 'underline');
			$this->cliEcho("Press ENTER to continue: ");

			$this->pressEnterToContinue();
			$this->cliEcho("\n");

			try
			{
				$this->cliEcho("License information\n", 'green', 'bold');
				$this->cliEcho("This software is Open Source Initiative approved Open Source Software.\nOpen Source Initiative Approved is a trademark of the Open Source Initiative.\n\n");
				$this->cliEcho("True to the the Open Source Definition, The Bug Genie is released\nunder the MPL 1.1 only. You can read the full license here:\n");
				$this->cliEcho("http://www.opensource.org/licenses/mozilla1.1.php\n\n", 'blue', 'underline');

				if ($this->getProvidedArgument('accept_license') != 'yes')
				{
					$this->cliEcho("Before you can continue the installation, you need to confirm that you \nagree to be bound by the terms in this license.\n\n");
					$this->cliEcho("Do you agree to be bound by the terms in the MPL 1.1 license?\n(type \"yes\" to agree, anything else aborts the installation): ");
					if (!$this->askToAccept()) throw new Exception($this->cliEcho('You need to accept the license to continue', 'red', 'bold'));
				}
				else
				{
					$this->cliEcho('You have accepted the license', 'yellow', 'bold');
					$this->cliEcho("\n\n");
				}

				$not_well = array();
				if (!is_writable('core/B2DB/'))
				{
					$not_well[] = 'b2db_perm';
				}
				if (!is_writable(THEBUGGENIE_PATH))
				{
					$not_well[] = 'root';
				}

				if (count($not_well) > 0)
				{
					$this->cliEcho("\n");
					foreach ($not_well as $afail)
					{
						switch ($afail)
						{
							case 'b2db_perm':
								$this->cliEcho("Could not write to the B2DB directory\n", 'red', 'bold');
								$this->cliEcho('The folder ');
								$this->cliEcho('include/B2DB', 'white', 'bold');
								$this->cliEcho(' folder needs to be writable');
								break;
							case 'root':
								$this->cliEcho("Could not write to the main directory\n", 'red', 'bold');
								$this->cliEcho('The top level folder must be writable during installation');
								break;
						}
					}

					throw new Exception("\n\nYou need to correct the above errors before the installation can continue.");
				}
				else
				{
					$this->cliEcho("Step 1 - database information\n");
					if (file_exists('core/b2db_bootstrap.inc.php'))
					{
						$this->cliEcho("You seem to already have completed this step successfully.\n");
						if ($this->getProvidedArgument('use_existing_db_info') == 'yes')
						{
							$this->cliEcho("\n");
							$this->cliEcho("Using existing database information\n", 'yellow', 'bold');
							$use_existing_db_info = true;
						}
						else
						{
							$this->cliEcho("Do you want to use the stored settings?\n", 'white', 'bold');
							$this->cliEcho("\nType \"no\" to enter new settings, press ENTER to use existing: ", 'white', 'bold');
							$use_existing_db_info = $this->askToDecline();
						}
						$this->cliEcho("\n");
					}
					else
					{
						$use_existing_db_info = false;
					}
					if (!$use_existing_db_info)
					{
						$this->cliEcho("The Bug Genie uses a database to store information. To be able to connect\nto your database, The Bug Genie needs some information, such as\ndatabase type, username, password, etc.\n\n");
						$this->cliEcho("Please select what kind of database you are installing The Bug Genie on:\n");
						\b2db\Core::setHTMLException(false);
						$db_types = array();
						foreach (\b2db\Core::getDBtypes() as $db_type => $db_desc)
						{
							$db_types[] = $db_type;
							$this->cliEcho(count($db_types) . ': ' . $db_desc . "\n", 'white', 'bold');
						}
						do
						{
							$this->cliEcho('Enter the corresponding number for the database (1-' . count($db_types) . '): ');
							$db_selection = $this->getInput();
							if (!isset($db_types[((int) $db_selection - 1)])) throw new Exception($db_selection . ' is not a valid database type selection');
							$db_type = $db_types[((int) $db_selection - 1)];
							$this->cliEcho("Selected database type: ");
							$this->cliEcho($db_type . "\n\n");
							$this->cliEcho("Please enter the database hostname: \n");
							$this->cliEcho('Database hostname [localhost]: ', 'white', 'bold');
							$db_hostname = $this->getInput();
							$db_hostname = ($db_hostname == '') ? 'localhost' : $db_hostname;
							$this->cliEcho("\nPlease enter the username The Bug Genie will use to connect to the database: \n");
							$this->cliEcho('Database username: ', 'white', 'bold');
							$db_username = $this->getInput();
							$this->cliEcho("Database password (press ENTER if blank): ", 'white', 'bold');
							$db_password = $this->getInput();
							$this->cliEcho("\nPlease enter the database The Bug Genie will use.\nIf it does not exist, The Bug Genie will create it for you.\n(the default database name is ");
							$this->cliEcho("thebuggenie_db", 'white', 'bold');
							$this->cliEcho(" - press ENTER to use that):\n");
							$this->cliEcho('Database name: ', 'white', 'bold');
							$db_name = $this->getInput('thebuggenie_db');
							$this->cliEcho("\n");
							$this->cliEcho("The following settings will be used:\n");
							$this->cliEcho("Database type: \t\t", 'white', 'bold');
							$this->cliEcho($db_type . "\n");
							$this->cliEcho("Database hostname: \t", 'white', 'bold');
							$this->cliEcho($db_hostname . "\n");
							$this->cliEcho("Database username: \t", 'white', 'bold');
							$this->cliEcho($db_username . "\n");
							$this->cliEcho("Database password: \t", 'white', 'bold');
							$this->cliEcho($db_password . "\n");
							$this->cliEcho("Database name: \t\t", 'white', 'bold');
							$this->cliEcho($db_name . "\n");

							$this->cliEcho("\nIf these settings are ok, press ENTER, or anything else to retry: ");

							$e_ok = $this->askToDecline();
						}
						while (!$e_ok);
						try
						{
							\b2db\Core::setHost($db_hostname);
							\b2db\Core::setUname($db_username);
							\b2db\Core::setPasswd($db_password);
							\b2db\Core::setDBtype($db_type);
							\b2db\Core::initialize();
							$engine_path = \b2db\Core::getEngineClassPath();
							if ($engine_path !== null)
								TBGContext::addAutoloaderClassPath($engine_path);
							else
								throw new Exception("Cannot initialize the B2DB engine");

							\b2db\Core::doConnect();
							\b2db\Core::createDatabase($db_name);
							\b2db\Core::setDBname($db_name);
							\b2db\Core::doConnect();
						}
						catch (Exception $e)
						{
							throw new Exception("Could not connect to the database:\n" . $e->getMessage());
						}
						\b2db\Core::setDBname($db_name);
						\b2db\Core::doSelectDB();
						$this->cliEcho("\nSuccessfully connected to the database.\n", 'green');
						$this->cliEcho("Press ENTER to continue ... ");
						$this->pressEnterToContinue();
						$this->cliEcho("\n");
						$this->cliEcho("Saving database connection information ... ", 'white', 'bold');
						$this->cliEcho("\n");
						\b2db\Core::saveConnectionParameters(THEBUGGENIE_CORE_PATH . 'b2db_bootstrap.inc.php');
						$this->cliEcho("Successfully saved database connection information.\n", 'green');
						$this->cliEcho("\n");
					}
					else
					{
						\b2db\Core::initialize(THEBUGGENIE_CORE_PATH . 'b2db_bootstrap.inc.php');
						$this->cliEcho("Successfully connected to the database.\n", 'green');
						if ($this->getProvidedArgument('use_existing_db_info') != 'yes')
						{
							$this->cliEcho("Press ENTER to continue ... ");
							$this->pressEnterToContinue();
						}
					}
					$this->cliEcho("\nThe Bug Genie needs some server settings to function properly...\n\n");

					do
					{
						$this->cliEcho("URL rewriting\n", 'cyan', 'bold');
						$this->cliEcho("The Bug Genie uses a technique called \"url rewriting\" - which allows for pretty\nURLs such as ") . $this->cliEcho('/issue/1', 'white', 'bold') . $this->cliEcho(' instead of ') . $this->cliEcho("viewissue.php?issue_id=1\n", 'white', 'bold');
						$this->cliEcho("Make sure you have read the URL_REWRITE document located in the root\nfolder, or at http://www.thebuggenie.com before you continue\n");

						if (!$this->hasProvidedArgument('url_subdir'))
						{
							$this->cliEcho("Press ENTER to continue ... ");
							$this->pressEnterToContinue();
						}
						$this->cliEcho("\n");
						
						$this->cliEcho("The Bug Genie subdir\n", 'white', 'bold');
						$this->cliEcho("This is the sub-path of the Web server where The Bug Genie will be located.\n");
						if ($this->hasProvidedArgument('url_subdir'))
						{
							$this->cliEcho('The Bug Genie subdir: ', 'white', 'bold');
							$url_subdir = $this->getProvidedArgument('url_subdir');
							$this->cliEcho($url_subdir, 'yellow', 'bold');
							$this->cliEcho("\n");
						}
						else
						{
							$this->cliEcho('Start and end this with a forward slash', 'white', 'bold');
							$this->cliEcho(". (ex: \"/thebuggenie/\")\nIf The Bug Genie is running at the root directory, just type \"/\" (without the quotes)\n\n");
							$this->cliEcho('The Bug Genie subdir: ', 'white', 'bold');
							$url_subdir = $this->getInput();
						}
						$this->cliEcho("\n");

						$this->cliEcho("The Bug Genie will now be accessible at\n");
						$this->cliEcho("http://example.com" . $url_subdir, 'white', 'bold');
						if ($this->hasProvidedArgument('url_subdir'))
						{
							$this->cliEcho("\n");
							$this->cliEcho("Using existing values", 'yellow', 'bold');
							$this->cliEcho("\n");
							$e_ok = true;
						}
						else
						{
							$this->cliEcho("\nPress ENTER if ok, or \"no\" to try again: ");
							$e_ok = $this->askToDecline();
						}
						$this->cliEcho("\n");
					}
					while (!$e_ok);

					if ($this->getProvidedArgument('setup_htaccess') != 'yes')
					{
						$this->cliEcho("Setup can autoconfigure your .htaccess file (located in the thebuggenie/ subfolder), so you don't have to.\n");
						$this->cliEcho('Would you like setup to auto-generate the .htaccess file for you?');
						$this->cliEcho("\nPress ENTER if ok, or \"no\" to not set up the .htaccess file: ");
						$htaccess_ok = $this->askToDecline();
					}
					else
					{
						$this->cliEcho('Autoconfiguring .htaccess', 'yellow', 'bold');
						$this->cliEcho("\n");
						$htaccess_ok = true;
					}
					$this->cliEcho("\n");

					if ($htaccess_ok)
					{
						if (!is_writable(THEBUGGENIE_PATH . 'thebuggenie/') || (file_exists(THEBUGGENIE_PATH . 'thebuggenie/.htaccess') && !is_writable(THEBUGGENIE_PATH . 'thebuggenie/.htaccess')))
						{
							$this->cliEcho("Permission denied when trying to save the [main folder]/thebuggenie/.htaccess\n", 'red', 'bold');
							$this->cliEcho("You will have to set up the .htaccess file yourself. See the README file for more information.\n", 'white', 'bold');
							$this->cliEcho('Please note: ', 'white', 'bold');
							$this->cliEcho("The Bug Genie will not function properly until the .htaccess file is properly set up!\n");
						}
						else
						{
							$content = str_replace('###PUT URL SUBDIRECTORY HERE###', $url_subdir, file_get_contents(THEBUGGENIE_CORE_PATH . 'templates/htaccess.template'));
							file_put_contents(THEBUGGENIE_PATH . 'thebuggenie/.htaccess', $content);
							if (file_get_contents(THEBUGGENIE_PATH . 'thebuggenie/.htaccess') != $content)
							{
								$this->cliEcho("Permission denied when trying to save the [main folder]/thebuggenie/.htaccess\n", 'red', 'bold');
								$this->cliEcho("You will have to set up the .htaccess file yourself. See the README file for more information.\n", 'white', 'bold');
								$this->cliEcho('Please note: ', 'white', 'bold');
								$this->cliEcho("The Bug Genie will not function properly until the .htaccess file is properly set up!\n");
							}
							else
							{
								$this->cliEcho("The .htaccess file was successfully set up...\n", 'green', 'bold');
							}
						}

					}
					else
					{
						$this->cliEcho("Skipping .htaccess auto-setup.");
					}
					
					if ($this->getProvidedArgument('setup_htaccess') != 'yes')
					{
						$this->cliEcho("Press ENTER to continue ... ");
						$this->pressEnterToContinue();
						$this->cliEcho("\n");
					}

					$enable_modules = array();

					if ($this->getProvidedArgument('enable_all_modules') != 'yes')
					{
						$this->cliEcho("You will now get a list of available modules.\nTo enable the module after installation, just press ENTER.\nIf you don't want to enable the module, type \"no\".\nRemember that all these modules can be disabled/uninstalled after installation.\n\n");
					}
					
					$this->cliEcho("Enable incoming and outgoing email? ", 'white', 'bold') . $this->cliEcho('(yes): ');
					$enable_modules['mailing'] = ($this->getProvidedArgument('enable_all_modules') == 'yes') ? true : $this->askToDecline();
					if ($this->getProvidedArgument('enable_all_modules') == 'yes') $this->cliEcho("Yes\n", 'yellow', 'bold');
					$this->cliEcho("Enable communication with version control systems (i.e. svn)? ", 'white', 'bold') . $this->cliEcho('(yes): ');
					$enable_modules['vcs_integration'] = ($this->getProvidedArgument('enable_all_modules') == 'yes') ? true : $this->askToDecline();
					if ($this->getProvidedArgument('enable_all_modules') == 'yes') $this->cliEcho("Yes\n", 'yellow', 'bold');

					$enable_modules['publish'] = true;

					$this->cliEcho("\n");
					$this->cliEcho("Creating tables ...\n", 'white', 'bold');
					$tables_path = THEBUGGENIE_CORE_PATH . 'classes' . DIRECTORY_SEPARATOR . 'B2DB' . DIRECTORY_SEPARATOR;
					TBGContext::addAutoloaderClassPath($tables_path);
					$tables_path_handle = opendir($tables_path);
					$tables_created = array();
					while ($table_class_file = readdir($tables_path_handle))
					{
						if (($tablename = mb_substr($table_class_file, 0, mb_strpos($table_class_file, '.'))) != '')
						{
							\b2db\Core::getTable($tablename)->create();
							$this->cliEcho("Creating table {$tablename}\n", 'white', 'bold');
						}
					}

					$this->cliEcho("\n");
					$this->cliEcho("All tables successfully created...\n\n", 'green', 'bold');
					$this->cliEcho("Setting up initial scope... \n", 'white', 'bold');
					TBGContext::reinitializeI18n('en_US');
					$scope = new TBGScope();
					$scope->setName('The default scope');
					$scope->addHostname('*');
					$scope->setEnabled();
					TBGContext::setScope($scope);
					$scope->save();
					TBGSettings::saveSetting('language', 'en_US');
					$this->cliEcho("Initial scope setup successfully... \n\n", 'green', 'bold');

					$this->cliEcho("Setting up modules... \n", 'white', 'bold');
					try
					{
						foreach ($enable_modules as $module => $install)
						{
							if ((bool) $install && file_exists(THEBUGGENIE_MODULES_PATH . $module . DS . 'module'))
							{
								$this->cliEcho("Installing {$module}... \n");
								TBGModule::installModule($module);
								$this->cliEcho("Module {$module} installed successfully...\n", 'green');
							}
						}

						$this->cliEcho("\n");
						$this->cliEcho("All modules installed successfully...\n", 'green', 'bold');
						$this->cliEcho("\n");

						$this->cliEcho("Finishing installation... \n", 'white', 'bold');
						if (!is_writable(THEBUGGENIE_PATH . 'installed'))
						{
							$this->cliEcho("\n");
							$this->cliEcho("Could not create the 'installed' file.\n", 'red', 'bold');
							$this->cliEcho("Please create the file ");
							$this->cliEcho(THEBUGGENIE_PATH . "installed\n", 'white', 'bold');
							$this->cliEcho("with the following line inside:\n");
							$this->cliEcho('3.0, installed ' . date('d.m.Y H:i'), 'blue', 'bold');
							$this->cliEcho("\n");
							$this->cliEcho("This can be done by running the following command when installation has finished:\n");
							$this->cliEcho('echo "3.0, installed ' . date('d.m.Y H:i').'" > '.THEBUGGENIE_PATH.'installed', 'white', 'bold');
							$this->cliEcho("\n");
							$this->cliEcho("Press ENTER to continue ... ");
							$this->pressEnterToContinue();
							$this->cliEcho("\n");
							$this->cliEcho("\n");
						}
						else
						{
							file_put_contents(THEBUGGENIE_PATH . 'installed', '3.0, installed ' . date('d.m.Y H:i'));
						}
						$this->cliEcho("The installation was completed successfully!\n", 'green', 'bold');
						$this->cliEcho("\nTo use The Bug Genie, access http://example.com" . $url_subdir . "index.php with a web-browser.\n");
						$this->cliEcho("The default username is ") . $this->cliEcho('Administrator') . $this->cliEcho(' and the password is ') . $this->cliEcho('admin');
						$this->cliEcho("\n\nFor support, please visit ") . $this->cliEcho('http://www.thebuggenie.com/', 'blue', 'underline');
						$this->cliEcho("\n");
					}
					catch (Exception $e)
					{
						throw new Exception("Could not install the $module module:\n" . $e->getMessage());
					}

				}
			}
			catch (Exception $e)
			{
				$this->cliEcho("\n\nThe installation was interrupted\n", 'red');
				$this->cliEcho($e->getMessage() . "\n");
				var_dump($e->getTraceAsString());die();
			}
			$this->cliEcho("\n");
		}

	}