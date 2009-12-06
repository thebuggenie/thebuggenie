<?php

	/**
	 * The scope class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * The scope class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class BUGSscope extends BUGSidentifiableclass implements BUGSidentifiable
	{
		
		protected $_description = '';
		
		protected $_is_enabled = false;
		
		protected $_shortname = '';
		
		protected $_administrator = null;
		
		protected $_hostname = '';
		
		static function getAll()
		{
			$res = B2DB::getTable('B2tScopes')->doSelectAll();
			$scopes = array();
	
			while ($row = $res->getNextRow())
			{
				$scopes[] = BUGSfactory::scopeLab($row->get(B2tScopes::ID), $row);
			}
	
			return $scopes;
		}
		
		/**
		 * Construct a new scope
		 *
		 * @param integer $id
		 * @param B2DBRow $row
		 */
		public function __construct($id, $row = null)
		{
			try
			{
				if ($row === null)
				{
					$row = B2DB::getTable('B2tScopes')->doSelectById($id);
				}
			}
			catch (Exception $e)
			{
				throw new Exception("There was an error setting up this scope ($id):\n" . $e->getMessage());
			}
			
			if (!$row instanceof B2DBRow)
			{
				throw new Exception("This scope ($id) does not exist");
			}
			
			$this->_itemid = $row->get(B2tScopes::ID);
			$this->_description = $row->get(B2tScopes::DESCRIPTION);
			$this->_is_enabled = ($row->get(B2tScopes::ENABLED) == 1) ? true : false;
			$this->_hostname = $row->get(B2tScopes::HOSTNAME);
			$this->_administrator = $row->get(B2tScopes::ADMIN);
		}
		
		public function __toString()
		{
			return $this->getName();
		}
		
		public function getID()
		{
			return $this->_itemid;
		}
		
		public function getName()
		{
			return $this->_name;
		}
		
		public function setName($name)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tScopes::NAME, $name);
			B2DB::getTable('B2tScopes')->doUpdateById($crit, $this->_itemid);
			
			$this->_name = $name;
		}
		
		public function getShortname()
		{
			return $this->_shortname;
		}
		
		public function setShortname($shortname)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tScopes::SHORTNAME, $shortname);
			B2DB::getTable('B2tScopes')->doUpdateById($crit, $this->_itemid);
			
			$this->_shortname = $shortname;
		}
		
		public function isEnabled()
		{
			return $this->_is_enabled;
		}
		
		public function setEnabled($enabled)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tScopes::ENABLED, $enabled);
			B2DB::getTable('B2tScopes')->doUpdateById($crit, $this->_itemid);
			$this->_is_enabled = ($enabled == 1) ? true : false;
		}
		
		public function getDescription()
		{
			return $this->_description;
		}
		
		public function setDescription($description)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tScopes::DESCRIPTION, $description);
			B2DB::getTable('B2tScopes')->doUpdateById($crit, $this->_itemid);
			
			$this->_description = $description;
		}
		
		public function getHostname()
		{
			return $this->_hostname;
		}
		
		public function setHostname($hostname)
		{
			$hostname = trim($hostname, "/"); 
			
			$this->_hostname = $hostname;
		}
		
		/**
		 * Returns the scope administrator
		 *
		 * @return BUGSuser
		 */
		public function getScopeAdmin()
		{
			if (!$this->_administrator instanceof BUGSuser && $this->_administrator != 0)
			{
				try
				{
					$this->_administrator = BUGSfactory::userLab($this->_administrator);
				}
				catch (Exception $e)
				{
					
				}
			}
			return $this->_administrator;
		}
		
		public function setScopeAdmin($uid)
		{
			$adminuser = BUGSfactory::userLab($uid);
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tScopes::ADMIN, $uid);
			$res = B2DB::getTable('B2tScopes')->doUpdateById($crit, $this->_itemid);
			$this->_administrator = $adminuser;
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tGroups::SCOPE, $this->_itemid);
			$crit->addOrderBy(B2tScopes::ID, B2DBCriteria::SORT_ASC);
			$adminuser->setGroup(B2DB::getTable('B2tGroups')->doSelectOne($crit)->get(B2tGroups::ID));
			foreach ($adminuser->getTeams() as $aTeam)
			{
				$aTeam = BUGSfactory::teamLab($aTeam);
				$aTeam->removeMember($adminuser->getID());
			}
		}

		public static function createNew($scope_name, $hostname)
		{
			$scope_id = B2DB::getTable('B2tScopes')->createNew($scope_name, $hostname);
			self::loadFixtures($scope_id);
			return BUGSfactory::scopeLab($scope_id);
		}
		
		public static function loadFixtures($scope_id)
		{
			$i18n = BUGScontext::getI18n();
			list ($admin_group_id, $users_group_id, $guest_group_id) = B2DB::getTable('B2tGroups')->loadFixtures($scope_id);

			$adminuser = BUGSuser::createNew('administrator', 'Administrator', 'Admin', $scope_id, true, true);
			$adminuser->setGroup($admin_group_id);
			$adminuser->changePassword('admin');
			$adminuser->setAvatar('admin');
			
			$guestuser = BUGSuser::createNew('guest', 'Guest user', 'Guest user', $scope_id, true, true);
			$guestuser->setGroup($guest_group_id);

			B2DB::getTable('B2tSettings')->loadFixtures($scope_id);
			BUGSsettings::saveSetting('defaultgroup', $users_group_id, 'core', $scope_id);
			BUGSsettings::saveSetting('defaultuserid', $guestuser->getID(), 'core', $scope_id);

			B2DB::getTable('B2tTeams')->loadFixtures($scope_id);
			B2DB::getTable('B2tPermissions')->loadFixtures($scope_id, $admin_group_id, $guest_group_id);

			B2DB::getTable('B2tUserState')->loadFixtures($scope_id);
			B2DB::getTable('B2tIssueTypes')->loadFixtures($scope_id);
			B2DB::getTable('B2tListTypes')->loadFixtures($scope_id);
			B2DB::getTable('B2tLinks')->loadFixtures($scope_id);
		}

		public function save()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tScopes::HOSTNAME, $this->_hostname);
			B2DB::getTable('B2tScopes')->doUpdateById($crit, $this->_itemid);
			BUGSsettings::saveSetting('url_host', $this->_hostname, 'core', $this->getID());
		}
		
		/**
		 * Creates a new scope and returns it
		 *
		 * @param string $shortname
		 * @param string $scopename
		 * @param bool   $enabled
		 * @param string $description
		 * 
		 * @return BUGSscope
		 */
		public static function createNewOld($scopeShortname, $scopeName, $scopeEnabled, $scopeDescription, $scopeHostname)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tScopes::SHORTNAME, $scopeShortname);
			if (B2DB::getTable('B2tScopes')->doCount($crit) != 0)
			{
				throw new Exception('Could not add this scope, since it already exists');
			}
			
			// Create the new scope and use the return value to create more stuff
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tScopes::NAME, $scopeName);
			$crit->addInsert(B2tScopes::ENABLED, $scopeEnabled);
			$crit->addInsert(B2tScopes::SHORTNAME, $scopeShortname);
			$crit->addInsert(B2tScopes::HOSTNAME, $scopeHostname);
			$crit->addInsert(B2tScopes::DESCRIPTION, $scopeDescription);
			$res = B2DB::getTable('B2tScopes')->doInsert($crit);
			$addedScope = BUGSfactory::scopeLab($res->getInsertID());
			
			// Create a new 'administrator' user inside the new scope
			$theAdminUser = BUGSuser::createNew('scope' . $scopeShortname . 'administrator', 'Scope ' . $scopeShortname . ' Administrator', 'Scope ' . $scopeShortname . ' Admin', $addedScope->getID(), 1, 1);
			BUGScontext::getUser()->addFriend($theAdminUser->getID());
			
			// Create a new 'Superuser' group inside the new scope and set the scope administrator as a member
			$theAdminGroup = BUGSgroup::createNew('Administrators', $addedScope->getID())->getID();
			$theAdminUser->setGroup($theAdminGroup);
			$addedScope->setScopeAdmin($theAdminUser->getUID());
			
			// Create a new 'guest' user inside the new scope
			$theGuestUser = BUGSuser::createNew('scope' . $scopeShortname . 'guest', "Guest", "Guest", $addedScope->getID(), 1, 1);
			
			// Create a new 'Guest' group inside the new scope and set the scope guest user as a member
			$theGuestGroup = BUGSgroup::createNew('Guests', $addedScope->getID())->getID();
			$theGuestUser->setGroup($theGuestGroup);
			
			// Create a new 'Users' group inside the new scope
			$theUserGroup = BUGSgroup::createNew('Users', $addedScope->getID())->getID();

			BUGSlogging::log("Installing modules");
			foreach (BUGScontext::getModules() as $module)
			{
				BUGSlogging::log("Installing " . $module->getName());
				// Set general user permissions for that module
				$module->setPermission(0, 0, 0, true, $addedScope->getID());
				BUGSlogging::log("Running " . $module->getClassname() . '::install(' . $addedScope->getID() . ')');
				call_user_func($module->getClassname() . '::install', $addedScope->getID());
			}
			
			// Set general user permissions for that scope
			BUGScontext::setPermission("b2canreportissues", 0, "core", 0, 0, 0, true, $addedScope->getID());
			BUGScontext::setPermission("b2canfindissues", 0, "core", 0, 0, 0, true, $addedScope->getID());
			BUGScontext::setPermission("b2viewconfig", 0, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			BUGScontext::setPermission("b2saveconfig", 12, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			BUGScontext::setPermission("b2saveconfig", 9, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			BUGScontext::setPermission("b2saveconfig", 10, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			BUGScontext::setPermission("b2saveconfig", 2, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			BUGScontext::setPermission("b2saveconfig", 4, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			BUGScontext::setPermission("b2saveconfig", 1, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			BUGScontext::setPermission("b2saveconfig", 15, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());

			// Set Guest user restrictions
			if (BUGScontext::isModuleLoaded('calendar'))
			{
				BUGScontext::getModule('calendar')->setPermission(0, $theGuestGroup, 0, false, $addedScope->getID());
			}
			BUGScontext::getModule('messages')->setPermission(0, $theGuestGroup, 0, false, $addedScope->getID());
			BUGScontext::setPermission("b2noaccountaccess", 0, "core", 0, $theGuestGroup, 0, false, $addedScope->getID());

			// Load standard settings into the new scope
			BUGSsettings::loadFixtures($addedScope->getID());
			if ($addedScope->getHostname() != '')
			{
				BUGSsettings::saveSetting('url_host', $addedScope->getHostname(), 'core', $addedScope->getID());
			}
			else
			{
				BUGSsettings::saveSetting('url_host', BUGSsettings::get('url_host'), 'core', $addedScope->getID());
			}
			BUGSsettings::saveSetting('url_subdir', BUGSsettings::get('url_subdir'), 'core', $addedScope->getID());
			BUGSsettings::saveSetting('local_path', BUGSsettings::get('local_path'), 'core', $addedScope->getID());
			
			// Update scope guest user setting to the new scope guest user
			$md5Pass = md5('password');
			BUGSsettings::saveSetting('defaultpwd', $md5Pass, 'core', $addedScope->getID());
			BUGSsettings::saveSetting('defaultuname', 'scope' . $scopeShortname . 'guest', 'core', $addedScope->getID());
			BUGSsettings::saveSetting('defaultgroup', $theUserGroup, 'core', $addedScope->getID());
			
			$b2_settings['requirelogin'] = 0;
			$b2_settings['defaultisguest'] = 1;
			$b2_settings['showloginbox'] = 1;
			$b2_settings['allowreg'] = 1;
			$b2_settings['local_path'] = BUGSsettings::get('local_path');
			$b2_settings['language'] = BUGSsettings::get('language');
			$b2_settings['url_subdir'] = BUGSsettings::get('url_subdir');
			foreach ($b2_settings as $b2_settings_name => $b2_settings_val)
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tSettings::MODULE, 'core');
				$crit->addInsert(B2tSettings::SCOPE, $addedScope->getID());
				$crit->addInsert(B2tSettings::NAME, $b2_settings_name);
				$crit->addInsert(B2tSettings::VALUE, $b2_settings_val);
				B2DB::getTable('B2tSettings')->doInsert($crit);
			}
			
			return $addedScope;
		}
	}
