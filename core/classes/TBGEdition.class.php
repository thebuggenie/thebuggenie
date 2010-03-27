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
		protected $_project = null;
		
		/**
		 * Editions components
		 *
		 * @var array|TBGComponent
		 */
		protected $_components = null;
		
		/**
		 * Edition builds
		 *
		 * @var array|TBGBuild
		 */
		protected $_builds = null;
		
		protected $_description = '';
		
		/**
		 * The edition leader
		 *
		 * @var TBGIdentifiableClass
		 */
		protected $_lead_by = null;
		
		/**
		 * The leader type for the edition, TBGIdentifiableClass::TYPE_USER or TBGIdentifiableClass::TYPE_TEAM
		 *
		 * @var integer
		 */
		protected $_lead_type = 0;

		/**
		 * The QA responsible for this edition
		 *
		 * @var TBGIdentifiableClass
		 */
		protected $_qa = null;
		
		/**
		 * The qa type for the edition, TBGIdentifiableClass::TYPE_USER or TBGIdentifiableClass::TYPE_TEAM
		 *
		 * @var integer
		 */
		protected $_qa_type = 0;
		
		/**
		 * The editions documentation URL
		 * 
		 * @var string
		 */
		protected $_doc_url = '';
						
		/**
		 * The owner type for the edition, TBGIdentifiableClass::TYPE_USER or TBGIdentifiableClass::TYPE_TEAM
		 * 
		 * @var integer
		 */
		protected $_owned_type = 0;
		
		/**
		 * The owner of the project
		 *  
		 * @var TBGIdentifiableClass
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
			$edition_id = B2DB::getTable('TBGEditionsTable')->createNew($e_name, $p_id, $e_id);
			
			TBGContext::setPermission("canseeedition", $edition_id, "core", 0, TBGContext::getUser()->getGroup()->getID(), 0, true);
			
			$edition = TBGFactory::editionLab($edition_id);
			TBGEvent::createNew('core', 'TBGProject::createNew', $edition)->trigger();

			return $edition;
		}

		/**
		 * Retrieve all editions for a specific project
		 *
		 * @param integer $project_id
		 * 
		 * @return array
		 */
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
				TBGEvent::createNew('core', 'TBGEdition::__construct', $this)->trigger();
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
		protected function _populateComponents()
		{
			if ($this->_components === null)
			{
				$this->_components = array();
				if ($res = B2DB::getTable('TBGEditionComponentsTable')->getByEditionID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_components[$row->get(TBGEditionComponentsTable::COMPONENT)] = TBGFactory::componentLab($row->get(TBGEditionComponentsTable::COMPONENT));
					}
				}
			}
		}
		
		/**
		 * Returns an array with all components
		 *
		 * @return array|TBGComponent
		 */
		public function getComponents()
		{
			$this->_populateComponents();
			return $this->_components;
		}
		
		/**
		 * Whether or not this edition has a component enabled
		 * 
		 * @param TBGComponent|integer $component The component to check for
		 * 
		 * @return boolean
		 */
		public function hasComponent($c_id)
		{
			if ($c_id instanceof TBGComponent)
			{
				$c_id = $c_id->getID();
			}
			return array_key_exists($c_id, $this->getComponents());
		}

		/**
		 * Whether this edition has a description set
		 *
		 * @return string
		 */
		public function hasDescription()
		{
			return (bool) $this->getDescription();
		}

		/**
		 * Adds an existing component to the edition
		 *
		 * @param TBGComponent|integer $component
		 */
		public function addComponent($c_id)
		{
			if ($c_id instanceof TBGComponent)
			{
				$c_id = $c_id->getID();
			}
			return B2DB::getTable('TBGEditionComponentsTable')->addEditionComponent($this->getID(), $c_id);
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
			B2DB::getTable('TBGEditionComponentsTable')->removeEditionComponent($this->getID(), $c_id);
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
		 * 
		 * @return TBGComponent
		 */
		public function getComponent($c_id)
		{
			$this->_populateComponents();
			if (array_key_exists($c_id, $this->_components))
			{
				return $this->_components[$c_id];
			}

			return null;
		}
		
		/**
		 * Populates builds inside the edition
		 *
		 * @return void
		 */
		protected function _populateBuilds()
		{
			if ($this->_builds === null)
			{
				$this->_builds = array();
				if ($res = B2DB::getTable('TBGBuildsTable')->getByEditionID($this->getID()))
				{
					while ($row = $res->getNextRow())
					{
						$this->_builds[$row->get(TBGBuildsTable::ID)] = TBGFactory::buildLab($row->get(TBGBuildsTable::ID), $row);
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
			$this->_populateBuilds();
			return $this->_builds;
		}

		/**
		 * Returns the default build
		 *
		 * @return TBGBuild
		 */
		public function getDefaultBuild()
		{
			$this->_populateBuilds();
			if (count($this->_builds) > 0)
			{
				foreach ($this->_builds as $build)
				{
					if ($build->isDefault() && $build->isLocked() == false)
					{
						return $build;
					}
				}
				return array_slice($this->_builds, 0, 1);
			}
			return 0;
		}

		public function _sortBuildsByReleaseDate(TBGBuild $build1, TBGBuild $build2)
		{
			if ($build1->getReleaseDate() == $build2->getReleaseDate())
			{
				return 0;
			}
			return ($build1->getReleaseDate() < $build2->getReleaseDate()) ? -1 : 1;
		}

		/**
		 * Returns the latest build
		 *
		 * @return TBGBuild
		 */
		public function getLatestBuild()
		{
			$this->_populateBuilds();
			if (count($this->getBuilds()) > 0)
			{
				$builds = usort($this->getBuilds(), array($this, '_sortBuildsByReleaseDate'));
				return array_slice($builds, 0, 1);
			}
			return null;
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
			if (is_numeric($this->_project))
			{
				try
				{
					$this->_project = TBGFactory::projectLab($this->_project);
				}
				catch (Exception $e)
				{
					$this->_project = null;
				}
			}
			return $this->_project;
		}

		/**
		 * Add an assignee to the edition
		 *
		 * @param TBGIdentifiableClass $assignee
		 * @param integer $role
		 * 
		 * @return boolean
		 */
		public function addAssignee(TBGIdentifiableClass $assignee, $role)
		{
			return B2DB::getTable('TBGEditionAssigneesTable')->addAssigneeToEdition($this->getID(), $assignee, $role);
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
		 * @return TBGIdentifiableClass
		 */
		public function getLeadBy()
		{
			if (is_numeric($this->_lead_by))
			{
				try
				{
					if ($this->_lead_type == TBGIdentifiableClass::TYPE_USER)
					{
						$this->_lead_by = TBGFactory::userLab($this->_lead_by);
					}
					elseif ($this->_lead_type == TBGIdentifiableClass::TYPE_TEAM)
					{
						$this->_lead_by = TBGFactory::teamLab($this->_lead_by);
					}
				}
				catch (Exception $e)
				{
					$this->_lead_by = null;
					$this->_lead_type = null;
				}
			}
			return $this->_lead_by;
		}
		
		/**
		 * Return the leader
		 *
		 * @uses self::getLeadBy()
		 *
		 * @return TBGIdentifiableClass
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
			return ($this->getLeadBy() instanceof TBGIdentifiableClass) ? true : false;
		}
		
		/**
		 * Return the leader id
		 *
		 * @return integer
		 */
		public function getLeaderID()
		{
			return ($this->hasLeader()) ? $this->getLeader()->getID() : null;
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
			if (is_numeric($this->_owned_by))
			{
				try
				{
					if ($this->_owned_type == TBGIdentifiableClass::TYPE_USER)
					{
						$this->_owned_by = TBGFactory::userLab($this->_owned_by);
					}
					elseif ($this->_owned_type == TBGIdentifiableClass::TYPE_TEAM)
					{
						$this->_owned_by = TBGFactory::teamLab($this->_owned_by);
					}
				}
				catch (Exception $e)
				{
					$this->_owned_by = null;
					$this->_owned_type = null;
				}
			}
			return $this->_owned_by;
		}
		
		/**
		 * Alias for getOwnedBy
		 * 
		 * @uses self::getOwnedBy()
		 * 
		 * @return TBGIdentifiableClass
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
		 * Returns whether or not this edition has an owner set
		 * 
		 * @return boolean
		 */
		public function hasOwner()
		{
			return ($this->getOwnedBy() instanceof TBGIdentifiableClass) ? true : false;
		}
		
		/**
		 * Return the owner id
		 *
		 * @return integer
		 */
		public function getOwnerID()
		{
			return ($this->hasOwner()) ? $this->getOwner()->getID() : null;
		}

		/**
		 * Return the assignee
		 *
		 * @return TBGIdentifiableClass
		 */
		public function getQA()
		{
			if (is_numeric($this->_qa))
			{
				try
				{
					if ($this->_qa_type == TBGIdentifiableClass::TYPE_USER)
					{
						$this->_qa = TBGFactory::userLab($this->_qa);
					}
					elseif ($this->_qa_type == TBGIdentifiableClass::TYPE_TEAM)
					{
						$this->_qa = TBGFactory::teamLab($this->_qa);
					}
				}
				catch (Exception $e)
				{
					$this->_qa = null;
					$this->_qa_type = null;
				}
			}
			return $this->_qa;
		}

		/**
		 * Returns whether or not this project has a QA set
		 * 
		 * @return boolean
		 */
		public function hasQA()
		{
			return ($this->getQA() instanceof TBGIdentifiableClass) ? true : false;
		}

		/**
		 * Return the qa id
		 *
		 * @return integer
		 */
		public function getQaID()
		{
			return ($this->hasQA()) ? $this->getQA()->getID() : null;
		}
		
		public function setLeadBy(TBGIdentifiableClass $lead_by)
		{
			$this->_lead_by = $lead_by->getID();
			$this->_lead_type = $lead_by->getType();
		}

		public function setQA(TBGIdentifiableClass $qa)
		{
			$this->_qa = $qa->getID();
			$this->_qa_type = $qa->getType();
		}

		public function setOwner(TBGIdentifiableClass $owned_by)
		{
			$this->_owned_by = $owned_by->getID();
			$this->_owned_type = $owned_by->getType();
		}
				
		/**
		 * Set if the edition is released
		 *
		 * @param boolean $released[optional]
		 */
		public function setReleased($released = true)
		{
			$this->_isreleased = (bool) $released;
		}

		/**
		 * Release the edition
		 *
		 * @uses self::setReleased()
		 */
		public function release()
		{
			$this->setReleased(true);
		}

		public function retract()
		{
			$this->setReleased(false);
		}

		/**
		 * Set if the edition is locked
		 *
		 * @param boolean $locked[optional]
		 */
		public function setLocked($locked = true)
		{
			$this->_locked = (bool) $locked;
		}

		public function lock()
		{
			$this->setLocked(true);
		}

		public function unlock()
		{
			$this->setLocked(false);
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
			B2DB::getTable('TBGEditionAssigneesTable')->deleteByEditionID($crit);
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
			$crit->addUpdate(TBGEditionsTable::LEAD_BY, $this->getLeaderID());
			$crit->addUpdate(TBGEditionsTable::QA, $this->getQaID());
			//$crit->addUpdate(TBGEditionsTable::OWNED_BY, $this->getOwnerID());
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
			return TBGContext::getUser()->hasPermission('canseeedition', $this->getID(), 'core');
		}
		
	}
