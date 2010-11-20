<?php

	class TBGSeverity extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::SEVERITY;

		/**
		 * Returns all severities available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			if (self::$_items === NULL)
			{
				self::$_items = array();
				if ($items = TBGListTypesTable::getTable()->getAllByItemType(self::SEVERITY))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$row_id] = TBGContext::factory()->TBGSeverity($row_id, $row);
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
			$res = parent::_createNew($name, self::SEVERITY);
			return TBGContext::factory()->TBGSeverity($res->getInsertID());
		}

		/**
		 * Delete a severity id
		 *
		 * @param integer $id
		 */
		public static function delete($id)
		{
			TBGListTypesTable::getTable()->deleteByTypeAndId(self::SEVERITY, $id);
		}
	
	}

