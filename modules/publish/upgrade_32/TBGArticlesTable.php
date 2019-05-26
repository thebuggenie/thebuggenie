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

        public function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::NAME, 255);
            parent::addText(self::CONTENT, false);
            parent::addBoolean(self::IS_PUBLISHED);
            parent::addInteger(self::DATE, 10);
            parent::addInteger(self::AUTHOR, 10);
            parent::addInteger(self::SCOPE, 10);
        }

    }
