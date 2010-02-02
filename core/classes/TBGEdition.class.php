<?php

	/**
	 * Edition class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Edition class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGEdition extends TBGVersionItem 
	{
		/**
		 * The project
		 *
		 * @var TBGProject
		 */
		protected $_project;
		
		protected $_populatedproject = false;
		
		/**
		 * Editions components
		 *
		 * @var array|TBGComponent
		 */
		protected $_components;
		
		protected $_populatedcomponents = false;
		
		/**
		 * Edition builds
		 *
		 * @var array|TBGBuild
		 */
		protected $_builds = null;
		
		protected $_description = '';
		
		protected $_lead_by = 0;
		
		protected $_lead_type = 0;

		protected $_qa = 0;
		
		protected $_qa_type = 0;
		
		/**
		 * The editions documentation URL
		 * 
		 * @var string
		 */
		protected $_doc_url = '';
						
		/**
		 * The owner type for the project, TBGIdentifiableClass::TYPE_USER or TBGIdentifiableClass::TYPE_TEAM
		 * 
		 * @var integer
		 */
		protected $_owned_type = 0;
		
		/**
		 * The owner of the project
		 *  
		 * @var TBGIdentifiable
		 */
		protected $_owned_by = 0;
		
		static protected $_editions = null;
		
		/**
		 * Creates a new TBGEdition
		 *
		 * @param string $e_name
		 * @param int $p_id the project id of the project you are adding the edition to
		 * @param int $e_id only provided if you want to specify the id
		 * 
		 * @return TBGEdition
		 */
		public static function createNew($e_name, $p_id, $e_id = null)
		{
			$crit = new B2DBCriteria();
			if ($e_id !== null)
			{
				$crit->addInsert(TBGEditionsTable::ID, $e_id);
			}
			$crit->addInsert(TBGEditionsTable::NAME, $e_name);
			$crit->addInsert(TBGEditionsTable::PROJECT, $p_id);
			$crit->addInsert(TBGEditionsTable::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(TBGEditionsTable::DESCRIPTION, '');
			$res = B2DB::getTable('TBGEditionsTable')->doInsert($crit);
			
			if ($e_id === null) $e_id = $res->getInsertID();
			
			TBGContext::setPermission("b2editionaccess", $e_id, "core", 0, TBGContext::getUser()->getGroup()->getID(), 0, true);
			return TBGFactory::editionLab($e_id);
		}
		
		public static function getAllByProjectID($project_id)
		{
			if (self::$_editions === null)
			{
				self::$_editions = array();
			}
			if (!array_key_exists($project_id, self::$_editions))
			{
				self::$_editions[$project_id] = array();
				if ($res = B2DB::getTable('TBGEditionsTable')->getByProjectID($project_id))
				{
					while ($row = $res->getNextRow())
					{
						$edition = TBGFactory::editionLab($row->get(TBGEditionsTable::ID), $row);
						self::$_editions[$project_id][$edition->getID()] = $edition;
					}
				}
			}
			return self::$_editions[$project_id];
		}
		
		/**
		 * Constructor function
		 *
		 * @param integer $e_id
		 * @param TBGProject $project
		 * @param TBGBuild $build
		 */
		public function __construct($e_id, $row = null)
		{
			if ($row === null)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGEditionsTable::SCOPE, TBGContext::getScope()->getID());
				$row = B2DB::getTable('TBGEditionsTable')->doSelectById($e_id, $crit);
			}
			if ($row instanceof B2DBRow)
			{
				$this->_itemid = $e_id;
				$this->_name = $row->get(TBGEditionsTable::NAME);
				$this->_isdefault = (bool) $row->get(TBGEditionsTable::IS_DEFAULT);
				$this->_locked = (bool) $row->get(TBGEditionsTable::LOCKED);
				$this->_isreleased = (bool) $row->get(TBGEditionsTable::RELEASED);
				$this->_isplannedreleased = (bool) $row->get(TBGEditionsTable::PLANNED_RELEASED);
				$this->_release_date = $row->get(TBGEditionsTable::RELEASE_DATE);
				$this->_description = $row->get(TBGEditionsTable::DESCRIPTION);
				$this->_lead_by = $row->get(TBGEditionsTable::LEAD_BY);
				$this->_lead_type = $row->get(TBGEditionsTable::LEAD_TYPE);
				$this->_qa = $row->get(TBGEditionsTable::QA);
				$this->_qa_type = $row->get(TBGEditionsTable::QA_TYPE);
				$this->_project = $row->get(TBGEditionsTable::PROJECT);
			}
			else
			{
				throw new Exception('This edition does not exist');
			}
		}
		
		/**
		 * Populates components inside the edition
		 *
		 * @return void
		 */
		public function doPopulateComponents()
		{
			$this->_components = array();
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGEditionComponentsTable::EDITION, $this->_itemid);
			$res = B2DB::getTable('TBGEditionComponentsTable')->doSelect($crit);
			if ($res->count() > 0)
			{
				while ($row = $res->getNextRow())
				{
					$this->_components[$row->get(TBGEditionComponentsTable::COMPONENT)] = TBGFactory::componentLab($row->get(TBGEditionComponentsTable::COMPONENT));
				}
			}
			$this->_populatedcomponents = true;
		}
		
		/**
		 * Returns an array with all components
		 *
		 * @return array|TBGComponent
		 */
		public function getComponents()
		{
			if (!$this->_populatedcomponents)
			{
				$this->doPopulateComponents();
			}
			return $this->_components;
		}
		
		/**
		 * Whether or not this edition has a component enabled
		 * 
		 * @param $c_id integer The component to check for
		 * 
		 * @return bool
		 */
		public function hasComponent($c_id)
		{
			if ($c_id instanceof TBGComponent)
			{
				$c_id = $c_id->getID();
			}
			return array_key_exists($c_id, $this->getComponents());
		}
		
		public function hasDescription()
		{
			return (bool) $this->getDescription();
		}

		/**
		 * Adds an existing component to the edition
		 *
		 * @param TBGComponent|integer $c_id
		 */
		public function addComponent($c_id)
		{
			if ($c_id instanceof TBGComponent)
			{
				$c_id = $c_id->getID();
			}
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGEditionComponentsTable::COMPONENT, $c_id);
			$crit->addInsert(TBGEditionComponentsTable::EDITION, $this->getID());
			$crit->addInsert(TBGEditionComponentsTable::SCOPE, TBGContext::getScope()->getID());
			$res = B2DB::getTable('TBGEditionComponentsTable')->doInsert($crit);
		}
		
		/**
		 * Removes an existing component from the edition
		 *
		 * @param TBGComponent|integer $c_id
		 */
		public function removeComponent($c_id)
		{
			if ($c_id instanceof TBGComponent)
			{
				$c_id = $c_id->getID();
			}
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGEditionComponentsTable::EDITION, $this->getID());
			$crit->addWhere(TBGEditionComponentsTable::COMPONENT, (int) $c_id);
			$crit->addWhere(TBGEditionComponentsTable::SCOPE, TBGContext::getScope()->getID());
			B2DB::getTable('TBGEditionComponentsTable')->doDelete($crit);
		}
		
		/**
		 * Returns the description
		 *
		 * @return string
		 */
		public function getDescription()
		{
			return $this->_description;
		}
		
		/**
		 * Returns the documentation url
		 *
		 * @return string
		 */
		public function getDocumentationURL()
		{
			return $this->_doc_url;
		}
		
		/**
		 * Returns the component specified
		 *
		 * @param integer $c_id
		 * @return TBGComponent
		 */
		public function getComponent($c_id)
		{
			if (!$this->_populatedcomponents)
			{
				$this->doPopulateComponents();
			}
			return $this->_components[$c_id];
		}
		
		/**
		 * Returns the default build
		 *
		 * @return TBGBuild
		 */
		public function getDefaultBuild()
		{
			if ($this->_builds === null)
			{
				$this->doPopulateBuilds();
			}
			if (count($this->_builds) > 0)
			{
				foreach ($this->_builds as $aBuild)
				{
					if ($aBuild->isDefault() && $aBuild->isLocked() == false)
					{
						return $aBuild;
					}
				}
				return array_shift($this->_builds);
			}
			return 0;
		}

		/**
		 * Returns the latest build
		 *
		 * @return TBGBuild
		 */
		public function getLatestBuild()
		{
			$crit = new B2DBCriteria();
			$crit->addSelectionColumn(TBGBuildsTable::ID);
			$crit->addWhere(TBGBuildsTable::EDITION, $this->_itemid);
			$crit->addWhere(TBGBuildsTable::RELEASED, 1);
			$crit->addOrderBy(TBGBuildsTable::RELEASE_DATE, 'desc');
			$res = B2DB::getTable('TBGBuildsTable')->doSelect($crit);
			
			if ($res->count() > 0)
			{
				while ($row = $res->getNextRow())
				{
					if (TBGContext::getUser()->hasPermission('b2buildaccess', $row->get(TBGBuildsTable::ID), 'core'))
					{
						return(TBGFactory::buildLab($row->get(TBGBuildsTable::ID)));
					}
				}
			}

			return 0;
		}
		
		/**
		 * Populates builds inside the edition
		 *
		 * @return void
		 */
		public function doPopulateBuilds()
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGBuildsTable::EDITION, $this->_itemid);
			$crit->addOrderBy(TBGBuildsTable::VERSION_MAJOR, 'desc');
			$crit->addOrderBy(TBGBuildsTable::VERSION_MINOR, 'desc');
			$crit->addOrderBy(TBGBuildsTable::VERSION_REVISION, 'desc');
			$res = B2DB::getTable('TBGBuildsTable')->doSelect($crit);
			$this->_builds = array();
			if ($res->count() > 0)
			{
				while ($row = $res->getNextRow())
				{
					if (!isset($this->_builds[$row->get(TBGBuildsTable::ID)]))
					{
						if (TBGContext::getUser()->hasPermission('b2buildaccess', $row->get(TBGBuildsTable::ID), 'core'))
						{
							$this->_builds[$row->get(TBGBuildsTable::ID)] = TBGFactory::buildLab($row->get(TBGBuildsTable::ID), $row);
						}
					}
				}
			}
		}

		/**
		 * Returns an array with all builds
		 *
		 * @return array|TBGBuild
		 */
		public function getBuilds()
		{
			if ($this->_builds === null)
			{
				$this->doPopulateBuilds();
			}
			return $this->_builds;
		}
		
		/**
		 * Invoked when trying to print the object
		 *
		 * @return string
		 */
		public function __toString()
		{
			return $this->_name;
		}
		
		/**
		 * Returns the edition parent project
		 *
		 * @return TBGProject
		 */
		public function getProject()
		{
			if (!$this->_project instanceof TBGProject)
			{
				$this->_project = TBGFactory::projectLab($this->_project);
			}
			return $this->_project;
		}
		
		public function addAssignee($assignee, $role)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGEditionAssigneesTable::EDITION_ID, $this->getID());
			$crit->addWhere(TBGEditionAssigneesTable::TARGET_TYPE, $role);
			switch (true)
			{
				case ($assignee instanceof TBGUser):
					$crit->addWhere(TBGEditionAssigneesTable::UID, $assignee->getID());
					break;
				case ($assignee instanceof TBGTeam):
					$crit->addWhere(TBGEditionAssigneesTable::TID, $assignee->getID());
					break;
				case ($assignee instanceof TBGCustomer):
					$crit->addWhere(TBGEditionAssigneesTable::CID, $assignee->getID());
					break;
			}
			$res = B2DB::getTable('TBGEditionAssigneesTable')->doSelectOne($crit);
			
			if (!$res instanceof B2DBRow)
			{
				$crit = new B2DBCriteria();
				switch (true)
				{
					case ($assignee instanceof TBGUser):
						$crit->addInsert(TBGEditionAssigneesTable::UID, $assignee->getID());
						break;
					case ($assignee instanceof TBGTeam):
						$crit->addInsert(TBGEditionAssigneesTable::TID, $assignee->getID());
						break;
					case ($assignee instanceof TBGCustomer):
						$crit->addInsert(TBGEditionAssigneesTable::CID, $assignee->getID());
						break;
				}
				$crit->addInsert(TBGEditionAssigneesTable::EDITION_ID, $this->getID());
				$crit->addInsert(TBGEditionAssigneesTable::TARGET_TYPE, $role);
				$crit->addInsert(TBGEditionAssigneesTable::SCOPE, TBGContext::getScope()->getID());
				try
				{
					$res = B2DB::getTable('TBGEditionAssigneesTable')->doInsert($crit);
				}
				catch (Exception $e)
				{
					throw $e;
				}
				return true;
			}
			return false;
		}

		public function getAssignees()
		{
			$uids = array();
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGEditionAssigneesTable::EDITION_ID, $this->getID());
			
			$res = B2DB::getTable('TBGEditionAssigneesTable')->doSelect($crit);
			while ($row = $res->getNextRow())
			{
				$uids[] = $row->get(TBGEditionAssigneesTable::UID);
			}
			return $uids;
		}

		/**
		 * Return the leader
		 *
		 * @return TBGIdentifiable
		 */
		public function getLeadBy()
		{
			if ($this->_lead_type == TBGIdentifiableClass::TYPE_USER)
			{
				return TBGFactory::userLab($this->_lead_by);
			}
			elseif ($this->_lead_type == TBGIdentifiableClass::TYPE_TEAM)
			{
				return TBGFactory::teamLab($this->_lead_by);
			}
			return null;
		}
		
		/**
		 * Return the leader
		 *
		 * @return TBGIdentifiable
		 */
		public function getLeader()
		{
			return $this->getLeadBy();
		}

		/**
		 * Returns the leader type
		 *
		 * @return integer
		 */
		public function getLeadType()
		{
			return $this->_lead_type;
		}
		
		/**
		 * Returns whether or not this project has a leader set
		 * 
		 * @return boolean
		 */
		public function hasLeader()
		{
			return ($this->getLeadBy() instanceof TBGIdentifiable) ? true : false;
		}
		
		/**
		 * Returns the QA type
		 *
		 * @return integer
		 */
		public function getQAType()
		{
			return $this->_qa_type;
		}

		/**
		 * Return the owner
		 *
		 * @return TBGIdentifiable
		 */
		public function getOwnedBy()
		{
			if ($this->_owned_type == TBGIdentifiableClass::TYPE_USER)
			{
				return TBGFactory::userLab($this->_owned_by);
			}
			elseif ($this->_owned_type == TBGIdentifiableClass::TYPE_TEAM)
			{
				return TBGFactory::teamLab($this->_owned_by);
			}
			return null;
		}
		
		/**
		 * Alias for getOwnedBy
		 * 
		 * @see getOwnedBy
		 * @return TBGIdentifiable
		 */
		public function getOwner()
		{
			return $this->getOwnedBy();
		}
		
		/**
		 * Returns the owner type
		 *
		 * @return integer
		 */
		public function getOwnerType()
		{
			return $this->_owned_type;
		}

		/**
		 * Returns whether or not this project has an owner set
		 * 
		 * @return boolean
		 */
		public function hasOwner()
		{
			return ($this->getOwnedBy() instanceof TBGIdentifiable) ? true : false;
		}
		
		/**
		 * Return the assignee
		 *
		 * @return TBGIdentifiable
		 */
		public function getQA()
		{
			if ($this->_qa_type == TBGIdentifiableClass::TYPE_USER)
			{
				return TBGFactory::userLab($this->_qa);
			}
			elseif ($this->_qa_type == TBGIdentifiableClass::TYPE_TEAM)
			{
				return TBGFactory::teamLab($this->_qa);
			}
			return null;
		}

		/**
		 * Returns whether or not this project has a QA set
		 * 
		 * @return boolean
		 */
		public function hasQA()
		{
			return ($this->getQA() instanceof TBGIdentifiable) ? true : false;
		}
		
		public function setLeadBy($l_id, $l_type)
		{
			$this->_lead_by = (int) $l_id;
			$this->_lead_type = (int) $l_type;
		}

		public function setQA($q_id, $q_type)
		{
			$this->_qa = (int) $q_id;
			$this->_qa_type = (int) $q_type;
		}

		public function setOwner($o_id, $o_type)
		{
			$this->_owned_by = (int) $o_id;
			$this->_owned_type = (int) $o_type;
		}
				
		/**
		 * Set if the edition is released
		 *
		 * @param integer|boolean $released
		 */
		public function setReleased($released)
		{
			$this->_isreleased = (bool) $released;
		}

		/**
		 * Set if the edition is locked
		 *
		 * @param integer|boolean $locked
		 */
		public function setLocked($locked)
		{
			$this->_locked = (bool) $locked;
		}
		
		/**
		 * Set the release date
		 *
		 * @param integer $release_date
		 */
		public function setReleaseDate($release_date)
		{
			$this->_release_date = $release_date;
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
		
		/**
		 * Set the edition description
		 *
		 * @param string $description
		 */
		public function setDescription($description)
		{
			$this->_description = $description;
		}
		
		/**
		 * Set the editions documentation url
		 *
		 * @param string $doc_url
		 */
		public function setDocumentationURL($doc_url)
		{
			$this->_doc_url = $doc_url;
		}

		public function delete()
		{
			B2DB::getTable('TBGEditionsTable')->doDeleteById($this->getID());
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGEditionAssigneesTable::EDITION_ID, $this->getID());
			$crit->addWhere(TBGEditionAssigneesTable::SCOPE, TBGContext::getScope()->getID());
			B2DB::getTable('TBGEditionAssigneesTable')->doDelete($crit);
		}
		
		public function save()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGEditionsTable::DESCRIPTION, $this->_description);
			$crit->addUpdate(TBGEditionsTable::DOC_URL, $this->_doc_url);
			$crit->addUpdate(TBGEditionsTable::NAME, $this->_name);
			$crit->addUpdate(TBGEditionsTable::LOCKED, $this->_locked);
			$crit->addUpdate(TBGEditionsTable::PLANNED_RELEASED, $this->_isplannedreleased);
			$crit->addUpdate(TBGEditionsTable::RELEASED, $this->_isreleased);
			$crit->addUpdate(TBGEditionsTable::RELEASE_DATE, $this->_release_date);
			B2DB::getTable('TBGEditionsTable')->doUpdateById($crit, $this->getID());
			
			return true;
		}
		
		/**
		 * Whether or not the current user can access the edition
		 * 
		 * @return boolean
		 */
		public function hasAccess()
		{
			return TBGContext::getUser()->hasPermission('b2editionaccess', $this->getID(), 'core');			
		}
		
	}
