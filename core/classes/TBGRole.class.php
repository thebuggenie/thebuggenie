<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
	class TBGRole extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::ROLE;

		/**
		 * @Relates(collection=true, joinclass="TBGRolePermissionsTable", foreign_column="permission")
		 */
		protected $_permissions = null;

		public static function loadFixtures(TBGScope $scope)
		{
			$roles = array();
			$roles['Developer'] = TBGProjectAssigneesTable::TYPE_DEVELOPER;
			$roles['Project manager'] = TBGProjectAssigneesTable::TYPE_PROJECTMANAGER;
			$roles['Tester'] = TBGProjectAssigneesTable::TYPE_TESTER;
			$roles['Documentation editor'] = TBGProjectAssigneesTable::TYPE_DOCUMENTOR;
			
			foreach ($roles as $name => $itemdata)
			{
				$role = new TBGRole();
				$role->setName($name);
				$role->setItemdata($itemdata);
				$role->setScope($scope);
				$role->save();
			}
		}
		
		/**
		 * Returns all project roles available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			return TBGListTypesTable::getTable()->getAllByItemType(self::ROLE);
		}

		public static function getAllForProject(TBGProject $project)
		{
			//
		}

	}
