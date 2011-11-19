<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
	class TBGReproducability extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::REPRODUCABILITY;

		public static function loadFixtures(TBGScope $scope)
		{
			$reproducabilities = array();
			$reproducabilities["Can't reproduce"] = '';
			$reproducabilities['Rarely'] = '';
			$reproducabilities['Often'] = '';
			$reproducabilities['Always'] = '';

			foreach ($reproducabilities as $name => $itemdata)
			{
				$reproducability = new TBGReproducability();
				$reproducability->setName($name);
				$reproducability->setItemdata($itemdata);
				$reproducability->setScope($scope);
				$reproducability->save();
			}
		}

		/**
		 * Returns all reproducabilities available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			return TBGListTypesTable::getTable()->getAllByItemType(self::REPRODUCABILITY);
		}

	}
