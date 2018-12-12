<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use b2db\Update;
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

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addBoolean(self::CONFIRMED);
            parent::addForeignKeyColumn(self::EDITION, Editions::getTable(), Editions::ID);
            parent::addForeignKeyColumn(self::ISSUE, Issues::getTable(), Issues::ID);
            parent::addForeignKeyColumn(self::STATUS, ListTypes::getTable(), ListTypes::ID);
        }
        
        protected function setupIndexes()
        {
            $this->addIndex('issue', self::ISSUE);
        }

        public function getByIssueIDs($issue_ids)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUE, $issue_ids, \b2db\Criterion::IN);
            $query->join(Issues::getTable(), Issues::ID, self::ISSUE, array(), \b2db\Join::INNER);
            $query->join(Editions::getTable(), Editions::ID, self::EDITION, array(), \b2db\Join::INNER);
            $res = $this->rawSelect($query, false);
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
            $edition_ids = [];
            $res = $this->getByIssueIDs($issue_ids);
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $edition_id = $row->get(self::EDITION);
                    $issue_id = $row->get(self::ISSUE);
                    if (!array_key_exists($issue_id, $this->_preloaded_values)) $this->_preloaded_values[$issue_id] = array();
                    $this->_preloaded_values[$issue_id][] = $row;
                    $edition_ids[$edition_id] = $edition_id;
                }
            }

            return $edition_ids;
        }

        public function clearPreloadedValues()
        {
            $this->_preloaded_custom_fields = null;
        }

        public function getByIssueIDandEditionID($issue_id, $edition_id)
        {
            $query = $this->getQuery();
            $query->where(self::EDITION, $edition_id);
            $query->where(self::ISSUE, $issue_id);
            $res = $this->rawSelectOne($query);
            return $res;
        }
        
        public function setIssueAffected($issue_id, $edition_id)
        {
            if (!$this->getByIssueIDandEditionID($issue_id, $edition_id))
            {
                $insertion = new Insertion();
                $insertion->add(self::ISSUE, $issue_id);
                $insertion->add(self::EDITION, $edition_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $ret = $this->rawInsert($insertion);
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
                $query = $this->getQuery();
                $query->where(self::ISSUE, $issue_id);
                $query->where(self::EDITION, $edition_id);
                $this->rawDelete($query);
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
                $update = new Update();
                $update->add(self::CONFIRMED, $confirmed);
                $this->rawUpdateById($update, $res->get(self::ID));
                
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
                $update = new Update();
                $update->add(self::STATUS, $status_id);
                $this->rawUpdateById($update, $res->get(self::ID));
                
                return true;
            }                
        }
        
    }
