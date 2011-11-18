<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
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
				$priority->setScope($scope);
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
			return TBGListTypesTable::getTable()->getAllByItemType(self::PRIORITY);
		}

		public function getValue()
		{
			return $this->_itemdata;
		}

	}
