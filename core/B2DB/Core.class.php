<?php

	namespace b2db;
	
	/**
	 * B2DB Core class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package b2db
	 * @subpackage core
	 */

	/**
	 * B2DB Core class
	 *
	 * @package b2db
	 * @subpackage core
	 */
	class Core
	{
		/**
		 * PDO object
		 *
		 * @var \PDO
		 */
		protected static $_db_connection = null;
		protected static $_db_host;
		protected static $_db_uname;
		protected static $_db_pwd;
		protected static $_db_name;
		protected static $_db_type;
		protected static $_db_port;
		protected static $_dsn;
		protected static $_tableprefix = '';
		protected static $_sqlhits = array();
		protected static $_sqltiming;
		protected static $_throwhtmlexception = true;
		protected static $_aliascnt = 0;
		protected static $_transaction_active = false;
		protected static $_tables = array();
		protected static $_debug_mode = true;
		protected static $_cache_dir;
		protected static $_cached_entity_classes = array();
		protected static $_cached_table_classes = array();

		/**
		 * Loads a table and adds it to the B2DBObject stack
		 * 
		 * @param Table $tbl_name
		 * 
		 * @return Table
		 */
		public static function loadNewTable(Table $table)
		{
			self::$_tables[\get_class($table)] = $table;
			return $table;
		}

		/**
		 * Enable or disable debug mode
		 *
		 * @param boolean $debug_mode
		 */
		public static function setDebugMode($debug_mode)
		{
			self::$_debug_mode = $debug_mode;
		}

		/**
		 * Return whether or not debug mode is enabled
		 *
		 * @return boolean
		 */
		public static function isDebugMode()
		{
			return self::$_debug_mode;
		}

		/**
		 * Add a table alias to alias counter
		 *
		 * @return integer
		 */
		public static function addAlias()
		{
			return self::$_aliascnt++;
		}

		/**
		 * Initialize B2DB and load related B2DB classes
		 *
		 * @param boolean $load_parameters[optional] whether to load connection parameters
		 */
		public static function initialize($bootstrap_location = null)
		{
			if (!defined('B2DB_BASEPATH'))
				throw new Exception('The constant B2DB_BASEPATH must be defined. B2DB_BASEPATH should be the full system path to B2DB');
			
			try
			{
				if ($bootstrap_location !== null && \file_exists($bootstrap_location))
					require $bootstrap_location;
				
			}
			catch (\Exception $e)
			{
				throw $e;
			}
			
		}

		/**
		 * Return true if B2DB is initialized with database name and username
		 *
		 * @return boolean
		 */
		public static function isInitialized()
		{
			return (bool) (self::getDBtype() != '' && self::getUname() != '');
		}

		/**
		 * Return the classpath to the B2DB engine files for the currently selected
		 * db engine
		 *
		 * @return string
		 */
		public static function getEngineClassPath()
		{
			return B2DB_BASEPATH . 'classes' . DIRECTORY_SEPARATOR;
		}

		/**
		 * Store connection parameters
		 *
		 * @param string $bootstrap_location Where to save the connection parameters
		 */
		public static function saveConnectionParameters($bootstrap_location)
		{
			$string = "<?php\n";
			$string .= "\t/**\n";
			$string .= "\t * B2DB sql parameters\n";
			$string .= "\t *\n";
			$string .= "\t * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>\n";
			$string .= "\t * @version 2.0\n";
			$string .= "\t * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)\n";
			$string .= "\t * @package b2db\n";
			$string .= "\t * @subpackage core\n";
			$string .= "\t */\n";
			$string .= "\n";
			$string .= "\tself::setUname('".addslashes(self::getUname())."');\n";
			$string .= "\tself::setPasswd('".addslashes(self::getPasswd())."');\n";
			$string .= "\tself::setTablePrefix('".self::getTablePrefix()."');\n";
			$string .= "\n";
			$string .= "\tself::setDSN('".self::getDSN()."');\n";
			$string .= "\n";
			try
			{
				if (\file_put_contents($bootstrap_location, $string) === false)
				{
					throw new Exception('Could not save the database connection details');
				}
			}
			catch (\Exception $e)
			{
				throw $e;
			}
		}
		
		/**
		 * Returns the Table object
		 *
		 * @param Table $tbl_name
		 * 
		 * @return Table
		 */
		public static function getTable($tbl_name)
		{
			if (!isset(self::$_tables[$tbl_name]))
			{
				self::loadNewTable(new $tbl_name());
			}
			if (!isset(self::$_tables[$tbl_name]))
			{
				throw new Exception('Table ' . $tbl_name . ' is not loaded');
			}
			return self::$_tables[$tbl_name];
		}

		/**
		 * Register a new SQL call
		 */
		public static function sqlHit($sql, $values, $time)
		{
			$backtrace = \debug_backtrace();
			$reserved_names = array('Core.class.php', 'Saveable.class.php', 'Criteria.class.php', 'Criterion.class.php', 'Resultset.class.php', 'Row.class.php', 'Statement.class.php', 'Transaction.class.php', 'B2DBTable.class.php', 'B2DB.class.php', 'Criteria.class.php', 'B2DBCriterion.class.php', 'B2DBResultset.class.php', 'Row.class.php', 'Statement.class.php', 'Transaction.class.php', 'Table.class.php', 'TBGB2DBTable.class.php');

			$trace = null;
			foreach ($backtrace as $t)
			{
				if (!\in_array(\basename($t['file']), $reserved_names))
				{
					$trace = $t;
					break;
				}
			}
			
			if (!$trace)
				$trace = array('file' => 'unknown', 'line' => 'unknown');

			self::$_sqlhits[] = array('sql' => $sql, 'values' => $values, 'time' => $time, 'filename' => $trace['file'], 'line' => $trace['line']);
			self::$_sqltiming += $time;
		}

		/**
		 * Get number of SQL calls
		 *
		 * @return integer
		 */
		public static function getSQLHits()
		{
			return self::$_sqlhits;
		}
		
		public static function getSQLCount()
		{
			return \count(self::$_sqlhits) +1;
		}
		
		public static function getSQLTiming()
		{
			return self::$_sqltiming;
		}

		/**
		 * Returns PDO object
		 *
		 * @return \PDO
		 */
		public static function getDBlink()
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
			catch (\PDOException $e)
			{
				throw new Exception($e->getMessage());
			}
			return $res;
		}
		
		/**
		 * Set the DSN
		 *
		 * @param string $dsn
		 */
		public static function setDSN($dsn)
		{
			$dsn_details = \parse_url($dsn);
			if (!\array_key_exists('scheme', $dsn_details))
			{
				throw new Exception('This does not look like a valid DSN - cannot read the database type');
			}
			try
			{
				self::setDBtype($dsn_details['scheme']);
				$dsn_details = \explode(';', $dsn_details['path']);
				foreach ($dsn_details as $dsn_detail)
				{
					$detail_info = \explode('=', $dsn_detail);
					if (\count($detail_info) != 2)
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
			catch (\Exception $e)
			{
				throw $e;
			}
			self::$_dsn = $dsn;
		}

		/**
		 * Generate the DSN when needed
		 */
		protected static function _generateDSN()
		{
			$dsn = self::getDBtype() . ":host=" . self::getHost();
			if (self::getPort())
			{
				$dsn .= ';port=' . self::getPort();
			}
			$dsn .= ';dbname='.self::getDBname();
			self::$_dsn = $dsn;
		}

		/**
		 * Return current DSN
		 *
		 * @return string
		 */
		public static function getDSN()
		{
			if (self::$_dsn === null)
			{
				self::_generateDSN();
			}
			return self::$_dsn;
		}

		/**
		 * Set the database host
		 *
		 * @param string $host
		 */
		public static function setHost($host)
		{
			self::$_db_host = $host;
		}

		/**
		 * Return the database host
		 *
		 * @return string
		 */
		public static function getHost()
		{
			return self::$_db_host;
		}

		/**
		 * Return the database port
		 *
		 * @return integer
		 */
		public static function getPort()
		{
			return self::$_db_port;
		}

		/**
		 * Set the database port
		 * 
		 * @param integer $port 
		 */
		public static function setPort($port)
		{
			self::$_db_port = $port;
		}

		/**
		 * Set database username
		 *
		 * @param string $uname
		 */
		public static function setUname($uname)
		{
			self::$_db_uname = $uname;
		}

		/**
		 * Get database username
		 *
		 * @return string
		 */
		public static function getUname()
		{
			return self::$_db_uname;
		}

		/**
		 * Set the database table prefix
		 *
		 * @param string $prefix
		 */
		public static function setTablePrefix($prefix)
		{
			self::$_tableprefix = $prefix;
		}

		/**
		 * Get the database table prefix
		 *
		 * @return string
		 */
		public static function getTablePrefix()
		{
			return self::$_tableprefix;
		}

		/**
		 * Set the database password
		 *
		 * @param string $upwd
		 */
		public static function setPasswd($upwd)
		{
			self::$_db_pwd = $upwd;
		}

		/**
		 * Return the database password
		 *
		 * @return string
		 */
		public static function getPasswd()
		{
			return self::$_db_pwd;
		}

		/**
		 * Set the database name
		 *
		 * @param string $dbname
		 */
		public static function setDBname($dbname)
		{
			self::$_db_name = $dbname;
			self::$_dsn = null;
		}

		/**
		 * Get the database name
		 *
		 * @return string
		 */
		public static function getDBname()
		{
			return self::$_db_name;
		}

		/**
		 * Set the database type
		 *
		 * @param string $dbtype
		 */
		public static function setDBtype($dbtype)
		{
			if (self::hasDBEngine($dbtype) == false)
			{
				throw new Exception('The selected database is not supported: "' . $dbtype . '".');
			}
			self::$_db_type = $dbtype;
		}

		/**
		 * Get the database type
		 *
		 * @return string
		 */
		public static function getDBtype()
		{
			if (!self::$_db_type && \defined('B2DB_SQLTYPE'))
			{
				self::setDBtype(B2DB_SQLTYPE);
			}
			return self::$_db_type;
		}

		public static function hasDBtype()
		{
			return (bool) (self::getDBtype() != '');
		}

		/**
		 * Try connecting to the database
		 */
		public static function doConnect()
		{
			if (!\class_exists('\\PDO'))
			{
				throw new B2DBException('B2DB needs the PDO PHP libraries installed. See http://php.net/PDO for more information.');
			}
			try
			{
				$uname = self::getUname();
				$pwd = self::getPasswd();
				if (self::$_db_connection instanceof \PDO)
				{
					self::$_db_connection = null;
				}
				self::$_db_connection = new \PDO(self::getDSN(), $uname, $pwd);
				if (!self::$_db_connection instanceof \PDO)
				{
					throw new Exception('Could not connect to the database, but not caught by PDO');
				}
				self::getDBLink()->query('SET NAMES UTF8');
			}
			catch (\PDOException $e)
			{
				throw new Exception($e->getMessage());
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		/**
		 * Select a database to use
		 *
		 * @param string $db
		 * @deprecated
		 */
		public static function doSelectDB($db = null)
		{
			return true;
		}

		/**
		 * Create the specified database
		 *
		 * @param string $db_name
		 */
		public static function createDatabase($db_name)
		{
			$res = self::getDBLink()->query('create database ' . $db_name);
		}

		/**
		 * Close the database connection
		 */
		public static function closeDBLink()
		{
			self::$_db_connection = null;
		}

		public static function isConnected()
		{
			return (bool) (self::$_db_connection instanceof \PDO);
		}

		public static function getCacheDir()
		{
			if (self::$_cache_dir === null)
			{
				$cache_dir = (\defined('B2DB_CACHEPATH')) ? \realpath(B2DB_CACHEPATH) : \realpath(B2DB_BASEPATH . 'cache/');
				self::$_cache_dir = $cache_dir;
			}
			return self::$_cache_dir;
		}
		
		/**
		 * Toggle the transaction state
		 *
		 * @param boolean $state
		 */
		public static function setTransaction($state)
		{
			self::$_transaction_active = $state;
		}
		
		/**
		 * Starts a new transaction
		 */
		public static function startTransaction()
		{
			return new Transaction();
		}
		
		public static function isTransactionActive()
		{
			return (bool) self::$_transaction_active == Transaction::STATE_STARTED;
		}
		
		/**
		 * Displays a nicely formatted exception message
		 *  
		 * @param Exception $exception
		 */
		public static function fatalError(\Exception $exception)
		{
			$ob_status = \ob_get_status();
			if (!empty($ob_status) && $ob_status['status'] != PHP_OUTPUT_HANDLER_END)
			{
				\ob_end_clean();
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
					<h1>An error occured in the B2DB database framework</h1>
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
						if (\array_key_exists('class', $trace_element))
						{
							echo '<strong>'.$trace_element['class'].$trace_element['type'].$trace_element['function'].'()</strong><br>';
						}
						elseif (\array_key_exists('function', $trace_element))
						{
							echo '<strong>'.$trace_element['function'].'()</strong><br>';
						}
						else
						{
							echo '<strong>unknown function</strong><br>';
						}
						if (\array_key_exists('file', $trace_element))
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

		/**
		 * Toggle HTML exception messages
		 *
		 * @param boolean $active
		 */
		public static function setHTMLException($active)
		{
			self::$_throwhtmlexception = $active;
		}

		/**
		 * Return whether exceptions are thrown and displayed as HTML
		 *
		 * @return boolean
		 */
		public static function throwExceptionAsHTML()
		{
			return self::$_throwhtmlexception;
		}

		/**
		 * Get available DB drivers
		 *
		 * @return array
		 */
		public static function getDBtypes()
		{
			$retarr = array();
			
			if (\class_exists('\PDO'))
			{
				$retarr['mysql'] = 'MySQL';
				$retarr['pgsql'] = 'PostgreSQL';
				$retarr['mssql'] = 'Microsoft SQL Server';
				/*
				$retarr['sqlite'] = 'SQLite';
				$retarr['sybase'] = 'Sybase';
				$retarr['dblib'] = 'DBLib';
				$retarr['firebird'] = 'Firebird';
				$retarr['ibm'] = 'IBM';
				$retarr['oci'] = 'Oracle';
				 */
			}
			else
			{
				throw new Exception('You need to have PHP PDO installed to be able to use B2DB');
			}
			
			return $retarr;
		}

		/**
		 * Whether a specific DB driver is supported
		 *
		 * @param string $driver
		 *
		 * @return boolean
		 */
		public static function hasDBEngine($driver)
		{
			return \array_key_exists($driver, self::getDBtypes());
		}

		protected static function cacheTableClass($classname)
		{
			if (!\class_exists($classname)) {
				throw new Exception("The class '{$classname}' does not exist");
			}
			self::$_cached_table_classes[$classname] = array('entity' => null, 'name' => null, 'discriminator' => null);

			$reflection = new \ReflectionClass($classname);
			$docblock = $reflection->getDocComment();
			$annotationset = new AnnotationSet($docblock);
			if (!$table_annotation = $annotationset->getAnnotation('Table')) {
				throw new Exception("The class '{$classname}' does not have a proper @Table annotation");
			}
			$table_name = $table_annotation->getProperty('name');

			if ($entity_annotation = $annotationset->getAnnotation('Entity'))
				self::$_cached_table_classes[$classname]['entity'] = $entity_annotation->getProperty('class');

			if ($entities_annotation = $annotationset->getAnnotation('Entities')) {
				$details = array('identifier' => $entities_annotation->getProperty('identifier'), 'classes' => $annotationset->getAnnotation('SubClasses')->getProperties());
				self::$_cached_table_classes[$classname]['entities'] = $details;
			}

			if ($discriminator_annotation = $annotationset->getAnnotation('Discriminator')) {
				$details = array('column' => "{$table_name}." . $discriminator_annotation->getProperty('column'), 'discriminators' => $annotationset->getAnnotation('Discriminators')->getProperties());
				self::$_cached_table_classes[$classname]['discriminator'] = $details;
			}

			if (!$table_annotation->hasProperty('name')) {
				throw new Exception("The class @Table annotation in '{$classname}' is missing required 'name' property");
			}

			self::$_cached_table_classes[$classname]['name'] = $table_name;

			$key = 'b2db_cache_'.$classname;
			if (!self::$_debug_mode) {
				\TBGCache::add($key, self::$_cached_table_classes[$classname]);
				\TBGCache::fileAdd($key, self::$_cached_table_classes[$classname]);
			}
		}

		protected static function cacheEntityClass($classname, $reflection_classname = null)
		{
			$rc_name = ($reflection_classname !== null) ? $reflection_classname : $classname;
			$reflection = new \ReflectionClass($rc_name);
			$annotationset = new AnnotationSet($reflection->getDocComment());

			if ($reflection_classname === null) {
				self::$_cached_entity_classes[$classname] = array('columns' => array(), 'relations' => array(), 'foreign_columns' => array(), );
				if (!$annotation = $annotationset->getAnnotation('Table')) {
					throw new Exception("The class '{$classname}' is missing a valid @Table annotation");
				} else {
					$tablename = $annotation->getProperty('name');
				}
				if (!\class_exists($tablename)) {
					throw new Exception("The class table class '{$tablename}' for class '{$classname}' does not exist");
				}
				self::$_cached_entity_classes[$classname]['table'] = $tablename;
				self::_populateCachedTableClassFiles($tablename);
				if (($re = $reflection->getExtension()) && $classnames = $re->getClassNames()) {
					foreach ($classnames as $extends_classname) {
						self::cacheEntityClass($classname, $extends_classname);
					}
				}
			}
			if (!\array_key_exists('name', self::$_cached_table_classes[self::$_cached_entity_classes[$classname]['table']])) {
				throw new Exception("The class @Table annotation in '".self::$_cached_entity_classes[$classname]['table']."' is missing required 'name' property");
			}
			$column_prefix = self::$_cached_table_classes[self::$_cached_entity_classes[$classname]['table']]['name'].'.';

			foreach ($reflection->getProperties() as $property) {
				if ($property instanceof \ReflectionProperty);
				$annotationset = new AnnotationSet($property->getDocComment());
				if ($annotationset->hasAnnotations()) {
					$property_name = $property->getName();
					if ($column_annotation = $annotationset->getAnnotation('Column'))
					{
						$column_name = $column_prefix . (($column_annotation->hasProperty('name')) ? $column_annotation->getProperty('name') : substr($property_name, 1));
						$column = array('property' => $property_name, 'default_value' => (($column_annotation->hasProperty('default_value')) ? $column_annotation->getProperty('default_value') : null), 'not_null' => (($column_annotation->hasProperty('not_null')) ? $column_annotation->getProperty('not_null') : false), 'name' => $column_name, 'type' => $column_annotation->getProperty('type'));
						switch ($column['type']) {
							case 'varchar':
							case 'string':
								$column['type'] = 'varchar';
								$column['length'] = ($column_annotation->hasProperty('length')) ? $column_annotation->getProperty('length') : null;
								break;
							case 'float':
								$column['precision'] = ($column_annotation->hasProperty('precision')) ? $column_annotation->getProperty('precision') : 2;
							case 'integer':
								$column['auto_inc'] = ($column_annotation->hasProperty('auto_increment')) ? $column_annotation->getProperty('auto_increment') : false;
								$column['unsigned'] = ($column_annotation->hasProperty('unsigned')) ? $column_annotation->getProperty('unsigned') : false;
								$column['length'] = ($column_annotation->hasProperty('length')) ? $column_annotation->getProperty('length') : 10;
								if ($column['type'] != 'float') {
									$column['default_value'] = ($column_annotation->hasProperty('default_value')) ? $column_annotation->getProperty('default_value') : 0;
								}
								break;
						}
						self::$_cached_entity_classes[$classname]['columns'][$column_name] = $column;
					}
					if ($annotation = $annotationset->getAnnotation('Id')) {
						self::$_cached_entity_classes[$classname]['id_column'] = $column_name;
					}
					if ($annotation = $annotationset->getAnnotation('Relates')) {
						$value = $annotation->getProperty('class');
						$collection = (bool) $annotation->getProperty('collection');
						$manytomany = (bool) $annotation->getProperty('manytomany');
						$joinclass = $annotation->getProperty('joinclass');
						$foreign_column = $annotation->getProperty('foreign_column');
						$f_column = $annotation->getProperty('column');
						self::$_cached_entity_classes[$classname]['relations'][$property_name] = array('collection' => $collection, 'property' => $property_name, 'foreign_column' => $foreign_column, 'manytomany' => $manytomany, 'joinclass' => $joinclass, 'class' => $annotation->getProperty('class'), 'column' => $f_column);
						if (!$collection) {
							if (!$column_annotation) {
								throw new Exception("The property '{$property_name}' in class '{$classname}' is missing an @Column annotation, or is improperly marked as not being a collection");
							}
							$column_name = $column_prefix . (($annotation->hasProperty('name')) ? $annotation->getProperty('name') : substr($property_name, 1));
							$column['class'] = self::getCachedB2DBTableClass($value);
							$column['key'] = ($annotation->hasProperty('key')) ? $annotation->getProperty('key') : null;
							self::$_cached_entity_classes[$classname]['foreign_columns'][$column_name] = $column;
						}
					}
				}
			}
			$key = 'b2db_cache_'.$classname;
			if (!self::$_debug_mode) {
				\TBGCache::add($key, self::$_cached_entity_classes[$classname]);
				\TBGCache::fileAdd($key, self::$_cached_entity_classes[$classname]);
			}
		}

		protected static function _populateCachedClassFiles($classname)
		{
			if (!array_key_exists($classname, self::$_cached_entity_classes))
			{
				$entity_key = 'b2db_cache_'.$classname;
				if (\TBGCache::has($entity_key)) {
					self::$_cached_entity_classes[$classname] = \TBGCache::get($entity_key);
				} elseif (\TBGCache::fileHas($entity_key)) {
					self::$_cached_entity_classes[$classname] = \TBGCache::fileGet($entity_key);
				} else {
					self::cacheEntityClass($classname);
				}
			}
		}
		
		protected static function _populateCachedTableClassFiles($classname)
		{
			if (!array_key_exists($classname, self::$_cached_table_classes))
			{
				$key = 'b2db_cache_'.$classname;
				if (\TBGCache::has($key)) {
					self::$_cached_table_classes[$classname] = \TBGCache::get($key);
				} elseif (\TBGCache::fileHas($key)) {
					self::$_cached_table_classes[$classname] = \TBGCache::fileGet($key);
				} else {
					self::cacheTableClass($classname);
				}
			}
		}

		public static function getTableDetails($classname)
		{
			$table = $classname::getTable();
			if ($table instanceof Table) {
				self::_populateCachedTableClassFiles($classname);
				return array('columns' => $table->getColumns(),
							'foreign_columns' => $table->getForeignColumns(),
							'id' => $table->getIdColumn(),
							'discriminator' => self::$_cached_table_classes[$classname]['discriminator'],
							'name' => self::$_cached_table_classes[$classname]['name']
							);
			}
		}

		public static function getCachedTableDetails($classname)
		{
			self::_populateCachedClassFiles($classname);
			if (\array_key_exists($classname, self::$_cached_entity_classes) && \array_key_exists('columns', self::$_cached_entity_classes[$classname]))
			{
				return array('columns' => self::$_cached_entity_classes[$classname]['columns'],
							'foreign_columns' => self::$_cached_entity_classes[$classname]['foreign_columns'],
							'id' => self::$_cached_entity_classes[$classname]['id_column'],
							'discriminator' => self::$_cached_table_classes[self::$_cached_entity_classes[$classname]['table']]['discriminator'],
							'name' => self::$_cached_table_classes[self::$_cached_entity_classes[$classname]['table']]['name']
							);
			}
			return null;
		}
		
		public static function getCachedEntityRelationDetails($classname, $property)
		{
			self::_populateCachedClassFiles($classname);
			if (\array_key_exists($classname, self::$_cached_entity_classes) && \array_key_exists($property, self::$_cached_entity_classes[$classname]['relations']))
			{
				return self::$_cached_entity_classes[$classname]['relations'][$property];
			}
			return null;
		}
	
		public static function getCachedColumnDetails($classname, $column)
		{
			self::_populateCachedClassFiles($classname);
			if (\array_key_exists($classname, self::$_cached_entity_classes) && \array_key_exists($column, self::$_cached_entity_classes[$classname]['columns']))
			{
				return self::$_cached_entity_classes[$classname]['columns'][$column];
			}
			return null;
		}

		public static function getCachedColumnPropertyName($classname, $column)
		{
			self::_populateCachedClassFiles($classname);
			if (\array_key_exists($classname, self::$_cached_entity_classes) && \array_key_exists($column, self::$_cached_entity_classes[$classname]['columns']))
			{
				return self::$_cached_entity_classes[$classname]['columns'][$column]['property'];
			}
			return null;
		}

		public static function getCachedB2DBTableClass($classname)
		{
			self::_populateCachedClassFiles($classname);
			if (\array_key_exists($classname, self::$_cached_entity_classes))
			{
				if (!\array_key_exists('table', self::$_cached_entity_classes[$classname])) {
					throw new Exception("The class '{$classname}' is missing a valid @Table annotation");
				}
				return self::$_cached_entity_classes[$classname]['table'];
			}
			return null;
		}

		public static function getCachedTableEntityClasses($classname)
		{
			self::_populateCachedTableClassFiles($classname);
			if (\array_key_exists($classname, self::$_cached_table_classes))
			{
				if (\array_key_exists('entities', self::$_cached_table_classes[$classname])) {
					return self::$_cached_table_classes[$classname]['entities'];
				}
			}
			return null;
		}

		public static function getCachedTableEntityClass($classname)
		{
			self::_populateCachedTableClassFiles($classname);
			if (\array_key_exists($classname, self::$_cached_table_classes))
			{
				if (!\array_key_exists('entity', self::$_cached_table_classes[$classname])) {
					throw new Exception("The class '{$classname}' is missing a valid @Entity annotation");
				}
				return self::$_cached_table_classes[$classname]['entity'];
			}
			return null;
		}

	}
	