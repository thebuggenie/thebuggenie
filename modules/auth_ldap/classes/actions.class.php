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
			try
			{
				$connection = TBGContext::getModule('auth_ldap')->connect();
				
				TBGLDAPAuthentication::getModule()->bind(TBGLDAPAuthentication::getModule()->getSetting('control_user'), TBGLDAPAuthentication::getModule()->getSetting('control_pass'), $connection);
				
				ldap_unbind($connection);
				
				TBGContext::setMessage('module_message', TBGContext::getI18n()->__('Connection test successful'));
				$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
			}
			catch (Exception $e)
			{
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Failed to connect to server'));
				TBGContext::setMessage('module_error_details', $e->getMessage());
				$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
			}
		}
		
		/**
		 * Prune users from users table who aren't in LDAP
		 *
		 * @param TBGRequest $request
		 */
		public function runPruneUsers(TBGRequest $request)
		{
			$validgroups = TBGContext::getModule('auth_ldap')->getSetting('groups');
			$users_dn = TBGContext::getModule('auth_ldap')->getSetting('u_dn');
			$username_attr = TBGContext::getModule('auth_ldap')->getSetting('u_attr');
			$fullname_attr = TBGContext::getModule('auth_ldap')->getSetting('f_attr');
			$email_attr = TBGContext::getModule('auth_ldap')->getSetting('e_attr');
			$groups_dn = TBGContext::getModule('auth_ldap')->getSetting('g_dn');
			$groups_members_attr = TBGContext::getModule('auth_ldap')->getSetting('g_attr');
			
			$users = TBGUser::getAll();
			$deletecount = 0;
			
			try
			{
				$connection = TBGContext::getModule('auth_ldap')->connect();
				TBGContext::getModule('auth_ldap')->bind(TBGContext::getModule('auth_ldap')->getSetting('control_user'), TBGContext::getModule('auth_ldap')->getSetting('control_pass'), $connection);

				$default = TBGSettings::getDefaultUserID();

				foreach ($users as $user)
				{
					if ($user->getID() == $default)
					{
						continue;
					}
					
					$username = $user->getUsername();
					
					// Windows Domains require the username to be in the form DOMAIN\User, only we don't want that, strip domain
					if (strstr($username, '\\'))
					{
						$data = explode('\\', $username);
						$username2 = $data[1];
					}
					else
					{
						$username2 = $username;
					}
					
					$fields = array($fullname_attr, $email_attr, 'cn');
					$filter = '('.$username_attr.'='.TBGLDAPAuthentication::getModule()->escape($username2).')';
					
					$results = ldap_search($connection, $users_dn, $filter, $fields);
					
					if (!$results)
					{
						TBGLogging::log('failed to search for user after binding: '.ldap_error($connection), 'ldap', TBGLogging::LEVEL_FATAL);
						throw new Exception(TBGContext::geti18n()->__('Search failed ').ldap_error($connection));
					}
					
					$data = ldap_get_entries($connection, $results);
					
					if ($data['count'] == 0)
					{
						$user->delete();
						$deletecount++;
						continue;
					}
	
					$user_dn = 'CN='.$data[0]['cn'][0].','.$users_dn;
					
					if ($validgroups != '')
					{
						if (strstr($validgroups, ','))
						{
							$groups = explode(',', $validgroups);
						}
						else
						{
							$groups = array();
							$groups[] = $validgroups;
						}
						
						$allowed = false;
						
						foreach ($groups as $group)
						{
							$fields2 = array($groups_members_attr);
							$filter2 = '(cn='.TBGLDAPAuthentication::getModule()->escape($group).')';
							
							$results2 = ldap_search($connection, $groups_dn, $filter2, $fields2);
							
							if (!$results2)
							{
								TBGLogging::log('failed to search for user after binding: '.ldap_error($connection), 'ldap', TBGLogging::LEVEL_FATAL);
								throw new Exception(TBGContext::geti18n()->__('Search failed ').ldap_error($connection));
							}
							
							$data2 = ldap_get_entries($connection, $results2);
							
							if ($data2['count'] == 0)
							{
								continue;
							}
							
							foreach ($data2[0][$groups_members_attr] as $member)
							{
								if ($member == $user_dn)
								{
									$allowed = true;
								}
							}
						}
						
						if ($allowed == false)
						{
							$user->delete();
							$deletecount++;
							continue;
						}
					}
				}
			}
			catch (Exception $e)
			{
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Pruning failed'));
				TBGContext::setMessage('module_error_details', $e->getMessage());
				$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
			}
			
			ldap_unbind($connection);
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
			$validgroups = TBGContext::getModule('auth_ldap')->getSetting('groups');
			$users_dn = TBGContext::getModule('auth_ldap')->getSetting('u_dn');
			$username_dn = TBGContext::getModule('auth_ldap')->getSetting('u_attr');
			$fullname_attr = TBGContext::getModule('auth_ldap')->getSetting('f_attr');
			$email_attr = TBGContext::getModule('auth_ldap')->getSetting('e_attr');
			$groups_dn = TBGContext::getModule('auth_ldap')->getSetting('g_dn');
			$groups_members_attr = TBGContext::getModule('auth_ldap')->getSetting('g_attr');
			
			$users = array();
			$importcount = 0;
			$updatecount = 0;
			
			try
			{
				$connection = TBGContext::getModule('auth_ldap')->connect();
				TBGContext::getModule('auth_ldap')->bind($request->getParameter('username'), $request->getParameter('password'), $connection);
				
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
			
			ldap_unbind($connection);
			TBGContext::setMessage('module_message', TBGContext::getI18n()->__('Import successful! %imp% users imported, %upd% users updated from LDAP', array('%imp%' => $importcount, '%upd%' => $updatecount)));
			$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
		}

	}
