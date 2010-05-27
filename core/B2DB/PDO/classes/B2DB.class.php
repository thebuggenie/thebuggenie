<?php

	/**
	 * PDO B2DB class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage pdo
	 */

	/**
	 * PDO B2DB class
	 *
	 * @package B2DB
	 * @subpackage pdo
	 */
	class B2DB extends BaseB2DB 
	{
		/**
		 * PDO object
		 *
		 * @var PDO
		 */
		protected static $_db_connection = null;

		public static function doConnect()
		{
			if (!class_exists('PDO'))
			{
				throw new B2DBException('The B2DB PDO engine needs the "PDO" PHP libraries installed. See http://php.net/PDO for more information.');
			}
			try
			{
				$uname = self::getUname();
				$pwd = self::getPasswd();
				if (self::$_db_connection instanceof PDO)
				{
					self::$_db_connection = null;
				}
				self::$_db_connection = new PDO(self::getDSN(), $uname, $pwd);
				if (!self::$_db_connection instanceof PDO)
				{
					throw new B2DBException('Could not connect to the database, but not caught by PDO');
				}
			}
			catch (PDOException $e)
			{
				throw new B2DBException($e->getMessage());
			}
			catch (B2DBException $e)
			{
				throw $e;
			}
		}
		
		public static function createDatabase($db_name)
		{
			$res = self::getDBLink()->query('create database ' . $db_name);
		}
		
		public static function doSelectDB($db = null)
		{
			return true;
		}
		
		public static function closeDBLink()
		{
			self::$_db_connection = null;
		}
		
		/**
		 * Returns PDO object
		 *
		 * @return PDO
		 */
		public static function getDBLink()
		{
			return self::$_db_connection;
		}
		
		/**
		 * returns a PDO resultset
		 *
		 * @param string $sql
		 */
		public static function simpleQuery($sql)
		{
			self::$_sqlhits++;
			try
			{
				$res = self::getDBLink()->query($sql);
			}
			catch (PDOException $e)
			{
				throw new B2DBException($e->getMessage());
			}
			return $res;
		}
	}
