<?php

	/**
	 * User class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	class TBGUser extends TBGIdentifiableClass implements TBGIdentifiable 
	{
		/**
		 * Unique id (uid)
		 *
		 * @var integer
		 * @access protected
		 */
		protected $uid = 0;
		
		/**
		 * Unique username (login name)
		 *
		 * @var string
		 * @access protected
		 */
		protected $uname = '';
		
		/**
		 * Whether or not the user has authenticated
		 * 
		 * @var boolean
		 */
		protected $authenticated = false;
		
		/**
		 * MD5 hash of password
		 *
		 * @var string
		 * @access protected
		 */
		protected $pwd = '';
		
		/**
		 * The users scope
		 *
		 * @var TBGScope
		 * @access protected
		 */
		protected $scope = null;
		
		/**
		 * User real name
		 *
		 * @var string
		 * @access protected
		 */
		protected $realname = '';
		
		/**
		 * User short name (buddyname)
		 *
		 * @var string
		 * @access protected
		 */
		protected $buddyname = '';
		
		/**
		 * User email
		 *
		 * @var string
		 * @access protected
		 */
		protected $email = '';
		
		/**
		 * Is email private?
		 *
		 * @var boolean
		 * @access protected
		 */
		protected $private_email = true;
		
		/**
		 * The user state
		 *
		 * @var TBGDatatype
		 */
		protected $state = null;
		
		/**
		 * User homepage
		 *
		 * @var string
		 * @access protected
		 */
		protected $homepage = '';
		
		/**
		 * Array of team ids where the current user is a member
		 *
		 * @var array
		 * @access protected
		 */
		protected $teams = null;
		
		/**
		 * The users avatar
		 *
		 * @var string
		 * @access protected
		 */
		protected $avatar = null;
		
		/**
		 * Whether to use the users gravatar or not
		 * 
		 * @var boolean
		 */
		protected $_use_gravatar = null;
		
		/**
		 * The users login error - if any
		 *
		 * @var string
		 * @access protected
		 */
		protected $login_error = '';
		
		/**
		 * Array of issues to follow up
		 *
		 * @var array
		 * @access protected
		 */
		protected $_starredissues = null;
		
		/**
		 * Array of issues assigned to the user
		 *
		 * @var array
		 * @access protected
		 */
		protected $userassigned = null;
		
		/**
		 * Array of issues assigned to the users team(s)
		 *
		 * @var array
		 * @access protected
		 */
		protected $teamassigned = array();
		
		/**
		 * Whether or not to show followups
		 *
		 * @var boolean
		 * @access protected
		 */
		protected $showfollowups = true;
		
		/**
		 * Whether or not to show issues assigned to the user / teams
		 *
		 * @var boolean
		 * @access protected
		 */
		protected $showassigned = true;
		
		/**
		 * Array of saved searches to show on the frontpage
		 *
		 * @var array
		 * @access protected
		 */
		protected $indexsearches = array();
		
		/**
		 * The users group 
		 * 
		 * @var TBGGroup
		 */
		protected $group = null;
	
		/**
		 * The users customer, if any
		 * 
		 * @var TBGCustomer
		 */
		protected $customer = null;
	
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
		protected $lastseen = 0;

		/**
		 * The timezone this user is in
		 *
		 * @var integer
		 */
		protected $_timezone = null;

		protected $row = null;
		protected $_joined = 0;
		protected $_friends = null;
		
		protected $_isenabled = false;
		protected $_isactivated = false;
		protected $_isdeleted = false;
		
		public static function getUsersByVerified($activated)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tUsers::ACTIVATED, ($activated) ? 1 : 0);
			$res = B2DB::getTable('B2tUsers')->doSelect($crit);
			
			$users = array();
			while ($row = $res->getNextRow())
			{
				$users[] = array('id' => $row->get(B2tUsers::ID));
			}
			return $users;
		}

		public static function getUsersByEnabled($enabled)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tUsers::ENABLED, ($enabled) ? 1 : 0);
			$res = B2DB::getTable('B2tUsers')->doSelect($crit);
			
			$users = array();
			while ($row = $res->getNextRow())
			{
				$users[] = array('id' => $row->get(B2tUsers::ID));
			}
			return $users;
		}

		/**
		 * Retrieve a user by username
		 *
		 * @param string $username
		 *
		 * @return TBGUser
		 */
		public static function getByUsername($username)
		{
			if ($row = B2DB::getTable('B2tUsers')->getByUsername($username))
			{
				return TBGFactory::userLab($row->get(B2tUsers::ID), $row);
			}
			return null;
		}
		
		public static function getUsers($details, $noScope = false, $unique = false)
		{
			$users = array();
			$crit = new B2DBCriteria();
			if (strlen($details) > 1)
			{
				if (stristr($details, "@"))
				{
					$crit->addWhere(B2tUsers::EMAIL, "%$details%", B2DBCriteria::DB_LIKE);
				}
				else
				{
					$crit->addWhere(B2tUsers::UNAME, "%$details%", B2DBCriteria::DB_LIKE);
				}
		
				if ($noScope == false)
				{
					$crit->addWhere(B2tUsers::SCOPE, TBGContext::getScope()->getID());
				}
			}
			else
			{
				$crit->addWhere(B2tUsers::UNAME, "$details%", B2DBCriteria::DB_LIKE);
			}
	
			$res = B2DB::getTable('B2tUsers')->doSelect($crit);
	
			if ($res->count() == 0 && strlen($details) > 1)
			{
				$crit = new B2DBCriteria();
				$ctn = $crit->returnCriterion(B2tUsers::UNAME, "%$details%", B2DBCriteria::DB_LIKE);
				$ctn->addOr(B2tUsers::BUDDYNAME, "%$details%", B2DBCriteria::DB_LIKE);
				$ctn->addOr(B2tUsers::REALNAME, "%$details%", B2DBCriteria::DB_LIKE);
				$crit->addWhere($ctn);
				if ($noScope == false)
				{
					$crit->addWhere(B2tUsers::SCOPE, TBGContext::getScope()->getID());
				}
				$res = B2DB::getTable('B2tUsers')->doSelect($crit);
			}
	
			if ($res->count() == 0)
			{
				return false;
			}
			elseif ($res->count() == 1)
			{
				while ($row = $res->getNextRow())
				{
					$users[] = array('id' => $row->get(B2tUsers::ID));
				}
				return $users;
			}
			elseif ($unique == true)
			{
				return false;
			}
			else
			{
				while ($row = $res->getNextRow())
				{
					$users[] = array('id' => $row->get(B2tUsers::ID));
				}
				return $users;
			}
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
				if ($username === null && $password === null)
				{
					if (TBGContext::getRequest()->hasCookie('b2_username') && TBGContext::getRequest()->hasCookie('b2_password'))
					{
						$username = TBGContext::getRequest()->getCookie('b2_username');
						$password = TBGContext::getRequest()->getCookie('b2_password');
						$row = B2DB::getTable('B2tUsers')->getByUsernameAndPassword($username, $password);
						if (!$row)
						{
							TBGContext::getResponse()->deleteCookie('b2_username');
							TBGContext::getResponse()->deleteCookie('b2_password');
							TBGContext::getResponse()->headerRedirect(TBGContext::getRouting()->generate('login'));
						}
					}
				}
				if ($username !== null && $password !== null)
				{
					$row = B2DB::getTable('B2tUsers')->getByUsernameAndPassword($username, $password);
				}
				elseif (!TBGSettings::isLoginRequired())
				{
					$row = B2DB::getTable('B2tUsers')->getByUserID(TBGSettings::getDefaultUserID());
				}
				if ($row)
				{
					if (!$row->get(B2tScopes::ENABLED))
					{
						throw new Exception('This account belongs to a scope that is not active');
					}
					elseif (!$row->get(B2tUsers::ACTIVATED))
					{
						throw new Exception('This account has not been activated yet');
					}
					elseif (!$row->get(B2tUsers::ENABLED))
					{
						throw new Exception('This account has been suspended');
					}
					$user = TBGFactory::userLab($row->get(B2tUsers::ID), $row);
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
	
			try
			{
				if ($user->isAuthenticated())
				{
					$user->updateLastSeen();
					if ($user->getScope() instanceof TBGScope)
					{
						$_SESSION['b2_scope'] = $user->getScope()->getID();
					}
					if (!($user->getGroup() instanceof TBGGroup))
					{
						throw new Exception('This user account belongs to a group that does not exist anymore. <br>Please contact the system administrator.');
					}
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			
			return $user;
	
		}
		
		/**
		 * Creates a new user and returns it
		 *
		 * @param string $username
		 * @param string $realname
		 * @param string $buddyname
		 * @param integer $scope
		 * @param boolean $activated
		 * @param boolean $enabled
		 * 
		 * @return TBGUser
		 */
		public static function createNew($username, $realname, $buddyname, $scope, $activated = false, $enabled = false, $password = 'password', $email = '', $pass_is_md5 = false, $u_id = null, $lastseen = null)
		{
			$crit = new B2DBCriteria();
			if ($u_id !== null)
			{
				$crit->addInsert(B2tUsers::ID, $u_id);
			}
			if ($lastseen !== null)
			{
				$crit->addInsert(B2tUsers::LASTSEEN, $lastseen);
			}
			$crit->addInsert(B2tUsers::UNAME, $username);
			$crit->addInsert(B2tUsers::REALNAME, $realname);
			$crit->addInsert(B2tUsers::BUDDYNAME, $buddyname);
			$crit->addInsert(B2tUsers::EMAIL, $email);
			if ($pass_is_md5)
			{
				$crit->addInsert(B2tUsers::PASSWD, $password);
			}
			else
			{
				$crit->addInsert(B2tUsers::PASSWD, md5($password));
			}
			$crit->addInsert(B2tUsers::SCOPE, $scope);
			$crit->addInsert(B2tUsers::ACTIVATED, $activated);
			$crit->addInsert(B2tUsers::ENABLED, $enabled);
			$crit->addInsert(B2tUsers::JOINED, $_SERVER["REQUEST_TIME"]);
			$crit->addInsert(B2tUsers::AVATAR, 'smiley');
			$res = B2DB::getTable('B2tUsers')->doInsert($crit);
	
			if ($u_id === null) $u_id = $res->getInsertID();
			
			$returnUser = TBGFactory::userLab($u_id);
			return $returnUser;
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
		 * @param integer $uid
		 * @param B2DBRow $row
		 */
		public function __construct($uid = null, $row = null)
		{
			if ($uid !== null)
			{
				if ($row === null)
				{
					$row = B2DB::getTable('B2tUsers')->doSelectById($uid);
				}
				if (!$row instanceof B2DBRow)
				{
					throw new Exception('This user does not exist');
				}
				try
				{
					$this->uid = $row->get(B2tUsers::ID);
					if (($row->get(B2tUsers::STATE) == TBGSettings::get('offlinestate') || $row->get(B2tUsers::STATE) == TBGSettings::get('awaystate')) && !TBGContext::getRequest()->getParameter('setuserstate')) 
					{ 
						$this->setState(TBGSettings::get('onlinestate')); 
					}
					if ($row->get(B2tUsers::GROUP_ID) != 0)
					{
						$this->group = TBGFactory::groupLab($row->get(B2tUsers::GROUP_ID), $row);
					}
					if ($row->get(B2tUsers::CUSTOMER_ID) != 0)
					{
						$this->customer = TBGFactory::customerLab($row->get(B2tUsers::CUSTOMER_ID), $row);
					}
					$this->authenticated = true;
					$this->uname = $row->get(B2tUsers::UNAME);
					$this->realname = $row->get(B2tUsers::REALNAME);
					$this->buddyname = $row->get(B2tUsers::BUDDYNAME);
					$this->email = $row->get(B2tUsers::EMAIL);
					$this->homepage = $row->get(B2tUsers::HOMEPAGE);
					$this->showfollowups = ($row->get(B2tUsers::SHOWFOLLOWUPS) == 1) ? true : false;
					$this->avatar = $row->get(B2tUsers::AVATAR);
					$this->_use_gravatar = (bool) $row->get(B2tUsers::USE_GRAVATAR);
					$this->scope = TBGFactory::scopeLab($row->get(B2tUsers::SCOPE), $row);
					$this->pwd = $row->get(B2tUsers::PASSWD);
					$this->showassigned = ($row->get(B2tUsers::SHOWASSIGNED) == 1) ? true : false;
					$this->private_email = ($row->get(B2tUsers::PRIVATE_EMAIL) == 1) ? true : false;
					$this->login_error = '';
					$this->state = $row->get(B2tUsers::STATE);
					$this->lastseen = $row->get(B2tUsers::LASTSEEN);
					$this->_joined = $row->get(B2tUsers::JOINED);
					$this->_isactivated = ($row->get(B2tUsers::ACTIVATED) == 1) ? true : false;
					$this->_isenabled = ($row->get(B2tUsers::ENABLED) == 1) ? true : false;
					$this->_isdeleted = ($row->get(B2tUsers::DELETED) == 1) ? true : false;
					$this->_timezone = (int) TBGSettings::get('timezone', 'core', null, $uid);
				}
				catch (Exception $e)
				{
					TBGLogging::log("Something went wrong setting up user with id {$uid}: ".$e->getMessage());
					throw $e;
				}
				TBGLogging::log("User with id {$uid} set up successfully");
			}
			else
			{
				TBGLogging::log('Setting up empty user object', 'main', TBGLogging::LEVEL_WARNING);
			}
		}
		
		public function getName()
		{
			return $this->realname;
		}
		
		public function getID()
		{
			return $this->uid;
		}
		
		public function getNameWithUsername()
		{
			return ($this->buddyname) ? $this->buddyname . ' (' . $this->uname . ')' : $this->uname;
		}
		
		public function __toString()
		{
			return $this->getNameWithUsername();
		}
		
		/**
		 * Checks whether the user has a login error or not
		 *
		 * @return boolean
		 */
		public function hasLoginError()
		{
			if ($this->login_error != '' && $this->login_error != 'guest')
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public function isAuthenticated()
		{
			return $this->authenticated;
		}
		
		public function updateLastSeen()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUsers::LASTSEEN, $_SERVER["REQUEST_TIME"]);
			$crit->addWhere(B2tUsers::ID, $this->uid);
			B2DB::getTable('B2tUsers')->doUpdate($crit);
			$this->lastseen = $_SERVER["REQUEST_TIME"];
		}
		
		public function getLastSeen()
		{
			return $this->lastseen;
		}
		
		public function getJoinedDate()
		{
			return $this->_joined;
		}
		
		/**
		 * Checks if the user is a member of the given team
		 *
		 * @param integer $teamid
		 * @return boolean
		 */
		public function isMemberOf($teamid)
		{
			$this->_populateTeams();
			if ($teamid != 0)
			{
				return in_array($teamid, $this->teams);
			}
			return false;
		}
		
		/**
		 * Populates team array when needed
		 *
		 */
		protected function _populateTeams()
		{
			if ($this->teams === null)
			{
				$this->teams = array();
				TBGLogging::log('Populating user teams');
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tTeamMembers::UID, $this->uid);
		
				if (B2DB::getTable('B2tTeamMembers')->doCount($crit) > 0)
				{
					$res = B2DB::getTable('B2tTeamMembers')->doSelect($crit);
					while ($row = $res->getNextRow())
					{
						$this->teams[$row->get(B2tTeams::ID)] = TBGFactory::teamLab($row->get(B2tTeams::ID), $row);
					}
				}
				TBGLogging::log('...done (Populating user teams)');
			}
		}
	
		/**
		 * Checks whether or not the user is logged in
		 *
		 * @return boolean
		 */
		public function isLoggedIn()
		{
			return ($this->uid != 0) ? true : false;
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
		 * Returns whether the user wants to see the issues flagged
		 *
		 * @return boolean
		 */
		public function showFollowUps($setting = null)
		{
			if ($setting != null)
			{
				$crit = new B2DBCriteria();
				$crit->addUpdate(B2tUsers::SHOWFOLLOWUPS, $setting);
				$res = B2DB::getTable('B2tUsers')->doUpdateById($crit, $this->uid);
				$this->showfollowups = ($setting == 0) ? false : true;
			}
			return $this->showfollowups;
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
				if ($res = B2DB::getTable('B2tIssues')->getOpenIssuesByUserAssigned($this->getUID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->userassigned[$row->get(B2tIssues::ID)] = TBGFactory::TBGIssueLab($row->get(B2tIssues::ID), $row);
					}
					ksort($this->userassigned, SORT_NUMERIC);
				}
			}
			return $this->userassigned;
		}
	
		/**
		 * Returns whether the user wants to see the issues assigned to him/her
		 *
		 * @return boolean
		 */
		public function showAssigned($setting = null)
		{
			if ($setting != null)
			{
				$crit = new B2DBCriteria();
				$crit->addUpdate(B2tUsers::SHOWASSIGNED, $setting);
				$res = B2DB::getTable('B2tUsers')->doUpdateById($crit, $this->uid);
				$this->showassigned = ($setting == 0) ? false : true;
			}
			return $this->showassigned;
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
				if ($res = B2DB::getTable('B2tIssues')->getOpenIssuesByTeamAssigned($team_id))
				{
					while ($row = $res->getNextRow())
					{
						$this->teamassigned[$team_id][$row->get(B2tIssues::ID)] = TBGFactory::TBGIssueLab($row->get(B2tIssues::ID), $row);
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
				if ($res = B2DB::getTable('B2tUserIssues')->getUserStarredIssues($this->getUID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_starredissues[$row->get(B2tIssues::ID)] = TBGFactory::TBGIssueLab($row->get(B2tIssues::ID), $row);
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
			TBGLogging::log("Starring issue with id {$issue_id} for user with id " . $this->getUID());
			if ($this->isLoggedIn() == true && $this->isGuest() == false)
			{
				if (array_key_exists($issue_id, $this->_starredissues))
				{
					TBGLogging::log('Already starred');
					return true;
				}
				TBGLogging::log('Logged in and unstarred, continuing');
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tUserIssues::ISSUE, $issue_id);
				$crit->addInsert(B2tUserIssues::UID, $this->uid);
				$crit->addInsert(B2tUserIssues::SCOPE, TBGContext::getScope()->getID());
				
				B2DB::getTable('B2tUserIssues')->doInsert($crit);
				$issue = TBGFactory::TBGIssueLab($issue_id);
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
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tUserIssues::ISSUE, $issue_id);
			$crit->addWhere(B2tUserIssues::UID, $this->uid);
				
			B2DB::getTable('B2tUserIssues')->doDelete($crit);
			unset($this->_starredissues[$issue_id]);
			return true;
		}
	
		/**
		 * Adds a friend to the buddy list
		 *
		 * @param integer $bid Friend to add
		 * @return boolean
		 */
		public function addFriend($bid)
		{
			if (!($this->isFriend($bid)))
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tBuddies::UID, $this->uid);
				$crit->addInsert(B2tBuddies::BID, $bid);
				$crit->addInsert(B2tBuddies::SCOPE, TBGContext::getScope()->getID());
				B2DB::getTable('B2tBuddies')->doInsert($crit);
				$this->_friends[$bid] = array('id' => $bid);
				return true;
			}
			else
			{
				return false;
			}
		}
	
		/**
		 * Returns an array of uids which are in the users buddy list
		 *
		 * @return array
		 */
		public function getFriends()
		{
			if ($this->_friends == null)
			{
				$this->_friends = array();
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tBuddies::UID, $this->uid);
				$crit->setFromTable(B2DB::getTable('B2tBuddies'));
				$crit->addJoin(B2DB::getTable('B2tUsers'), B2tUsers::ID, B2tBuddies::BID);
				if ($res = B2DB::getTable('B2tBuddies')->doSelect($crit))
				{
					while ($row = $res->getNextRow())
					{
						$this->_friends[$row->get(B2tBuddies::BID)] = TBGFactory::userLab($row->get(B2tBuddies::BID), $row);
					}
				}
			}
			return $this->_friends;
		}
		
		/**
		 * Removes a user from the list of buddies
		 *
		 * @param integer $bid UID of user to remove
		 * @return nothing
		 */
		public function removeFriend($bid)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tBuddies::UID, $this->uid);
			$crit->addWhere(B2tBuddies::BID, $bid);
			
			B2DB::getTable('B2tBuddies')->doDelete($crit);
			unset($this->_friends[$bid]);
		}
	
		/**
		 * Check if the given user is a friend of this user
		 *
		 * @param integer $uid UID of user to check
		 * @return boolean
		 */
		public function isFriend($uid)
		{
			if ($this->_friends === null)
			{
				$this->getFriends();
			}
			if (isset($this->_friends[$uid]))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	
		/**
		 * Change the password to a new password
		 *
		 * @param string $newpassword
		 */
		public function changePassword($newpassword)
		{
			$this->pwd = md5($newpassword);
		}
		
		public function setRandomPassword()
		{
			$newPass = bugs_createPassword();
			$md5newPass = md5($newPass);
			$this->changePassword($newPass);
			return $newPass;
		}
	
		/**
		 * Set the user state to this state 
		 *
		 * @param integer $s_id
		 * @return nothing
		 */
		public function setState($s_id)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUsers::STATE, $s_id);
			$crit->addWhere(B2tUsers::ID, $this->uid);
			
			B2DB::getTable('B2tUsers')->doUpdate($crit);
			$this->state = $s_id;
		}
		
		/**
		 * Get the current user state
		 *
		 * @return TBGDatatype
		 */
		public function getState()
		{
			$now = $_SERVER["REQUEST_TIME"];
			if (($this->lastseen < ($now - (60 * 10))) && ($this->state != TBGSettings::get('offlinestate') && $this->state != TBGSettings::get('awaystate')))
			{
				$this->setState(TBGSettings::get('awaystate'));
			}
			if ($this->lastseen < ($now - (60 * 30)) && $this->state != TBGSettings::get('offlinestate'))
			{
				$this->setState(TBGSettings::get('offlinestate'));
			}
			TBGContext::trigger('core', 'TBGUser::getState', $this);
			
			if (!$this->state instanceof TBGUserstate)
			{
				if ($this->state == 0)
				{
					$this->state = TBGSettings::get('offlinestate');
				}
				$this->state = TBGFactory::userstateLab($this->state);
			}
			return $this->state;
		}
		
		public function isEnabled()
		{
			return $this->_isenabled;
		}
		
		public function isActivated()
		{
			return $this->_isactivated;
		}
		
		public function isDeleted()
		{
			return $this->_isdeleted;
		}
		
		/**
		 * Returns an array of teams which the current user is a member of
		 *
		 * @return array
		 */
		public function getTeams()
		{
			$this->_populateTeams();
			return $this->teams;
		}
		
		public function getType()
		{
			return self::TYPE_USER;
		}
		
		private function _setUserDetail($detail, $value)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate($detail, $value);
			B2DB::getTable('B2tUsers')->doUpdateById($crit, $this->uid);
			return true;
		}
	
		/**
		 * Set whether or not the email address is hidden for normal users
		 *
		 * @param boolean $val
		 */
		public function setEmailPrivate($val)
		{
			$this->private_email = (bool) $val;
		}
		
		/**
		 * Returns whether or not the email address is private
		 *
		 * @return boolean
		 */
		public function isEmailPrivate()
		{
			return $this->private_email;
		}

		/**
		 * Returns whether or not the email address is public
		 *
		 * @return boolean
		 */
		public function isEmailPublic()
		{
			return !$this->private_email;
		}

		public function getPasswordMD5()
		{
			return $this->pwd;
		}
		
		/**
		 * Sets the login error to something
		 *
		 * @param string $login_error
		 */
		public function setLoginError($login_error)
		{
			$this->login_error = $login_error;
		}
		
		/**
		 * Returns the current users login error
		 *
		 * @return string
		 */
		public function getLoginError()
		{
			return $this->login_error;
		}
		
		/**
		 * Returns the scope of this user
		 *
		 * @return TBGScope
		 */
		public function getScope()
		{
			return $this->scope;
		}
		
		/**
		 * Returns the UID of this user
		 *
		 * @return integer
		 */
		public function getUID()
		{
			return $this->uid;
		}
		
		/**
		 * Returns the user group
		 *
		 * @return TBGGroup
		 */
		public function getGroup()
		{
			return $this->group;
		}
		
		public function setGroup($gid)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUsers::GROUP_ID, (int) $gid);
			B2DB::getTable('B2tUsers')->doUpdateById($crit, $this->uid);
			$this->group = TBGFactory::groupLab($gid);
		}
		
		/**
		 * Returns the login name (username)
		 *
		 * @return string
		 */
		public function getUname()
		{
			return $this->uname;
		}
		
		public function getUsername()
		{
			return $this->getUname();
		}
		
		/**
		 * Returns an md5 hash of the user password
		 *
		 * @return string
		 */
		public function getMD5Password()
		{
			return $this->pwd;
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
			return $this->hasPasswordMD5(md5($password));
		}

		/**
		 * Return whether or not the users password is this
		 *
		 * @param string $password MD5-hashed password
		 *
		 * @return boolean
		 */
		public function hasPasswordMD5($password)
		{
			return (bool) ($password == $this->getMD5Password());
		}

		/**
		 * Returns the real name (full name) of the user
		 *
		 * @return string
		 */
		public function getRealname()
		{
			return $this->realname;
		}
		
		/**
		 * Returns the buddy name (friendly name) of the user
		 *
		 * @return string
		 */
		public function getBuddyname()
		{
			return $this->buddyname;
		}
		
		/**
		 * Returns the email of the user
		 *
		 * @return string
		 */
		public function getEmail()
		{
			return $this->email;
		}
		
		/**
		 * Returns the users homepage
		 *
		 * @return unknown
		 */
		public function getHomepage()
		{
			return $this->homepage;
		}

		/**
		 * Set this users homepage
		 *
		 * @param string $homepage
		 */
		public function setHomepage($homepage)
		{
			$this->homepage = $homepage;
		}
		
		/**
		 * Set the avatar image
		 *
		 * @param string $avatar
		 */
		public function setAvatar($avatar)
		{
			$this->_setUserDetail(B2tUsers::AVATAR, $avatar);
			$this->avatar = $avatar;
		}
		
		/**
		 * Returns the avatar of the user
		 *
		 * @return string
		 */
		public function getAvatar()
		{
			return ($this->avatar != '') ? $this->avatar : 'user';
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
			if ($this->usesGravatar())
			{
				$url = 'http://www.gravatar.com/avatar/' . md5(trim($this->getEmail())) . '.png?d=wavatar&amp;s=';
				$url .= ($small) ? 22 : 48; 
			}
			else
			{
				$url = TBGSettings::getURLsubdir() . 'avatars/' . $this->getAvatar();
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
			if ($this->isGuest()) return false;
			return (bool) $this->_use_gravatar;
		}
		
		/**
		 * Updates user information
		 *
		 * @param string $realname
		 * @param string $buddyname
		 * @param string $homepage
		 * @param string $email
		 */
		public function updateUserDetails($realname = null, $buddyname = null, $homepage = null, $email = null, $uname = null)
		{
			$crit = new B2DBCriteria();
			
			if ($realname !== null) $crit->addUpdate(B2tUsers::REALNAME, $realname);
			if ($buddyname !== null) $crit->addUpdate(B2tUsers::BUDDYNAME, $buddyname);
			if ($homepage !== null) $crit->addUpdate(B2tUsers::HOMEPAGE, $homepage);
			if ($email !== null) $crit->addUpdate(B2tUsers::EMAIL, $email);
			if ($uname !== null) $crit->addUpdate(B2tUsers::UNAME, $uname);
			
			$res = B2DB::getTable('B2tUsers')->doUpdateById($crit, $this->uid);
			
			if ($realname !== null) $this->realname = $realname;
			if ($buddyname !== null) $this->buddyname = $buddyname;
			if ($homepage !== null) $this->homepage = $homepage;
			if ($email !== null) $this->email = $email;
			if ($uname !== null) $this->uname = $uname;
		}

		/**
		 * Set the users email address
		 *
		 * @param string $email A valid email address
		 */
		public function setEmail($email)
		{
			$this->email = $email;
		}

		/**
		 * Set the users realname
		 *
		 * @param string $realname
		 */
		public function setRealname($realname)
		{
			$this->realname = $realname;
		}

		/**
		 * Set the users buddyname
		 *
		 * @param string $buddyname
		 */
		public function setBuddyname($buddyname)
		{
			$this->buddyname = $buddyname;
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

		public function setEnabled($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUsers::ENABLED, ($val) ? 1 : 0);
			B2DB::getTable('B2tUsers')->doUpdateById($crit, $this->getID());
			$this->_isenabled = $val;
			if (!$val && $this->getUname() == TBGSettings::get('defaultuname'))
			{
				TBGSettings::saveSetting('requirelogin', 1);
			}
		}
		
		public function setValidated($val)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUsers::ACTIVATED, ($val) ? 1 : 0);
			B2DB::getTable('B2tUsers')->doUpdateById($crit, $this->getID());
			$this->_isactivated = $val;
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
			$res = B2DB::getTable('B2tUsers')->getByDetails($details);
			
			if (!$res || $res->count() > 1) return false;
			
			return TBGFactory::userLab($row->get(B2tUsers::ID), $row);
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
			
			if ($res = B2DB::getTable('B2tUsers')->getByDetails($details))
			{
				while ($row = $res->getNextRow())
				{
					$retarr[$row->get(B2tUsers::ID)] = TBGFactory::userLab($row->get(B2tUsers::ID), $row);
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
			TBGLogging::log('...done (Checking permissions '.$permission_type.')');
			
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
		public function hasPageAccess($page, $target_id = null)
		{
			if ($target_id === null)
			{
				return $this->hasPermission("page_{$page}_access", 0, "core", true, true);
			}
			else
			{
				$retval = $this->hasPermission("page_{$page}_access", $target_id, "core", true, null);
				return ($retval === null) ? $this->hasPermission("page_{$page}_access", 0, "core", true, null) : $retval;
			}
		}

		/**
		 * Save changes made to the user object
		 *
		 * @return TBGUser The user object
		 */
		public function save()
		{
			$crit = B2DB::getTable('B2tUsers')->getCriteria();
			$crit->addUpdate(B2tUsers::REALNAME, $this->realname);
			$crit->addUpdate(B2tUsers::BUDDYNAME, $this->buddyname);
			$crit->addUpdate(B2tUsers::USE_GRAVATAR, (bool) $this->_use_gravatar);
			$crit->addUpdate(B2tUsers::PRIVATE_EMAIL, (bool) $this->private_email);
			$crit->addUpdate(B2tUsers::PASSWD, $this->pwd);
			$crit->addUpdate(B2tUsers::EMAIL, $this->email);
			$crit->addUpdate(B2tUsers::HOMEPAGE, $this->homepage);

			$res = B2DB::getTable('B2tUsers')->doUpdateById($crit, $this->getID());

			TBGSettings::saveSetting('timezone', $this->_timezone, 'core', null, $this->getID());

			return true;
		}

		/**
		 * Get this users timezone
		 *
		 * @return mixed
		 */
		public function getTimezone()
		{
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
		 * Return whether the user can vote on issues for a specific product
		 *  
		 * @param integer $product_id The Product id
		 * 
		 * @return boolean
		 */
		public function canVoteOnIssuesForProduct($product_id)
		{
			return (bool) $this->hasPermission("b2canvote", $product_id);
		}
		
		/**
		 * Return whether the user can vote for a specific issue
		 * 
		 * @param integer $issue_id The issue id
		 * 
		 * @return boolean
		 */
		public function canVoteForIssue($issue_id)
		{
			return !(bool) $this->hasPermissions("b2cantvote", $issue_id);
		}

		/**
		 * Return if the user can add builds to an issue for a given project
		 * 
		 * @param integer $project_id The project id
		 * 
		 * @return boolean
		 */
		public function canAddBuildsToIssuesForProject($project_id)
		{
			return (bool) $this->hasPermission('b2canaddbuilds', $project_id);
		}

		/**
		 * Return if the user can add components to an issue for a given project
		 * 
		 * @param integer $project_id The project id
		 * 
		 * @return boolean
		 */
		public function canAddComponentsToIssuesForProject($project_id)
		{
			return (bool) $this->hasPermission('b2canaddcomponents', $project_id);
		}

		/**
		 * Return if the user can report new issues
		 *
		 * @param integer $product_id[optional] A product id
		 * @return boolean
		 */
		public function canReportIssues($product_id = null)
		{
			$retval = null;
			if ($product_id !== null)
			{
				$retval = $this->hasPermission('cancreateissues', $product_id, 'core', true, null);
				$retval = ($retval !== null) ? $retval : $this->hasPermission('cancreateandeditissues', $product_id, 'core', true, null);
			}
			return ($retval !== null) ? $retval : (bool) ($this->hasPermission('cancreateissues') || $this->hasPermission('cancreateandeditissues'));
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
			return (bool) ($savedsearch->get(B2tSavedSearches::IS_PUBLIC) || $savedsearch->get(B2tSavedSearches::UID) == $this->getID());
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
		 * Return a list of the users latest log items
		 * 
		 * @param integer $number Limit to a number of changes
		 * 
		 * @return array
		 */
		public function getLatestActions($number = 10)
		{
			if ($items = B2DB::getTable('B2tLog')->getByUserID($this->getUID(), $number))
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
				
				$projects = B2DB::getTable('B2tProjectAssignees')->getProjectsByUserID($this->getUID());
				$edition_projects = B2DB::getTable('B2tEditionAssignees')->getProjectsByUserID($this->getUID());
				$component_projects = B2DB::getTable('B2tComponentAssignees')->getProjectsByUserID($this->getUID());

				$project_ids = array_merge(array_keys($projects), array_keys($edition_projects), array_keys($component_projects));
				foreach ($project_ids as $project_id)
				{
					$this->_associated_projects[$project_id] = TBGFactory::projectLab($project_id);
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
		
	}
