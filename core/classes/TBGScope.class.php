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
	class TBGScope extends TBGIdentifiableClass
	{
		
		protected $_description = '';
		
		protected $_is_enabled = false;
		
		protected $_shortname = '';
		
		protected $_administrator = null;
		
		protected $_hostname = '';
		
		static function getAll()
		{
			$res = TBGScopesTable::getTable()->doSelectAll();
			$scopes = array();
	
			while ($row = $res->getNextRow())
			{
				$scopes[] = TBGFactory::scopeLab($row->get(TBGScopesTable::ID), $row);
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
					$row = TBGScopesTable::getTable()->doSelectById($id);
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
			
			$this->_itemid = $row->get(TBGScopesTable::ID);
			$this->_description = $row->get(TBGScopesTable::DESCRIPTION);
			$this->_is_enabled = ($row->get(TBGScopesTable::ENABLED) == 1) ? true : false;
			$this->_hostname = $row->get(TBGScopesTable::HOSTNAME);
			$this->_administrator = $row->get(TBGScopesTable::ADMIN);
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
			$crit->addUpdate(TBGScopesTable::NAME, $name);
			TBGScopesTable::getTable()->doUpdateById($crit, $this->_itemid);
			
			$this->_name = $name;
		}
		
		public function getShortname()
		{
			return $this->_shortname;
		}
		
		public function setShortname($shortname)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGScopesTable::SHORTNAME, $shortname);
			TBGScopesTable::getTable()->doUpdateById($crit, $this->_itemid);
			
			$this->_shortname = $shortname;
		}
		
		public function isEnabled()
		{
			return $this->_is_enabled;
		}
		
		public function setEnabled($enabled)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGScopesTable::ENABLED, $enabled);
			TBGScopesTable::getTable()->doUpdateById($crit, $this->_itemid);
			$this->_is_enabled = ($enabled == 1) ? true : false;
		}
		
		public function getDescription()
		{
			return $this->_description;
		}
		
		public function setDescription($description)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGScopesTable::DESCRIPTION, $description);
			TBGScopesTable::getTable()->doUpdateById($crit, $this->_itemid);
			
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
		 * @return TBGUser
		 */
		public function getScopeAdmin()
		{
			if (!$this->_administrator instanceof TBGUser && $this->_administrator != 0)
			{
				try
				{
					$this->_administrator = TBGFactory::userLab($this->_administrator);
				}
				catch (Exception $e)
				{
					
				}
			}
			return $this->_administrator;
		}
		
		public function setScopeAdmin($uid)
		{
			$adminuser = TBGFactory::userLab($uid);
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGScopesTable::ADMIN, $uid);
			$res = TBGScopesTable::getTable()->doUpdateById($crit, $this->_itemid);
			$this->_administrator = $adminuser;
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGGroupsTable::SCOPE, $this->_itemid);
			$crit->addOrderBy(TBGScopesTable::ID, B2DBCriteria::SORT_ASC);
			$adminuser->setGroup(B2DB::getTable('TBGGroupsTable')->doSelectOne($crit)->get(TBGGroupsTable::ID));
			foreach ($adminuser->getTeams() as $aTeam)
			{
				$aTeam = TBGFactory::teamLab($aTeam);
				$aTeam->removeMember($adminuser->getID());
			}
		}

		public static function createNew($scope_name, $hostname)
		{
			$scope_id = TBGScopesTable::getTable()->createNew($scope_name, $hostname);
			self::loadFixtures($scope_id);
			return TBGFactory::scopeLab($scope_id);
		}
		
		public static function loadFixtures($scope_id)
		{
			$i18n = TBGContext::getI18n();
			list ($admin_group_id, $users_group_id, $guest_group_id) = B2DB::getTable('TBGGroupsTable')->loadFixtures($scope_id);
			
			$adminuser = TBGUser::createNew('administrator', 'Administrator', 'Admin', $scope_id, true, true);
			$adminuser->setGroup($admin_group_id);

			if (TBGContext::isInstallmode())
			{
				$salt = sha1(time().mt_rand(1000, 10000));
				$adminuser->changePassword('admin'.$salt); // Settings not active yet
			}
			else
			{
				$adminuser->changePassword('admin');
			}
			$adminuser->setAvatar('admin');
			$adminuser->save();
			
			$guestuser = TBGUser::createNew('guest', 'Guest user', 'Guest user', $scope_id, true, true);
			$guestuser->setGroup($guest_group_id);
			$guestuser->save();

			B2DB::getTable('TBGSettingsTable')->loadFixtures($scope_id);
			TBGSettings::saveSetting('defaultgroup', $users_group_id, 'core', $scope_id);
			TBGSettings::saveSetting('defaultuserid', $guestuser->getID(), 'core', $scope_id);
			
			if (TBGContext::isInstallmode())
			{
				TBGSettings::saveSetting('salt', $salt, 'core', 1);
			}

			B2DB::getTable('TBGTeamsTable')->loadFixtures($scope_id);
			B2DB::getTable('TBGPermissionsTable')->loadFixtures($scope_id, $admin_group_id, $guest_group_id);

			B2DB::getTable('TBGUserStateTable')->loadFixtures($scope_id);
			B2DB::getTable('TBGIssueTypesTable')->loadFixtures($scope_id);
			B2DB::getTable('TBGListTypesTable')->loadFixtures($scope_id);
			B2DB::getTable('TBGLinksTable')->loadFixtures($scope_id);
		}

		public function save()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGScopesTable::HOSTNAME, $this->_hostname);
			TBGScopesTable::getTable()->doUpdateById($crit, $this->_itemid);
			TBGSettings::saveSetting('url_host', $this->_hostname, 'core', $this->getID());
		}
		
		/**
		 * Creates a new scope and returns it
		 *
		 * @param string $shortname
		 * @param string $scopename
		 * @param bool   $enabled
		 * @param string $description
		 * 
		 * @return TBGScope
		 */
		public static function createNewOld($scopeShortname, $scopeName, $scopeEnabled, $scopeDescription, $scopeHostname)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGScopesTable::SHORTNAME, $scopeShortname);
			if (TBGScopesTable::getTable()->doCount($crit) != 0)
			{
				throw new Exception('Could not add this scope, since it already exists');
			}
			
			// Create the new scope and use the return value to create more stuff
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGScopesTable::NAME, $scopeName);
			$crit->addInsert(TBGScopesTable::ENABLED, $scopeEnabled);
			$crit->addInsert(TBGScopesTable::SHORTNAME, $scopeShortname);
			$crit->addInsert(TBGScopesTable::HOSTNAME, $scopeHostname);
			$crit->addInsert(TBGScopesTable::DESCRIPTION, $scopeDescription);
			$res = TBGScopesTable::getTable()->doInsert($crit);
			$addedScope = TBGFactory::scopeLab($res->getInsertID());
			
			// Create a new 'administrator' user inside the new scope
			$theAdminUser = TBGUser::createNew('scope' . $scopeShortname . 'administrator', 'Scope ' . $scopeShortname . ' Administrator', 'Scope ' . $scopeShortname . ' Admin', $addedScope->getID(), 1, 1);
			TBGContext::getUser()->addFriend($theAdminUser->getID());
			
			// Create a new 'Superuser' group inside the new scope and set the scope administrator as a member
			$theAdminGroup = TBGGroup::createNew('Administrators', $addedScope->getID())->getID();
			$theAdminUser->setGroup($theAdminGroup);
			$addedScope->setScopeAdmin($theAdminUser->getUID());
			
			// Create a new 'guest' user inside the new scope
			$theGuestUser = TBGUser::createNew('scope' . $scopeShortname . 'guest', "Guest", "Guest", $addedScope->getID(), 1, 1);
			
			// Create a new 'Guest' group inside the new scope and set the scope guest user as a member
			$theGuestGroup = TBGGroup::createNew('Guests', $addedScope->getID())->getID();
			$theGuestUser->setGroup($theGuestGroup);
			
			// Create a new 'Users' group inside the new scope
			$theUserGroup = TBGGroup::createNew('Users', $addedScope->getID())->getID();

			TBGLogging::log("Installing modules");
			foreach (TBGContext::getModules() as $module)
			{
				TBGLogging::log("Installing " . $module->getName());
				// Set general user permissions for that module
				$module->setPermission(0, 0, 0, true, $addedScope->getID());
				TBGLogging::log("Running " . $module->getClassname() . '::install(' . $addedScope->getID() . ')');
				call_user_func($module->getClassname() . '::install', $addedScope->getID());
			}
			
			// Set general user permissions for that scope
			TBGContext::setPermission("b2canreportissues", 0, "core", 0, 0, 0, true, $addedScope->getID());
			TBGContext::setPermission("b2canfindissues", 0, "core", 0, 0, 0, true, $addedScope->getID());
			TBGContext::setPermission("b2viewconfig", 0, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			TBGContext::setPermission("b2saveconfig", 12, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			TBGContext::setPermission("b2saveconfig", 9, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			TBGContext::setPermission("b2saveconfig", 10, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			TBGContext::setPermission("b2saveconfig", 2, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			TBGContext::setPermission("b2saveconfig", 4, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			TBGContext::setPermission("b2saveconfig", 1, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());
			TBGContext::setPermission("b2saveconfig", 15, "core", 0, $theAdminGroup, 0, true, $addedScope->getID());

			// Set Guest user restrictions
			if (TBGContext::isModuleLoaded('calendar'))
			{
				TBGContext::getModule('calendar')->setPermission(0, $theGuestGroup, 0, false, $addedScope->getID());
			}
			TBGContext::getModule('messages')->setPermission(0, $theGuestGroup, 0, false, $addedScope->getID());
			TBGContext::setPermission("b2noaccountaccess", 0, "core", 0, $theGuestGroup, 0, false, $addedScope->getID());

			// Load standard settings into the new scope
			TBGSettings::loadFixtures($addedScope->getID());
			if ($addedScope->getHostname() != '')
			{
				TBGSettings::saveSetting('url_host', $addedScope->getHostname(), 'core', $addedScope->getID());
			}
			else
			{
				TBGSettings::saveSetting('url_host', TBGSettings::get('url_host'), 'core', $addedScope->getID());
			}
			TBGSettings::saveSetting('url_subdir', TBGSettings::get('url_subdir'), 'core', $addedScope->getID());
			TBGSettings::saveSetting('local_path', TBGSettings::get('local_path'), 'core', $addedScope->getID());
			
			// Update scope guest user setting to the new scope guest user
			$hashPass = TBGUser::hashPassword('password');
			TBGSettings::saveSetting('defaultpwd', $hashPass, 'core', $addedScope->getID());
			TBGSettings::saveSetting('defaultuname', 'scope' . $scopeShortname . 'guest', 'core', $addedScope->getID());
			TBGSettings::saveSetting('defaultgroup', $theUserGroup, 'core', $addedScope->getID());
			
			$b2_settings['requirelogin'] = 0;
			$b2_settings['defaultisguest'] = 1;
			$b2_settings['allowreg'] = 1;
			$b2_settings['local_path'] = TBGSettings::get('local_path');
			$b2_settings['language'] = TBGSettings::get('language');
			$b2_settings['url_subdir'] = TBGSettings::get('url_subdir');
			foreach ($b2_settings as $b2_settings_name => $b2_settings_val)
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(TBGSettingsTable::MODULE, 'core');
				$crit->addInsert(TBGSettingsTable::SCOPE, $addedScope->getID());
				$crit->addInsert(TBGSettingsTable::NAME, $b2_settings_name);
				$crit->addInsert(TBGSettingsTable::VALUE, $b2_settings_val);
				B2DB::getTable('TBGSettingsTable')->doInsert($crit);
			}
			
			return $addedScope;
		}
	}
