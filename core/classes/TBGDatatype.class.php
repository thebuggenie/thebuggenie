<?php

	/**
	 * Generic datatype class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Generic datatype class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	abstract class TBGDatatype extends TBGDatatypeBase
	{
		static protected $_b2dbtablename = 'TBGListTypesTable';

		/**
		 * Item type status
		 *
		 */
		const STATUS = 'status';
		
		/**
		 * Item type priority
		 *
		 */
		const PRIORITY = 'priority';
		
		/**
		 * Item type reproducability
		 *
		 */
		const REPRODUCABILITY = 'reproducability';
		
		/**
		 * Item type resolution
		 *
		 */
		const RESOLUTION = 'resolution';
		
		/**
		 * Item type severity
		 *
		 */
		const SEVERITY = 'severity';
		
		/**
		 * Item type issue type
		 *
		 */
		const ISSUETYPE = 'issuetype';
		
		/**
		 * Item type category
		 *
		 */
		const CATEGORY = 'category';
		
		/**
		 * Item type user state 
		 *
		 */
		const USERSTATE = 'userstate';
		
		public static function loadFixtures(TBGScope $scope)
		{
			TBGCategory::loadFixtures($scope);
			TBGPriority::loadFixtures($scope);
			TBGReproducability::loadFixtures($scope);
			TBGResolution::loadFixtures($scope);
			TBGSeverity::loadFixtures($scope);
			TBGStatus::loadFixtures($scope);
		}
		
		/**
		 * Create a new field option and return the row
		 *
		 * @param string $name
		 * @param string $itemtype
		 * @param mixed $itemdata
		 *
		 * @return B2DBResultset
		 */
		protected static function _createNew($name, $itemtype, $itemdata = null)
		{
			$res = TBGListTypesTable::getTable()->createNew($name, $itemtype, $itemdata);
			return $res;
		}

		public static function getTypes()
		{
			$types = array();
			$types['status'] = 'TBGStatus';
			$types['priority'] = 'TBGPriority';
			$types['category'] = 'TBGCategory';
			$types['severity'] = 'TBGSeverity';
			$types['reproducability'] = 'TBGReproducability';
			$types['resolution'] = 'TBGResolution';
			
			return $types;
		}

		public function isBuiltin()
		{
			return true;
		}

	}
