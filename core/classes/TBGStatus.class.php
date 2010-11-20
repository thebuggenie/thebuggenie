<?php

	class TBGStatus extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::STATUS;

		/**
		 * Returns all statuses available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			if (self::$_items === NULL)
			{
				self::$_items = array();
				if ($items = TBGListTypesTable::getTable()->getAllByItemType(self::STATUS))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$row_id] = TBGContext::factory()->TBGStatus($row_id, $row);
					}
				}
			}
			return self::$_items;
		}

		/**
		 * Create a new status
		 *
		 * @param string $name The status description
		 * @param string $itemdata[optional] The color if any (default FFF)
		 *
		 * @return TBGStatus
		 */
		public static function createNew($name, $itemdata = null)
		{
			$itemdata = ($itemdata === null || trim($itemdata) == '') ? '#FFF' : $itemdata;
			if (substr($itemdata, 0, 1) != '#')
			{
				$itemdata = '#'.$itemdata;
			}
			
			$res = parent::_createNew($name, self::STATUS, $itemdata);
			return TBGContext::factory()->TBGStatus($res->getInsertID());
		}

		/**
		 * Delete a status id
		 *
		 * @param integer $id
		 */
		public static function delete($id)
		{
			TBGListTypesTable::getTable()->deleteByTypeAndId(self::STATUS, $id);
		}
		
		/**
		 * Return the status color
		 * 
		 * @return string
		 */
		public function getColor()
		{
			return $this->_itemdata;
		}

	}

