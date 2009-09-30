<?php

	/**
	 * PDO statement class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage pdo
	 */

	/**
	 * PDO statement class
	 *
	 * @package B2DB
	 * @subpackage pdo
	 */
	class B2DBStatement extends BaseB2DBStatement  
	{
		
		/**
		 * PDO statement
		 *
		 * @var PDOStatement
		 */
		public $statement;
		
		public $values = array();
		
		public $params = array();
		
		protected $insert_id = null;
		
		public function __construct($crit)
		{
			try
			{
				parent::__construct($crit);
				$this->_prepare();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function __destruct()
		{
		}
		
		/**
		 * Performs a query, then returns a resultset
		 *
		 * @param string $action[optional] The crud action performed (select, insert, update, delete, create, alter)
		 *
		 * @return B2DBResultset
		 */
		public function performQuery($action = '')
		{
			try
			{
				$values = ($this->getCriteria() instanceof BaseB2DBCriteria) ? $this->getCriteria()->getValues() : array();
				BUGSlogging::log('executing PDO query (' . B2DB::getSQLHits() . ')', 'B2DB');
				if (BaseB2DB::isDebugMode())
				{
					BUGSlogging::log($this->printSQL(), 'B2DB');
				}
				if (!$res = $this->statement->execute($values))
				{
					$error = $this->statement->errorInfo();
					throw new B2DBException($error[2], $this->printSQL());
				}
				BUGSlogging::log('done', 'B2DB');
				B2DB::sqlHit();
				if ($this->getCriteria() instanceof BaseB2DBCriteria && $this->getCriteria()->action == 'insert')
				{
					$this->insert_id = B2DB::getDBLink()->lastInsertId();
				}
				$action = ($this->getCriteria() instanceof B2DBCriteria) ? $this->getCriteria()->action : '';
				return parent::performQuery();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function getInsertID()
		{
			return $this->insert_id;
		}
		
		public function getNumRows()
		{
			return $this->statement->rowCount();
		}
		
		public function fetch()
		{
			try
			{
				if ($this->values = $this->statement->fetch(PDO::FETCH_ASSOC))
				{
					return $this->values;
				}
				else
				{
					return false;
				}
			}
			catch (PDOException $e)
			{
				throw new B2DBException('An error occured while trying to fetch the result: "' . $e->getMessage() . '"');
			}
		}
		
		public function resetPtr()
		{
			$this->statement->reset();
		}
		
		public function printSQL()
		{
			$str = '';
			if ($this->getCriteria() instanceof B2DBCriteria)
			{
				$str .= $this->crit->getSQL();
				foreach ($this->crit->getValues() as $val)
				{
					if (is_object($val))
					{
						throw new B2DBException('waat');
					}
					$val = '\'' . $val . '\'';
					$str = substr_replace($str, $val, strpos($str, '?'), 1);
				}
			}
			return $str;
		}
		
		protected function _prepare()
		{
			try
			{
				if (!B2DB::getDBLink() instanceof PDO)
				{
					throw new B2DBException('Connection not up, can\'t prepare the statement');
				}
				if ($this->crit instanceof B2DBCriteria)
				{
					$this->statement = B2DB::getDBLink()->prepare($this->crit->getSQL());
				}
				else
				{
					$this->statement = B2DB::getDBLink()->prepare($this->custom_sql);
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function getColumnValuesForCurrentRow()
		{
			return $this->values;
		}

	}
