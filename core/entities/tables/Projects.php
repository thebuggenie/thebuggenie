<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Update;
    use thebuggenie\core\entities\Project;
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
     * @method static Projects getTable() Retrieves an instance of this table
     * @method Project selectById(integer $id) Retrieves a project
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
        const ISSUES_LOCK_TYPE = 'projects.issues_lock_type';
        const DELETED = 'projects.deleted';
        const SMALL_ICON = 'projects.small_icon';
        const LARGE_ICON = 'projects.large_icon';
        const ALLOW_CHANGING_WITHOUT_WORKING = 'projects.allow_freelancing';
        const WORKFLOW_SCHEME_ID = 'projects.workflow_scheme_id';
        const ISSUETYPE_SCHEME_ID = 'projects.issuetype_scheme_id';
        const AUTOASSIGN = 'projects.autoassign';
        const PARENT_PROJECT_ID = 'projects.parent';
        const ARCHIVED = 'projects.archived';

        protected function setupIndexes()
        {
            $this->addIndex('scope', self::SCOPE);
            $this->addIndex('scope_name', array(self::SCOPE, self::NAME));
            $this->addIndex('workflow_scheme_id', self::WORKFLOW_SCHEME_ID);
            $this->addIndex('issuetype_scheme_id', self::ISSUETYPE_SCHEME_ID);
            $this->addIndex('parent', self::PARENT_PROJECT_ID);
            $this->addIndex('parent_scope', array(self::PARENT_PROJECT_ID, self::SCOPE));
        }

        public function getByPrefix($prefix)
        {
            $query = $this->getQuery();
            $query->where(self::PREFIX, $prefix);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            return $this->selectOne($query);
        }

        public function getAll()
        {
            $query = $this->getQuery();
            $query->addOrderBy(self::NAME, \b2db\QueryColumnSort::SORT_ASC);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::DELETED, false);
            $query->indexBy(self::KEY);
            $res = $this->select($query, false);
            return $res;
        }

        public function getAllIncludingDeleted()
        {
            $query = $this->getQuery();
            $query->addOrderBy(self::NAME, \b2db\QueryColumnSort::SORT_ASC);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->indexBy(self::KEY);
            $res = $this->select($query, false);
            return $res;
        }

        public function getByID($id, $scoped = true)
        {
            if ($scoped)
            {
                $query = $this->getQuery();
                $query->where(self::SCOPE, framework\Context::getScope()->getID());
                $row = $this->rawSelectById($id, $query, false);
            }
            else
            {
                $row = $this->rawSelectById($id);
            }
            return $row;
        }

        public function getByClientID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::CLIENT, $id);
            $row = $this->rawSelect($query, false);
            return $row;
        }

        public function getByParentID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::PARENT_PROJECT_ID, $id);
            $query->where(self::DELETED, false);

            $res = $this->select($query, false);
            return $res;
        }

        public function quickfind($projectname)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $criteria = new Criteria();
            $criteria->where(self::NAME, "%{$projectname}%", \b2db\Criterion::LIKE);
            $criteria->or(self::KEY, strtolower("%{$projectname}%"), \b2db\Criterion::LIKE);
            $query->and($criteria);

            return $this->select($query);
        }

        public function getByKey($key)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::KEY, $key);
            $query->where(self::KEY, '', \b2db\Criterion::NOT_EQUALS);
            $row = $this->rawSelectOne($query, false);
            return $row;
        }

        public function countByIssuetypeSchemeID($scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $query->where(self::DELETED, false);
            $query->where(self::ARCHIVED, false);

            return $this->count($query);
        }

        public function countByWorkflowSchemeID($scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::WORKFLOW_SCHEME_ID, $scheme_id);
            $query->where(self::DELETED, false);
            $query->where(self::ARCHIVED, false);

            return $this->count($query);
        }

        public function countProjects($scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->where(self::DELETED, false);
            $query->where(self::ARCHIVED, false);

            return $this->count($query);
        }

        public function getByUserID($user_id)
        {
            $query = $this->getQuery();

            $criteria = new Criteria();
            $criteria->where(self::LEADER_USER, $user_id);
            $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where($criteria);

            $criteria = new Criteria();
            $criteria->where(self::OWNER_USER, $user_id);
            $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->or($criteria);

            return $this->select($query);
        }

        public function updateByIssuetypeSchemeID($scheme_id)
        {
            $schemes = \thebuggenie\core\entities\IssuetypeScheme::getAll();
            foreach ($schemes as $default_scheme_id => $scheme)
            {
                break;
            }

            $query = $this->getQuery();
            $update = new Update();

            $update->add(self::ISSUETYPE_SCHEME_ID, $default_scheme_id);

            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->rawUpdate($update, $query);
        }

        public function getByFileID($file_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $criteria = new Criteria();
            $criteria->where(self::SMALL_ICON, $file_id);
            $criteria->or(self::LARGE_ICON, $file_id);
            $query->and($criteria);

            return $this->select($query);
        }

        public function getFileIds()
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::SMALL_ICON, 'file_id_small');
            $query->addSelectionColumn(self::LARGE_ICON, 'file_id_large');

            $res = $this->rawSelect($query);
            $file_ids = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $file_ids[$row['file_id_small']] = $row['file_id_small'];
                    $file_ids[$row['file_id_large']] = $row['file_id_large'];
                }
            }

            return $file_ids;
        }

    }
