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

		/**
		 * Returns a statement
		 *
		 * @param B2DBCriteria $crit
		 *
		 * @return B2DBStatement
		 */
		public static function getPreparedStatement($crit)
		{
			try
			{
				$statement = new B2DBStatement($crit);
			}
			catch (Exception $e)
			{
				throw $e;
			}

			return $statement;
		}

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

		/**
		 * Return the ID for the inserted record
		 */
		abstract public function getInsertID();

		/**
		 * Return the number of affected rows
		 */
		abstract public function getNumRows();

		/**
		 * Fetch the resultset
		 */
		abstract public function fetch();

		/**
		 * Prepare the statement
		 */
		abstract protected function _prepare();
		
	}
