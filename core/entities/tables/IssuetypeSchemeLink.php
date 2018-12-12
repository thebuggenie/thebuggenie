<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use b2db\Update;
    use thebuggenie\core\framework;

    /**
     * Link table between issue type scheme and issue type
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Link table between issue type scheme and issue type
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="issuetype_scheme_link")
     */
    class IssuetypeSchemeLink extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'issuetype_scheme_link';
        const ID = 'issuetype_scheme_link.id';
        const SCOPE = 'issuetype_scheme_link.scope';
        const ISSUETYPE_SCHEME_ID = 'issuetype_scheme_link.issuetype_scheme_id';
        const ISSUETYPE_ID = 'issuetype_scheme_link.issuetype_id';
        const REPORTABLE = 'issuetype_scheme_link.reportable';
        const REDIRECT_AFTER_REPORTING = 'issuetype_scheme_link.redirect_after_reporting';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::ISSUETYPE_SCHEME_ID, IssuetypeSchemes::getTable(), IssuetypeSchemes::ID);
            parent::addForeignKeyColumn(self::ISSUETYPE_ID, IssueTypes::getTable(), IssueTypes::ID);
            parent::addBoolean(self::REPORTABLE, true);
            parent::addBoolean(self::REDIRECT_AFTER_REPORTING, true);
        }

        protected function setupIndexes()
        {
            $this->addIndex('issuetypescheme_scope', array(self::ISSUETYPE_SCHEME_ID, self::SCOPE));
        }

        public function getByIssuetypeSchemeID($issuetype_scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUETYPE_SCHEME_ID, $issuetype_scheme_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $return_array = array();
            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    $i_id = $row->get(self::ISSUETYPE_ID);
                    $return_array[$i_id] = array('reportable' => (bool) $row->get(self::REPORTABLE), 'redirect' => (bool) $row->get(self::REDIRECT_AFTER_REPORTING));
                }
                if (count($return_array))
                {
                    $i_ids = array_keys($return_array);
                    $issuetypes = \thebuggenie\core\entities\Issuetype::getB2DBTable()->getByIds($i_ids);
                    foreach ($i_ids as $i_id)
                    {
                        if (array_key_exists($i_id, $issuetypes))
                        {
                            $return_array[$i_id]['issuetype'] = $issuetypes[$i_id];
                        }
                        else
                        {
                            unset($return_array[$i_id]);
                        }
                    }
                }
            }

            return $return_array;
        }

        public function deleteByIssuetypeSchemeID($scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function deleteByIssuetypeID($type_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUETYPE_ID, $type_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function associateIssuetypeWithScheme($issuetype_id, $scheme_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $insertion->add(self::ISSUETYPE_ID, $issuetype_id);
            $insertion->add(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $this->rawInsert($insertion);
        }

        public function unAssociateIssuetypeWithScheme($issuetype_id, $scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ISSUETYPE_ID, $issuetype_id);
            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $this->rawDelete($query);
        }

        public function setIssuetypeRedirectedAfterReportingForScheme($issuetype_id, $issuetype_scheme_id, $redirected = true)
        {
            $query = $this->getQuery();
            $update = new Update();

            $update->add(self::REDIRECT_AFTER_REPORTING, $redirected);

            $query->where(self::ISSUETYPE_SCHEME_ID, $issuetype_scheme_id);
            $query->where(self::ISSUETYPE_ID, $issuetype_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $this->rawUpdate($update, $query);
        }

        public function setIssuetypeReportableForScheme($issuetype_id, $issuetype_scheme_id, $reportable = true)
        {
            $query = $this->getQuery();
            $update = new Update();

            $update->add(self::REPORTABLE, $reportable);

            $query->where(self::ISSUETYPE_SCHEME_ID, $issuetype_scheme_id);
            $query->where(self::ISSUETYPE_ID, $issuetype_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $this->rawUpdate($update, $query);
        }

        public function countByIssuetypeID($issuetype_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUETYPE_ID, $issuetype_id);
            return $this->count($query);
        }

    }
