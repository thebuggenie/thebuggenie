<?php

	class TBGStatus extends TBGDatatype 
	{

		protected static $_items = null;
		
		protected $_itemtype = TBGDatatype::STATUS;
		
		public static function loadFixtures(TBGScope $scope)
		{
			$statuses = array();
			$statuses['New'] = '#FFF';
			$statuses['Investigating'] = '#C2F533';
			$statuses['Confirmed'] = '#FF55AA';
			$statuses['Not a bug'] = '#44FC1D';
			$statuses['Being worked on'] = '#5C5';
			$statuses['Near completion'] = '#7D3';
			$statuses['Ready for testing / QA'] = '#55C';
			$statuses['Testing / QA'] = '#77C';
			$statuses['Closed'] = '#C2F588';
			$statuses['Postponed'] = '#FA5';
			$statuses['Done'] = '#7D3';
			$statuses['Fixed'] = '#5C5';

			foreach ($statuses as $name => $itemdata)
			{
				$status = new TBGStatus();
				$status->setName($name);
				$status->setItemdata($itemdata);
				$status->save();
			}
		}
		
		/**
		 * Returns all statuses available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			if (self::$_items === null)
			{
				self::$_items = array();
				if ($items = TBGListTypesTable::getTable()->getAllByItemType(self::STATUS))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$row_id] = TBGContext::factory()->TBGStatus($row_id, $row);
					}
				}
			}
			return self::$_items;
		}

		/**
		 * Create a new status
		 *
		 * @param string $name The status description
		 * @param string $itemdata[optional] The color if any (default FFF)
		 *
		 * @return TBGStatus
		 */
		public static function createNew($name, $itemdata = null)
		{
			$itemdata = ($itemdata === null || trim($itemdata) == '') ? '#FFF' : $itemdata;
			if (mb_substr($itemdata, 0, 1) != '#')
			{
				$itemdata = '#'.$itemdata;
			}
			
			$res = parent::_createNew($name, self::STATUS, $itemdata);
			return TBGContext::factory()->TBGStatus($res->getInsertID());
		}

		public static function getStatusByKeyish($key)
		{
			foreach (self::getAll() as $status)
			{
				if ($status->getKey() == str_replace(array(' ', '/'), array('', ''), mb_strtolower($key)))
				{
					return $status;
				}
			}
			return null;
		}
		
		/**
		 * Return the status color
		 * 
		 * @return string
		 */
		public function getColor()
		{
			return $this->_itemdata;
		}
		
		public function hasLinkedWorkflowStep()
		{
			return (bool) \b2db\Core::getTable('TBGWorkflowStepsTable')->countByStatusID($this->getID());
		}
		
		public function canBeDeleted()
		{
			return !$this->hasLinkedWorkflowStep();
		}

	}
