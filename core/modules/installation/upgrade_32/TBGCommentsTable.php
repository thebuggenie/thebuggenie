<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Comments table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Comments table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="comments_32")
     */
    class TBGCommentsTable extends ScopedTable
    {

        const B2DBNAME = 'comments';
        const ID = 'comments.id';
        const SCOPE = 'comments.scope';
        const TARGET_ID = 'comments.target_id';
        const TARGET_TYPE = 'comments.target_type';
        const CONTENT = 'comments.content';
        const IS_PUBLIC = 'comments.is_public';
        const POSTED_BY = 'comments.posted_by';
        const POSTED = 'comments.posted';
        const UPDATED_BY = 'comments.updated_by';
        const UPDATED = 'comments.updated';
        const DELETED = 'comments.deleted';
        const MODULE = 'comments.module';
        const COMMENT_NUMBER = 'comments.comment_number';
        const SYSTEM_COMMENT = 'comments.system_comment';
        const REPLY_TO_COMMENT = 'comments.reply_to_comment';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addInteger(self::TARGET_ID, 10);
            parent::addInteger(self::TARGET_TYPE, 3);
            parent::addText(self::CONTENT, false);
            parent::addInteger(self::POSTED, 10);
            parent::addInteger(self::UPDATED, 10);
            parent::addInteger(self::COMMENT_NUMBER, 10);
            parent::addInteger(self::REPLY_TO_COMMENT, 10);
            parent::addBoolean(self::DELETED);
            parent::addBoolean(self::IS_PUBLIC, true);
            parent::addVarchar(self::MODULE, 50);
            parent::addBoolean(self::SYSTEM_COMMENT);
            parent::addInteger(self::SCOPE, 10);
            parent::addInteger(self::UPDATED_BY, 10);
            parent::addInteger(self::POSTED_BY, 10);
        }

    }
