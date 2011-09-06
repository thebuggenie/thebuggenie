<?php

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
		
		public function _construct(\b2db\Row $row, $foreign_key = null)
		{
			//echo '<pre>';
			//var_dump($row);
			//var_dump($foreign_key);
			//var_dump($row->get(TBGListTypesTable::NAME, $foreign_key));
			//die();
		}

		/**
		 * Returns all project roles available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			if (self::$_items === NULL)
			{
				self::$_items = array();
				if ($items = TBGListTypesTable::getTable()->getAllByItemType(self::PROJECTROLE))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$row_id] = TBGContext::factory()->TBGProjectRole($row_id, $row);
					}
				}
			}
			return self::$_items;
		}

	}
