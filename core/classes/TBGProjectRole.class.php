<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
	class TBGProjectRole extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::PROJECTROLE;

		public static function loadFixtures(TBGScope $scope)
		{
			$roles = array();
			$roles['Developer'] = TBGProjectAssigneesTable::TYPE_DEVELOPER;
			$roles['Project manager'] = TBGProjectAssigneesTable::TYPE_PROJECTMANAGER;
			$roles['Tester'] = TBGProjectAssigneesTable::TYPE_TESTER;
			$roles['Documentation editor'] = TBGProjectAssigneesTable::TYPE_DOCUMENTOR;
			
			foreach ($roles as $name => $itemdata)
			{
				$role = new TBGProjectRole();
				$role->setName($name);
				$role->setItemdata($itemdata);
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
			return TBGListTypesTable::getTable()->getAllByItemType(self::PROJECTROLE);
		}

		public static function getAllForProject(TBGProject $project)
		{
			//
		}

	}
