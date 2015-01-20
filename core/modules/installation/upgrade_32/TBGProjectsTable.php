<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    use thebuggenie\core\entities\tables\ScopedTable;

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
     * @Table(name="projects_32")
     */
    class TBGProjectsTable extends ScopedTable
    {

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
        const RELEASED = 'projects.isreleased';
        const PLANNED_RELEASED = 'projects.isplannedreleased';
        const RELEASE_DATE = 'projects.release_date';
        const ENABLE_BUILDS = 'projects.enable_builds';
        const ENABLE_EDITIONS = 'projects.enable_editions';
        const ENABLE_COMPONENTS = 'projects.enable_components';
        const SHOW_IN_SUMMARY = 'projects.show_in_summary';
        const SUMMARY_DISPLAY = 'projects.summary_display';
        const HAS_DOWNLOADS = 'projects.has_downloads';
        const QA_USER = 'projects.qa_responsible_team';
        const QA_TEAM = 'projects.qa_responsible_user';
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

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::NAME, 200);
            parent::_addVarchar(self::KEY, 200);
            parent::_addInteger(self::SMALL_ICON, 10);
            parent::_addInteger(self::LARGE_ICON, 10);
            parent::_addVarchar(self::PREFIX, 25, '');
            parent::_addBoolean(self::USE_PREFIX);
            parent::_addBoolean(self::USE_SCRUM);
            parent::_addBoolean(self::HAS_DOWNLOADS);
            parent::_addVarchar(self::HOMEPAGE, 200, '');
            parent::_addInteger(self::OWNER_USER, 10);
            parent::_addInteger(self::OWNER_TEAM, 10);
            parent::_addInteger(self::LEADER_USER, 10);
            parent::_addInteger(self::LEADER_TEAM, 10);
            parent::_addInteger(self::QA_USER, 10);
            parent::_addInteger(self::QA_TEAM, 10);
            parent::_addText(self::DESCRIPTION, false);
            parent::_addVarchar(self::DOC_URL, 200, '');
            parent::_addBoolean(self::ALLOW_CHANGING_WITHOUT_WORKING);
            parent::_addBoolean(self::RELEASED);
            parent::_addInteger(self::RELEASE_DATE, 10);
            parent::_addBoolean(self::ENABLE_BUILDS);
            parent::_addBoolean(self::ENABLE_EDITIONS);
            parent::_addBoolean(self::ENABLE_COMPONENTS);
            parent::_addBoolean(self::SHOW_IN_SUMMARY, true);
            parent::_addVarchar(self::SUMMARY_DISPLAY, 15, 'issuetypes');
            parent::_addBoolean(self::LOCKED);
            parent::_addBoolean(self::ARCHIVED);
            parent::_addInteger(self::SCOPE, 10);
            parent::_addInteger(self::WORKFLOW_SCHEME_ID, 10);
            parent::_addInteger(self::ISSUETYPE_SCHEME_ID, 10);
            parent::_addInteger(self::CLIENT, 10);
            parent::_addInteger(self::PARENT_PROJECT_ID, 10);
            parent::_addBoolean(self::DELETED);
            parent::_addBoolean(self::AUTOASSIGN);
        }

    }
