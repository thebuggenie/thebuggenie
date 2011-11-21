<?php

	/**
	 * Generic datatype class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Generic datatype class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 *
	 * @Table(name="TBGListTypesTable")
	 */
	abstract class TBGDatatype extends TBGDatatypeBase
	{
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
		 * Item type project role
		 *
		 */
		const ROLE = 'role';
		
		public static function loadFixtures(TBGScope $scope)
		{
			TBGCategory::loadFixtures($scope);
			TBGPriority::loadFixtures($scope);
			TBGReproducability::loadFixtures($scope);
			TBGResolution::loadFixtures($scope);
			TBGSeverity::loadFixtures($scope);
			TBGStatus::loadFixtures($scope);
			TBGRole::loadFixtures($scope);
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
		
		public function canBeDeleted()
		{
			return true;
		}

	}
