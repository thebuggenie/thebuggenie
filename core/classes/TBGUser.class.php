<?php

	/**
	 * User class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * User class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGUser extends TBGIdentifiableClass 
	{
		
		static protected $_b2dbtablename = 'TBGUsersTable';

		static protected $_num_users = null;
		
		/**
		 * All users
		 * 
		 * @var array
		 */
		static protected $_users = null;
		
		/**
		 * Unique username (login name)
		 *
		 * @var string
		 */
		protected $_username = '';
		
		/**
		 * Whether or not the user has authenticated
		 * 
		 * @var boolean
		 */
		protected $authenticated = false;
		
		/**
		 * Hashed password
		 *
		 * @var string
		 */
		protected $_password = '';
		
		/**
		 * The users scope
		 *
		 * @var TBGScope
		 */
		protected $_scope = null;
		
		/**
		 * User real name
		 *
		 * @var string
		 */
		protected $_realname = '';
		
		/**
		 * User short name (buddyname)
		 *
		 * @var string
		 */
		protected $_buddyname = '';
		
		/**
		 * User email
		 *
		 * @var string
		 */
		protected $_email = '';
		
		/**
		 * Is email private?
		 *
		 * @var boolean
		 * @access protected
		 */
		protected $_private_email = true;
		
		/**
		 * The user state
		 *
		 * @var TBGUserstate
		 * @Class TBGUserstate
		 */
		protected $_userstate = null;
		
		/**
		 * Whether the user has a custom userstate set
		 * 
		 * @var boolean
		 */
		protected $_customstate = false;
		
		/**
		 * User homepage
		 *
		 * @var string
		 */
		protected $_homepage = '';

		/**
		 * Users language
		 *
		 * @var string
		 */
		protected $_language = '';
		
		/**
		 * Array of team ids where the current user is a member
		 *
		 * @var array
		 * 
		 * @access protected
		 */
		protected $teams = null;
		
		/**
		 * Array of client ids where the current user is a member
		 *
		 * @var array
		 */
		protected $clients = null;
				
		/**
		 * The users avatar
		 *
		 * @var string
		 */
		protected $_avatar = null;
		
		/**
		 * Whether to use the users gravatar or not
		 * 
		 * @var boolean
		 */
		protected $_use_gravatar = true;
		
		/**
		 * The users login error - if any
		 *
		 * @var string
		 */
		protected $login_error = '';
		
		/**
		 * Array of issues to follow up
		 *
		 * @var array
		 */
		protected $_starredissues = null;
		
		/**
		 * Array of issues assigned to the user
		 *
		 * @var array
		 */
		protected $userassigned = null;
		
		/**
		 * Array of issues assigned to the users team(s)
		 *
		 * @var array
		 */
		protected $teamassigned = array();
		
		/**
		 * Array of saved searches to show on the frontpage
		 *
		 * @var array
		 */
		protected $indexsearches = array();
		
		/**
		 * The users group 
		 * 
		 * @var TBGGroup
		 * @Class TBGGroup
		 */
		protected $_group_id = null;
	
		/**
		 * A list of the users associated projects, if any
		 * 
		 * @var array
		 */
		protected $_associated_projects = null;
		
		/**
		 * Timestamp of when the user was last seen
		 *
		 * @var integer
		 */
		protected $_lastseen = 0;

		/**
		 * The timezone this user is in
		 *
		 * @var integer
		 */
		protected $_timezone = null;

		/**
		 * This users upload quota (MB)
		 * 
		 * @var integer 
		 */
		protected $_quota;

		/**
		 * When this user joined
		 * 
		 * @var integer
		 */
		protected $_joined = 0;
		
		/**
		 * This users friends
		 * 
		 * @var array An array of TBGUser objects
		 */
		protected $_friends = null;
		
		/**
		 * Whether the user is enabled
		 * 
		 * @var boolean
		 */
		protected $_enabled = false;
		
		/**
		 * Whether the user is activated
		 * 
		 * @var boolean
		 */
		protected $_activated = false;
		
		/**
		 * Whether the user is deleted
		 * 
		 * @var boolean
		 */
		protected $_deleted = false;
		
		/**
		 * Retrieve a user by username
		 *
		 * @param string $username
		 *
		 * @return TBGUser
		 */
		public static function getByUsername($username, $scope = null)
		{
			if ($row = TBGUsersTable::getTable()->getByUsername($username, $scope))
			{
				return TBGContext::factory()->TBGUser($row->get(TBGUsersTable::ID), $row);
			}
			return null;
		}
		
		/**
		 * Retrieve all userrs
		 *
		 * @return array
		 */
		public static function getAll()
		{
			if (self::$_users === null)
			{
				self::$_users = array();
				if ($res = \b2db\Core::getTable('TBGUsersTable')->getAll())
				{
					while ($row = $res->getNextRow())
					{
						self::$_users[$row->get(TBGUsersTable::ID)] = TBGContext::factory()->TBGUser($row->get(TBGUsersTable::ID), $row);
					}
				}
			}
			return self::$_users;
		}
		
		/**
		 * Load user fixtures for a specified scope
		 * 
		 * @param TBGScope $scope
		 * @param TBGGroup $admin_group
		 * @param TBGGroup $user_group
		 * @param TBGGroup $guest_group 
		 */
		public static function loadFixtures(TBGScope $scope, TBGGroup $admin_group, TBGGroup $user_group, TBGGroup $guest_group)
		{
			$adminuser = new TBGUser();
			$adminuser->setUsername('administrator');
			$adminuser->setRealname('Administrator');
			$adminuser->setBuddyname('Admin');
			$adminuser->setGroup($admin_group);
			$adminuser->setPassword('admin');
			$adminuser->setActivated();
			$adminuser->setEnabled();
			$adminuser->setAvatar('admin');
			$adminuser->setScope($scope);
			$adminuser->save();
			
			$guestuser = new TBGUser();
			$guestuser->setUsername('guest');
			$guestuser->setRealname('Guest user');
			$guestuser->setBuddyname('Guest user');
			$guestuser->setGroup($guest_group);
			$guestuser->setPassword('password'); // Settings not active yet
			$guestuser->setActivated();
			$guestuser->setEnabled();
			$guestuser->setScope($scope);
			$guestuser->save();

			TBGSettings::saveSetting('defaultuserid', $guestuser->getID(), 'core', $scope->getID());
		}
		
		/**
		 * Take a raw password and convert it to the hashed format
		 * 
		 * @param string $password
		 * 
		 * @return hashed password
		 */
		public static function hashPassword($password, $salt = null)
		{
			$salt = ($salt !== null) ? $salt : TBGSettings::getPasswordSalt();
			return crypt($password, '$2a$07$'.$salt.'$');
		}
		
		/**
		 * Returns the logged in user, or default user if not logged in
		 *
		 * @param string $uname
		 * @param string $upwd
		 * 
		 * @return TBGUser
		 */
		public static function loginCheck($username = null, $password = null)
		{
			try
			{
				$row = null;
				$external = false;
				$raw = true;

				// If no username and password specified, check if we have a session that exists already
				if ($username === null && $password === null)
				{
					if (TBGContext::getRequest()->hasCookie('tbg3_username') && TBGContext::getRequest()->hasCookie('tbg3_password'))
					{
						$username = TBGContext::getRequest()->getCookie('tbg3_username');
						$password = TBGContext::getRequest()->getCookie('tbg3_password');
						$row = TBGUsersTable::getTable()->getByUsernameAndPassword($username, $password);
						$raw = false;

						if (!$row)
						{
							TBGContext::logout();
							throw new Exception('No such login');
							//TBGContext::getResponse()->headerRedirect(TBGContext::getRouting()->generate('login'));
						}
					}
				}
				
				// If we have authentication details, validate them
				if (TBGSettings::isUsingExternalAuthenticationBackend() && $username !== null && $password !== null)
				{
					$external = true;
					TBGLogging::log('Authenticating with backend: '.TBGSettings::getAuthenticationBackend(), 'auth', TBGLogging::LEVEL_INFO);
					try
					{
						$mod = TBGContext::getModule(TBGSettings::getAuthenticationBackend());
						if ($mod->getType() !== TBGModule::MODULE_AUTH)
						{
							TBGLogging::log('Auth module is not the right type', 'auth', TBGLogging::LEVEL_FATAL);
						}
						if (TBGContext::getRequest()->hasCookie('tbg3_username') && TBGContext::getRequest()->hasCookie('tbg3_password'))
						{
							$row = $mod->verifyLogin($username, $password);
						}
						else
						{
							$row = $mod->doLogin($username, $password);
						}
						if(!$row)
						{
							// Invalid
							TBGContext::logout();
							throw new Exception('No such login');
							//TBGContext::getResponse()->headerRedirect(TBGContext::getRouting()->generate('login'));
						}
					}
					catch (Exception $e)
					{
						throw $e;
					}
				}
				// If we don't have login details, the backend may autologin from cookies or something
				elseif (TBGSettings::isUsingExternalAuthenticationBackend())
				{
					$external = true;
					TBGLogging::log('Authenticating without credentials with backend: '.TBGSettings::getAuthenticationBackend(), 'auth', TBGLogging::LEVEL_INFO);
					try
					{
						$mod = TBGContext::getModule(TBGSettings::getAuthenticationBackend());
						if ($mod->getType() !== TBGModule::MODULE_AUTH)
						{
							TBGLogging::log('Auth module is not the right type', 'auth', TBGLogging::LEVEL_FATAL);
						}

						$row = $mod->doAutoLogin();
						
						if(!$row)
						{
							// Invalid
							TBGContext::logout();
							throw new Exception('No such login');
							//TBGContext::getResponse()->headerRedirect(TBGContext::getRouting()->generate('login'));
						}
					}
					catch (Exception $e)
					{
						throw $e;
					}
				}
				elseif ($username !== null && $password !== null)
				{
					$external = false;
					TBGLogging::log('Using internal authentication', 'auth', TBGLogging::LEVEL_INFO);
					// First test a pre-encrypted password
					$row = TBGUsersTable::getTable()->getByUsernameAndPassword($username, $password);

					if (!$row)
					{
						// Then test an unencrypted password
						$row = TBGUsersTable::getTable()->getByUsernameAndPassword($username, self::hashPassword($password));

						if(!$row)
						{
							// This is a legacy account from a 3.1 upgrade - try sha1 salted
							$salt = TBGSettings::getPasswordSalt();
							$row = TBGUsersTable::getTable()->getByUsernameAndPassword($username, sha1($password.$salt));
							if(!$row)
							{
								// Invalid
								TBGContext::logout();
								throw new Exception('No such login');
								//TBGContext::getResponse()->headerRedirect(TBGContext::getRouting()->generate('login'));
							}
							else 
							{
								// convert sha1 to new password type
								$user = new TBGUser($row->get(TBGUsersTable::ID), $row);
								$user->changePassword($password);
								$user->save();
								unset($user);
							}
						}
					}
				}
				elseif (TBGContext::isCLI())
				{
					$row = TBGUsersTable::getTable()->getByUsername(TBGContext::getCurrentCLIusername());
				
				}
				// guest user
				elseif (!TBGSettings::isLoginRequired())
				{
					$row = TBGUsersTable::getTable()->getByUserID(TBGSettings::getDefaultUserID());
				}

				if ($row)
				{
					if (!$row->get(TBGScopesTable::ENABLED))
					{
						throw new Exception('This account belongs to a scope that is not active');
					}
					elseif (!$row->get(TBGUsersTable::ACTIVATED))
					{
						throw new Exception('This account has not been activated yet');
					}
					elseif (!$row->get(TBGUsersTable::ENABLED))
					{
						throw new Exception('This account has been suspended');
					}
					$user = TBGContext::factory()->TBGUser($row->get(TBGUsersTable::ID), $row);
					
					if ($external == false)
					{
						if ($raw == false)
						{
							TBGContext::getResponse()->setCookie('tbg3_password', $password);
						}
						else
						{
							TBGContext::getResponse()->setCookie('tbg3_password', TBGUser::hashPassword($password));
						}
						TBGContext::getResponse()->setCookie('tbg3_username', $username);
					}
				}
				elseif (TBGSettings::isLoginRequired())
				{
					throw new Exception('Login required');
				}
				else
				{
					throw new Exception('No such login');
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			return $user;
	
		}
		
		/**
		 * Create and return a temporary password
		 * 
		 * @return string
		 */
		public static function createPassword($len = 8)
		{
			$pass = '';
			$lchar = 0;
			$char = 0;
			for($i = 0; $i < $len; $i++)
			{
				while($char == $lchar)
				{
					$char = mt_rand(48, 109);
					if($char > 57) $char += 7;
					if($char > 90) $char += 6;
				}
				$pass .= chr($char);
				$lchar = $char;
			}
			return $pass;
		}

		public static function getUsersCount()
		{
			if (self::$_num_users === null)
			{
				self::$_num_users = TBGUsersTable::getTable()->countUsers();
			}

			return self::$_num_users;
		}

		/**
		 * Pre-save function to check for conflicting usernames and to make
		 * sure some properties are set
		 * 
		 * @param boolean $is_new Whether this is a new user object
		 */
		protected function _preSave($is_new)
		{
			$compare_user = self::getByUsername($this->getUsername(), $this->getScope());
			if ($compare_user instanceof TBGUser && $compare_user->getID() && $compare_user->getID() != $this->getID())
			{
				throw new Exception(TBGContext::getI18n()->__('This username already exists'));
			}
			if (!$this->_realname)
			{
				$this->_realname = $this->_username;
			}
			if (!$this->_buddyname)
			{
				$this->_buddyname = $this->_username;
			}
			if (!$this->_group_id)
			{
				$this->setGroup(TBGSettings::getDefaultGroup());
			}
			if ($this->_deleted)
			{
				try
				{
					if ($this->getGroup() instanceof TBGGroup)
					{
						$this->getGroup()->removeMember($this);
					}
				}
				catch (Exception $e) {}
				
				$this->_group_id = null;
				TBGTeamMembersTable::getTable()->clearTeamsByUserID($this->getID());
				TBGClientMembersTable::getTable()->clearClientsByUserID($this->getID());
			}
		}

		/**
		 * Performs post-save actions on user objects
		 * 
		 * This includes firing off events for modules to listen to (e.g. so 
		 * activation emails can be sent out), and setting up a default 
		 * dashboard for the new user.
		 * 
		 * @param boolean $is_new Whether this is a new object or not (automatically passed to the function from B2DB)
		 */
		protected function _postSave($is_new)
		{
			if ($is_new)
			{
				$event = TBGEvent::createNew('core', 'TBGUser::createNew', $this);
				$event->trigger();
				
				// If the event isn't processed we automatically enable the user
				// since we can be sure no activation email has been sent out
				if (!$event->isProcessed())
				{
					$this->setEnabled();
					$this->setActivated();
					$this->save();
				}
				
				// Set up a default dashboard for the user
				TBGDashboardViewsTable::getTable()->setDefaultViews($this->getID(), TBGDashboardViewsTable::TYPE_USER);
			}
			
			if ($this->_timezone !== null)
			{
				TBGSettings::saveSetting('timezone', $this->_timezone, 'core', null, $this->getID());
			}
			else
			{
				TBGSettings::saveSetting('timezone', 'sys', 'core', null, $this->getID());
			}
			
			if ($this->_language != null)
			{
				TBGSettings::saveSetting('language', $this->_language, 'core', null, $this->getID());
			}
			else
			{
				TBGSettings::saveSetting('language', 'sys', 'core', null, $this->getID());
			}
		}
		
		/**
		 * Returns whether the current user is a guest or not
		 * 
		 * @return boolean
		 */
		public static function isThisGuest()
		{
			if (TBGContext::getUser() instanceof TBGUser)
			{
				return TBGContext::getUser()->isGuest();
			}
			else
			{
				return true;
			}
		}
		
		/**
		 * Class constructor
		 *
		 * @param \b2db\Row $row
		 */
		public function _construct(\b2db\Row $row, $foreign_key = null)
		{
			TBGLogging::log("User with id {$this->getID()} set up successfully");
		}
		
		/**
		 * Retrieve the users real name
		 * 
		 * @return string
		 */
		public function getName()
		{
			if ($this->isDeleted())
			{
				return __('No such user');
			}
			return ($this->_buddyname) ? $this->_buddyname : (($this->_realname) ? $this->_realname : $this->_username);
		}
		
		/**
		 * Retrieve the users id
		 * 
		 * @return integer
		 */
		public function getID()
		{
			return $this->_id;
		}
		
		/**
		 * Retrieve this users realname and username combined 
		 * 
		 * @return string "Real Name (username)"
		 */
		public function getNameWithUsername()
		{
			if ($this->isDeleted())
			{
				return __('No such user');
			}
			return ($this->_buddyname) ? $this->_buddyname . ' (' . $this->_username . ')' : $this->_username;
		}
		
		public function __toString()
		{
			return $this->getNameWithUsername();
		}

		/**
		 * Whether this user is authenticated or just an authenticated guest
		 * 
		 * @return boolean
		 */
		public function isAuthenticated()
		{
			return (bool) ($this->getID() == TBGContext::getUser()->getID());
		}
		
		/**
		 * Set users "last seen" property to NOW
		 */
		public function updateLastSeen()
		{
			$this->_lastseen = NOW;
		}
		
		/**
		 * Return timestamp for when this user was last online
		 * 
		 * @return integer
		 */
		public function getLastSeen()
		{
			return $this->_lastseen;
		}
		
		/**
		 * Marks this user with the Online user state
		 */
		public function setOnline()
		{
			$this->_userstate = TBGSettings::getOnlineState();
			$this->_customstate = !$this->isOffline();
		}

		/**
		 * Marks this user with the Offline user state
		 */
		public function setOffline()
		{
			$this->_userstate = TBGSettings::getOfflineState();
			$this->_customstate = true;
			$this->save();
		}
		
		/**
		 * Retrieve the timestamp for when this user joined
		 * 
		 * @return integer
		 */
		public function getJoinedDate()
		{
			return $this->_joined;
		}
		
		/**
		 * Populates team array when needed
		 */
		protected function _populateTeams()
		{
			if ($this->teams === null)
			{
				$this->teams = array('assigned' => array(), 'ondemand' => array());
				TBGLogging::log('Populating user teams');
				if ($res = TBGTeamMembersTable::getTable()->getTeamIDsForUserID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$team = TBGContext::factory()->TBGTeam($row->get(TBGTeamsTable::ID), $row);
						if ($team->isOndemand())
						{
							$this->teams['ondemand'][$team->getID()] = $team;
						}
						else
						{
							$this->teams['assigned'][$team->getID()] = $team;
						}
					}
				}
				TBGLogging::log('...done (Populating user teams)');
			}
		}
		
		/**
		 * Checks if the user is a member of the given team
		 *
		 * @param TBGTeam $team
		 * 
		 * @return boolean
		 */
		public function isMemberOfTeam(TBGTeam $team)
		{
			$this->_populateTeams();
			return (array_key_exists($team->getID(), $this->teams['assigned']) || array_key_exists($team->getID(), $this->teams['ondemand']));
		}
		
		/**
		 * Populates client array when needed
		 *
		 */
		protected function _populateClients()
		{
			if ($this->clients === null)
			{
				$this->clients = array();
				TBGLogging::log('Populating user clients');
				if ($res = TBGClientMembersTable::getTable()->getClientIDsForUserID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->clients[$row->get(TBGClientsTable::ID)] = TBGContext::factory()->TBGClient($row->get(TBGClientsTable::ID), $row);
					}
				}
				TBGLogging::log('...done (Populating user clients)');
			}
		}
	
		/**
		 * Checks if the user is a member of the given client
		 *
		 * @param TBGClient $client
		 * 
		 * @return boolean
		 */
		public function isMemberOfClient(TBGClient $client)
		{
			$this->_populateClients();
			return array_key_exists($client->getID(), $this->clients);
		}

		/**
		 * Return all this user's clients
		 *
		 * @return array
		 */
		public function getClients()
		{
			$this->_populateClients();
			return $this->clients;
		}
		
		/**
		 * Checks whether or not the user is logged in
		 *
		 * @return boolean
		 */
		public function isLoggedIn()
		{
			return ($this->_id != 0) ? true : false;
		}
		
		/**
		 * Checks whether or not the current user is a "regular" or "guest" user
		 *
		 * @return boolean
		 */
		public function isGuest()
		{
			return (bool) (!$this->isLoggedIn() || ($this->getID() == TBGSettings::getDefaultUserID() && TBGSettings::isDefaultUserGuest()));
		}
	
		/**
		 * Returns an array of issue ids which are directly assigned to the current user
		 *
		 * @return array
		 */
		public function getUserAssignedIssues()
		{
			if ($this->userassigned === null)
			{
				$this->userassigned = array();
				if ($res = TBGIssuesTable::getTable()->getOpenIssuesByUserAssigned($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->userassigned[$row->get(TBGIssuesTable::ID)] = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID), $row);
					}
					ksort($this->userassigned, SORT_NUMERIC);
				}
			}
			return $this->userassigned;
		}
	
		/**
		 * Returns an array of issue ids assigned to the given team
		 *
		 * @param integer $team_id The team id
		 * @return array
		 */
		public function getUserTeamAssignedIssues($team_id)
		{
			if (!array_key_exists($team_id, $this->teamassigned))
			{
				$this->teamassigned[$team_id] = array();
				if ($res = TBGIssuesTable::getTable()->getOpenIssuesByTeamAssigned($team_id))
				{
					while ($row = $res->getNextRow())
					{
						$this->teamassigned[$team_id][$row->get(TBGIssuesTable::ID)] = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID), $row);
					}
				}
				ksort($this->teamassigned[$team_id], SORT_NUMERIC);
			}
			return $this->teamassigned[$team_id];
		}

		/**
		 * Populate the array of starred issues
		 */
		protected function _populateStarredIssues()
		{
			if ($this->_starredissues === null)
			{
				$this->_starredissues = array();
				if ($res = \b2db\Core::getTable('TBGUserIssuesTable')->getUserStarredIssues($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_starredissues[$row->get(TBGIssuesTable::ID)] = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID), $row);
					}
					ksort($this->_starredissues, SORT_NUMERIC);
				}
			}
		}
		
		/**
		 * Returns an array of issues ids which are "starred" by this user
		 *
		 * @return array
		 */
		public function getStarredIssues()
		{
			$this->_populateStarredIssues();
			return $this->_starredissues;
		}
		
		/**
		 * Returns whether or not an issue is starred
		 * 
		 * @param integer $issue_id The issue ID to check
		 * 
		 * @return boolean
		 */
		public function isIssueStarred($issue_id)
		{
			$this->_populateStarredIssues();
			return array_key_exists($issue_id, $this->_starredissues);
		}
		
		/**
		 * Adds an issue to the list of issues "starred" by this user 
		 *
		 * @param integer $issue_id ID of issue to add
		 * @return boolean
		 */
		public function addStarredIssue($issue_id)
		{
			$this->_populateStarredIssues();
			TBGLogging::log("Starring issue with id {$issue_id} for user with id " . $this->getID());
			if ($this->isLoggedIn() == true && $this->isGuest() == false)
			{
				if (array_key_exists($issue_id, $this->_starredissues))
				{
					TBGLogging::log('Already starred');
					return true;
				}
				TBGLogging::log('Logged in and unstarred, continuing');
				$crit = new \b2db\Criteria();
				$crit->addInsert(TBGUserIssuesTable::ISSUE, $issue_id);
				$crit->addInsert(TBGUserIssuesTable::UID, $this->_id);
				$crit->addInsert(TBGUserIssuesTable::SCOPE, TBGContext::getScope()->getID());
				
				\b2db\Core::getTable('TBGUserIssuesTable')->doInsert($crit);
				$issue = TBGContext::factory()->TBGIssue($issue_id);
				$this->_starredissues[$issue->getID()] = $issue;
				ksort($this->_starredissues);
				TBGLogging::log('Starred');
				return true;
			}
			else
			{
				TBGLogging::log('Not logged in');
				return false;
			}
		}
	
		/**
		 * Removes an issue from the list of flagged issues
		 *
		 * @param integer $issue_id ID of issue to remove
		 */
		public function removeStarredIssue($issue_id)
		{
			$crit = new \b2db\Criteria();
			$crit->addWhere(TBGUserIssuesTable::ISSUE, $issue_id);
			$crit->addWhere(TBGUserIssuesTable::UID, $this->_id);
				
			\b2db\Core::getTable('TBGUserIssuesTable')->doDelete($crit);
			unset($this->_starredissues[$issue_id]);
			return true;
		}
	
		/**
		 * Sets up the internal friends array
		 */
		protected function _setupFriends()
		{
			if ($this->_friends === null)
			{
				$this->_friends = array();
				if ($res = TBGBuddiesTable::getTable()->getFriendsByUserID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_friends[$row->get(TBGBuddiesTable::BID)] = TBGContext::factory()->TBGUser($row->get(TBGBuddiesTable::BID));
					}
				}
			}
		}

		/**
		 * Adds a friend to the buddy list
		 *
		 * @param TBGUser $user Friend to add
		 * 
		 * @return boolean
		 */
		public function addFriend($user)
		{
			if (!($this->isFriend($user)) && !$user->isDeleted())
			{
				TBGBuddiesTable::getTable()->addFriend($this->getID(), $user->getID());
				$this->_friends[$user->getID()] = $user;
				return true;
			}
			else
			{
				return false;
			}
		}
	
		/**
		 * Get all this users friends
		 *
		 * @return array An array of TBGUsers
		 */
		public function getFriends()
		{
			$this->_setupFriends();
			return $this->_friends;
		}
		
		/**
		 * Removes a user from the list of buddies
		 *
		 * @param TBGUser $user User to remove
		 */
		public function removeFriend($user)
		{
			TBGBuddiesTable::getTable()->removeFriendByUserID($this->getID(), $user->getID());
			if (is_array($this->_friends))
			{
				unset($this->_friends[$user->getID()]);
			}
		}
	
		/**
		 * Check if the given user is a friend of this user
		 *
		 * @param TBGUser $user The user to check
		 * 
		 * @return boolean
		 */
		public function isFriend($user)
		{
			$this->_setupFriends();
			if (empty($this->_friends)) return false;
			return array_key_exists($user->getID(), $this->_friends);
		}
	
		/**
		 * Change the password to a new password
		 *
		 * @param string $newpassword
		 */
		public function changePassword($newpassword)
		{
			$this->_password = self::hashPassword($newpassword);
		}
		
		/**
		 * Alias for changePassword
		 * 
		 * @param string $newpassword
		 * 
		 * @see self::changePassword
		 */
		public function setPassword($newpassword)
		{
			return $this->changePassword($newpassword);
		}
		
		/**
		 * Set the user state to this state 
		 *
		 * @param integer $s_id
		 * @return nothing
		 */
		public function setState(TBGUserstate $state)
		{
			$this->_userstate = $state;
		}
		
		/**
		 * Whether this user is currently active on the site
		 * 
		 * @return boolean
		 */
		public function isActive()
		{
			return (bool) ($this->_lastseen > (NOW - (60 * 10)));
		}
		
		/**
		 * Whether this user is currently inactive (but not logged out) on the site
		 * 
		 * @return boolean
		 */
		public function isAway()
		{
			return (bool) (($this->_lastseen < (NOW - (60 * 10))) && ($this->_lastseen > (NOW - (60 * 30))));
		}
		
		/**
		 * Whether this user is currently offline (timed out or explicitly logged out)
		 * 
		 * @return boolean
		 */
		public function isOffline()
		{
			if ($this->_customstate)
			{
				return (!$this->getState() instanceof TBGUserState) ? false : !$this->getState()->isOnline();
			}
			elseif ($this->_lastseen < (NOW - (60 * 30)))
			{
				return true;
			}
			else
			{
				return (!$this->getState() instanceof TBGUserState) ? false : !$this->getState()->isOnline();
			}
		}
		
		/**
		 * Get the current user state
		 *
		 * @return TBGUserstate
		 */
		public function getState()
		{
			if ($this->_customstate)
			{
				return $this->_getPopulatedObjectFromProperty('_userstate');
			}
			
			if ($this->isActive())
				return TBGSettings::getOnlineState();
			elseif ($this->isAway())
				return TBGSettings::getAwayState();
			else
				return TBGSettings::getOfflineState();
		}
		
		/**
		 * Whether this user is enabled or not
		 * 
		 * @return boolean
		 */
		public function isEnabled()
		{
			return $this->_enabled;
		}

		/**
		 * Set whether this user is activated or not
		 * 
		 * @param boolean $val[optional] 
		 */
		public function setActivated($val = true)
		{
			$this->_activated = (boolean) $val;
		}

		/**
		 * Whether this user is activated or not
		 * 
		 * @return boolean
		 */
		public function isActivated()
		{
			return $this->_activated;
		}
		
		/**
		 * Whether this user is deleted or not
		 * 
		 * @return boolean
		 */
		public function isDeleted()
		{
			return $this->_deleted;
		}

		public function markAsDeleted()
		{
			$this->_deleted = true;
		}
		
		/**
		 * Returns an array of teams which the current user is a member of
		 *
		 * @return array
		 */
		public function getTeams()
		{
			$this->_populateTeams();
			return $this->teams['assigned'];
		}
		
		/**
		 * Returns an array of teams which the current user is a member of
		 *
		 * @return array
		 */
		public function getOndemandTeams()
		{
			$this->_populateTeams();
			return $this->teams['ondemand'];
		}
		
		/**
		 * Clear this users teams
		 */
		public function clearTeams()
		{
			\b2db\Core::getTable('TBGTeamMembersTable')->clearTeamsByUserID($this->getID());
		}
		
		/**
		 * Clear this users clients
		 */
		public function clearClients()
		{
			\b2db\Core::getTable('TBGClientMembersTable')->clearClientsByUserID($this->getID());
		}
		
		/**
		 * Add this user to a team
		 * 
		 * @param TBGTeam $team 
		 */
		public function addToTeam(TBGTeam $team)
		{
			$team->addMember($this);
			$this->teams = null;
		}

		/**
		 * Add this user to a client
		 * 
		 * @param TBGClient $client 
		 */
		public function addToClient(TBGClient $client)
		{
			$client->addMember($this);
			$this->clients = null;
		}

		/**
		 * Return the identifiable type
		 * 
		 * @return integer
		 */
		public function getType()
		{
			return TBGIdentifiableClass::TYPE_USER;
		}
		
		/**
		 * Set whether or not the email address is hidden for normal users
		 *
		 * @param boolean $val
		 */
		public function setEmailPrivate($val)
		{
			$this->_private_email = (bool) $val;
		}
		
		/**
		 * Returns whether or not the email address is private
		 *
		 * @return boolean
		 */
		public function isEmailPrivate()
		{
			return $this->_private_email;
		}

		/**
		 * Returns whether or not the email address is public
		 *
		 * @return boolean
		 */
		public function isEmailPublic()
		{
			return !$this->_private_email;
		}
		
		/**
		 * Returns the user group
		 *
		 * @return TBGGroup
		 */
		public function getGroup()
		{
			return $this->_group_id;
		}

		/**
		 * Return this users group ID if any
		 * 
		 * @return integer
		 */
		public function getGroupID()
		{
			if (is_object($this->getGroup()))
			{
				return $this->getGroup()->getID();
			}
			elseif (is_numeric($this->getGroup()))
			{
				return $this->getGroup();
			}

			return null;
		}
		
		/**
		 * Set this users group
		 * 
		 * @param TBGGroup $group 
		 */
		public function setGroup(TBGGroup $group)
		{
			$this->_group_id = $group;
		}
		
		/**
		 * Set the username
		 *
		 * @param string $username
		 */
		public function setUsername($username)
		{
			$this->_username = $username;
		}

		/**
		 * Return this users' username
		 * 
		 * @return string
		 */
		public function getUsername()
		{
			return $this->_username;
		}
		
		/**
		 * Returns a hash of the user password
		 *
		 * @return string
		 */
		public function getHashPassword()
		{
			return $this->_password;
		}

		/**
		 * Return whether or not the users password is this
		 *
		 * @param string $password Unhashed password
		 *
		 * @return boolean
		 */
		public function hasPassword($password)
		{
			return $this->hasPasswordHash(self::hashPassword($password));
		}

		/**
		 * Return whether or not the users password is this
		 *
		 * @param string $password Hashed password
		 *
		 * @return boolean
		 */
		public function hasPasswordHash($password)
		{
			return (bool) ($password == $this->getHashPassword());
		}

		/**
		 * Returns the real name (full name) of the user
		 *
		 * @return string
		 */
		public function getRealname()
		{
			return $this->_realname;
		}
		
		/**
		 * Returns the buddy name (friendly name) of the user
		 *
		 * @return string
		 */
		public function getBuddyname()
		{
			return $this->_buddyname;
		}

		/**
		 * Return the users nickname (buddyname)
		 *
		 * @uses self::getBuddyname()
		 *
		 * @return string
		 */
		public function getNickname()
		{
			return $this->getBuddyname();
		}

		public function getDisplayName()
		{
			return ($this->getRealname() == '') ? $this->getBuddyname() : $this->getRealname();
		}
		
		/**
		 * Returns the email of the user
		 *
		 * @return string
		 */
		public function getEmail()
		{
			return $this->_email;
		}
		
		/**
		 * Returns the users homepage
		 *
		 * @return unknown
		 */
		public function getHomepage()
		{
			return $this->_homepage;
		}

		/**
		 * Set this users homepage
		 *
		 * @param string $homepage
		 */
		public function setHomepage($homepage)
		{
			$this->_homepage = $homepage;
		}
		
		/**
		 * Set the avatar image
		 *
		 * @param string $avatar
		 */
		public function setAvatar($avatar)
		{
			$this->_avatar = $avatar;
		}
		
		/**
		 * Returns the avatar of the user
		 *
		 * @return string
		 */
		public function getAvatar()
		{
			return ($this->_avatar != '') ? $this->_avatar : 'user';
		}
		
		/**
		 * Return the users avatar url
		 * 
		 * @param boolean $small[optional] Whether to get the URL for the small avatar (default small)
		 * 
		 * @return string an URL to put in an <img> tag
		 */
		public function getAvatarURL($small = true)
		{
			$url = '';
			if ($this->usesGravatar() && $this->getEmail())
			{
				$url = 'http://www.gravatar.com/avatar/' . md5(trim($this->getEmail())) . '.png?d=wavatar&amp;s=';
				$url .= ($small) ? 22 : 48; 
			}
			else
			{
				$url = TBGContext::getTBGPath() . 'avatars/' . $this->getAvatar();
				if ($small) $url .= '_small';
				$url .= '.png';
			}
			return $url;
		}
		
		/**
		 * Return whether the user uses gravatar for avatars
		 * 
		 * @return boolean
		 */
		public function usesGravatar()
		{
			if (!TBGSettings::isGravatarsEnabled()) return false;
			if ($this->isGuest()) return false;
			return (bool) $this->_use_gravatar;
		}
		
		/**
		 * Set the users email address
		 *
		 * @param string $email A valid email address
		 */
		public function setEmail($email)
		{
			$this->_email = $email;
		}

		/**
		 * Set the users realname
		 *
		 * @param string $realname
		 */
		public function setRealname($realname)
		{
			$this->_realname = $realname;
		}

		/**
		 * Set the users buddyname
		 *
		 * @param string $buddyname
		 */
		public function setBuddyname($buddyname)
		{
			$this->_buddyname = $buddyname;
		}

		/**
		 * Set whether the user uses gravatar
		 *
		 * @param string $val
		 */
		public function setUsesGravatar($val)
		{
			$this->_use_gravatar = (bool) $val;
		}

		/**
		 * Set whether this user is enabled or not
		 * 
		 * @param boolean $val[optional]
		 */
		public function setEnabled($val = true)
		{
			$this->_enabled = $val;
		}
		
		/**
		 * Set whether this user is validated or not
		 * 
		 * @param boolean $val[optional]
		 */
		public function setValidated($val = true)
		{
			$this->_activated = $val;
		}
		
		/**
		 * Set the user's joined date
		 * 
		 * @param integer $val[optional]
		 */
		public function setJoined($val = null)
		{
			if ($val === null)
			{
				$val = time();
			}
			$this->_joined = $val;
		}
		
		/**
		 * Find one user based on details
		 * 
		 * @param string $details Any user detail (email, username, realname or buddyname)
		 * 
		 * @return TBGUser
		 */
		public static function findUser($details)
		{
			$res = TBGUsersTable::getTable()->getByDetails($details);

			if (!$res || $res->count() > 1) return false;
			$row = $res->getNextRow();
			
			return TBGContext::factory()->TBGUser($row->get(TBGUsersTable::ID), $row);
		}

		/**
		 * Find users based on details
		 * 
		 * @param string $details Any user detail (email, username, realname or buddyname)
		 * @param integer $limit[optional] an optional limit on the number of results
		 * 
		 * @return array
		 */
		public static function findUsers($details, $limit = null)
		{
			$retarr = array();
			
			if ($res = TBGUsersTable::getTable()->getByDetails($details))
			{
				while ($row = $res->getNextRow())
				{
					$retarr[$row->get(TBGUsersTable::ID)] = TBGContext::factory()->TBGUser($row->get(TBGUsersTable::ID), $row);
				}
			}
			return $retarr;
		}
	
		/**
		 * Perform a permission check on this user
		 * 
		 * @param string $permission_type The permission key
		 * @param integer $target_id[optional] a target id if applicable
		 * @param string $module_name[optional] the module for which the permission is valid
		 * @param boolean $explicit[optional] whether to check for an explicit permission and return false if not set
		 * @param boolean $permissive[optional] whether to return false or true when explicit fails
		 * 
		 * @return boolean
		 */
		public function hasPermission($permission_type, $target_id = 0, $module_name = 'core', $explicit = false, $permissive = false)
		{
			TBGLogging::log('Checking permission '.$permission_type);
			$group_id = ($this->getGroup() instanceof TBGGroup) ? $this->getGroup()->getID() : 0;
			$retval = TBGContext::checkPermission($permission_type, $this->getID(), $group_id, $this->getTeams(), $target_id, $module_name, $explicit, $permissive);
			TBGLogging::log('...done (Checking permissions '.$permission_type.') - return was '.(($retval) ? 'true' : 'false'));
			
			return $retval;
		}

		/**
		 * Whether this user can access the specified module
		 * 
		 * @param string $module The module key
		 * 
		 * @return boolean
		 */
		public function hasModuleAccess($module)
		{
			return TBGContext::getModule($module)->hasAccess($this->getID());
		}
	
		/**
		 * Whether this user can access the specified page
		 * 
		 * @param string $page The page key
		 * 
		 * @return boolean
		 */
		public function hasPageAccess($page, $target_id = null, $explicit = true, $permissive = null)
		{
			$permissive = (isset($permissive)) ? $permissive : TBGSettings::isPermissive();
			if ($target_id === null)
			{
				$retval = $this->hasPermission("page_{$page}_access", 0, "core", $explicit, $permissive);
				return $retval;
			}
			else
			{
				$retval = $this->hasPermission("page_{$page}_access", $target_id, "core", true, $permissive);
				return ($retval === null) ? $this->hasPermission("page_{$page}_access", 0, "core", true, $permissive) : $retval;
			}
		}
		
		/**
		 * Check whether the user can access the specified project page
		 * 
		 * @param string $page The page key
		 * @param integer $project_id
		 * 
		 * @return boolean 
		 */
		public function hasProjectPageAccess($page, $project_id)
		{
			return (bool) ($this->hasPageAccess($page, $project_id) || $this->hasPageAccess('project_allpages', $project_id)); 
		}

		/**
		 * Get this users timezone
		 *
		 * @return mixed
		 */
		public function getTimezone()
		{
			if ($this->_timezone == null)
			{
				$this->_timezone = TBGSettings::get('timezone', 'core', null, $this->getID());
			}
			return $this->_timezone;
		}

		/**
		 * Set this users timezone
		 *
		 * @param integer $timezone
		 */
		public function setTimezone($timezone)
		{
			$this->_timezone = $timezone;
		}

		/**
		 * Return if the user can report new issues
		 *
		 * @param integer $product_id[optional] A product id
		 * @return boolean
		 */
		public function canReportIssues($project_id = null)
		{
			$retval = null;
			if ($project_id !== null)
			{
				if (is_numeric($project_id)) $project_id = TBGContext::factory()->TBGProject($project_id);
			
				if ($project_id->isArchived()): return false; endif;
				
				$project_id = ($project_id instanceof TBGProject) ? $project_id->getID() : $project_id;
				$retval = $this->hasPermission('cancreateissues', $project_id, 'core', true, null);
				$retval = ($retval !== null) ? $retval : $this->hasPermission('cancreateandeditissues', $project_id, 'core', true, null);
			}
			$retval = ($retval !== null) ? $retval : $this->hasPermission('cancreateissues', 0, 'core', true, null);
			$retval = ($retval !== null) ? $retval : $this->hasPermission('cancreateandeditissues', 0, 'core', true, null);
			
			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}

		/**
		 * Return if the user can search for issues
		 *
		 * @return boolean
		 */
		public function canSearchForIssues()
		{
			return (bool) ($this->hasPermission('canfindissues') || $this->hasPermission('canfindissuesandsavesearches'));
		}

		/**
		 * Return if the user can edit the main menu
		 *
		 * @return boolean
		 */
		public function canEditMainMenu()
		{
			return (bool) ($this->hasPermission('caneditmainmenu'));
		}

		/**
		 * Return if the user can see comments
		 *
		 * @return boolean
		 */
		public function canViewComments()
		{
			return (bool) ($this->hasPermission('canviewcomments') || $this->hasPermission('canpostandeditcomments'));
		}

		/**
		 * Return if the user can post comments
		 *
		 * @return boolean
		 */
		public function canPostComments()
		{
			return (bool) ($this->hasPermission('canpostcomments') || $this->hasPermission('canpostandeditcomments'));
		}

		/**
		 * Return if the user can see non public comments
		 *
		 * @return boolean
		 */
		public function canSeeNonPublicComments()
		{
			return (bool) ($this->hasPermission('canseenonpubliccomments') || $this->hasPermission('canpostseeandeditallcomments'));
		}

		/**
		 * Return if the user can create public saved searches
		 *
		 * @return boolean
		 */
		public function canCreatePublicSearches()
		{
			return (bool) ($this->hasPermission('cancreatepublicsearches') || $this->hasPermission('canfindissuesandsavesearches'));
		}

		/**
		 * Return whether the user can access a saved search
		 *
		 * @param B2DBrow $savedsearch
		 * 
		 * @return boolean
		 */
		public function canAccessSavedSearch($savedsearch)
		{
			return (bool) ($savedsearch->get(TBGSavedSearchesTable::IS_PUBLIC) || $savedsearch->get(TBGSavedSearchesTable::UID) == $this->getID());
		}

		/**
		 * Return if the user can access configuration pages
		 *
		 * @param integer $section[optional] a section, or the configuration frontpage
		 * 
		 * @return boolean
		 */
		public function canAccessConfigurationPage($section = null)
		{
			return (bool) ($this->hasPermission('canviewconfig', $section, 'core', true) || $this->hasPermission('cansaveconfig', $section, 'core', true) || $this->hasPermission('canviewconfig', 0, 'core', true) || $this->hasPermission('cansaveconfig', 0, 'core', true));
		}

		/**
		 * Return if the user can save configuration in a section
		 *
		 * @return boolean
		 */
		public function canSaveConfiguration($section, $module = 'core')
		{
			return (bool) ($this->hasPermission('cansaveconfig', $section, $module, true) || $this->hasPermission('cansaveconfig', 0, $module, true));
		}

		/**
		 * Return if the user can manage a project
		 *
		 * @param TBGProject $project
		 * 
		 * @return boolean
		 */
		public function canManageProject(TBGProject $project)
		{
			return (bool) $this->hasPermission('canmanageproject', $project->getID());
		}

		/**
		 * Return if the user can manage releases for a project
		 *
		 * @param TBGProject $project
		 *
		 * @return boolean
		 */
		public function canManageProjectReleases(TBGProject $project)
		{
			if ($project->isArchived()): return false; endif;
			return (bool) ($this->hasPermission('canmanageprojectreleases', $project->getID()) || $this->hasPermission('canmanageproject', $project->getID()));
		}

		/**
		 * Return if the user can edit project details and settings
		 *
		 * @param TBGProject $project
		 *
		 * @return boolean
		 */
		public function canEditProjectDetails(TBGProject $project)
		{
			if ($project->isArchived()): return false; endif;
			return (bool) ($this->hasPermission('caneditprojectdetails', $project->getID(), 'core', true) || $this->hasPermission('canmanageproject', $project->getID(), 'core', true));
		}

		/**
		 * Return if the user can add scrum user stories
		 *
		 * @param TBGProject $project
		 *
		 * @return boolean
		 */
		public function canAddScrumUserStories(TBGProject $project)
		{
			if ($project->isArchived()): return false; endif;
			return (bool) ($this->hasPermission('canaddscrumuserstories', $project->getID(), 'core', true) || $this->hasPermission('candoscrumplanning', $project->getID(), 'core', true) || $this->hasPermission('canaddscrumuserstories', 0, 'core', true) || $this->hasPermission('candoscrumplanning', 0, 'core', true));
		}

		/**
		 * Return if the user can add scrum sprints
		 *
		 * @param TBGProject $project
		 *
		 * @return boolean
		 */
		public function canAddScrumSprints(TBGProject $project)
		{
			if ($project->isArchived()): return false; endif;
			return (bool) ($this->hasPermission('canaddscrumsprints', $project->getID(), 'core', true) || $this->hasPermission('candoscrumplanning', $project->getID(), 'core', true) || $this->hasPermission('canaddscrumsprints', 0, 'core', true) || $this->hasPermission('candoscrumplanning', 0, 'core', true));
		}

		/**
		 * Return if the user can assign scrum user stories
		 *
		 * @param TBGProject $project
		 *
		 * @return boolean
		 */
		public function canAssignScrumUserStories(TBGProject $project)
		{
			if ($project->isArchived()): return false; endif;
			return (bool) ($this->hasPermission('canassignscrumuserstoriestosprints', $project->getID(), 'core', true) || $this->hasPermission('candoscrumplanning', $project->getID(), 'core', true) || $this->hasPermission('canassignscrumuserstoriestosprints', 0, 'core', true) || $this->hasPermission('candoscrumplanning', 0, 'core', true));
		}

		/**
		 * Return if the user can change its own password
		 *
		 * @param TBGProject $project
		 *
		 * @return boolean
		 */
		public function canChangePassword()
		{
			return (bool) ($this->hasPermission('canchangepassword', 0, 'core', true, $this->hasPermission('page_account_access', 0, 'core', true)));
		}
		
		/**
		 * Return a list of the users latest log items
		 * 
		 * @param integer $number Limit to a number of changes
		 * 
		 * @return array
		 */
		public function getLatestActions($number = 10)
		{
			if ($items = TBGLogTable::getTable()->getByUserID($this->getID(), $number))
			{
				return $items;
			}
			else
			{
				return array();
			}
		}

		/**
		 * Clears the associated projects cache (useful only when you know that you've changed assignees this same request
		 * 
		 * @return null
		 */
		public function clearAssociatedProjectsCache()
		{
			$this->_associated_projects = null;
		}
		
		/**
		 * Get all the projects a user is associated with
		 * 
		 * @return array
		 */
		public function getAssociatedProjects()
		{
			if ($this->_associated_projects === null)
			{
				$this->_associated_projects = array();
				
				$projects = \b2db\Core::getTable('TBGProjectAssigneesTable')->getProjectsByUserID($this->getID());
				$edition_projects = \b2db\Core::getTable('TBGEditionAssigneesTable')->getProjectsByUserID($this->getID());
				$component_projects = \b2db\Core::getTable('TBGComponentAssigneesTable')->getProjectsByUserID($this->getID());
				$lo_projects = \b2db\Core::getTable('TBGProjectsTable')->getByUserID($this->getID());

				$project_ids = array_merge(array_keys($projects), array_keys($edition_projects), array_keys($component_projects), array_keys($lo_projects));

				foreach ($this->getTeams() as $team)
				{
					$projects_team = \b2db\Core::getTable('TBGProjectAssigneesTable')->getProjectsByTeamID($team->getID());
					$edition_projects_team = \b2db\Core::getTable('TBGEditionAssigneesTable')->getProjectsByTeamID($team->getID());
					$component_projects_team = \b2db\Core::getTable('TBGComponentAssigneesTable')->getProjectsByTeamID($team->getID());
					$project_ids = array_merge(array_keys($projects_team), array_keys($edition_projects_team), array_keys($component_projects_team), $project_ids);	
				}
				
				$project_ids = array_unique($project_ids);
				
				foreach ($project_ids as $project_id)
				{
					try
					{
						$this->_associated_projects[$project_id] = TBGContext::factory()->TBGProject($project_id);
					}
					catch (Exception $e) { }
				}
			}
			
			return $this->_associated_projects;
		}
		
		/**
		 * Return an array of issues that has changes pending
		 * 
		 * @return array
		 */
		public function getIssuesPendingChanges()
		{
			return TBGChangeableItem::getChangedItems('TBGIssue');
		}

		public function setLanguage($language)
		{
			$this->_language = $language;
		}

		public function getLanguage()
		{
			return ($this->_language != '') ? $this->_language : TBGSettings::getLanguage();
		}

		/**
		 * Return an array of issues that has changes pending
		 * 
		 * @param int $number number of issues to be retrieved
		 * 
		 * @return array
		 */		
		public function getIssues($number = null)
		{
			$retval = array();
			if ($res = \b2db\Core::getTable('TBGIssuesTable')->getIssuesPostedByUser($this->getID(), $number))
			{
				while ($row = $res->getNextRow())
				{
					$issue = TBGContext::factory()->TBGIssue($row->get(TBGIssuesTable::ID), $row);
					$retval[$issue->getID()] = $issue;
				}
			}
			
			return $retval;
		}
		
	}
