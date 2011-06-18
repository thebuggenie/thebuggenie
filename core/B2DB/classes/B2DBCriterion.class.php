<?php

	/**
	 * B2DB Criterion Base class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */

	/**
	 * B2DB Criterion Base class
	 *
	 * @package B2DB
	 * @subpackage core
	 */
	class B2DBCriterion
	{
		var $wheres = array();
		var $ors = array();

		/**
		 * Add an "or" part to the criterion
		 *
		 * @param string $column
		 * @param mixed $value[optional]
		 * @param string $operator[optional]
		 */
		public function addOr($column, $value = null, $operator = B2DBCriteria::DB_EQUALS)
		{
			if (!is_array($value))
			{
				$this->ors[] = array('column' => $column, 'value' => $value, 'operator' => $operator);
			}
			else
			{
				$this->ors[] = array('column' => $column, 'value' => $value, 'operator' => $operator);
			}
		}

		/**
		 * Add a "where" part to the criterion
		 *
		 * @param string $column
		 * @param mixed $value[optional]
		 * @param string $operator[optional]
		 */
		public function addWhere($column, $value = null, $operator = B2DBCriteria::DB_EQUALS)
		{
			if (!is_array($value))
			{
				$this->wheres[] = array('column' => $column, 'value' => $value, 'operator' => $operator);
			}
			else
			{
				$this->wheres[] = array('column' => $column, 'value' => $value, 'operator' => $operator);
			}
		}

		/**
		 * Generate a new criterion
		 *
		 * @param string $column
		 * @param mixed $value[optional]
		 * @param string $operator[optional]
		 * @param string $variable[optional]
		 * @param string $additional[optional]
		 * @param string $special[optional]
		 */
		public function __construct($column, $value = '', $operator = B2DBCriteria::DB_EQUALS, $variable = '', $additional = '', $special = '')
		{
			if ($column != '')
			{
				$this->wheres[] = array('column' => $column, 'value' => $value, 'operator' => $operator, 'variable' => $variable, 'additional' => $additional, 'special' => $special);
			}
		}
	}
	