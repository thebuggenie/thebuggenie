<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
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
				$resolution->setScope($scope);
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
			return TBGListTypesTable::getTable()->getAllByItemType(self::RESOLUTION);
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
