<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
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
