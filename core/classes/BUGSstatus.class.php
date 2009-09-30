<?php

	class BUGSstatus extends BUGSdatatype 
	{

		protected static $_items = null;

		/**
		 * Returns all statuses available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			if (self::$_items === NULL)
			{
				self::$_items = array();
				if ($items = B2DB::getTable('B2tListTypes')->getAllByItemType(self::STATUS))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$row_id] = BUGSfactory::BUGSstatusLab($row_id, $row);
					}
				}
			}
			return self::$_items;
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
				$this->initialize($item_id, self::STATUS, $row);
			}
			catch (Exception $e)
			{
				throw $e;
			}
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

	}

?>