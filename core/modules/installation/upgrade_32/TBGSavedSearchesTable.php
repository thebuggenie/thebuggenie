<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * @Table(name="savedsearches_32")
     */
    class TBGSavedSearchesTable extends ScopedTable
    {

        const B2DBNAME = 'savedsearches';
        const ID = 'savedsearches.id';
        const SCOPE = 'savedsearches.scope';
        const NAME = 'savedsearches.name';
        const DESCRIPTION = 'savedsearches.description';
        const GROUPBY = 'savedsearches.groupby';
        const GROUPORDER = 'savedsearches.grouporder';
        const ISSUES_PER_PAGE = 'savedsearches.issues_per_page';
        const TEMPLATE_NAME = 'savedsearches.templatename';
        const TEMPLATE_PARAMETER = 'savedsearches.templateparameter';
        const APPLIES_TO_PROJECT = 'savedsearches.applies_to_project';
        const IS_PUBLIC = 'savedsearches.is_public';
        const UID = 'savedsearches.uid';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::NAME, 200);
            parent::_addText(self::DESCRIPTION, false);
            parent::_addBoolean(self::IS_PUBLIC);
            parent::_addVarchar(self::TEMPLATE_NAME, 200);
            parent::_addVarchar(self::TEMPLATE_PARAMETER, 200);
            parent::_addInteger(self::ISSUES_PER_PAGE, 10);
            parent::_addVarchar(self::GROUPBY, 100);
            parent::_addVarchar(self::GROUPORDER, 5);
            parent::_addInteger(self::APPLIES_TO_PROJECT, 10);
            parent::_addInteger(self::UID, 10);
            parent::_addInteger(self::SCOPE, 10);
        }

    }
