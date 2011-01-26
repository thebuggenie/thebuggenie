<?php

	class TBGPriority extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::PRIORITY;

		public static function loadFixtures(TBGScope $scope)
		{
			$priorities = array();
			$priorities['Critical'] = 1;
			$priorities['Needs to be fixed'] = 2;
			$priorities['Must fix before next release'] = 3;
			$priorities['Low'] = 4;
			$priorities['Normal'] = 5;

			foreach ($priorities as $name => $itemdata)
			{
				$priority = new TBGPriority();
				$priority->setName($name);
				$priority->setItemdata($itemdata);
				$priority->save();
			}
		}
		
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
		
		public function getValue()
		{
			return $this->_itemdata;
		}

	}
