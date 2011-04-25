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
			$settings = array('username', 'password', 'port', 'hostname', 'dn');
			foreach ($settings as $setting)
			{
				if ($request->hasParameter($setting))
				{
					$this->saveSetting($setting, $request->getParameter($setting));
				}
			}
		}
	}

