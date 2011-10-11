<?php

	class TBGCategory extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::CATEGORY;

		public static function loadFixtures(TBGScope $scope)
		{
			$categories = array('General', 'Security', 'User interface');
			
			foreach ($categories as $name)
			{
				$category = new TBGCategory();
				$category->setName($name);
				$category->save();
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
		 * Returns all categories available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			if (self::$_items === NULL)
			{
				self::$_items = array();
				if ($items = TBGListTypesTable::getTable()->getAllByItemType(self::CATEGORY))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$row_id] = TBGContext::factory()->TBGCategory($row_id, $row);
					}
				}
			}
			return self::$_items;
		}

	}
