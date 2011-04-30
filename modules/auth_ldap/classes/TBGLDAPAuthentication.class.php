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
			$settings = array('port', 'hostname', 'u_dn', 'groups', 'u_attr', 'g_attr', 'e_attr', 'f_attr', 'g_dn');
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
			if ($connection == false): $failed = true; endif;

			if ($failed)
			{
				throw new Exception(TBGContext::geti18n()->__('Failed to connect to server'));
			}
			
			return $connection;
		}
		
		public function bind($username, $password, $connection)
		{
			$bind = ldap_bind($connection, $lduser, $ldpass);
			if ($bind == false): $failed = true; endif;

			// do connection here, set failed to true if failed
			if ($failed)
			{
				ldap_unbind($connection);
				throw new Exception(TBGContext::geti18n()->__('Failed to bind: ').ldap_error($connection));
				TBGLogging::log('bind failed: '.ldap_error($connection), 'ldap', TBGLogging::LEVEL_FATAL);
			}
		}
		
		public function loginCheck($username, $password)
		{
			$validgroups = $this->getSetting('groups');
			$users_dn = $this->getSetting('u_dn');
			$username_dn = $this->getSetting('u_attr');
			$fullname_attr = $this->getSetting('f_attr');
			$email_attr = $this->getSetting('e_attr');
			$groups_dn = $this->getSetting('g_dn');
			$groups_members_attr = $this->getSetting('g_attr');
			
			$email = null;
			$realname = $username;
			
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
					$user->setRealname($realname);
					$user->setPassword(TBGUser::createPassword(7)); // update password
					$user->setEmail($email); // update email address
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
					$user->setPassword(TBGUser::createPassword(7));
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
	}

