<?php

	/**
	 * actions for the ldap_authentication module
	 */
	class auth_ldapActions extends TBGAction
	{

		/**
		 * Test the LDAP connection
		 *
		 * @param TBGRequest $request
		 */
		public function runTestConnection(TBGRequest $request)
		{
			TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Failed to connect to server'));
			TBGContext::setMessage('module_error_details', 'This functionality is currently unimplemented');
			$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
		}
		
		/**
		 * Prune users from users table who aren't in LDAP
		 *
		 * @param TBGRequest $request
		 */
		public function runPruneUsers(TBGRequest $request)
		{
			$users = TBGUser::getAll();
			$deletecount = 0;
			
			foreach ($users as $user)
			{
				try
				{
					$exists = true;
					$username = $user->getUsername();
					/*
					 * Look up for a user with username in variable in LDAP
					 * 
					 * If it does NOT exist, set $exists to false
					 * 
					 * If we have an error, log it and throw an exception.
					 * 
					 * To log do:
					 * TBGLogging::log('error goes here', 'ldap', TBGLogging::LEVEL_FATAL);
					 */
					if (!$exists)
					{
						$user->delete();
						$user->save();
						$deletecount++;
					}
				}
				catch (Exception $e)
				{
					TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Pruning failed'));
					TBGContext::setMessage('module_error_details', $e->getMessage());
					$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
				}
			}
			
			TBGContext::setMessage('module_message', TBGContext::getI18n()->__('Pruning successful! %del% users deleted', array('%del%' => $deletecount)));
			$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
		}
		
		/**
		 * Import all valid users
		 * 
		 * @param TBGRequest $request
		 */
		public function runImportUsers(TBGRequest $request)
		{
			$host = TBGContext::getModule('auth_ldap')->getSetting('hostname');
			$port = TBGContext::getModule('auth_ldap')->getSetting('port');
			$lduser = TBGContext::getModule('auth_ldap')->getSetting('username');
			$ldpass = TBGContext::getModule('auth_ldap')->getSetting('password');
			$validgroups = TBGContext::getModule('auth_ldap')->getSetting('groups');
			$dn = TBGContext::getModule('auth_ldap')->getSetting('dn');
			
			$users = array();
			$importcount = 0;
			$updatecount = 0;
			
			try
			{
				/*
				 * Build an array of all valid LDAP users here.
				 * 
				 * Each array element should be an associative array:
				 * 
				 * array (
				 *			'username' => username of user,
				 *			'password' => password of user from ldap, will be overwritten later,
				 *			'email' => email from ldap or null if unavailable,
				 *			'realname' => realname of user, or the username if unavailable
				 * )
				 * 
				 * So we have an array: array(array(), array()...) etc., with an element (being an array for each user
				 * If we have an error, log it and throw an exception.
				 * 
				 * To log do:
				 * TBGLogging::log('error goes here', 'ldap', TBGLogging::LEVEL_FATAL);
				 */
			}
			catch (Exception $e)
			{
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Import failed'));
				TBGContext::setMessage('module_error_details', $e->getMessage());
				$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
			}
				
			foreach ($users as $ldapuser)
			{
				$username = $ldapuser['username'];
				$password = $ldapuser['password']; // People will be unable to log in with this as is isnt TBG format, but it prevents blank passwords. Password will be set on login.
				$email = $ldapuser['email'];
				$realname = $ldapuser['realname'];
				
				try
				{
					$user = TBGUser::getByUsername($username);
					if ($user instanceof TBGUser)
					{
						$user->setRealname($realname);
						$user->setPassword($password); // update password
						$user->setEmail($email); // update emaila ddress
						$user->save();
						$updatecount++;
					}
					else
					{
						// create user
						$user = new TBGUser();
						$user->setUsername($username);
						$user->setRealname($realname);
						$user->setBuddyname($username);
						$user->setEmail($email);
						$user->setEnabled();
						$user->setActivated();
						$user->setPassword($password);
						$user->setJoined();
						$user->save();
						$importcount++;
					}
					
					return TBGUsersTable::getByUsername($username);
				}
				catch (Exception $e)
				{
					TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Import failed'));
					TBGContext::setMessage('module_error_details', $e->getMessage());
					$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
				}
			}
			
			TBGContext::setMessage('module_message', TBGContext::getI18n()->__('Import successful! %imp% users imported, %upd% users updated from LDAP', array('%imp%' => $importcount, '%upd%' => $updatecount)));
			$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
		}

	}

