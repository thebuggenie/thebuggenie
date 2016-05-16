<?php

    namespace thebuggenie\extensions\b2db;

    use b2db\Core;
    use b2db\Criteria;

    class Statement extends \b2db\Statement
    {
        public static function getPreparedStatement($crit)
        {
            try {
                $statement = new Statement($crit);
            } catch (\Exception $e) {
                throw $e;
            }

            return $statement;
        }

        public function performQuery()
        {
            try {
                $values = ($this->getCriteria() instanceof Criteria) ? $this->getCriteria()->getValues() : array();
                if (Core::isDebugMode()) {
                    if (Core::isDebugLoggingEnabled())
                        \caspar\core\Logging::log('executing PDO query (' . Core::getSQLCount() . ') - ' . (($this->getCriteria() instanceof Criteria) ? $this->getCriteria()->action : 'unknown'), 'B2DB');

                    $pretime = Core::getDebugTime();
                }

                $res = $this->statement->execute($values);

                if (!$res) {
                    $error = $this->statement->errorInfo();
                    if (Core::isDebugMode()) {
                        Core::sqlHit($this, $pretime);
                    }
                    throw new Exception($error[2], $this->printSQL());
                }
                if (Core::isDebugLoggingEnabled())
                    \caspar\core\Logging::log('done', 'B2DB');

                if ($this->getCriteria() instanceof Criteria && $this->getCriteria()->action == 'insert') {
                    if (Core::getDBtype() == 'mysql') {
                        $this->insert_id = Core::getDBLink()->lastInsertId();
                    } elseif (Core::getDBtype() == 'pgsql') {
                        $this->insert_id = Core::getDBLink()->lastInsertId(Core::getTablePrefix() . $this->getCriteria()->getTable()->getB2DBName() . '_id_seq');
                        if (Core::isDebugLoggingEnabled()) {
                            \caspar\core\Logging::log('sequence: ' . Core::getTablePrefix() . $this->getCriteria()->getTable()->getB2DBName() . '_id_seq', 'b2db');
                            \caspar\core\Logging::log('id is: ' . $this->insert_id, 'b2db');
                        }
                    }
                }

                $retval = new Resultset($this);

                if (Core::isDebugMode())
                    Core::sqlHit($this, $pretime);

                if (!$this->getCriteria() || $this->getCriteria()->action != 'select') {
                    $this->statement->closeCursor();
                }
                return $retval;
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
