<?php

	/**
	 * B2DB Exception class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */

	/**
	 * B2DB Exception class
	 *
	 * @package B2DB
	 * @subpackage core
	 */
	class B2DBException extends Exception
	{
		
		protected $_sql = null;
		
		public function __construct($message, $sql = null)
		{
			parent::__construct($message);
			$this->_sql = $sql;
		}
		
		public function getSQL()
		{
			return $this->_sql;
		}
		
	}
	
