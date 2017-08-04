<?php

    namespace thebuggenie\core\modules\installation\upgrade_4112;

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
     * @Table(name="comments")
     * @Entity(class="\thebuggenie\core\modules\installation\upgrade_4112\Comment")
     * @Discriminator(column="target_type")
     * @Discriminators(\thebuggenie\core\entities\Issue=1, \thebuggenie\core\entities\Article=2)
     */
    class CommentsTable extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 3;
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

    }
