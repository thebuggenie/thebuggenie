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

		/**
		 * Create a new resolution
		 *
		 * @param string $name The status description
		 *
		 * @return TBGResolution
		 */
		public static function createNew($name)
		{
			$res = parent::_createNew($name, self::REPRODUCABILITY);
			return TBGContext::factory()->TBGReproducability($res->getInsertID());
		}
		
	}
