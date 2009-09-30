<?php

	/**
	 * MySQLi B2DB class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage mysqli
	 */

	/**
	 * MySQLi B2DB class
	 *
	 * @package B2DB
	 * @subpackage mysqli
	 */
	class B2DB extends BaseB2DB 
	{
		/**
		 * mysqli object
		 *
		 * @var mysqli
		 */
		static $_db_connection = null;
		
		static public function doConnect()
		{
			if (!class_exists('mysqli'))
			{
				throw new B2DBException('The B2DB MySQL engine needs the "mysqli" PHP extension enabled. This is usually possible by uncommenting a line in the php configuration file. See http://php.net/mysqli for more information.');
			}
			if (self::$_db_name != '')
			{
				self::$_db_connection = new mysqli(self::$_db_host, self::$_db_uname, self::$_db_pwd, self::$_db_name);
			}
			else
			{
				self::$_db_connection = new mysqli(self::$_db_host, self::$_db_uname, self::$_db_pwd);
			}
			if (mysqli_connect_errno())
			{
				throw new B2DBException(mysqli_connect_error());
			}
		}
		
		static public function createDatabase($db_name)
		{
			self::$_db_connection->query('create schema if not exists ' . $db_name);
		}
		
		static public function doSelectDB($db = null)
		{
			if ($db === null)
			{
				$db = self::$_db_name;
			}
			return self::$_db_connection->select_db($db);
		}
		
		static public function closeDBLink()
		{
			self::$_db_connection->close();
		}
		
		/**
		 * Returns mysqli object
		 *
		 * @return mysqli
		 */
		static public function getDBLink()
		{
			return parent::getDBlink();
		}
		
		/**
		 * returns a mysqli resultset
		 *
		 * @param string $sql
		 */
		static public function simpleQuery($sql)
		{
			self::$_sqlhits++;
			$res = self::getDBLink()->query($sql);
			if (mysqli_errno() || !$res)
			{
				throw new B2DBException(self::$_db_connection->error);
			}
			return $res;
		}
	}

?>