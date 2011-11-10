<?php

	/**
	 * @Table(name="TBGListTypesTable")
	 */
	class TBGSeverity extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::SEVERITY;

		public static function loadFixtures(TBGScope $scope)
		{
			$severities = array();
			$severities['Low'] = '';
			$severities['Normal'] = '';
			$severities['Critical'] = '';

			foreach ($severities as $name => $itemdata)
			{
				$severity = new TBGSeverity();
				$severity->setName($name);
				$severity->setItemdata($itemdata);
				$severity->save();
			}
		}
		
		/**
		 * Returns all severities available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			return TBGListTypesTable::getTable()->getAllByItemType(self::SEVERITY);
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

	}
