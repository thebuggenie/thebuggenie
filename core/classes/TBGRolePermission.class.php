<?php

	/**
	 * @Table(name="TBGRolePermissionsTable")
	 */
	class TBGRolePermission extends TBGIdentifiableClass
	{

		/**
		 * @Column(name="module", type="string", length="100")
		 */
		protected $_module = 'core';

		/**
		 * @Column(name="target_id", type="string", length="250")
		 */
		protected $_target_id;

		/**
		 * @Column(name="permission", type="string", length="250")
		 */
		protected $_permission;

		/**
		 * @Column(name="role_id", type="integer", length="10")
		 * @Relates(class="TBGRole")
		 */
		protected $_role;

		public function getModule()
		{
			return $this->_module;
		}

		public function setModule($module)
		{
			$this->_module = $module;
		}

		public function getTargetID()
		{
			return $this->_target_id;
		}

		public function setTargetID($target_id)
		{
			$this->_target_id = $target_id;
		}

		public function getPermission()
		{
			return $this->_permission;
		}

		public function setPermission($permission)
		{
			$this->_permission = $permission;
		}

		public function getRole()
		{
			return $this->_role;
		}

		public function setRole($role)
		{
			$this->_role = $role;
		}

		public function hasTargetID()
		{
			return (bool) ($this->_target_id);
		}

		public function getReplacedTargetID(TBGProject $project)
		{
			return str_replace('%project_key%', $project->getKey(), $this->_target_id);
		}

	}
