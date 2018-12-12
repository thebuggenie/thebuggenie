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

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::NAME, 200);
            parent::addVarchar(self::KEY, 200);
            parent::addInteger(self::SMALL_ICON, 10);
            parent::addInteger(self::LARGE_ICON, 10);
            parent::addVarchar(self::PREFIX, 25, '');
            parent::addBoolean(self::USE_PREFIX);
            parent::addBoolean(self::USE_SCRUM);
            parent::addBoolean(self::HAS_DOWNLOADS);
            parent::addVarchar(self::HOMEPAGE, 200, '');
            parent::addInteger(self::OWNER_USER, 10);
            parent::addInteger(self::OWNER_TEAM, 10);
            parent::addInteger(self::LEADER_USER, 10);
            parent::addInteger(self::LEADER_TEAM, 10);
            parent::addInteger(self::QA_USER, 10);
            parent::addInteger(self::QA_TEAM, 10);
            parent::addText(self::DESCRIPTION, false);
            parent::addVarchar(self::DOC_URL, 200, '');
            parent::addBoolean(self::ALLOW_CHANGING_WITHOUT_WORKING);
            parent::addBoolean(self::RELEASED);
            parent::addInteger(self::RELEASE_DATE, 10);
            parent::addBoolean(self::ENABLE_BUILDS);
            parent::addBoolean(self::ENABLE_EDITIONS);
            parent::addBoolean(self::ENABLE_COMPONENTS);
            parent::addBoolean(self::SHOW_IN_SUMMARY, true);
            parent::addVarchar(self::SUMMARY_DISPLAY, 15, 'issuetypes');
            parent::addBoolean(self::LOCKED);
            parent::addBoolean(self::ARCHIVED);
            parent::addInteger(self::SCOPE, 10);
            parent::addInteger(self::WORKFLOW_SCHEME_ID, 10);
            parent::addInteger(self::ISSUETYPE_SCHEME_ID, 10);
            parent::addInteger(self::CLIENT, 10);
            parent::addInteger(self::PARENT_PROJECT_ID, 10);
            parent::addBoolean(self::DELETED);
            parent::addBoolean(self::AUTOASSIGN);
        }

    }
