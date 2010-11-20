<?php

	class TBGCategory extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::CATEGORY;

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

		/**
		 * Create a new resolution
		 *
		 * @param string $name The status description
		 *
		 * @return TBGResolution
		 */
		public static function createNew($name)
		{
			$res = parent::_createNew($name, self::CATEGORY);
			return TBGContext::factory()->TBGCategory($res->getInsertID());
		}

		/**
		 * Delete a category id
		 *
		 * @param integer $id
		 */
		public static function delete($id)
		{
			TBGListTypesTable::getTable()->deleteByTypeAndId(self::CATEGORY, $id);
		}

	}
