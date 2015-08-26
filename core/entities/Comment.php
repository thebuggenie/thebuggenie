<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\core\helpers\MentionableProvider;
    use thebuggenie\core\framework;
    use thebuggenie\modules\publish;
    use \Michelf\MarkdownExtra;

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
     * @Table(name="\thebuggenie\core\entities\tables\Comments")
     */
    class Comment extends IdentifiableScoped implements MentionableProvider
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

        protected $_log_item_count = null;

        protected static $_comment_count = array();

        protected $_parser = null;

        /**
         *
         * Returns all comments for a given item
         *
         */
        static function getComments($target_id, $target_type, $sort_order = \b2db\Criteria::SORT_ASC)
        {
            $comments = tables\Comments::getTable()->getComments($target_id, $target_type, $sort_order);
            self::$_comment_count[$target_type][$target_id] = count($comments);

            return $comments;
        }

        static function getRecentCommentsByAuthor($user_id, $target_type = self::TYPE_ISSUE, $limit = 10)
        {
            $comments = tables\Comments::getTable()->getRecentCommentsByUserIDandTargetType($user_id, $target_type, $limit);
            return $comments;
        }

        static function countComments($target_id, $target_type, $include_system_comments = true)
        {
            if (!array_key_exists($target_type, self::$_comment_count))
                self::$_comment_count[$target_type] = array();

            if (!array_key_exists($target_id, self::$_comment_count[$target_type]))
                self::$_comment_count[$target_type][$target_id] = array();

            if (!array_key_exists((int) $include_system_comments, self::$_comment_count[$target_type][$target_id]))
                self::$_comment_count[$target_type][$target_id][(int) $include_system_comments] = (int) tables\Comments::getTable()->countComments($target_id, $target_type, $include_system_comments);

            return (int) self::$_comment_count[$target_type][$target_id][(int) $include_system_comments];
        }

        public function setPublic($var)
        {
            $this->_is_public = (bool) $var;
        }

        public function setContent($var)
        {
            $this->_content = $var;
        }

        public function setIsPublic($var)
        {
            $this->_is_public = (bool) $var;
        }

        public function setUpdatedBy($var)
        {
            $this->_updated = NOW;
            $this->_updated_by = $var;
        }

        /**
         * Perform a permission check based on a key, and whether or not to
         * check if the permission is explicitly set
         *
         * @param string $key The permission key to check for
         *
         * @return boolean
         */
        protected function _permissionCheckWithID($key)
        {
            $retval = framework\Context::getUser()->hasPermission($key, $this->getID(), 'core');
            $retval = ($retval !== null) ? $retval : framework\Context::getUser()->hasPermission($key, 0, 'core');

            return $retval;
        }

        /**
         * Perform a permission check based on a key, and whether or not to
         * check for the equivalent "*own" permission if the comment is posted
         * by the same user
         *
         * @param string $key The permission key to check for
         * @param boolean $exclusive Whether to perform a similar check for "own"
         *
         * @return boolean
         */
        protected function _permissionCheck($key, $exclusive = false)
        {
            $retval = ($this->getPostedByID() == framework\Context::getUser()->getID() && !$exclusive) ? $this->_permissionCheckWithID($key.'own') : null;
            $retval = ($retval !== null) ? $retval : $this->_permissionCheckWithID($key);
            return ($retval !== null) ? $retval : null;
        }

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if ($is_new)
            {
                if (!$this->_posted)
                {
                    $this->_posted = NOW;
                }
                if (!$this->_comment_number)
                {
                    $this->_comment_number = tables\Comments::getTable()->getNextCommentNumber($this->_target_id, $this->_target_type);
                }
            }
        }

        protected function _addNotification($type, $user)
        {
            $notification = new Notification();
            $notification->setTarget($this);
            $notification->setTriggeredByUser($this->getPostedByID());
            $notification->setUser($user);
            $notification->setNotificationType($type);
            $notification->save();
        }

        protected function _addIssueNotifications()
        {
            foreach ($this->getTarget()->getSubscribers() as $user)
            {
                if ($user->getID() == $this->getPostedByID()) continue;
                $this->_addNotification(Notification::TYPE_ISSUE_COMMENTED, $user);
            }
        }

        /**
         * Returns the associated parser object
         *
         * @return \thebuggenie\core\helpers\ContentParser
         */
        protected function _getParser()
        {
            if (!isset($this->_parser))
            {
                $this->_parseContent();
            }
            return $this->_parser;
        }

        public function hasMentions()
        {
            return $this->_getParser()->hasMentions();
        }

        public function getMentions()
        {
            return $this->_getParser()->getMentions();
        }

        protected function _postSave($is_new)
        {
            if ($is_new)
            {
                $tty = $this->getTargetType();
                $tid = $this->getTargetID();
                if (array_key_exists($tty, self::$_comment_count) && array_key_exists($tid, self::$_comment_count[$tty]) && array_key_exists((int) $this->isSystemComment(), self::$_comment_count[$tty][$tid]))
                    self::$_comment_count[$tty][$tid][(int) $this->isSystemComment()]++;

                if (!$this->isSystemComment())
                {
                    if ($this->_getParser()->hasMentions())
                    {
                        foreach ($this->_getParser()->getMentions() as $user)
                        {
                            if ($user->getID() == framework\Context::getUser()->getID()) continue;
                            $this->_addNotification(Notification::TYPE_COMMENT_MENTIONED, $user);
                        }
                    }
                    if ($this->getTargetType() == self::TYPE_ISSUE)
                    {
                        $this->_addIssueNotifications();
                        if (framework\Settings::getUserSetting(framework\Settings::SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ISSUES, $this->getPostedByID()))
                            $this->getTarget()->addSubscriber($this->getPostedByID());
                    }
                    if ($this->getTargetType() == self::TYPE_ARTICLE)
                    {
                        if (framework\Settings::getUserSetting(framework\Settings::SETTINGS_USER_SUBSCRIBE_CREATED_UPDATED_COMMENTED_ARTICLES, $this->getPostedByID()))
                            $this->getTarget()->addSubscriber($this->getPostedByID());
                    }
                }
            }
        }

        protected function _canPermissionOrSeeAndEditAllComments($permission)
        {
            $retval = $this->_permissionCheck($permission);
            $retval = ($retval === null) ? $this->_permissionCheck('canpostseeandeditallcomments', true) : $retval;

            return $retval;
        }

        protected function _canPermissionOrSeeAndEditComments($permission)
        {
            $retval = $this->_permissionCheck($permission);
            $retval = ($retval === null) ? $this->_permissionCheck('canpostandeditcomments', true) : $retval;

            return $retval;
        }

        /**
         * Return if the user can edit this comment
         *
         * @return boolean
         */
        public function canUserEditComment()
        {
            if ($this->isSystemComment()) return false;
            $retval = $this->_canPermissionOrSeeAndEditAllComments('caneditcomments');

            return ($retval !== null) ? $retval : framework\Settings::isPermissive();
        }

        /**
         * Return if the user can edit own comment
         *
         * @return boolean
         */
        public function canUserEditOwnComment()
        {
            $retval = $this->_canPermissionOrSeeAndEditComments('caneditcommentsown');

            return ($retval !== null) ? $retval : framework\Settings::isPermissive();
        }

        /**
         * Return if the user can delete this comment
         *
         * @return boolean
         */
        public function canUserDeleteComment()
        {
            if ($this->isSystemComment()) return false;
            $retval = $this->_canPermissionOrSeeAndEditAllComments('candeletecomments');

            return ($retval !== null) ? $retval : framework\Settings::isPermissive();
        }

        /**
         * Return if the user can delete own comment
         *
         * @return boolean
         */
        public function canUserDeleteOwnComment()
        {
            $retval = $this->_canPermissionOrSeeAndEditComments('candeletecommentsown');

            return ($retval !== null) ? $retval : framework\Settings::isPermissive();
        }

        /**
         * Return if the user can delete comment
         *
         * @param \thebuggenie\core\entities\User $user A User
         *
         * @return boolean
         */
        public function canUserDelete(\thebuggenie\core\entities\User $user)
        {
            $can_delete = false;

            try
            {
                // Delete comment if valid user and...
                if ($user instanceof \thebuggenie\core\entities\User)
                {
                    if (($this->postedByUser($user->getID()) && $this->canUserDeleteOwnComment()) // the user posted the comment AND the user can delete own comments
                        || $this->canUserDeleteComment()) // OR the user can delete all comments
                    {
                        $can_delete = true;
                    }//endif
                }//endif
            }//endtry
            catch (\Exception $e){ }
            return $can_delete;
        }

        /**
         * Return if the user can edit comment
         *
         * @param \thebuggenie\core\entities\User $user A User
         *
         * @return boolean
         */
        public function canUserEdit(\thebuggenie\core\entities\User $user)
        {
            $can_edit = false;

            try
            {
                // Edit comment if valid user and...
                if ($user instanceof \thebuggenie\core\entities\User)
                {
                    if (($this->postedByUser($user->getID()) && $this->canUserEditOwnComment()) // the user posted the comment AND the user can edit own comments
                        || $this->canUserEditComment()) // OR the user can edit all comments
                    {
                        $can_edit = true;
                    }//endif
                }//endif
            }//endtry
            catch (\Exception $e){ }
            return $can_edit;
        }

        /**
         * Return if the specified user can view this comment
         *
         * @param \thebuggenie\core\entities\User $user A User
         *
         * @return boolean
         */
        public function isViewableByUser(\thebuggenie\core\entities\User $user)
        {
            $can_view = false;

            try
            {
                // Show comment if valid user and...
                if ($user instanceof \thebuggenie\core\entities\User)
                {

                    if ((!$this->isPublic() && $user->canSeeNonPublicComments()) // the comment is hidden and the user can see hidden comments
                        || ($this->isPublic() && $user->canViewComments()) // OR the comment is public and  user can see public comments
                        || ($this->postedByUser($user->getID()))) // OR the user posted the comment
                    {
                        $can_view = true;
                    }//endif

                }//endif
            }//endtry
            catch (\Exception $e){ }
            return $can_view;
        }

        /**
         * Returns the user who last updated the comment
         *
         * @return \thebuggenie\core\entities\User
         */
        public function getUpdatedBy()
        {
            return ($this->_updated_by instanceof \thebuggenie\core\entities\User) ? $this->_updated_by : \thebuggenie\core\entities\User::getB2DBTable()->selectById($this->_updated_by);
        }

        /**
         * Returns the user who posted the comment
         *
         * @return \thebuggenie\core\entities\User
         */
        public function getPostedBy()
        {
            try
            {
                return ($this->_posted_by instanceof \thebuggenie\core\entities\User) ? $this->_posted_by : \thebuggenie\core\entities\User::getB2DBTable()->selectById($this->_posted_by);
            }
            catch (\Exception $e)
            {
                return null;
            }
        }

        /**
         * Return the poster id
         *
         * @return integer
         */
        public function getPostedByID()
        {
            $poster = null;
            try
            {
                $poster = $this->getPostedBy();
            }
            catch (\Exception $e) {}
            return ($poster instanceof \thebuggenie\core\entities\common\Identifiable) ? $poster->getID() : null;
        }

        /**
         * Return the whether or not the user owns this comment
         *
         * @param int $user_id A user's ID
         *
         * @return bool
         */
        public function postedByUser($user_id)
        {
            $posted_by_id = null;

            try
            {
                $posted_by_id = $this->getPostedByID();

                if (!empty($posted_by_id) && !empty($user_id))
                {
                    if ($posted_by_id == $user_id)
                    {
                        return true;
                    }//endif
                }//endif
                else
                {
                    return false;
                }//endelse
            }//endtry
            catch (\Exception $e) { }
            return false;
        }//end postedByUser

        public function setPostedBy($var)
        {
            if (is_object($var))
            {
                $var = $var->getID();
            }
            $this->_posted_by = $var;
        }

        public function isPublic()
        {
            return $this->_is_public;
        }

        public function getContent()
        {
            return $this->_content;
        }

        protected function _parseContent($options = array())
        {
            switch ($this->_syntax)
            {
                case framework\Settings::SYNTAX_MD:
                    $parser = new \thebuggenie\core\helpers\TextParserMarkdown();
                    $text = $parser->transform($this->_content);
                    break;
                case framework\Settings::SYNTAX_PT:
                    $options = array('plain' => true);
                case framework\Settings::SYNTAX_MW:
                                default:
                    $parser = new \thebuggenie\core\helpers\TextParser($this->_content);
                    foreach ($options as $option => $value)
                    {
                        $parser->setOption($option, $value);
                    }
                    $text = $parser->getParsedText();
                    break;
            }

            if (isset($parser))
            {
                $this->_parser = $parser;
            }
            return $text;
        }

        public function getParsedContent($options = array())
        {
            return $this->_parseContent($options);
        }

        public function getUpdated()
        {
            return $this->_updated;
        }

        public function getPosted()
        {
            return $this->_posted;
        }

        public function isSystemComment()
        {
            return $this->_system_comment;
        }

        public function getTargetID()
        {
            return $this->_target_id;
        }

        public function getTarget()
        {
            if ($this->_target === null)
            {
                switch ($this->getTargetType())
                {
                    case self::TYPE_ISSUE:
                        $this->_target = \thebuggenie\core\entities\Issue::getB2DBTable()->selectById($this->_target_id);
                        break;
                    case self::TYPE_ARTICLE:
                        $this->_target = publish\entities\tables\Articles::getTable()->selectById($this->_target_id);
                        break;
                    default:
                        $event = \thebuggenie\core\framework\Event::createNew('core', 'Comment::getTarget', $this);
                        $event->trigger();
                        $this->_target = $event->getReturnValue();
                }
            }

            return $this->_target;
        }

        public function setTargetID($var)
        {
            $this->_target_id = $var;
        }

        public function getTargetType()
        {
            return $this->_target_type;
        }

        public function setTargetType($var)
        {
            $this->_target_type = $var;
        }

        public function setSystemComment($val = true)
        {
            $this->_system_comment = $val;
        }

        public function getModuleName()
        {
            return $this->_module;
        }

        public function setModuleName($var)
        {
            $this->_module = $var;
        }

        public function getCommentNumber()
        {
            return (int) $this->_comment_number;
        }

        public function toJSON()
        {
            $return_values = array(
                'id' => $this->getID(),
                'created_at' => $this->getPosted(),
                'comment_number' => $this->getCommentNumber(),
                'posted_by' => ($this->getPostedBy() instanceof \thebuggenie\core\entities\common\Identifiable) ? $this->getPostedBy()->toJSON() : null,
                'content' => $this->getContent(),
                'system_comment' => $this->isSystemComment(),
            );

            return $return_values;
        }

        public function setReplyToComment($reply_to_comment_id)
        {
            $this->_reply_to_comment = $reply_to_comment_id;
        }

        public function getReplyToComment()
        {
            if (!is_object($this->_reply_to_comment) && $this->_reply_to_comment)
            {
                $this->_b2dbLazyload('_reply_to_comment');
            }
            return $this->_reply_to_comment;
        }

        public function isReply()
        {
            return (bool) ($this->getReplyToComment() instanceof \thebuggenie\core\entities\Comment);
        }

        public function hasAssociatedChanges()
        {
            if (is_array($this->_log_items))
            {
                $this->_log_item_count = count($this->_log_items);
            }
            elseif ($this->_log_item_count === null)
            {
                $this->_log_item_count = $this->_b2dbLazycount('_log_items');
            }
            return $this->_log_item_count;
        }

        public function getLogItems()
        {
            return $this->_b2dbLazyload('_log_items');
        }

        public function getSyntax()
        {
            return $this->_syntax;
        }

        public function setSyntax($syntax)
        {
            if (!is_numeric($syntax)) $syntax = framework\Settings::getSyntaxValue($syntax);

            $this->_syntax = (int) $syntax;
        }

        public function getMentionableUsers()
        {
            $users = array($this->getPostedByID() => $this->getPostedBy());
            foreach ($this->getMentions() as $user)
            {
                $users[$user->getID()] = $user;
            }

            return $users;
        }

    }
