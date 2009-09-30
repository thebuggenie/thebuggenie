<?php

	/**
	 * B2DB Base class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */

	/**
	 * B2DB Base class
	 *
	 * @package B2DB
	 * @subpackage core
	 */
	class BaseB2DB
	{
		static protected $_db_connection;
		static protected $_db_host, $_db_uname, $_db_pwd, $_db_name, $_db_type, $_db_port = null;
		static protected $_dsn = null;
		static protected $_sqlhits;
		static protected $_throwhtmlexception = false;
		static protected $_aliases = array();
		static protected $_transaction_active = false;
		static protected $_tables = array();
		static protected $_debug_mode = true;

		/**
		 * Loads a table and adds it to the B2DBObject stack
		 * 
		 * @param BaseB2DBtable $tbl_name
		 * 
		 * @return B2DBtable
		 */
		static public function loadNewTable(BaseB2DBtable $table)
		{
			self::$_tables[get_class($table)] = $table;
			return $table;
		}
		
		static public function setDebugMode($debug_mode)
		{
			self::$_debug_mode = $debug_mode;
		}
		
		static public function isDebugMode()
		{
			return self::$_debug_mode;
		}
		
		static public function addAlias()
		{
			$rnd_no = rand(1, 3000);
			while (in_array($rnd_no, self::$_aliases))
			{
				$rnd_no = rand(1, 3000);
			}
			self::$_aliases[] = $rnd_no;
			return $rnd_no;
		}
		
		static public function initialize($dont_load_params = false)
		{
			if (!defined('B2DB_BASEPATH'))
			{
				throw new B2DBException('The constant B2DB_BASEPATH must be defined. B2DB_BASEPATH should be the full system path to B2DB');
			}
			
			try
			{
				if (!$dont_load_params)
				{
					if (file_exists(B2DB_BASEPATH . 'sql_parameters.inc.php'))
					{
						require B2DB_BASEPATH . 'sql_parameters.inc.php';
					}
				}
				
				if (self::getDBtype() != '')
				{
					$b2db_engine_path = '';
					if (file_exists(B2DB_BASEPATH . self::getDBtype() . '/classes/B2DB.class.php'))
					{
						$b2db_engine_path = B2DB_BASEPATH . self::getDBtype();
					}
					else
					{
						$b2db_engine_path = B2DB_BASEPATH . 'PDO';
					}
					
					BUGScontext::addClasspath($b2db_engine_path . '/classes/');
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			
		}
		
		static public function saveConnectionParameters()
		{
			$string = "<?php\n";
			$string .= "\t/**\n";
			$string .= "\t * B2DB sql parameters\n";
			$string .= "\t *\n";
			$string .= "\t * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>\n";
			$string .= "\t * @version 2.0\n";
			$string .= "\t * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)\n";
			$string .= "\t * @package B2DB\n";
			$string .= "\t * @subpackage core\n";
			$string .= "\t */\n";
			$string .= "\n";
			$string .= "\tself::setUname('".self::getUname()."');\n";
			$string .= "\tself::setPasswd('".self::getPasswd()."');\n";
			$string .= "\n";
			$string .= "\tself::setDSN('".self::getDSN()."');\n";
			$string .= "\n";
			$string .= "?>";
			try
			{
				if (file_put_contents(B2DB_BASEPATH . 'sql_parameters.inc.php', $string) === false)
				{
					throw new B2DBException('Could not save the database connection details');
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		/**
		 * Returns the B2DBTable
		 *
		 * @param BaseB2DBTable $tbl_name
		 * 
		 * @return BaseB2DBTable
		 */
		static public function getTable($tbl_name)
		{
			if (!isset(self::$_tables[$tbl_name]))
			{
				try
				{
					if (!class_exists($tbl_name))
					{
						throw new B2DBException("Class $tbl_name does not exist, cannot load it".print_r(get_declared_classes()).print_r(BUGScontext::getClasspaths()));
					}
					self::loadNewTable(new $tbl_name());
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			if (!isset(self::$_tables[$tbl_name]))
			{
				throw new B2DBException('Table ' . $tbl_name . ' is not loaded');
			}
			return self::$_tables[$tbl_name];
		}

		static public function getTables()
		{
			return self::$_tables;
		}

		static public function setTables($tables)
		{
			self::$_tables = $tables;
		}
		
		static public function sqlHit()
		{
			self::$_sqlhits++;
		}

		static public function getSQLHits()
		{
			return (int) self::$_sqlhits;
		}

		static public function getDBlink()
		{
			return self::$_db_connection;
		}

		static public function setDSN($dsn)
		{
			$dsn_details = parse_url($dsn);
			if (!array_key_exists('scheme', $dsn_details))
			{
				throw new B2DBException('This does not look like a valid DSN - cannot read the database type');
			}
			try
			{
				self::setDBtype($dsn_details['scheme']);
				$dsn_details = explode(';', $dsn_details['path']);
				foreach ($dsn_details as $dsn_detail)
				{
					$detail_info = explode('=', $dsn_detail);
					if (count($detail_info) != 2)
					{
						throw new B2DBException('This does not look like a valid DSN - cannot read the connection details');
					}
					switch ($detail_info[0])
					{
						case 'host':
							self::setHost($detail_info[1]);
							break;
						case 'port':
							self::setPort($detail_info[1]);
							break;
						case 'dbname':
							self::setDBname($detail_info[1]);
							break;
					}
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			self::$_dsn = $dsn;
		}
		
		static public function getDSN()
		{
			return self::$_dsn;
		}
		
		static public function setHost($host)
		{
			self::$_db_host = $host;
		}
		
		static public function getHost()
		{
			return self::$_db_host;
		}
		
		static public function getPort()
		{
			return self::$_db_port;
		}
		
		static public function setPort($port)
		{
			self::$_db_port = $port;
		}

		static public function setUname($uname)
		{
			self::$_db_uname = $uname;
		}

		static public function getUname()
		{
			return self::$_db_uname;
		}

		static public function setPasswd($upwd)
		{
			self::$_db_pwd = $upwd;
		}

		static public function getPasswd()
		{
			return self::$_db_pwd;
		}
		
		static public function setDBname($dbname)
		{
			self::$_db_name = $dbname;
		}

		static public function getDBname()
		{
			return self::$_db_name;
		}

		static public function setDBtype($dbtype)
		{
			if (self::hasDBEngine($dbtype) == false)
			{
				throw new B2DBException('The selected database is not supported: "' . $dbtype . '".');
			}
			self::$_db_type = $dbtype;
		}

		static public function getDBtype()
		{
			if (!self::$_db_type && defined('B2DB_SQLTYPE'))
			{
				self::setDBtype(B2DB_SQLTYPE);
			}
			return self::$_db_type;
		}

		static public function doConnect()
		{
			self::$_db_connection = b2db_sql_connect(self::$_db_host, self::$_db_uname, self::$_db_pwd) or die(b2db_sql_fatal_error(1));
		}

		static public function doSelectDB($db_name = null)
		{
			if ($db_name == null)
			{
				$db_name = self::$_db_name;
			}
			b2db_sql_select_db($db_name, self::$_db_connection) or die(b2db_sql_fatal_error(2));
		}

		/**
		 * Returns a statement
		 *
		 * @param B2DBCriteria $crit
		 * 
		 * @return B2DBStatement
		 */
		static public function prepareStatement($crit)
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

		static public function closeDBLink()
		{
			if (self::$_transaction_active)
			{
				b2db_sql_rollback_transaction(self::$_db_connection);
			}
			b2db_sql_close(self::$_db_connection);
		}
		
		static public function setTransaction($state)
		{
			self::$_transaction_active = $state;
		}
		
		/**
		 * Starts a new transaction
		 */
		static public function startTransaction()
		{
			return new B2DBTransaction();
		}
		
		/**
		 * Displays a nicely formatted exception message
		 *  
		 * @param B2DBException $exception
		 */
		static public function fatalError(B2DBException $exception)
		{
			$ob_status = ob_get_status();
			if (!empty($ob_status) && $ob_status['status'] != PHP_OUTPUT_HANDLER_END)
			{
				ob_end_clean();
			}
			if (self::$_throwhtmlexception)
			{
				echo "
				<style>
				body { background-color: #DFDFDF; font-family: \"Droid Sans\", \"Trebuchet MS\", \"Liberation Sans\", \"Nimbus Sans L\", \"Luxi Sans\", Verdana, sans-serif; font-size: 13px; }
				h1 { margin: 5px 0 15px 0; font-size: 18px; }
				h2 { margin: 15px 0 0 0; font-size: 15px; }
				.rounded_box {background: transparent; margin:0px;}
				.rounded_box h4 { margin-bottom: 0px; margin-top: 7px; font-size: 14px; }
				.xtop, .xbottom {display:block; background:transparent; font-size:1px;}
				.xb1, .xb2, .xb3, .xb4 {display:block; overflow:hidden;}
				.xb1, .xb2, .xb3 {height:1px;}
				.xb2, .xb3, .xb4 {background:#F9F9F9; border-left:1px solid #CCC; border-right:1px solid #CCC;}
				.xb1 {margin:0 5px; background:#CCC;}
				.xb2 {margin:0 3px; border-width:0 2px;}
				.xb3 {margin:0 2px;}
				.xb4 {height:2px; margin:0 1px;}
				.xboxcontent {display:block; background:#F9F9F9; border:0 solid #CCC; border-width:0 1px; padding: 0 5px 0 5px;}
				.xboxcontent table td.description { padding: 3px 3px 3px 0;}
				.white .xb2, .white .xb3, .white .xb4 { background: #FFF; border-color: #CCC; }
				.white .xb1 { background: #CCC; }
				.white .xboxcontent { background: #FFF; border-color: #CCC; }
				</style>
				<div class=\"rounded_box white\" style=\"margin: 30px auto 0 auto; width: 600px;\">
					<b class=\"xtop\"><b class=\"xb1\"></b><b class=\"xb2\"></b><b class=\"xb3\"></b><b class=\"xb4\"></b></b>
					<div class=\"xboxcontent\" style=\"vertical-align: middle; padding: 10px 10px 10px 15px;\">
					<img style=\"float: left; margin-right: 10px;\" src=\"".BUGScontext::getTBGPath()."messagebox_warning.png\"><h1>An error occured in the B2DB database framework</h1>
					<h2>The following error occured:</h2>
					<i>".$exception->getMessage()."</i><br>
					";
					if ($exception->getSQL())
					{
						echo "<h2>SQL was:</h2>";
						echo $exception->getSQL();
						echo '<br>';
					}
					echo "<h2>Stack trace:</h2>
					<ul>";
					foreach ($exception->getTrace() as $trace_element)
					{
						echo '<li>';
						if (array_key_exists('class', $trace_element))
						{
							echo '<strong>'.$trace_element['class'].$trace_element['type'].$trace_element['function'].'()</strong><br>';
						}
						elseif (array_key_exists('function', $trace_element))
						{
							echo '<strong>'.$trace_element['function'].'()</strong><br>';
						}
						else
						{
							echo '<strong>unknown function</strong><br>';
						}
						if (array_key_exists('file', $trace_element))
						{
							echo '<span style="color: #55F;">'.$trace_element['file'].'</span>, line '.$trace_element['line'];
						}
						else
						{
							echo '<span style="color: #C95;">unknown file</span>';
						}	
						echo '</li>';
					}
					echo "
					</ul></div>
					<b class=\"xbottom\"><b class=\"xb4\"></b><b class=\"xb3\"></b><b class=\"xb2\"></b><b class=\"xb1\"></b></b>
				</div>
				";
			}
			else
			{
				echo "B2DB error\n";
				echo 'The following error occurred in ' . $e->getFile() . ' at line ' . $e->getLine() . ":\n";
				echo $e->getMessage() . "\n\n";
				echo "Trace:\n";
				echo $e->getTraceAsString() . "\n\n";
				echo self::$_db_connection->error . "\n\n";
				echo "For more information, refer to the B2DB manual.\n";
			}
		}
		
		static public function setHTMLException($val)
		{
			self::$_throwhtmlexception = $val;
		}
		
		static public function throwExceptionAsHTML()
		{
			return self::$_throwhtmlexception;
		}
		
		static public function getDBtypes()
		{
			$retarr = array();
			
			//$retarr['mysqli'] = 'MySQL (mysqli)';
			if (class_exists('PDO'))
			{
				$retarr['mysql'] = 'MySQL (PDO - recommended)';
				$retarr['pgsql'] = 'PostgreSQL (PDO - experimental)';
				/*$retarr['mssql'] = 'MsSQL (PDO)';
				$retarr['sybase'] = 'Sybase (PDO)';
				$retarr['dblib'] = 'DBLib (PDO)';
				$retarr['firebird'] = 'Firebird (PDO)';
				$retarr['ibm'] = 'IBM (PDO)';
				$retarr['oci'] = 'Oracle (PDO)';
				$retarr['sqlite'] = 'SQLite (PDO)';*/
			}
			
			return $retarr;
		}
		
		static public function hasDBEngine($engine)
		{
			return array_key_exists($engine, self::getDBtypes());
		}
		
	}
	