<?php

    namespace thebuggenie\extensions\b2db;

    use b2db\Criteria;

    class Resultset extends \b2db\Resultset implements \Countable
    {
        public function __construct(Statement $statement)
        {
            try {
                $this->crit = $statement->getCriteria();
                if ($this->crit instanceof Criteria) {
                    if ($this->crit->action == 'insert') {
                        $this->insert_id = $statement->getInsertID();
                    } elseif ($this->crit->action == 'select') {
                        while ($row = $statement->fetch()) {
                            $this->rows[] = new Row($row, $statement);
                        }
                        $this->max_ptr = count($this->rows);
                        $this->int_ptr = 0;
                    } elseif ($this->crit->action = 'count') {
                        $value = $statement->fetch();
                        $this->max_ptr = $value['num_col'];
                    }
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }

        public function getNextRow()
        {
            if ($this->_next()) {
                $row = $this->getCurrentRow();
                if ($row instanceof Row) {
                    return $row;
                }
                throw new \Exception('This should never happen. Please file a bug report');
            } else {
                return false;
            }
        }
    }
