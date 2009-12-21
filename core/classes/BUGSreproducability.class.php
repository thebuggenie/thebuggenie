<?php

	class BUGSreproducability extends BUGSdatatype 
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
				if ($items = B2DB::getTable('B2tListTypes')->getAllByItemType(self::REPRODUCABILITY))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$row_id] = BUGSfactory::BUGSreproducabilityLab($row_id, $row);
					}
				}
			}
			return self::$_items;
		}

		/**
		 * Delete a reproducability id
		 *
		 * @param integer $id
		 */
		public static function delete($id)
		{
			B2DB::getTable('B2tListTypes')->deleteByTypeAndId(self::REPRODUCABILITY, $id);
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