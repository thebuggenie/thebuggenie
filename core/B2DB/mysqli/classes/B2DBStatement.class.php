<?php

	/**
	 * MySQLi statement class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage mysqli
	 */

	/**
	 * MySQLi statement class
	 *
	 * @package B2DB
	 * @subpackage mysqli
	 */
	class B2DBStatement extends BaseB2DBStatement  
	{
		
		/**
		 * mysqli statement
		 *
		 * @var mysqli_stmt
		 */
		public $statement;
		
		public $values = array();
		
		public $params = array();
		
		public function __construct($crit)
		{
			try
			{
				parent::__construct($crit);
				$this->statement = B2DB::getDBlink()->stmt_init();
				if (!$this->statement)
				{
					throw new B2DBException('An error occured: ' . B2DB::getDBLink()->error);
				}
				$this->_prepare();
				if ($this->crit instanceof BaseB2DBCriteria)
				{
					$this->_bindParams();
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function __destruct()
		{
			//$this->statement->free_result();
			//$this->statement->close();
		}
		
		/**
		 * Performs a query, then returns a resultset
		 *
		 * @return B2DBResultset
		 */
		public function performQuery()
		{
			try
			{
				if (!$this->statement->execute())
				{
					throw new B2DBException($this->statement->error);
				}
				B2DB::sqlHit();
				if ($this->getCriteria()->action == 'select')
				{
					$this->_bindValues();
				}
				return parent::performQuery();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function getInsertID()
		{
			return $this->statement->insert_id;
		}
		
		public function getNumRows()
		{
			return $this->statement->num_rows;
		}
		
		public function fetch()
		{
			$fetchval = $this->statement->fetch();
			if ($fetchval === false)
			{
				throw new B2DBException('An error occured while trying to fetch the result: "' . $this->statement->error . '"');
			}
			elseif ($fetchval === true)
			{
				//echo 'fetching one row';
				return true;
			}
			else
			{
				//echo 'no more rows';
				return false;
			}
			//$this->statement->free_result();
		}
		
		public function resetPtr()
		{
			$this->statement->reset();
		}
		
		protected function _bindParams()
		{
			$formatstring = '';
			
			$cc = 0;
			if (count($this->crit->getValues()) > 0)
			{
				foreach ($this->crit->getValues() as $value)
				{
					if (is_int($value))
					{
						$formatstring .= 'i';
					}
					elseif (is_double($value))
					{
						$formatstring .= 'd';
					}
					elseif (is_string($value))
					{
						$formatstring .= 's';
					}
					else
					{
						$formatstring .= 'b';
					}
					
				}
				$this->params[] = $formatstring;
				foreach ($this->crit->getValues() as $value)
				{
					$this->params[] = $value;
				}
				if (!call_user_func_array(array($this->statement, 'bind_param'), $this->params))
				{
					throw new B2DBException('Error binding parameters ('.B2DB::getDBLink()->error.'). SQL: ' . $this->printSQL());
				}
			}
		}
		
		public function printSQL()
		{
			$str = $this->crit->getSQL();
			foreach ($this->crit->getValues() as $val)
			{
				$val = '\'' . $val . '\'';
				$str = substr_replace($str, $val, strpos($str, '?'), 1);
			}
			return $str;
		}
		
		protected function _prepare()
		{
			try
			{
				if ($this->crit instanceof B2DBCriteria)
				{
					$this->statement->prepare($this->crit->getSQL());
				}
				else
				{
					$this->statement->prepare($this->custom_sql);
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		protected function _bindValues()
		{
			$cc = 0;
			foreach ($this->getCriteria()->getSelectionColumns() as $aColumn)
			{
				${"v$cc"} = '';
				$this->values[] = &${"v$cc"};
				$cc++;
			}
			if (!call_user_func_array(array($this->statement, 'bind_result'), $this->values))
			{
				throw new B2DBException('Cannot bind values (' . print_r($this->values) . '): "' . $this->statement->error . '"');
			}
		}
		
		public function getColumnValuesForCurrentRow()
		{
			return $this->values;
		}

	}

?>