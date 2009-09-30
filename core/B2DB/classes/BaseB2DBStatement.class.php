<?php

	/**
	 * B2DB Statement Base class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */

	/**
	 * B2DB Statement Base class
	 *
	 * @package B2DB
	 * @subpackage core
	 */
	abstract class BaseB2DBStatement
	{
		/**
		 * Current BaseB2DBCriteria
		 *
		 * @var B2DBCriteria
		 */
		protected $crit;
		
		// Can be used to store statement object from sql
		public $statement;
		
		public $custom_sql = '';

		public function __construct($crit)
		{
			if ($crit instanceof BaseB2DBCriteria)
			{
				$this->crit = $crit;
			}
			else
			{
				$this->custom_sql = $crit;
			}
		}

		/**
		 * Performs database query and returns the result
		 *
		 * @param string $action
		 * @return B2DBResultset
		 */
		public function performQuery($action = '')
		{
			try
			{
				$resultset = new B2DBResultset($this);
			}
			catch (Exception $e)
			{
				throw $e;
			}
			//echo $resultset->printSQL() . '<br><br>';
			return $resultset;
		}
		
		/**
		 * Returns the criteria object
		 *
		 * @return B2DBCriteria
		 */
		public function getCriteria()
		{
			return $this->crit;
		}
		
		abstract public function getInsertID();
		
		abstract public function getNumRows();
		
		abstract public function fetch();
		
		abstract protected function _prepare();
		
	}
