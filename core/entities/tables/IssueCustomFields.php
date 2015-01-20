<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * Issue <-> custom fields relations table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Issue <-> custom fields relations table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="issuecustomfields")
     */
    class IssueCustomFields extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'issuecustomfields';
        const ID = 'issuecustomfields.id';
        const SCOPE = 'issuecustomfields.scope';
        const ISSUE_ID = 'issuecustomfields.issue_id';
        const CUSTOMFIELDOPTION_ID = 'issuecustomfields.customfieldoption_id';
        const CUSTOMFIELDS_ID = 'issuecustomfields.customfields_id';
        const OPTION_VALUE = 'issuecustomfields.option_value';

        protected $_preloaded_custom_fields = null;

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::ISSUE_ID, Issues::getTable(), Issues::ID);
            parent::_addForeignKeyColumn(self::CUSTOMFIELDS_ID, CustomFields::getTable(), CustomFields::ID);
            parent::_addForeignKeyColumn(self::CUSTOMFIELDOPTION_ID, CustomFieldOptions::getTable(), CustomFieldOptions::ID);
            parent::_addText(self::OPTION_VALUE, false);
        }

        public function getAllValuesByIssueIDs($issue_ids)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUE_ID, $issue_ids, Criteria::DB_IN);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->doSelect($crit, false);

            return $res;
        }

        public function getAllValuesByIssueID($issue_id)
        {
            if (is_array($this->_preloaded_custom_fields))
            {
                if (array_key_exists($issue_id, $this->_preloaded_custom_fields))
                {
                    $values = $this->_preloaded_custom_fields[$issue_id];
                    unset($this->_preloaded_custom_fields[$issue_id]);
                    return $values;
                }
                else
                {
                    return array();
                }
            }
            else
            {
                $res = $this->getAllValuesByIssueIDs(array($issue_id));
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
            $this->_preloaded_custom_fields = array();
            $res = $this->getAllValuesByIssueIDs($issue_ids);
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $issue_id = $row->get(self::ISSUE_ID);
                    if (!array_key_exists($issue_id, $this->_preloaded_custom_fields)) $this->_preloaded_custom_fields[$issue_id] = array();
                    $this->_preloaded_custom_fields[$issue_id][] = $row;
                }
            }
        }

        public function clearPreloadedValues()
        {
            $this->_preloaded_custom_fields = null;
        }

        public function getRowByCustomFieldIDandIssueID($customdatatype_id, $issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUE_ID, $issue_id);
            $crit->addWhere(self::CUSTOMFIELDS_ID, $customdatatype_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            $row = $this->doSelectOne($crit);

            return $row;
        }

        public function saveIssueCustomFieldValue($value, $customdatatype_id, $issue_id)
        {
            $crit = $this->getCriteria();
            if ($row = $this->getRowByCustomFieldIDandIssueID($customdatatype_id, $issue_id))
            {
                if ($value === null)
                {
                    $this->doDeleteById($row->get(self::ID));
                }
                else
                {
                    $crit->addUpdate(self::OPTION_VALUE, $value);
                    $res = $this->doUpdateById($crit, $row->get(self::ID));
                }
            }
            elseif ($value !== null)
            {
                $crit->addInsert(self::ISSUE_ID, $issue_id);
                $crit->addInsert(self::OPTION_VALUE, $value);
                $crit->addInsert(self::CUSTOMFIELDS_ID, $customdatatype_id);
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $res = $this->doInsert($crit);
            }
        }
        
        public function saveIssueCustomFieldOption($option_id, $customdatatype_id, $issue_id)
        {
            $crit = $this->getCriteria();
            if ($row = $this->getRowByCustomFieldIDandIssueID($customdatatype_id, $issue_id))
            {
                if ($option_id === null)
                {
                    $this->doDeleteById($row->get(self::ID));
                }
                else
                {
                    $crit->addUpdate(self::CUSTOMFIELDOPTION_ID, $option_id);
                    $res = $this->doUpdateById($crit, $row->get(self::ID));
                }
            }
            elseif ($option_id !== null)
            {
                $crit->addInsert(self::ISSUE_ID, $issue_id);
                $crit->addInsert(self::CUSTOMFIELDOPTION_ID, $option_id);
                $crit->addInsert(self::CUSTOMFIELDS_ID, $customdatatype_id);
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $res = $this->doInsert($crit);
            }
        }

        public function doDeleteByFieldId($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::CUSTOMFIELDS_ID, $id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->doSelect($crit);
            
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $this->doDeleteById($row->get(self::ID));
                }
            }
        }

        public function _migrateData(\b2db\Table $old_table)
        {
            switch ($old_table->getVersion())
            {
                case 1:
                    if ($res = $old_table->doSelectAll())
                    {
                        $customfields = \thebuggenie\core\entities\CustomDatatype::getB2DBTable()->selectAll();
                        while ($row = $res->getNextRow())
                        {
                            $customfield_id = $row->get(self::CUSTOMFIELDS_ID);
                            $customfield = (array_key_exists($customfield_id, $customfields)) ? $customfields[$customfield_id] : null;
                            if ($customfield instanceof \thebuggenie\core\entities\CustomDatatype && $customfield->hasCustomOptions())
                            {
                                $customfieldoption = CustomFieldOptions::getTable()->getByValueAndCustomfieldID((int) $row->get(self::OPTION_VALUE), $customfield->getID());
                                if ($customfieldoption instanceof \thebuggenie\core\entities\CustomDatatypeOption)
                                {
                                    $crit = $this->getCriteria();
                                    $crit->addUpdate(self::CUSTOMFIELDOPTION_ID, $customfieldoption->getID());
                                    $crit->addUpdate(self::OPTION_VALUE, null);
                                    $this->doUpdateById($crit, $row->get(self::ID));
                                }
                                elseif($row->get(self::ID))
                                {
                                    $this->doDeleteById($row->get(self::ID));
                                }
                            }
                        }
                    }
                    break;
            }
        }

    }
