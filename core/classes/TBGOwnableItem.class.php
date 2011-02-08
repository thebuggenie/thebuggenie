<?php

	/**
	 * Ownable item class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * Ownable item class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class TBGOwnableItem extends TBGVersionItem
	{

		/**
		 * The lead type for the project, TBGIdentifiableClass::TYPE_USER or TBGIdentifiableClass::TYPE_TEAM
		 * 
		 * @var integer
		 */
		protected $_leader_type = 0;

		/**
		 * The lead for the project
		 *  
		 * @var TBGUser
		 * @Class TBGUser
		 */
		protected $_leader = 0;
		
		/**
		 * The QA type for the project, TBGIdentifiableClass::TYPE_USER or TBGIdentifiableClass::TYPE_TEAM
		 * 
		 * @var integer
		 */
		protected $_qa_responsible_type = 0;
		
		/**
		 * The QA for the project
		 *  
		 * @var TBGIdentifiable
		 */
		protected $_qa_responsible = 0;
		
		/**
		 * The owner type for the project, TBGIdentifiableClass::TYPE_USER or TBGIdentifiableClass::TYPE_TEAM
		 * 
		 * @var integer
		 */
		protected $_owner_type = 0;
		
		/**
		 * The owner of the project
		 *  
		 * @var TBGIdentifiable
		 */
		protected $_owner = 0;

		/**
		 * Return the identifiable object for a specific field
		 *
		 * @param string $field
		 *
		 * @return TBGIdentifiable
		 */
		protected function _getIdentifiable($field)
		{
			if (is_numeric($this->$field))
			{
				$type_field = "{$field}_type";
				try
				{
					if ($this->$type_field == TBGIdentifiableClass::TYPE_USER)
					{
						$this->$field = TBGContext::factory()->TBGUser($this->$field);
					}
					elseif ($this->$type_field == TBGIdentifiableClass::TYPE_TEAM)
					{
						$this->$field = TBGContext::factory()->TBGTeam($this->$field);
					}
				}
				catch (Exception $e)
				{
					$this->$field = null;
					$this->$type_field = null;
				}
			}
	
			return $this->$field;
		}
		
		/**
		 * Returns an identifiable type
		 *
		 * @param string $field
		 *
		 * @return integer
		 */
		protected function _getFieldType($field)
		{
			$identifiable = $this->_getIdentifiable($field);
			return ($identifiable instanceof TBGIdentifiableClass) ? $identifiable->getType() : null;
		}
		
		/**
		 * Return the field id for a valid identifiable if true
		 *
		 * @param string $field
		 *
		 * @return integer
		 */
		protected function _getFieldID($field)
		{
			$identifiable = $this->_getIdentifiable($field);
			return ($identifiable instanceof TBGIdentifiableClass) ? $identifiable->getID() : null;
		}
		
		/**
		 * Returns whether or not this object has the field set to and identifiable object
		 * 
		 * @param string $field
		 *
		 * @return boolean
		 */
		protected function _hasIdentifiable($field)
		{
			return (bool) ($this->_getIdentifiable($field) instanceof TBGIdentifiable);
		}
		
		/**
		 * Set an identifiable field
		 * 
		 * @param TBGIdentifiableClass $identifiable
		 * @param string $field
		 */
		protected function _setIdentifiable(TBGIdentifiableClass $identifiable, $field)
		{
			$type_field = "{$field}_type";
			
			$this->$field = $identifiable;
			$this->$type_field = $identifiable->getType();
			
			$this->applyInitialPermissionSet($identifiable, $field);
		}
		
		protected function _unsetIdentifiable($field)
		{
			$type_field = "{$field}_type";
			
			$this->$field = null;
			$this->$type_field = null;
		}

		/**
		 * Return the leader
		 *
		 * @return TBGIdentifiable
		 */
		public function getLeader()
		{
			return $this->_getIdentifiable('_leader');
		}
		
		/**
		 * Set the leader
		 * 
		 * @param TBGIdentifiableClass $leader
		 */
		public function setLeader(TBGIdentifiableClass $leader)
		{
			$this->_setIdentifiable($leader, '_leader');
		}
		
		/**
		 * Clear the leader
		 */
		public function unsetLeader()
		{
			$this->_unsetIdentifiable('_leader');
		}
		
		/**
		 * Return the leader type
		 * 
		 * @return integer
		 */
		public function getLeaderType()
		{
			return $this->_getFieldType('_leader');
		}
		
		/**
		 * Return the leader id
		 * 
		 * @return integer
		 */
		public function getLeaderID()
		{
			return $this->_getFieldID('_leader');
		}
		
		/**
		 * Return whether the leader is set
		 * 
		 * @return boolean
		 */
		public function hasLeader()
		{
			return $this->_hasIdentifiable('_leader');
		}
		
		/**
		 * Return the owner
		 *
		 * @return TBGIdentifiable
		 */
		public function getOwner()
		{
			return $this->_getIdentifiable('_owner');
		}
		
		/**
		 * Set the owner
		 * 
		 * @param TBGIdentifiableClass $owner
		 */
		public function setOwner(TBGIdentifiableClass $owner)
		{
			$this->_setIdentifiable($owner, '_owner');
		}
		
		/**
		 * Clear the owner
		 */
		public function unsetOwner()
		{
			$this->_unsetIdentifiable('_owner');
		}
		
		/**
		 * Return the owner type
		 * 
		 * @return integer
		 */
		public function getOwnerType()
		{
			return $this->_getFieldType('_owner');
		}
		
		/**
		 * Return the owner id
		 * 
		 * @return integer
		 */
		public function getOwnerID()
		{
			return $this->_getFieldID('_owner');
		}
		
		/**
		 * Return whether the owner is set
		 * 
		 * @return boolean
		 */
		public function hasOwner()
		{
			return $this->_hasIdentifiable('_owner');
		}
		
		/**
		 * Return the qa responsible
		 *
		 * @return TBGIdentifiable
		 */
		public function getQaResponsible()
		{
			return $this->_getIdentifiable('_qa_responsible');
		}
		
		/**
		 * Set the qa responsible
		 * 
		 * @param TBGIdentifiableClass $qa responsible
		 */
		public function setQaResponsible(TBGIdentifiableClass $qa_responsible)
		{
			$this->_setIdentifiable($qa_responsible, '_qa_responsible');
		}
		
		/**
		 * Clear the qa responsible
		 */
		public function unsetQaResponsible()
		{
			$this->_unsetIdentifiable('_qa_responsible');
		}
		
		/**
		 * Return the qa responsible type
		 * 
		 * @return integer
		 */
		public function getQaResponsibleType()
		{
			return $this->_getFieldType('_qa_responsible');
		}
		
		/**
		 * Return the qa responsible id
		 * 
		 * @return integer
		 */
		public function getQaResponsibleID()
		{
			return $this->_getFieldID('_qa_responsible');
		}
		
		/**
		 * Return whether the qa responsible is set
		 * 
		 * @return boolean
		 */
		public function hasQaResponsible()
		{
			return $this->_hasIdentifiable('_qa_responsible');
		}
		
		public function applyInitialPermissionSet(TBGIdentifiable $identifiable, $type)
		{
			$permission_set = TBGContext::getProjectAssigneeDefaultPermissionSet($this, $type);
			$uid = ($identifiable->getType() == TBGIdentifiableClass::TYPE_USER) ? $identifiable->getID() : null;
			$tid = ($identifiable->getType() == TBGIdentifiableClass::TYPE_TEAM) ? $identifiable->getID() : null;
			
			foreach ($permission_set as $permission)
			{
				TBGContext::setPermission($permission, $this->getID(), 'core', $uid, null, $tid, true);
			}
			
			if (!$this instanceof TBGProject)
			{
				$extrapermissions = array();
				$extrapermissions[] = 'page_project_allpages_access';
				$extrapermissions[] = 'canseeproject';
				$extrapermissions[] = 'canseeprojecthierarchy';
				$extrapermissions[] = 'cancreateandeditissues';
				$extrapermissions[] = 'canpostandeditcomments';
				
				$project_id = $this->getProject()->getID();
				foreach ($extrapermissions as $permission)
				{
					TBGContext::setPermission($permission, $project_id, 'core', $uid, null, $tid, true);
				}
			}
		}
		
	}