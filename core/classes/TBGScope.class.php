<?php

	/**
	 * The scope class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * The scope class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 *
	 * @Table(name="TBGScopesTable")
	 */
	class TBGScope extends TBGIdentifiableClass
	{
		
		protected static $_scopes = null;

		/**
		 * The name of the object
		 *
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_name;

		/**
		 * @var string
		 * @Column(type="string", length=200)
		 */
		protected $_description = '';
		
		/**
		 * @var boolean
		 * @Column(type="boolean")
		 */
		protected $_enabled = false;
		
		/**
		 * @var string
		 */
		protected $_shortname = '';
		
		protected $_administrator = null;
		
		protected $_hostnames = null;

		protected $_is_secure = false;

		/**
		 * @var boolean
		 * @Column(type="boolean")
		 */
		protected $_uploads_enabled = true;

		/**
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_max_upload_limit = 0;

		/**
		 * @var boolean
		 * @Column(type="boolean")
		 */
		protected $_custom_workflows_enabled = true;

		/**
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_max_workflows = 0;

		/**
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_max_users = 0;

		/**
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_max_projects = 0;

		/**
		 * @var integer
		 * @Column(type="integer", length=10)
		 */
		protected $_max_teams = 0;

		/**
		 * Return all available scopes
		 * 
		 * @return array|TBGScope
		 */
		static function getAll()
		{
			if (self::$_scopes === null)
			{
				self::$_scopes = TBGScopesTable::getTable()->selectAll();
			}

			return self::$_scopes;
		}
		
		/**
		 * Return the items name
		 *
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}

		/**
		 * Set the edition name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}

		public function isEnabled()
		{
			return $this->_enabled;
		}

		public function isDefault()
		{
			return in_array('*', $this->getHostnames());
		}
		
		public function setEnabled($enabled = true)
		{
			$this->_enabled = (bool) $enabled;
		}
		
		public function getDescription()
		{
			return $this->_description;
		}
		
		public function setDescription($description)
		{
			$this->_description = $description;
		}
		
		protected function _populateHostnames()
		{
			if ($this->_hostnames === null)
			{
				if ($this->_id)
					$this->_hostnames = TBGScopeHostnamesTable::getTable()->getHostnamesForScope($this->getID());
				else
					$this->_hostnames = array();
			}
		}

		public function getHostnames()
		{
			$this->_populateHostnames();
			return $this->_hostnames;
		}
		
		public function addHostname($hostname)
		{
			$hostname = trim($hostname, "/");
			$this->_populateHostnames();
			$this->_hostnames[] = $hostname;
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
					$this->_administrator = TBGContext::factory()->TBGUser($this->_administrator);
				}
				catch (Exception $e) { }
			}
			return $this->_administrator;
		}
		
		protected function _preDelete()
		{
			$tables = array(
				'TBGIssueCustomFieldsTable', 'TBGIssueAffectsEditionTable',
				'TBGIssueAffectsBuildTable', 'TBGIssueAffectsComponentTable', 'TBGIssueFilesTable',
				'TBGIssueRelationsTable', 'TBGIssuetypeSchemeLinkTable', 'TBGIssuetypeSchemesTable',
				'TBGIssueTypesTable', 'TBGListTypesTable', 'TBGIssuesTable', 'TBGCommentsTable',
				'TBGComponentAssignedTeamsTable', 'TBGComponentAssignedUsersTable', 
				'TBGProjectAssignedTeamsTable', 'TBGProjectAssignedUsersTable',
				'TBGEditionAssignedTeamsTable', 'TBGEditionAssignedUsersTable',
				'TBGComponentsTable', 'TBGEditionsTable', 'TBGBuildsTable', 'TBGMilestonesTable',
				'TBGIssuesTable', 'TBGProjectsTable', 'TBGUserScopesTable'
			);
			foreach($tables as $table)
			{
				$table::getTable()->deleteFromScope($this->getID());
			}
		}

		protected function _postSave($is_new)
		{
			TBGScopeHostnamesTable::getTable()->saveScopeHostnames($this->getHostnames(), $this->getID());
			// Load fixtures for this scope if it's a new scope
			if ($is_new)
			{
				if (!$this->isDefault())
				{
					$prev_scope = TBGContext::getScope();
					TBGContext::setScope($this);
				}
				$this->loadFixtures();
				if (!$this->isDefault())
				{
					TBGModule::installModule('publish', $this);
					TBGContext::setScope($prev_scope);
					TBGContext::clearPermissionsCache();
				}
			}
		}
		
		public function _construct(\b2db\Row $row, $foreign_key = null)
		{
			if (TBGContext::isCLI())
			{
				$this->_hostname = php_uname('n');
			}
			else
			{
				$hostprefix = (!array_key_exists('HTTPS', $_SERVER) || $_SERVER['HTTPS'] == '' || $_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
				$this->_is_secure = (bool) ($hostprefix == 'https');
				$this->_hostname = "{$hostprefix}://{$_SERVER['SERVER_NAME']}";
				$port = $_SERVER['SERVER_PORT'];
				if ($port != 80)
				{
					$this->_hostname .= ":{$port}";
				}
			}
		}

		public function isSecure()
		{
			return $this->_is_secure;
		}

		public function getCurrentHostname($clean = false)
		{
                        if ($clean)
                        {
                                // a scheme is needed before php 5.4.7
                                // thus, let's add the prefix http://
                                if (!stristr($this->_hostname,'http')) {
                                        $url = parse_url('http://'.$this->_hostname);
                                } else {
                                        $url = parse_url($this->_hostname);
                                }
                                return $url['host'];
                        }
                        return $this->_hostname;
		}
		
		public function loadFixtures()
		{
			// Load initial settings
			TBGSettingsTable::getTable()->loadFixtures($this);
			TBGSettings::loadSettings();

			// Load group, users and permissions fixtures
			TBGGroup::loadFixtures($this);

			// Load initial teams
			TBGTeam::loadFixtures($this);

			// Set up user states, like "available", "away", etc
			TBGUserstate::loadFixtures($this);

			// Set up data types
			list($b_id, $f_id, $e_id, $t_id, $u_id, $i_id) = TBGIssuetype::loadFixtures($this);
			$scheme = TBGIssuetypeScheme::loadFixtures($this);
			TBGIssueFieldsTable::getTable()->loadFixtures($this, $scheme, $b_id, $f_id, $e_id, $t_id, $u_id, $i_id);
			TBGDatatype::loadFixtures($this);

			// Set up workflows
			TBGWorkflow::loadFixtures($this);
			TBGWorkflowScheme::loadFixtures($this);
			TBGWorkflowIssuetypeTable::getTable()->loadFixtures($this);

			// Set up left menu links
			TBGLinksTable::getTable()->loadFixtures($this);
		}

		public function isUploadsEnabled()
		{
			return ($this->isDefault() || $this->_uploads_enabled);
		}

		public function setUploadsEnabled($enabled = true)
		{
			$this->_uploads_enabled = $enabled;
		}

		public function isCustomWorkflowsEnabled()
		{
			return ($this->isDefault() || $this->_custom_workflows_enabled);
		}

		public function setCustomWorkflowsEnabled($enabled = true)
		{
			$this->_custom_workflows_enabled = $enabled;
		}

		public function setMaxWorkflowsLimit($limit)
		{
			$this->_max_workflows = $limit;
		}

		public function getMaxWorkflowsLimit()
		{
			return ($this->isDefault()) ? 0 : (int) $this->_max_workflows;
		}

		public function hasCustomWorkflowsAvailable()
		{
			if ($this->isCustomWorkflowsEnabled())
				return ($this->getMaxWorkflowsLimit()) ? (TBGWorkflow::getCustomWorkflowsCount() < $this->getMaxWorkflowsLimit()) : true;
			else
				return false;
		}

		public function setMaxUploadLimit($limit)
		{
			$this->_max_upload_limit = $limit;
		}

		public function getMaxUploadLimit()
		{
			return ($this->isDefault()) ? 0 : (int) $this->_max_upload_limit;
		}

		public function getMaxUsers()
		{
			return ($this->isDefault()) ? 0 : (int) $this->_max_users;
		}

		public function setMaxUsers($limit)
		{
			$this->_max_users = $limit;
		}

		public function hasUsersAvailable()
		{
			return ($this->getMaxUsers()) ? (TBGUser::getUsersCount() < $this->getMaxUsers()) : true;
		}

		public function getMaxProjects()
		{
			return ($this->isDefault()) ? 0 : (int) $this->_max_projects;
		}

		public function setMaxProjects($limit)
		{
			$this->_max_projects = $limit;
		}

		public function hasProjectsAvailable()
		{
			return ($this->getMaxProjects()) ? (TBGProject::getProjectsCount() < $this->getMaxProjects()) : true;
		}

		public function getMaxTeams()
		{
			return ($this->isDefault()) ? 0 : (int) $this->_max_teams;
		}

		public function setMaxTeams($limit)
		{
			$this->_max_teams = $limit;
		}

		public function hasTeamsAvailable()
		{
			return ($this->getMaxTeams()) ? (TBGTeam::countAll() < $this->getMaxTeams()) : true;
		}
		
	}
