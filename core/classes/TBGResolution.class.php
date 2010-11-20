<?php

	class TBGResolution extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::RESOLUTION;

		/**
		 * Returns all resolutions available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			if (self::$_items === NULL)
			{
				self::$_items = array();
				if ($items = TBGListTypesTable::getTable()->getAllByItemType(self::RESOLUTION))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$row_id] = TBGContext::factory()->TBGResolution($row_id, $row);
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
			$res = parent::_createNew($name, self::RESOLUTION);
			return TBGContext::factory()->TBGResolution($res->getInsertID());
		}

		/**
		 * Delete a resolution id
		 *
		 * @param integer $id
		 */
		public static function delete($id)
		{
			TBGListTypesTable::getTable()->deleteByTypeAndId(self::RESOLUTION, $id);
		}

	}

