<?php

    namespace thebuggenie\modules\publish\upgrade_32;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * @Table(name="articles_32")
     */
    class TBGArticlesTable extends ScopedTable
    {

        const B2DBNAME = 'articles';
        const ID = 'articles.id';
        const NAME = 'articles.name';
        const CONTENT = 'articles.content';
        const IS_PUBLISHED = 'articles.is_published';
        const DATE = 'articles.date';
        const AUTHOR = 'articles.author';
        const SCOPE = 'articles.scope';

        public function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::NAME, 255);
            parent::_addText(self::CONTENT, false);
            parent::_addBoolean(self::IS_PUBLISHED);
            parent::_addInteger(self::DATE, 10);
            parent::_addInteger(self::AUTHOR, 10);
            parent::_addInteger(self::SCOPE, 10);
        }

    }
