<?php

    namespace thebuggenie\extensions\b2db;

    use b2db\Criteria;

    class Table extends \b2db\Table
    {
        public function doSelect(Criteria $crit, $join = 'all')
        {
            if ($crit == null)
                $crit = new Criteria();
            $crit->setFromTable($this);
            $crit->setupJoinTables($join);
            $crit->generateSelectSQL();

            $statement = Statement::getPreparedStatement($crit);

            $resultset = $statement->performQuery();
            return ($resultset->count()) ? $resultset : null;
        }
    }
