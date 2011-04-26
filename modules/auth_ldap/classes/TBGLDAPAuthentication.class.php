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

		protected $_module_version = '0.1';

		/**
		 * Return an instance of this module
		 *
		 * @return LDAP Authentication
		 */
		public static function getModule()
		{
			return TBGContext::getModule('auth_ldap');
		}

		protected function _initialize(TBGI18n $i18n)
		{
			$this->setLongName($i18n->__('LDAP Authentication'));
			$this->setConfigTitle($i18n->__('LDAP Authentication'));
			$this->setDescription($i18n->__('Allows authentication against a LDAP or Active Directory server'));
			$this->setConfigDescription($i18n->__('Configure server connection settings'));
			$this->setHasConfigSettings();
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
			$settings = array('username', 'password', 'port', 'hostname', 'dn', 'groups');
			foreach ($settings as $setting)
			{
				if ($request->hasParameter($setting))
				{
					$this->saveSetting($setting, $request->getParameter($setting));
				}
			}
		}
		
		public function loginCheck($username, $password)
		{
			$host = $this->getSetting('hostname');
			$port = $this->getSetting('port');
			$lduser = $this->getSetting('username');
			$ldpass = $this->getSetting('password');
			$validgroups = $this->getSetting('groups');
			$dn = $this->getSetting('dn');
			
			$failed = true;
			$email = null;
			$realname = $username;
			
			/*
			 * Do the LDAP check here.
			 * 
			 * If login successful, set $failed = false;, else true
			 * If a connection error or something, throw an exception and log
			 * 
			 * If we can, set $mail and $realname to correct values from LDAP
			 * otherwise don't touch those variables.
			 * 
			 * To log do:
			 * TBGLogging::log('error goes here', 'ldap', TBGLogging::LEVEL_FATAL);
			 */
			
			if (!$failed)
			{
				try
				{
					$user = TBGUser::getByUsername($username);
					if ($user instanceof TBGUser)
					{
						$user->setRealname($realname);
						$user->setPassword($password); // update password
						$user->setEmail($email); // update emaila ddress
						$user->save();
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
					}
					
					return TBGUsersTable::getByUsername($username);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			else
			{
				return false; // login failed
			}
		}
	}

