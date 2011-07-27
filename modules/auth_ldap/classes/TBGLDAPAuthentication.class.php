<?php

	/**
	 * LDAP Authentication
	 *
	 * @author
	 * @version 0.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package auth_ldap
	 * @subpackage core
	 */

	/**
	 * LDAP Authentication
	 *
	 * @package auth_ldap
	 * @subpackage core
	 */
	class TBGLDAPAuthentication extends TBGModule
	{

		protected $_longname = 'LDAP Authentication';
		
		protected $_description = 'Allows authentication against a LDAP or Active Directory server';
		
		protected $_module_config_title = 'LDAP Authentication';
		
		protected $_module_config_description = 'Configure server connection settings';
		
		protected $_module_version = '0.1';
		
		protected $_has_config_settings = true;

		/**
		 * Return an instance of this module
		 *
		 * @return LDAP Authentication
		 */
		public static function getModule()
		{
			return TBGContext::getModule('auth_ldap');
		}

		protected function _initialize()
		{
		}
		
		protected function _addRoutes()
		{
			$this->addRoute('ldap_test', '/test/ldap', 'testConnection');
			$this->addRoute('ldap_prune', '/configure/module/auth_ldap/prune', 'pruneUsers');
			$this->addRoute('ldap_import', '/configure/module/auth_ldap/import', 'importUsers');
		}

		protected function _install($scope)
		{
		}

		protected function _uninstall()
		{
		}
		
		public final function getType()
		{
			return parent::MODULE_AUTH;
		}

		public function getRoute()
		{
			return TBGContext::getRouting()->generate('ldap_authentication_index');
		}

		public function postConfigSettings(TBGRequest $request)
		{
			$settings = array('hostname', 'u_dn', 'groups', 'u_attr', 'g_attr', 'e_attr', 'f_attr', 'g_dn', 'control_user', 'control_pass');
			foreach ($settings as $setting)
			{
				if ($request->hasParameter($setting))
				{
					$this->saveSetting($setting, $request->getParameter($setting));
				}
			}
		}
		
		public function connect()
		{
			$host = $this->getSetting('hostname');
			$failed = false;

			$connection = ldap_connect($host);
			ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
			
			if ($connection == false): $failed = true; endif;

			if ($failed)
			{
				throw new Exception(TBGContext::geti18n()->__('Failed to connect to server'));
			}
			
			return $connection;
		}
		
		public function bind($lduser, $ldpass, $connection)
		{
			$bind = ldap_bind($connection, $lduser, $ldpass);

			if (!$bind)
			{
				ldap_unbind($connection);
				TBGLogging::log('bind failed: '.ldap_error($connection), 'ldap', TBGLogging::LEVEL_FATAL);
				throw new Exception(TBGContext::geti18n()->__('Failed to bind: ').ldap_error($connection));
			}
		}
		
		public function escape($string)
		{
			$chars = array('\\', '*', '()', ')', chr(0));
			foreach ($chars as $char)
			{
				$string = str_replace($char, '\\'.$char, $string);
			}

			return $string;
		}

		public function doLogin($username, $password, $mode = 1)
		{	
			$validgroups = $this->getSetting('groups');
			$users_dn = $this->getSetting('u_dn');
			$username_attr = $this->escape($this->getSetting('u_attr'));
			$fullname_attr = $this->escape($this->getSetting('f_attr'));
			$email_attr = $this->escape($this->getSetting('e_attr'));
			$groups_dn = $this->getSetting('g_dn');
			$groups_members_attr = $this->escape($this->getSetting('g_attr'));
			
			$email = null;
			
			/*
			 * Do the LDAP check here.
			 * 
			 * If a connection error or something, throw an exception and log
			 * 
			 * If we can, set $mail and $realname to correct values from LDAP
			 * otherwise don't touch those variables.
			 * 
			 * To log do:
			 * TBGLogging::log('error goes here', 'ldap', TBGLogging::LEVEL_FATAL);
			 */
			try
			{
				$connection = $this->connect();
				
				if ($mode == 1)
				{
					$this->bind($username, $password, $connection);
				}
				else
				{
					$control_user = $this->getSetting('control_user');
					$control_password = $this->getSetting('control_pass');

					$this->bind($control_user, $control_password, $connection);
				}
				
				
				// Assume bind successful, otherwise we would have had an exception
				
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
				$filter = '('.$username_attr.'='.$this->escape($username2).')';
				
				$results = ldap_search($connection, $users_dn, $filter, $fields);
				
				if (!$results)
				{
					TBGLogging::log('failed to search for user after binding: '.ldap_error($connection), 'ldap', TBGLogging::LEVEL_FATAL);
					throw new Exception(TBGContext::geti18n()->__('Search failed ').ldap_error($connection));
				}
				
				$data = ldap_get_entries($connection, $results);
				
				if ($data['count'] == 0)
				{
					TBGLogging::log('bind OK but user '.$username.' does not exist in DN '.$users_dn.', attribute '.$username_attr, 'ldap', TBGLogging::LEVEL_FATAL);
					throw new Exception(TBGContext::geti18n()->__('User does not exist in DN'));
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
						$filter2 = '(cn='.$this->escape($group).')';
						
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
						throw new Exception(TBGContext::getI18n()->__('You are not a member of a group allowed to log in'));
					}
				}

				if (!array_key_exists($fullname_attr, $data[0]))
				{
					$realname = $username;
				}
				else
				{
					$realname = $data[0][$fullname_attr][0];
				}
				
				if (!array_key_exists($email_attr, $data[0]))
				{
					$email = '';
				}
				else
				{
					$email = $data[0][$email_attr][0];
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			
			try
			{
				$user = TBGUser::getByUsername($username);
				if ($user instanceof TBGUser)
				{
					$temp_password = TBGUser::createPassword(7);
					$user->setBuddyname($realname);
					$user->setRealname($realname);
					$user->setPassword($temp_password); // update password
					$user->setEmail($email); // update email address
					$user->save();
				}
				else
				{
					if ($mode == 1)
					{
						$temp_password = TBGUser::createPassword(7);
						// create user
						$user = new TBGUser();
						$user->setUsername($username);
						$user->setRealname('temporary');
						$user->setBuddyname($username);
						$user->setEmail('temporary');
						$user->setEnabled();
						$user->setActivated();
						$user->setPassword($temp_password);
						$user->setJoined();
						$user->save();
					}
					else
					{
						throw new Exception('User does not exist in TBG');
					}
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}

			TBGContext::getResponse()->setCookie('tbg3_username', $username);
			TBGContext::getResponse()->setCookie('tbg3_password', TBGUser::hashPassword($temp_password));

			return TBGUsersTable::getTable()->getByUsername($username);
		}

		public function verifyLogin($username)
		{
			return $this->doLogin($username, 'a', 2);
		}
	}

