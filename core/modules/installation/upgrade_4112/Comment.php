<?php

    namespace thebuggenie\core\modules\installation\upgrade_4112;

    use thebuggenie\core\entities\common\IdentifiableScoped,
        thebuggenie\core\framework;

    /**
     * Class used for comments
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Class used for comments
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\modules\installation\upgrade_4112\CommentsTable")
     */
    class Comment extends IdentifiableScoped
    {

        /**
         * Issue comment
         */
        const TYPE_ISSUE = 1;

        /**
         * Article comment
         */
        const TYPE_ARTICLE = 2;

        /**
         * @Column(type="text")
         */
        protected $_content;

        /**
         * Who posted the comment
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_posted_by;

        /**
         * Who last updated the comment
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_updated_by;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_posted;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_updated;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_target_id;

        /**
         * @var \thebuggenie\core\entities\common\IdentifiableScoped
         */
        protected $_target;

        /**
         * @Column(type="integer", length=5)
         */
        protected $_target_type = self::TYPE_ISSUE;

        /**
         * @Column(type="boolean")
         */
        protected $_is_public = true;

        /**
         * @Column(type="string", length=100)
         */
        protected $_module = 'core';

        /**
         * @Column(type="boolean")
         */
        protected $_deleted = false;

        /**
         * @Column(type="boolean")
         */
        protected $_system_comment = false;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_comment_number = 0;

        /**
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Comment")
         */
        protected $_reply_to_comment = 0;

        /**
         * @Column(type="integer", length=10, default=1)
         */
        protected $_syntax = framework\Settings::SYNTAX_MW;

        /**
         * List of log items linked to this comment
         *
         * @var array
         * @Relates(class="\thebuggenie\core\entities\LogItem", collection=true, foreign_column="comment_id")
         */
        protected $_log_items;

    }
