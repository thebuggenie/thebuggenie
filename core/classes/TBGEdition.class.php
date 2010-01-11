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
				$crit->addInsert(B2tEditions::ID, $e_id);
			}
			$crit->addInsert(B2tEditions::NAME, $e_name);
			$crit->addInsert(B2tEditions::PROJECT, $p_id);
			$crit->addInsert(B2tEditions::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(B2tEditions::DESCRIPTION, '');
			$res = B2DB::getTable('B2tEditions')->doInsert($crit);
			
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
				if ($res = B2DB::getTable('B2tEditions')->getByProjectID($project_id))
				{
					while ($row = $res->getNextRow())
					{
						$edition = TBGFactory::editionLab($row->get(B2tEditions::ID), $row);
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
				$crit->addWhere(B2tEditions::SCOPE, TBGContext::getScope()->getID());
				$row = B2DB::getTable('B2tEditions')->doSelectById($e_id, $crit);
			}
			if ($row instanceof B2DBRow)
			{
				$this->_itemid = $e_id;
				$this->_name = $row->get(B2tEditions::NAME);
				$this->_isdefault = (bool) $row->get(B2tEditions::IS_DEFAULT);
				$this->_locked = (bool) $row->get(B2tEditions::LOCKED);
				$this->_isreleased = (bool) $row->get(B2tEditions::RELEASED);
				$this->_isplannedreleased = (bool) $row->get(B2tEditions::PLANNED_RELEASED);
				$this->_release_date = $row->get(B2tEditions::RELEASE_DATE);
				$this->_description = $row->get(B2tEditions::DESCRIPTION);
				$this->_lead_by = $row->get(B2tEditions::LEAD_BY);
				$this->_lead_type = $row->get(B2tEditions::LEAD_TYPE);
				$this->_qa = $row->get(B2tEditions::QA);
				$this->_qa_type = $row->get(B2tEditions::QA_TYPE);
				$this->_project = $row->get(B2tEditions::PROJECT);
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
			$crit->addWhere(B2tEditionComponents::EDITION, $this->_itemid);
			$res = B2DB::getTable('B2tEditionComponents')->doSelect($crit);
			if ($res->count() > 0)
			{
				while ($row = $res->getNextRow())
				{
					$this->_components[$row->get(B2tEditionComponents::COMPONENT)] = TBGFactory::componentLab($row->get(B2tEditionComponents::COMPONENT));
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
			$crit->addInsert(B2tEditionComponents::COMPONENT, $c_id);
			$crit->addInsert(B2tEditionComponents::EDITION, $this->getID());
			$crit->addInsert(B2tEditionComponents::SCOPE, TBGContext::getScope()->getID());
			$res = B2DB::getTable('B2tEditionComponents')->doInsert($crit);
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
			$crit->addWhere(B2tEditionComponents::EDITION, $this->getID());
			$crit->addWhere(B2tEditionComponents::COMPONENT, (int) $c_id);
			$crit->addWhere(B2tEditionComponents::SCOPE, TBGContext::getScope()->getID());
			B2DB::getTable('B2tEditionComponents')->doDelete($crit);
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
			$crit->addSelectionColumn(B2tBuilds::ID);
			$crit->addWhere(B2tBuilds::EDITION, $this->_itemid);
			$crit->addWhere(B2tBuilds::RELEASED, 1);
			$crit->addOrderBy(B2tBuilds::RELEASE_DATE, 'desc');
			$res = B2DB::getTable('B2tBuilds')->doSelect($crit);
			
			if ($res->count() > 0)
			{
				while ($row = $res->getNextRow())
				{
					if (TBGContext::getUser()->hasPermission('b2buildaccess', $row->get(B2tBuilds::ID), 'core'))
					{
						return(TBGFactory::buildLab($row->get(B2tBuilds::ID)));
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
			$crit->addWhere(B2tBuilds::EDITION, $this->_itemid);
			$crit->addOrderBy(B2tBuilds::VERSION_MAJOR, 'desc');
			$crit->addOrderBy(B2tBuilds::VERSION_MINOR, 'desc');
			$crit->addOrderBy(B2tBuilds::VERSION_REVISION, 'desc');
			$res = B2DB::getTable('B2tBuilds')->doSelect($crit);
			$this->_builds = array();
			if ($res->count() > 0)
			{
				while ($row = $res->getNextRow())
				{
					if (!isset($this->_builds[$row->get(B2tBuilds::ID)]))
					{
						if (TBGContext::getUser()->hasPermission('b2buildaccess', $row->get(B2tBuilds::ID), 'core'))
						{
							$this->_builds[$row->get(B2tBuilds::ID)] = TBGFactory::buildLab($row->get(B2tBuilds::ID), $row);
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
			$crit->addWhere(B2tEditionAssignees::EDITION_ID, $this->getID());
			$crit->addWhere(B2tEditionAssignees::TARGET_TYPE, $role);
			switch (true)
			{
				case ($assignee instanceof TBGUser):
					$crit->addWhere(B2tEditionAssignees::UID, $assignee->getID());
					break;
				case ($assignee instanceof TBGTeam):
					$crit->addWhere(B2tEditionAssignees::TID, $assignee->getID());
					break;
				case ($assignee instanceof TBGCustomer):
					$crit->addWhere(B2tEditionAssignees::CID, $assignee->getID());
					break;
			}
			$res = B2DB::getTable('B2tEditionAssignees')->doSelectOne($crit);
			
			if (!$res instanceof B2DBRow)
			{
				$crit = new B2DBCriteria();
				switch (true)
				{
					case ($assignee instanceof TBGUser):
						$crit->addInsert(B2tEditionAssignees::UID, $assignee->getID());
						break;
					case ($assignee instanceof TBGTeam):
						$crit->addInsert(B2tEditionAssignees::TID, $assignee->getID());
						break;
					case ($assignee instanceof TBGCustomer):
						$crit->addInsert(B2tEditionAssignees::CID, $assignee->getID());
						break;
				}
				$crit->addInsert(B2tEditionAssignees::EDITION_ID, $this->getID());
				$crit->addInsert(B2tEditionAssignees::TARGET_TYPE, $role);
				$crit->addInsert(B2tEditionAssignees::SCOPE, TBGContext::getScope()->getID());
				try
				{
					$res = B2DB::getTable('B2tEditionAssignees')->doInsert($crit);
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
			$crit->addWhere(B2tEditionAssignees::EDITION_ID, $this->getID());
			
			$res = B2DB::getTable('B2tEditionAssignees')->doSelect($crit);
			while ($row = $res->getNextRow())
			{
				$uids[] = $row->get(B2tEditionAssignees::UID);
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
			B2DB::getTable('B2tEditions')->doDeleteById($this->getID());
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tEditionAssignees::EDITION_ID, $this->getID());
			$crit->addWhere(B2tEditionAssignees::SCOPE, TBGContext::getScope()->getID());
			B2DB::getTable('B2tEditionAssignees')->doDelete($crit);
		}
		
		public function save()
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tEditions::DESCRIPTION, $this->_description);
			$crit->addUpdate(B2tEditions::DOC_URL, $this->_doc_url);
			$crit->addUpdate(B2tEditions::NAME, $this->_name);
			$crit->addUpdate(B2tEditions::LOCKED, $this->_locked);
			$crit->addUpdate(B2tEditions::PLANNED_RELEASED, $this->_isplannedreleased);
			$crit->addUpdate(B2tEditions::RELEASED, $this->_isreleased);
			$crit->addUpdate(B2tEditions::RELEASE_DATE, $this->_release_date);
			B2DB::getTable('B2tEditions')->doUpdateById($crit, $this->getID());
			
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
