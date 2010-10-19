<?php

	class TBGReproducability extends TBGDatatype 
	{

		protected static $_items = null;

		/**
		 * Returns all reproducabilities available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			if (self::$_items === NULL)
			{
				self::$_items = array();
				if ($items = TBGListTypesTable::getTable()->getAllByItemType(self::REPRODUCABILITY))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$row_id] = TBGFactory::TBGReproducabilityLab($row_id, $row);
					}
				}
			}
			return self::$_items;
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
			return TBGFactory::TBGReproducabilityLab($res->getInsertID());
		}

		/**
		 * Delete a reproducability id
		 *
		 * @param integer $id
		 */
		public static function delete($id)
		{
			TBGListTypesTable::getTable()->deleteByTypeAndId(self::REPRODUCABILITY, $id);
		}

		/**
		 * Constructor
		 * 
		 * @param integer $item_id The item id
		 * @param B2DBrow $row [optional] A B2DBrow to use
		 * @return 
		 */
		public function __construct($item_id, $row = null)
		{
			try
			{
				$this->initialize($item_id, self::REPRODUCABILITY, $row);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
	}

?>