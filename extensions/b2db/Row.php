<?php

    namespace thebuggenie\extensions\b2db;

    class Row extends \b2db\Row implements \ArrayAccess
    {
        public function offsetExists($offset)
        {
            // Code below in Criteria throws exception due to missing comma in custom column.
            // "list($table_name, $column_name) = explode('.', $column);"
            // So before reaching that code do the comma check.
            if (strpos($offset, '.') === false)
                return (bool) array_key_exists($offset, $this->_fields);

            return parent::offsetExists($offset);
        }
    }
