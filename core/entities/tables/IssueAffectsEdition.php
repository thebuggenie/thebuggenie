<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * Issue affects edition table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Issue affects edition table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="issueaffectsedition")
     */
    class IssueAffectsEdition extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'issueaffectsedition';
        const ID = 'issueaffectsedition.id';
        const SCOPE = 'issueaffectsedition.scope';
        const ISSUE = 'issueaffectsedition.issue';
        const EDITION = 'issueaffectsedition.edition';
        const CONFIRMED = 'issueaffectsedition.confirmed';
        const STATUS = 'issueaffectsedition.status';
        
        protected $_preloaded_values = null;

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addBoolean(self::CONFIRMED);
            parent::_addForeignKeyColumn(self::EDITION, Editions::getTable(), Editions::ID);
            parent::_addForeignKeyColumn(self::ISSUE, Issues::getTable(), Issues::ID);
            parent::_addForeignKeyColumn(self::STATUS, ListTypes::getTable(), ListTypes::ID);
        }
        
        protected function _setupIndexes()
        {
            $this->_addIndex('issue', self::ISSUE);
        }

        public function getByIssueIDs($issue_ids)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUE, $issue_ids, Criteria::DB_IN);
            $crit->addJoin(Issues::getTable(), Issues::ID, self::ISSUE, array(), Criteria::DB_INNER_JOIN);
            $crit->addJoin(Editions::getTable(), Editions::ID, self::EDITION, array(), Criteria::DB_INNER_JOIN);
            $res = $this->doSelect($crit, false);
            return $res;
        }

        public function getByIssueID($issue_id)
        {
            if (is_array($this->_preloaded_values))
            {
                if (array_key_exists($issue_id, $this->_preloaded_values))
                {
                    $values = $this->_preloaded_values[$issue_id];
                    unset($this->_preloaded_values[$issue_id]);
                    return $values;
                }
                else
                {
                    return array();
                }
            }
            else
            {
                $res = $this->getByIssueIDs(array($issue_id));
                $rows = array();
                if ($res)
                {
                    while ($row = $res->getNextRow())
                    {
                        $rows[] = $row;
                    }
                }

                return $rows;
            }
        }

        public function preloadValuesByIssueIDs($issue_ids)
        {
            $this->_preloaded_values = array();
            $res = $this->getByIssueIDs($issue_ids);
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $issue_id = $row->get(self::ISSUE);
                    if (!array_key_exists($issue_id, $this->_preloaded_values)) $this->_preloaded_values[$issue_id] = array();
                    $this->_preloaded_values[$issue_id][] = $row;
                }
            }
        }

        public function clearPreloadedValues()
        {
            $this->_preloaded_custom_fields = null;
        }

        public function getByIssueIDandEditionID($issue_id, $edition_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::EDITION, $edition_id);
            $crit->addWhere(self::ISSUE, $issue_id);
            $res = $this->doSelectOne($crit);
            return $res;
        }
        
        public function setIssueAffected($issue_id, $edition_id)
        {
            if (!$this->getByIssueIDandEditionID($issue_id, $edition_id))
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::ISSUE, $issue_id);
                $crit->addInsert(self::EDITION, $edition_id);
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $ret = $this->doInsert($crit);
                return $ret->getInsertID();
            }
            else
            {
                return false;
            }
        }

        public function deleteByIssueIDandEditionID($issue_id, $edition_id)
        {
            if (!$this->getByIssueIDandEditionID($issue_id, $edition_id))
            {
                return false;
            }
            else
            {
                $crit = $this->getCriteria();
                $crit->addWhere(self::ISSUE, $issue_id);
                $crit->addWhere(self::EDITION, $edition_id);
                $this->doDelete($crit);
                return true;
            }
        }
        
        public function confirmByIssueIDandEditionID($issue_id, $edition_id, $confirmed = true)
        {
            if (!($res = $this->getByIssueIDandEditionID($issue_id, $edition_id)))
            {
                return false;
            }
            else
            {
                $crit = $this->getCriteria();
                $crit->addUpdate(self::CONFIRMED, $confirmed);
                $this->doUpdateById($crit, $res->get(self::ID));
                
                return true;
            }                
        }
        
        public function setStatusByIssueIDandEditionID($issue_id, $edition_id, $status_id)
        {
            if (!($res = $this->getByIssueIDandEditionID($issue_id, $edition_id)))
            {
                return false;
            }
            else
            {
                $crit = $this->getCriteria();
                $crit->addUpdate(self::STATUS, $status_id);
                $this->doUpdateById($crit, $res->get(self::ID));
                
                return true;
            }                
        }
        
    }
