<?php

	class TBGResolution extends TBGDatatype 
	{

		protected static $_items = null;

		protected $_key = null;
		
		protected $_itemtype = TBGDatatype::RESOLUTION;

		public static function loadFixtures(TBGScope $scope)
		{
			$resolutions["CAN'T REPRODUCE"] = '';
			$resolutions["WON'T FIX"] = '';
			$resolutions["NOT AN ISSUE"] = '';
			$resolutions["POSTPONED"] = '';
			$resolutions["RESOLVED"] = '';
			$resolutions["CAN'T FIX"] = '';
			$resolutions["DUPLICATE"] = '';

			foreach ($resolutions as $name => $itemdata)
			{
				$resolution = new TBGResolution();
				$resolution->setName($name);
				$resolution->setItemdata($itemdata);
				$resolution->save();
			}
		}
		
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

		public static function getResolutionByKeyish($key)
		{
			foreach (self::getAll() as $resolution)
			{
				if ($resolution->getKey() == str_replace(array(' ', '/', "'"), array('', '', ''), mb_strtolower($key)))
				{
					return $resolution;
				}
			}
			return null;
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

		protected function _generateKey()
		{
			$this->_key = str_replace(array(' ', '/', "'"), array('', '', ''), mb_strtolower($this->getName()));
		}
		
		public function getKey()
		{
			if ($this->_key == null)
			{
				$this->_generateKey();
			}
			return $this->_key;
		}
		
	}
