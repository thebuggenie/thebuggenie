<?php

    namespace thebuggenie\modules\publish\entities\tables;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * @Table(name="articleviews")
     */
    class ArticleViews extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'articleviews';
        const ID = 'articleviews.id';
        const ARTICLE_ID = 'articleviews.article_id';
        const USER_ID = 'articleviews.user_id';
        const SCOPE = 'articleviews.scope';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::USER_ID, \thebuggenie\core\entities\tables\Users::getTable(), \thebuggenie\core\entities\tables\Users::ID);
            parent::_addForeignKeyColumn(self::ARTICLE_ID, Articles::getTable(), Articles::ID);
        }
    }

