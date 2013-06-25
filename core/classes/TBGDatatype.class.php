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
		
		/**
		 * Item type activity type
		 *
		 */
		const ACTIVITYTYPE = 'activitytype';

		public static function loadFixtures(TBGScope $scope)
		{
			TBGCategory::loadFixtures($scope);
			TBGPriority::loadFixtures($scope);
			TBGReproducability::loadFixtures($scope);
			TBGResolution::loadFixtures($scope);
			TBGSeverity::loadFixtures($scope);
			TBGStatus::loadFixtures($scope);
			TBGRole::loadFixtures($scope);
			foreach (self::getTypes() as $type => $class)
			{
				TBGContext::setPermission('set_datatype_'.$type, 0, 'core', 0, 0, 0, true, $scope->getID());
			}
		}
		
		public static function getTypes()
		{
			$types = array();
			$types[self::STATUS] = 'TBGStatus';
			$types[self::PRIORITY] = 'TBGPriority';
			$types[self::CATEGORY] = 'TBGCategory';
			$types[self::SEVERITY] = 'TBGSeverity';
			$types[self::REPRODUCABILITY] = 'TBGReproducability';
			$types[self::RESOLUTION] = 'TBGResolution';
			$types[self::ACTIVITYTYPE] = 'TBGActivityType';

			$types = TBGEvent::createNew('core', 'TBGDatatype::getTypes', null, array(), $types)->getReturnList();
			
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

		public static function has($item_id)
		{
			$items = static::getAll();
			return array_key_exists($item_id, $items);
		}

		/**
		 * Returns all severities available
		 *
		 * @return array
		 */
		public static function getAll()
		{
			return TBGListTypesTable::getTable()->getAllByItemType(static::ITEMTYPE);
		}

	}
