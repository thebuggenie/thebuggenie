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
		static $_db_connection = null;
		
		static public function doConnect()
		{
			if (!class_exists('PDO'))
			{
				throw new B2DBException('The B2DB PDO engine needs the "PDO" PHP libraries installed. See http://php.net/PDO for more information.');
			}
			try
			{
				$uname = self::getUname();
				$pwd = self::getPasswd();
				if (!($dsn = self::getDSN()))
				{
					$dsn = self::getDBtype() . ":host=" . self::getHost();
					if (self::getPort())
					{
						$dsn .= ';port=' . self::getPort();
					}
					/*switch (self::getDBtype())
					{
						case 'pgsql':
							$dsn .= ";dbname=" . self::getDBname() . "\'";
							break;
						default:
							$dsn .= ';dbname='.self::getDBname();
							break;
					}*/
                                        $dsn .= ';dbname='.self::getDBname();
					self::$_dsn = $dsn;
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
		
		static public function createDatabase($db_name)
		{
			$res = self::getDBLink()->query('create database ' . $db_name);
		}
		
		static public function doSelectDB($db = null)
		{
			return true;
		}
		
		static public function closeDBLink()
		{
			self::$_db_connection = null;
		}
		
		/**
		 * Returns PDO object
		 *
		 * @return PDO
		 */
		static public function getDBLink()
		{
			return self::$_db_connection;
		}
		
		/**
		 * returns a PDO resultset
		 *
		 * @param string $sql
		 */
		static public function simpleQuery($sql)
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
