<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;

    /**
     * @Table(name="savedsearchfilters")
     * @Entity(class="\thebuggenie\core\entities\SearchFilter")
     */
    class SavedSearchFilters extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'savedsearchfilters';
        const ID = 'savedsearchfilters.id';
        const SCOPE = 'savedsearchfilters.scope';
        const VALUE = 'savedsearchfilters.value';
        const OPERATOR = 'savedsearchfilters.operator';
        const SEARCH_ID = 'savedsearchfilters.search_id';
        const FILTER_KEY = 'savedsearchfilters.filter_key';
        
        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::VALUE, 200);
            parent::_addVarchar(self::OPERATOR, 40);
            parent::_addVarchar(self::FILTER_KEY, 100);
            parent::_addForeignKeyColumn(self::SEARCH_ID, SavedSearches::getTable(), SavedSearches::ID);
        }

        public function getFiltersBySavedSearchID($savedsearch_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::SEARCH_ID, $savedsearch_id);

            $retarr = array();

            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    if (!array_key_exists($row->get(self::FILTER_KEY), $retarr)) $retarr[$row->get(self::FILTER_KEY)] = array();
                    $retarr[$row->get(self::FILTER_KEY)][] = array('operator' => $row->get(self::OPERATOR), 'value' => $row->get(self::VALUE));
                }
            }

            return $retarr;
        }

        protected function _saveFilterForSavedSearch($saved_search_id, $filter_key, $value, $operator)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addInsert(self::SEARCH_ID, $saved_search_id);
            $crit->addInsert(self::FILTER_KEY, $filter_key);
            $crit->addInsert(self::VALUE, $value);
            $crit->addInsert(self::OPERATOR, $operator);
            $this->doInsert($crit);
        }

        public function deleteBySearchID($saved_search_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::SEARCH_ID, $saved_search_id);
            $this->doDelete($crit);
        }

        public function saveFiltersForSavedSearch($saved_search_id, $filters)
        {
            foreach ($filters as $filter => $filter_info)
            {
                if (array_key_exists('value', $filter_info))
                {
                    $this->_saveFilterForSavedSearch($saved_search_id, $filter, $filter_info['value'], $filter_info['operator']);
                }
                else
                {
                    foreach ($filter_info as $k => $single_filter)
                    {
                        $this->_saveFilterForSavedSearch($saved_search_id, $filter, $single_filter['value'], $single_filter['operator']);
                    }
                }
            }
            
        }
        
    }
