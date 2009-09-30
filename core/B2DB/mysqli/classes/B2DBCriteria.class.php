<?php

	/**
	 * MySQLi Criteria class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage mysqli
	 */

	/**
	 * MySQLi Criteria class
	 *
	 * @package B2DB
	 * @subpackage mysqli
	 */
	class B2DBCriteria extends BaseB2DBCriteria 
	{

		public function addUpdate($column, $value)
		{
			if (is_bool($value))
			{
				$value = (int) $value;
			}
			$this->updates[] = array('column' => $column, 'value' => $value);
		}
		
	}

?>