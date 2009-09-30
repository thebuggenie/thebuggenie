<?php

	/**
	 * B2DB Resultset Base class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */

	/**
	 * B2DB Resultset Base class
	 *
	 * @package B2DB
	 * @subpackage core
	 */
	abstract class BaseB2DBResultset
	{
		protected $rows = array();
		
		/**
		 * b2dbcriteria
		 *
		 * @var B2DBCriteria
		 */
		protected $crit;
		protected $int_ptr;
		protected $max_ptr;
		protected $insert_id;
		protected $num_col;

		public function __construct(B2DBStatement $statement)
		{
			try
			{
				$this->crit = $statement->getCriteria();
				if ($this->crit instanceof BaseB2DBCriteria)
				{
					if ($this->crit->action == 'insert')
					{
						$this->insert_id = $statement->getInsertID();
					}
					elseif ($this->crit->action == 'select')
					{
						while ($row = $statement->fetch())
						{
							$this->rows[] = new B2DBRow($row, $statement);
						}
						$this->max_ptr = count($this->rows);
						$this->int_ptr = 0;
					}
					elseif ($this->crit->action = 'count')
					{
						$value = $statement->fetch();
						$this->num_col = $value['num_col'];
					}
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function next()
		{
			if ($this->int_ptr == $this->max_ptr)
			{
				return false;
			}
			else
			{
				$this->int_ptr++;
				return true;
			}
		}

		public function count()
		{
			return $this->max_ptr;
		}
		
		public function getNumberOfRows()
		{
			return $this->count();
		}
		
		public function getCount()
		{
			return $this->num_col;
		}

		/**
		 * Returns the current row
		 *
		 * @return B2DBRow
		 */
		public function getCurrentRow()
		{
			if ($this->int_ptr == 0)
			{
				BUGSlogging::log('This is not a valid row');
			}
			if (isset($this->rows[($this->int_ptr - 1)]))
			{
				return $this->rows[($this->int_ptr - 1)];
			}
			return null;
		}
		
		/**
		 * Advances through the resultset and returns the current row
		 * Returns false when there are no more rows
		 *
		 * @return B2DBRow
		 */
		public function getNextRow()
		{
			if ($this->next())
			{
				$theRow = $this->getCurrentRow();
				if ($theRow instanceof BaseB2DBRow)
				{
					return $theRow;
				}
				throw new B2DBException('This should never happen. Please file a bug report');
			}
			else
			{
				return false;
			}
		}
		
		public function get($column, $foreign_key = null)
		{
			$theRow = $this->getCurrentRow();
			if ($theRow instanceof BaseB2DBRow)
			{
				return $theRow->get($column, $foreign_key);
			}
			else
			{
				throw new B2DBException('Cannot return value of ' . $column . ' on a row that doesn\' exist');
			}
		}

		public function getAllRows()
		{
			return $this->rows;
		}

		public function resetPtr()
		{
			$this->int_ptr = 0;
		}

		public function getSQL()
		{
			return ($this->crit instanceof B2DBCriteria) ? $this->crit->getSQL() : '';
		}
		
		public function printSQL()
		{
			$str = '';
			if ($this->crit instanceof B2DBCriteria)
			{
				$str .= $this->crit->getSQL();
				foreach ($this->crit->getValues() as $val)
				{
					if (!is_int($val))
					{
						$val = '\'' . $val . '\'';
					}
					$str = substr_replace($str, $val, strpos($str, '?'), 1);
				}
			}
			return $str;
		}
	
		public function getInsertID()
		{
			return $this->insert_id;
		}
	}
