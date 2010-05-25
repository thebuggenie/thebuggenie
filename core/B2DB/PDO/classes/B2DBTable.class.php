<?php

	/**
	 * PDO table class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage pdo
	 */

	/**
	 * PDO table class
	 *
	 * @package B2DB
	 * @subpackage pdo
	 */
	class B2DBTable extends BaseB2DBTable 
	{
		
		protected function getQC()
		{
			$qc = '`';
			switch (B2DB::getDBtype())
			{
				case 'pgsql':
					$qc = '"';
					break;
			}
			return $qc;
		}
		
		protected function _createToSQL()
		{
			$sql = '';
			$qc = $this->getQC();
			$sql .= "CREATE TABLE $qc" . B2DB::getTablePrefix() . $this->b2db_name . "$qc (\n";
			$field_sql = array();
			foreach ($this->_columns as $a_column)
			{
				$fsql = '';
				$fsql .= " $qc" . $this->_getRealColumnFieldName($a_column['name']) . "$qc ";
				switch ($a_column['type'])
				{
					case 'integer':
						if (B2DB::getDBtype() == 'pgsql' && isset($a_column['auto_inc']) && $a_column['auto_inc'] == true)
						{
							$fsql .= 'SERIAL';
						}
						else
						{
							$fsql .= 'INTEGER';
						}
						if ($a_column['unsigned'] && B2DB::getDBtype() != 'pgsql') $fsql .= ' UNSIGNED';
						break;
					case 'varchar':
						$fsql .= 'VARCHAR(' . $a_column['length'] . ')';
						break;
					case 'float':
						$fsql .= 'FLOAT(' . $a_column['precision'] . ')';
						if ($a_column['unsigned'] && B2DB::getDBtype() != 'pgsql') $fsql .= ' UNSIGNED';
						break;
					case 'blob':
						$fsql .= (B2DB::getDBtype() == 'mysql') ? 'LONGBLOB' : 'BLOB';
						break;
					case 'text':
					case 'boolean':
						$fsql .= strtoupper($a_column['type']);
						break;
				}
				if ($a_column['not_null']) $fsql .= ' NOT NULL';
				if ($a_column['type'] != 'text')
				{
					if (isset($a_column['auto_inc']) && $a_column['auto_inc'] == true && B2DB::getDBtype() != 'pgsql')
					{
						$fsql .= ' AUTO_INCREMENT';
					}
					elseif (isset($a_column['default_value']) && $a_column['default_value'] !== null && !(isset($a_column['auto_inc']) && $a_column['auto_inc'] == true && B2DB::getDBtype() == 'pgsql'))
					{
						if (is_int($a_column['default_value']))
						{
							if ($a_column['type'] == 'boolean')
							{
								$fsql .= ' DEFAULT ';
								$fsql .= ($a_column['default_value']) ? 'true' : 'false';
							}
							else
							{
								$fsql .= ' DEFAULT ' . $a_column['default_value'];
							}
						}
						else
						{
							$fsql .= ' DEFAULT \'' . $a_column['default_value'] . '\'';
						}
					}
				}
				$field_sql[] = $fsql;
			}
			$sql .= join(",\n", $field_sql);
			$sql .= ", PRIMARY KEY ($qc" . $this->_getRealColumnFieldName($this->id_column) . "$qc) ";
			$sql .= ') ';
			if (B2DB::getDBtype() != 'pgsql') $sql .= 'AUTO_INCREMENT=' . $this->_autoincrement_start_at . ' ';
			$sql .= 'CHARACTER SET ' . $this->_charset;
			return $sql;
		}
		
		protected function _dropToSQL()
		{
			$sql = '';
			$sql .= 'DROP TABLE IF EXISTS ' . B2DB::getTablePrefix() . $this->b2db_name;
			return $sql;
		}
		
	}
