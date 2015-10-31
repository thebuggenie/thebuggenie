<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Projects table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Projects table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method Projects getTable() Retrieves an instance of this table
     * @method \thebuggenie\core\entities\Project selectById(integer $id) Retrieves a project
     *
     * @Table(name="projects")
     * @Entity(class="\thebuggenie\core\entities\Project")
     */
    class Projects extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 3;
        const B2DBNAME = 'projects';
        const ID = 'projects.id';
        const SCOPE = 'projects.scope';
        const NAME = 'projects.name';
        const KEY = 'projects.key';
        const PREFIX = 'projects.prefix';
        const USE_PREFIX = 'projects.use_prefix';
        const USE_SCRUM = 'projects.use_scrum';
        const HOMEPAGE = 'projects.homepage';
        const OWNER_USER = 'projects.owner_user';
        const OWNER_TEAM = 'projects.owner_team';
        const LEADER_TEAM = 'projects.leader_team';
        const LEADER_USER = 'projects.leader_user';
        const CLIENT = 'projects.client';
        const DESCRIPTION = 'projects.description';
        const DOC_URL = 'projects.doc_url';
        const WIKI_URL = 'projects.wiki_url';
        const RELEASED = 'projects.isreleased';
        const PLANNED_RELEASED = 'projects.isplannedreleased';
        const RELEASE_DATE = 'projects.release_date';
        const ENABLE_BUILDS = 'projects.enable_builds';
        const ENABLE_EDITIONS = 'projects.enable_editions';
        const ENABLE_COMPONENTS = 'projects.enable_components';
        const SHOW_IN_SUMMARY = 'projects.show_in_summary';
        const SUMMARY_DISPLAY = 'projects.summary_display';
        const HAS_DOWNLOADS = 'projects.has_downloads';
        const QA = 'projects.qa_responsible';
        const QA_TYPE = 'projects.qa_responsible_type';
        const LOCKED = 'projects.locked';
        const DELETED = 'projects.deleted';
        const SMALL_ICON = 'projects.small_icon';
        const LARGE_ICON = 'projects.large_icon';
        const ALLOW_CHANGING_WITHOUT_WORKING = 'projects.allow_freelancing';
        const WORKFLOW_SCHEME_ID = 'projects.workflow_scheme_id';
        const ISSUETYPE_SCHEME_ID = 'projects.issuetype_scheme_id';
        const AUTOASSIGN = 'projects.autoassign';
        const PARENT_PROJECT_ID = 'projects.parent';
        const ARCHIVED = 'projects.archived';

        public function _setupIndexes()
        {
            $this->_addIndex('scope', self::SCOPE);
            $this->_addIndex('scope_name', array(self::SCOPE, self::NAME));
            $this->_addIndex('workflow_scheme_id', self::WORKFLOW_SCHEME_ID);
            $this->_addIndex('issuetype_scheme_id', self::ISSUETYPE_SCHEME_ID);
            $this->_addIndex('parent', self::PARENT_PROJECT_ID);
            $this->_addIndex('parent_scope', array(self::PARENT_PROJECT_ID, self::SCOPE));
        }

        public function getByPrefix($prefix)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PREFIX, $prefix);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            return $this->selectOne($crit);
        }

        public function getAll()
        {
            $crit = $this->getCriteria();
            $crit->addOrderBy(self::NAME, Criteria::SORT_ASC);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::DELETED, false);
            $crit->indexBy(self::KEY);
            $res = $this->select($crit, false);
            return $res;
        }

        public function getAllIncludingDeleted()
        {
            $crit = $this->getCriteria();
            $crit->addOrderBy(self::NAME, Criteria::SORT_ASC);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->indexBy(self::KEY);
            $res = $this->select($crit, false);
            return $res;
        }

        public function getByID($id, $scoped = true)
        {
            if ($scoped)
            {
                $crit = $this->getCriteria();
                $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
                $row = $this->doSelectById($id, $crit, false);
            }
            else
            {
                $row = $this->doSelectById($id);
            }
            return $row;
        }

        public function getByClientID($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::CLIENT, $id);
            $row = $this->doSelect($crit, false);
            return $row;
        }

        public function getByParentID($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::PARENT_PROJECT_ID, $id);
            $crit->addWhere(self::DELETED, false);

            $res = $this->select($crit, false);
            return $res;
        }

        public function quickfind($projectname)
        {
            $crit = $this->getCriteria();
            $ctn = $crit->returnCriterion(self::NAME, "%{$projectname}%", Criteria::DB_LIKE);
            $ctn->addOr(self::KEY, strtolower("%{$projectname}%"), Criteria::DB_LIKE);
            $crit->addWhere($ctn);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($crit);
        }

        public function getByKey($key)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::KEY, $key);
            $crit->addWhere(self::KEY, '', Criteria::DB_NOT_EQUALS);
            $row = $this->doSelectOne($crit, false);
            return $row;
        }

        public function countByIssuetypeSchemeID($scheme_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::ARCHIVED, false);

            return $this->doCount($crit);
        }

        public function countByWorkflowSchemeID($scheme_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::WORKFLOW_SCHEME_ID, $scheme_id);
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::ARCHIVED, false);

            return $this->doCount($crit);
        }

        public function countProjects($scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope);
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::ARCHIVED, false);

            return $this->doCount($crit);
        }

        public function getByUserID($user_id)
        {
            $crit = $this->getCriteria();
            $ctn = $crit->returnCriterion(self::LEADER_USER, $user_id);
            $ctn->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere($ctn);
            $ctn = $crit->returnCriterion(self::OWNER_USER, $user_id);
            $ctn->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addOr($ctn);

            return $this->select($crit);
        }

        public function updateByIssuetypeSchemeID($scheme_id)
        {
            $schemes = \thebuggenie\core\entities\IssuetypeScheme::getAll();
            foreach ($schemes as $default_scheme_id => $scheme)
            {
                break;
            }

            $crit = $this->getCriteria();

            $crit->addWhere(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addUpdate(self::ISSUETYPE_SCHEME_ID, $default_scheme_id);

            $res = $this->doUpdate($crit);
        }

        public function getByFileID($file_id)
        {
            $crit = $this->getCriteria();
            $ctn = $crit->returnCriterion(self::SMALL_ICON, $file_id);
            $ctn->addOr(self::LARGE_ICON, $file_id);
            $crit->addWhere($ctn);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($crit);
        }

    }
