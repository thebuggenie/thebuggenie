<?php

	/**
	 * PDO transaction class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage pdo
	 */

	/**
	 * PDO transaction class
	 *
	 * @package B2DB
	 * @subpackage pdo
	 */
	class B2DBTransaction extends BaseB2DBTransaction  
	{

		/**
		 * B2DBTransaction constructor
		 *
		 * @return B2DBTransaction
		 */
		public function __construct()
		{
			if (B2DB::getDBLink()->beginTransaction())
			{
				$this->state = self::DB_TRANSACTION_STARTED;
				B2DB::setTransaction(true);
			}
			return $this;
		}
		
		public function end()
		{
			if ($this->state == self::DB_TRANSACTION_COMMITED)
			{
				$this->state = self::DB_TRANSACTION_ENDED;
				B2DB::setTransaction(false);
			}
		}
		
	}
