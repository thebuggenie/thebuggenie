<?php

	class BUGSseverity extends BUGSdatatype 
	{

		protected static $_items = null;

		/**
		 * Returns all severities available
		 * 
		 * @return array 
		 */		
		public static function getAll()
		{
			if (self::$_items === NULL)
			{
				self::$_items = array();
				if ($items = B2DB::getTable('B2tListTypes')->getAllByItemType(self::SEVERITY))
				{
					foreach ($items as $row_id => $row)
					{
						self::$_items[$row_id] = BUGSfactory::BUGSseverityLab($row_id, $row);
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
		 * @return BUGSresolution
		 */
		public static function createNew($name)
		{
			$res = parent::_createNew($name, self::SEVERITY);
			return BUGSfactory::BUGSseverityLab($res->getInsertID());
		}

		/**
		 * Delete a severity id
		 *
		 * @param integer $id
		 */
		public static function delete($id)
		{
			B2DB::getTable('B2tListTypes')->deleteByTypeAndId(self::SEVERITY, $id);
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
				$this->initialize($item_id, self::SEVERITY, $row);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	
	}

?>