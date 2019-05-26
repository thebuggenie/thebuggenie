<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
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
        
        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::VALUE, 200);
            parent::addVarchar(self::OPERATOR, 40);
            parent::addVarchar(self::FILTER_KEY, 100);
            parent::addForeignKeyColumn(self::SEARCH_ID, SavedSearches::getTable(), SavedSearches::ID);
        }

        public function getFiltersBySavedSearchID($savedsearch_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::SEARCH_ID, $savedsearch_id);

            $retarr = array();

            if ($res = $this->rawSelect($query))
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
            $insertion = new Insertion();
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $insertion->add(self::SEARCH_ID, $saved_search_id);
            $insertion->add(self::FILTER_KEY, $filter_key);
            $insertion->add(self::VALUE, $value);
            $insertion->add(self::OPERATOR, $operator);
            $this->rawInsert($insertion);
        }

        public function deleteBySearchID($saved_search_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::SEARCH_ID, $saved_search_id);
            $this->rawDelete($query);
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
