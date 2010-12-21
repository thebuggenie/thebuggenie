<?php

	/**
	 * The scope class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
		
		static protected $_b2dbtablename = 'TBGScopesTable';
		
		protected $_description = '';
		
		protected $_enabled = false;
		
		protected $_shortname = '';
		
		protected $_administrator = null;
		
		protected $_hostname = '';
		
		static function getAll()
		{
			$res = TBGScopesTable::getTable()->doSelectAll();
			$scopes = array();
	
			while ($row = $res->getNextRow())
			{
				$scopes[] = TBGContext::factory()->TBGScope($row->get(TBGScopesTable::ID), $row);
			}
	
			return $scopes;
		}
		
		public function getShortname()
		{
			return $this->_shortname;
		}
		
		public function setShortname($shortname)
		{
			$this->_shortname = $shortname;
		}
		
		public function isEnabled()
		{
			return $this->_enabled;
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
					$this->_administrator = TBGContext::factory()->TBGUser($this->_administrator);
				}
				catch (Exception $e)
				{
					
				}
			}
			return $this->_administrator;
		}
		
		public function setScopeAdmin($uid)
		{
			$adminuser = TBGContext::factory()->TBGUser($uid);
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGScopesTable::ADMINISTRATOR, $uid);
			$res = TBGScopesTable::getTable()->doUpdateById($crit, $this->_id);
			$this->_administrator = $adminuser;
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGGroupsTable::SCOPE, $this->_id);
			$crit->addOrderBy(TBGScopesTable::ID, B2DBCriteria::SORT_ASC);
			$adminuser->setGroup(TBGGroupsTable::getTable()->doSelectOne($crit)->get(TBGGroupsTable::ID));
			foreach ($adminuser->getTeams() as $aTeam)
			{
				$aTeam = TBGContext::factory()->TBGTeam($aTeam);
				$aTeam->removeMember($adminuser->getID());
			}
		}

		public function _postSave($is_new)
		{
			// Load fixtures for this scope if it's a new scope
			if ($is_new) $this->loadFixtures();
			
			// Save the hostname to the settings table
			TBGSettings::saveSetting('url_host', $this->_hostname, 'core', $this->getID());
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
			TBGIssuetype::loadFixtures($this);
			TBGIssuetypeScheme::loadFixtures($this);
			TBGDatatype::loadFixtures($this);
			
			// Set up workflows
			TBGWorkflow::loadFixtures($this);
			TBGWorkflowSchemesTable::getTable()->loadFixtures($this);
			TBGWorkflowIssuetypeTable::getTable()->loadFixtures($this);
			
			// Set up left menu links
			TBGLinksTable::getTable()->loadFixtures($this);
		}
		
	}
