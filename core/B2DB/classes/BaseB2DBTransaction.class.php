<?php

	/**
	 * B2DB Transaction Base class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */

	/**
	 * B2DB Transaction Base class
	 *
	 * @package B2DB
	 * @subpackage core
	 */
	abstract class BaseB2DBTransaction
	{
		protected $statements = array();
		protected $resultsets = array();
		protected $state = 0;
		
		const DB_TRANSACTION_UNSTARTED = 0;
		const DB_TRANSACTION_STARTED = 1;
		const DB_TRANSACTION_COMMITED = 2;
		const DB_TRANSACTION_ROLLEDBACK = 3;
		const DB_TRANSACTION_ENDED = 4;
		
		/**
		 * B2DBTransaction constructor
		 *
		 * @param B2DBObject $conn
		 */
		public function __construct()
		{
			if (B2DB::getDBLink()->autocommit(false))
			{
				$this->state = self::DB_TRANSACTION_STARTED;
				B2DB::setTransaction(true);
			}
			return $this;
		}
		
		public function __destruct()
		{
			if ($this->state == self::DB_TRANSACTION_STARTED)
			{
				echo 'forcing transaction rollback';
			}
		}
		
		public function end()
		{
			if (B2DB::getDBLink()->autocommit(true))
			{
				$this->state = self::DB_TRANSACTION_ENDED;
				B2DB::setTransaction(false);
			}
		}
		
		public function commitAndEnd()
		{
			$this->commit();
			$this->end();
		}
		
		public function commit()
		{
			if ($this->state == self::DB_TRANSACTION_STARTED)
			{
				if (B2DB::getDBLink()->commit())
				{
					$this->state = self::DB_TRANSACTION_COMMITED;
					B2DB::setTransaction(false);
				}
				else
				{
					throw new B2DBException('Error committing transaction: ' . B2DB::getDBLink()->error);
				}
			}
			else
			{
				//b2db_sql_error('There is no active transaction');
				throw new B2DBException('There is no active transaction');
			}
		}
		
		public function rollback()
		{
			if ($this->state == self::DB_TRANSACTION_STARTED)
			{
				if (B2DB::getDBLink()->rollback())
				{
					$this->state = self::DB_TRANSACTION_ROLLEDBACK;
					B2DB::setTransaction(false);
				}
				else
				{
					throw new B2DBException('Error rolling back transaction: ' . B2DB::getDBLink()->error);
				}
			}
			else
			{
				throw new B2DBException('There is no active transaction');
			}
		}
		
	}
