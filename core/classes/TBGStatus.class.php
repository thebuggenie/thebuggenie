<?php

	class TBGStatus extends TBGDatatype 
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
						self::$_items[$row_id] = TBGFactory::TBGStatusLab($row_id, $row);
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
			if (substr($itemdata, 0, 1) != '#')
			{
				$itemdata = '#'.$itemdata;
			}
			
			$res = parent::_createNew($name, self::STATUS, $itemdata);
			return TBGFactory::TBGStatusLab($res->getInsertID());
		}

		/**
		 * Delete a status id
		 *
		 * @param integer $id
		 */
		public static function delete($id)
		{
			B2DB::getTable('B2tListTypes')->deleteByTypeAndId(self::STATUS, $id);
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