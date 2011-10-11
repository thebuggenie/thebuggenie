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
			$validgroups = TBGContext::getModule('auth_ldap')->getSetting('groups');
			$base_dn = TBGContext::getModule('auth_ldap')->getSetting('b_dn');
			$groups_members_attr = TBGContext::getModule('auth_ldap')->getSetting('g_attr');
			$group_class = TBGContext::getModule('auth_ldap')->getSetting('g_type');
			
			try
			{
				$connection = TBGContext::getModule('auth_ldap')->connect();
				
				TBGLDAPAuthentication::getModule()->bind($connection, TBGLDAPAuthentication::getModule()->getSetting('control_user'), TBGLDAPAuthentication::getModule()->getSetting('control_pass'));
			}
			catch (Exception $e)
			{
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Failed to connect to server'));
				TBGContext::setMessage('module_error_details', $e->getMessage());
				$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
			}

			$nonexisting = array();

			try
			{
				if ($validgroups != '')
				{
					/*
					 * We will repeat this for every group, but groups are supplied as a comma-separated list
					 */
					if (strstr($validgroups, ','))
					{
						$groups = explode(',', $validgroups);
					}
					else
					{
						$groups = array();
						$groups[] = $validgroups;
					}
					
					// Check if specified groups exist
					foreach ($groups as $group)
					{
						/*
						 * Find the group we are looking for, we search the entire directory
						 * We want to find 1 group, if we don't get 1, silently ignore this group.
						 */
						$fields2 = array($groups_members_attr);
						$filter2 = '(&(cn='.TBGLDAPAuthentication::getModule()->escape($group).')(objectClass='.TBGLDAPAuthentication::getModule()->escape($group_class).'))';
						
						$results2 = ldap_search($connection, $base_dn, $filter2, $fields2);
						
						if (!$results2)
						{
							TBGLogging::log('failed to search for user: '.ldap_error($connection), 'ldap', TBGLogging::LEVEL_FATAL);
							throw new Exception(TBGContext::geti18n()->__('Search failed: ').ldap_error($connection));
						}
						
						$data2 = ldap_get_entries($connection, $results2);
						
						if ($data2['count'] != 1)
						{
							$nonexisting[] = $group;
						}
					}
				}
			}
			catch (Exception $e)
			{
				ldap_unbind($connection);
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Failed to validate groups'));
				TBGContext::setMessage('module_error_details', $e->getMessage());
				$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
			}
			
			if (count($nonexisting) == 0)
			{
				ldap_unbind($connection);
				TBGContext::setMessage('module_message', TBGContext::getI18n()->__('Connection test successful'));
				$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
			}
			else
			{
				ldap_unbind($connection);
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Some of the groups you specified don\'t exist'));
				TBGContext::setMessage('module_error_details', TBGContext::getI18n()->__('The following groups for the group restriction could not be found: %groups%', array('%groups%' => implode(', ', $nonexisting))));
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
			$base_dn = TBGContext::getModule('auth_ldap')->getSetting('b_dn');
			$dn_attr = TBGContext::getModule('auth_ldap')->getSetting('dn_attr');
			$username_attr = TBGContext::getModule('auth_ldap')->getSetting('u_attr');
			$fullname_attr = TBGContext::getModule('auth_ldap')->getSetting('f_attr');
			$email_attr = TBGContext::getModule('auth_ldap')->getSetting('e_attr');
			$groups_members_attr = TBGContext::getModule('auth_ldap')->getSetting('g_attr');
			
			$user_class = TBGContext::getModule('auth_ldap')->getSetting('u_type');
			$group_class = TBGContext::getModule('auth_ldap')->getSetting('g_type');
			
			$users = TBGUser::getAll();
			$deletecount = 0;
			
			try
			{
				$connection = TBGContext::getModule('auth_ldap')->connect();
				TBGContext::getModule('auth_ldap')->bind($connection, TBGContext::getModule('auth_ldap')->getSetting('control_user'), TBGContext::getModule('auth_ldap')->getSetting('control_pass'));

				$default = TBGSettings::getDefaultUserID();

				foreach ($users as $user)
				{
					if ($user->getID() == $default)
					{
						continue;
					}
					
					$username = $user->getUsername();
					
					$fields = array($fullname_attr, $email_attr, 'cn', $dn_attr);
					$filter = '(&(objectClass='.TBGLDAPAuthentication::getModule()->escape($user_class).')('.$username_attr.'='.TBGLDAPAuthentication::getModule()->escape($username).'))';
					
					$results = ldap_search($connection, $base_dn, $filter, $fields);
					
					if (!$results)
					{
						TBGLogging::log('failed to search for user: '.ldap_error($connection), 'ldap', TBGLogging::LEVEL_FATAL);
						throw new Exception(TBGContext::geti18n()->__('Search failed: ').ldap_error($connection));
					}
					
					$data = ldap_get_entries($connection, $results);
					
					/*
					 * If a user is not found, delete it
					 */
					if ($data['count'] != 1)
					{
						$user->delete();
						$deletecount++;
						continue;
					}
					
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
							$filter2 = '(&(objectClass='.TBGLDAPAuthentication::getModule()->escape($group_class).')(cn='.TBGLDAPAuthentication::getModule()->escape($group).'))';
							
							$results2 = ldap_search($connection, $base_dn, $filter2, $fields2);
							
							if (!$results2)
							{
								TBGLogging::log('failed to search for user: '.ldap_error($connection), 'ldap', TBGLogging::LEVEL_FATAL);
								throw new Exception(TBGContext::geti18n()->__('Search failed: ').ldap_error($connection));
							}
							
							$data2 = ldap_get_entries($connection, $results2);
							
							if ($data2['count'] != 1)
							{
								continue;
							}

							foreach ($data2[0][$groups_members_attr] as $member)
							{
								$member = preg_replace('/(?<=,) +(?=[a-zA-Z])/', '', $member);
								$user_dn = preg_replace('/(?<=,) +(?=[a-zA-Z])/', '', $data[0][strtolower($dn_attr)][0]);
								
								if (!is_numeric($member) && strtolower($member) == strtolower($user_dn))
								{
									$allowed = true;
								}
							}
						}
						
						/*
						 * If a user is not allowed access, delete it
						 */
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
				ldap_unbind($connection);
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
			$base_dn = TBGContext::getModule('auth_ldap')->getSetting('b_dn');
			$dn_attr = TBGContext::getModule('auth_ldap')->getSetting('dn_attr');
			$username_attr = TBGContext::getModule('auth_ldap')->getSetting('u_attr');
			$fullname_attr = TBGContext::getModule('auth_ldap')->getSetting('f_attr');
			$email_attr = TBGContext::getModule('auth_ldap')->getSetting('e_attr');
			$groups_members_attr = TBGContext::getModule('auth_ldap')->getSetting('g_attr');
			
			$user_class = TBGContext::getModule('auth_ldap')->getSetting('u_type');
			$group_class = TBGContext::getModule('auth_ldap')->getSetting('g_type');
			
			$users = array();
			$importcount = 0;
			$updatecount = 0;
			
			try
			{
				/*
				 * Connect and bind to the control user
				 */
				$connection = TBGContext::getModule('auth_ldap')->connect();
				TBGContext::getModule('auth_ldap')->bind($connection, TBGContext::getModule('auth_ldap')->getSetting('control_user'), TBGContext::getModule('auth_ldap')->getSetting('control_pass'));
				
				/*
				 * Get a list of all users of a certain objectClass
				 */
				$fields = array($fullname_attr, $username_attr, $email_attr, 'cn', $dn_attr);
				$filter = '(objectClass='.TBGLDAPAuthentication::getModule()->escape($user_class).')';

				$results = ldap_search($connection, $base_dn, $filter, $fields);
				if (!$results)
				{
					TBGLogging::log('failed to search for users: '.ldap_error($connection), 'ldap', TBGLogging::LEVEL_FATAL);
					throw new Exception(TBGContext::geti18n()->__('Search failed: ').ldap_error($connection));
				}
				
				$data = ldap_get_entries($connection, $results);
				
				/*
				 * For every user that exists, process it.
				 */
				for ($i = 0; $i != $data['count']; $i++)
				{
					$user_dn = $data[$i][strtolower($dn_attr)][0];
					
					/*
					 * If groups are specified, perform group restriction tests
					 */
					if ($validgroups != '')
					{
						/*
						 * We will repeat this for every group, but groups are supplied as a comma-separated list
						 */
						if (strstr($validgroups, ','))
						{
							$groups = explode(',', $validgroups);
						}
						else
						{
							$groups = array();
							$groups[] = $validgroups;
						}
						
						// Assumed we are initially banned
						$allowed = false;
						
						foreach ($groups as $group)
						{
							// No need to carry on looking if we have access
							if ($allowed == true): continue; endif;
							
							/*
							 * Find the group we are looking for, we search the entire directory
							 * We want to find 1 group, if we don't get 1, silently ignore this group.
							 */
							$fields2 = array($groups_members_attr);
							$filter2 = '(&(cn='.TBGLDAPAuthentication::getModule()->escape($group).')(objectClass='.TBGLDAPAuthentication::getModule()->escape($group_class).'))';
							
							$results2 = ldap_search($connection, $base_dn, $filter2, $fields2);
							
							if (!$results2)
							{
								TBGLogging::log('failed to search for user: '.ldap_error($connection), 'ldap', TBGLogging::LEVEL_FATAL);
								throw new Exception(TBGContext::geti18n()->__('Search failed: ').ldap_error($connection));
							}
							
							$data2 = ldap_get_entries($connection, $results2);
							
							if ($data2['count'] != 1)
							{
								continue;
							}
							
							/*
							 * Look through the group's member list. If we are found, grant access.
							 */
							foreach ($data2[0][strtolower($groups_members_attr)] as $member)
							{
								$member = preg_replace('/(?<=,) +(?=[a-zA-Z])/', '', $member);
								$user_dn = preg_replace('/(?<=,) +(?=[a-zA-Z])/', '', $user_dn);
								if (!is_numeric($member) && strtolower($member) == strtolower($user_dn))
								{
									$allowed = true;
								}
							}
						}
						
						if ($allowed == false)
						{
							continue;
						}
					}

					$users[$i] = array();
	
					/*
					 * Set user's properties.
					 * Realname is obtained from directory, if not found we set it to the username
					 * Email is obtained from directory, if not found we set it to blank
					 */
					if (!array_key_exists(strtolower($fullname_attr), $data[$i]))
					{
						$users[$i]['realname'] = $data[$i]['cn'][0];
					}
					else
					{
						$users[$i]['realname'] = $data[$i][strtolower($fullname_attr)][0];
					}
					
					if (!array_key_exists(strtolower($email_attr), $data[$i]))
					{
						$users[$i]['email'] = '';
					}
					else
					{
						$users[$i]['email'] = $data[$i][strtolower($email_attr)][0];
					}
					$users[$i]['username'] = $data[$i][strtolower($username_attr)][0];
				}

			}
			catch (Exception $e)
			{
				TBGContext::setMessage('module_error', TBGContext::getI18n()->__('Import failed'));
				TBGContext::setMessage('module_error_details', $e->getMessage());
				$this->forward(TBGContext::getRouting()->generate('configure_module', array('config_module' => 'auth_ldap')));
			}
				
			/*
			 * For every user that was found, either create a new user object, or update
			 * the existing one. This will update the created and updated counts as appropriate.
			 */
			foreach ($users as $ldapuser)
			{
				$username = $ldapuser['username'];
				$email = $ldapuser['email'];
				$realname = $ldapuser['realname'];
				
				try
				{
					$user = TBGUser::getByUsername($username);
					if ($user instanceof TBGUser)
					{
						$user->setRealname($realname);
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
						$user->setBuddyname($realname);
						$user->setEmail($email);
						$user->setEnabled();
						$user->setActivated();
						$user->setPassword($user->getJoinedDate().$username);
						$user->setJoined();
						$user->save();
						$importcount++;
					}					
				}
				catch (Exception $e)
				{
					ldap_unbind($connection);
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
