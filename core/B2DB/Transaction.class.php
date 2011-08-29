<?php

	namespace b2db;
	
	/**
	 * Transaction class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package b2db
	 * @subpackage core
	 */

	/**
	 * Transaction class
	 *
	 * @package b2db
	 * @subpackage core
	 */
	class Transaction
	{
		protected $state = 0;
		
		const DB_TRANSACTION_UNSTARTED = 0;
		const DB_TRANSACTION_STARTED = 1;
		const DB_TRANSACTION_COMMITED = 2;
		const DB_TRANSACTION_ROLLEDBACK = 3;
		const DB_TRANSACTION_ENDED = 4;
		
		public function __construct()
		{
			if (Core::getDBLink()->beginTransaction())
			{
				$this->state = self::DB_TRANSACTION_STARTED;
				Core::setTransaction(true);
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
			if ($this->state == self::DB_TRANSACTION_COMMITED)
			{
				$this->state = self::DB_TRANSACTION_ENDED;
				Core::setTransaction(false);
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
				if (Core::getDBLink()->commit())
				{
					$this->state = self::DB_TRANSACTION_COMMITED;
					Core::setTransaction(false);
				}
				else
				{
					throw new Exception('Error committing transaction: ' . Core::getDBLink()->error);
				}
			}
			else
			{
				throw new Exception('There is no active transaction');
			}
		}
		
		public function rollback()
		{
			if ($this->state == self::DB_TRANSACTION_STARTED)
			{
				if (Core::getDBLink()->rollback())
				{
					$this->state = self::DB_TRANSACTION_ROLLEDBACK;
					Core::setTransaction(false);
				}
				else
				{
					throw new Exception('Error rolling back transaction: ' . Core::getDBLink()->error);
				}
			}
			else
			{
				throw new Exception('There is no active transaction');
			}
		}
		
	}
