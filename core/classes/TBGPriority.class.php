<?php

	class TBGPriority extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::PRIORITY;

		/**
		 * Returns all priorities available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			if (self::$_items === NULL)
			{
				self::$_items = array();
				if ($items = TBGListTypesTable::getTable()->getAllByItemType(self::PRIORITY))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$row_id] = TBGContext::factory()->TBGPriority($row_id, $row);
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
			$res = parent::_createNew($name, self::PRIORITY);
			return TBGContext::factory()->TBGPriority($res->getInsertID());
		}

		/**
		 * Delete a priority id
		 *
		 * @param integer $id
		 */
		public static function delete($id)
		{
			TBGListTypesTable::getTable()->deleteByTypeAndId(self::PRIORITY, $id);
		}

	}

