<?php

	/**
	 * B2DB Row Base class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */

	/**
	 * B2DB Row Base class
	 *
	 * @package B2DB
	 * @subpackage core
	 */
	abstract class BaseB2DBRow
	{
		protected $_fields = array();
		
		/**
		 * Criteria
		 *
		 * @var B2DBStatement
		 */
		protected $_statement = null;
		
		/**
		 * Constructor
		 * 
		 * @param B2DBstatement $statement
		 */
		public function __construct($row, $statement)
		{
			foreach ($row as $key => $val)
			{
				$this->_fields[$key] = $val;
			}
			//try
			//{
				$this->_statement = $statement;
				/*$values = $this->_statement->getColumnValuesForCurrentRow();
				var_dump($values);
				$columns = $this->_statement->getCriteria()->getSelectionColumns();
				$cc = 0;
				foreach ($columns as $aColumn)
				{
					if ($aColumn['alias'] != '')
					{
						$this->_fields[$aColumn['alias']] = $values[$cc];
					}
					else
					{
						if (!array_key_exists($cc, $values))
						{
							die(var_dump($values, $columns));
						}
						$this->_fields[$this->_statement->getCriteria()->getSelectionAlias($aColumn['column'])] = $values[$cc]; 
					}
					$cc++;
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			//var_dump($this->_fields);*/
		}
		
		public function get($column, $foreign_key = null)
		{
			if ($this->_statement == null)
			{
				throw new B2DBException('Statement did not execute, cannot return unknown value for column ' . $column);
			}
			else
			{
				if ($foreign_key !== null)
				{
					foreach ($this->_statement->getCriteria()->getForeignTables() as $aft)
					{
						if ($aft['col2'] == $foreign_key)
						{
							$columnname = $this->_statement->getCriteria()->getColumnName($column);
							$column = $aft['jointable']->getB2DBAlias() . '.' . $columnname;
							break;
						}
					}
				}
				else
				{
					$column = $this->_statement->getCriteria()->getSelectionColumn($column);
				}
				if (isset($this->_fields[$this->_statement->getCriteria()->getSelectionAlias($column)]))
				{
					return $this->_fields[$this->_statement->getCriteria()->getSelectionAlias($column)];
				}
				else
				{
					return '';
				}
			}
		}
		
	}
	