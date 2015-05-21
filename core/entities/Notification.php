<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\modules\publish\entities\tables\Articles;
    use thebuggenie\core\framework;

    /**
     * Notification item class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Notification item class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\Notifications")
     */
    class Notification extends IdentifiableScoped
    {

        const TYPE_ISSUE_CREATED = 'issue_created';
        const TYPE_ISSUE_UPDATED = 'issue_updated';
        const TYPE_ISSUE_COMMENTED = 'issue_commented';
        const TYPE_ISSUE_MENTIONED = 'issue_mentioned';
        const TYPE_ARTICLE_UPDATED = 'article_updated';
        const TYPE_ARTICLE_COMMENTED = 'article_commented';
        const TYPE_ARTICLE_MENTIONED = 'article_mentioned';
        const TYPE_COMMENT_MENTIONED = 'comment_mentioned';

        /**
         * @Column(type="integer", length=10)
         */
        protected $_target_id;

        /**
         * The notification target
         *
         * @var \thebuggenie\core\entities\common\IdentifiableScoped
         */
        protected $_target;

        /**
         * @Column(type="string", length=100)
         */
        protected $_notification_type;

        /**
         * @Column(type="string", length=50, default="core")
         */
        protected $_module_name = 'core';

        /**
         * @Column(type="boolean", default="0")
         */
        protected $_is_read = false;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_created_at;

        /**
         * Who triggered the notification
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_triggered_by_user_id;

        /**
         * Who the notification is for
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_user_id;

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if ($is_new)
            {
                $this->_created_at = NOW;
            }
        }

        protected function _postSave($is_new)
        {
            if (!$is_new)
            {
                if ($this->isRead() && $this->getCreatedAt() < NOW - (86400 * 30))
                {
                    $this->delete();
                }
            }
        }

        /**
         * Returns the object which the notification is for
         *
         * @return \thebuggenie\core\entities\common\IdentifiableScoped
         */
        public function getTarget()
        {
            if ($this->_target === null)
            {
                if ($this->_module_name == 'core')
                {
                    switch ($this->_notification_type)
                    {
                        case self::TYPE_ARTICLE_COMMENTED:
                        case self::TYPE_ARTICLE_MENTIONED:
                        case self::TYPE_ISSUE_COMMENTED:
                        case self::TYPE_ISSUE_MENTIONED:
                        case self::TYPE_COMMENT_MENTIONED:
                            $this->_target = tables\Comments::getTable()->selectById((int) $this->_target_id);
                            break;
                        case self::TYPE_ISSUE_UPDATED:
                        case self::TYPE_ISSUE_CREATED:
                            $this->_target = \thebuggenie\core\entities\Issue::getB2DBTable()->selectById((int) $this->_target_id);
                            break;
                        case self::TYPE_ARTICLE_UPDATED:
                            $this->_target = tables\Articles::getTable()->selectById((int) $this->_target_id);
                            break;
                    }
                }
                else
                {
                    $event = new \thebuggenie\core\framework\Event('core', 'thebuggenie\core\entities\Notification::getTarget', $this);
                    $event->triggerUntilProcessed();
                    $this->_target = $event->getReturnValue();
                }
            }
            return $this->_target;
        }

        public function setTarget($target)
        {
            $this->_target_id = $target->getID();
            $this->_target = $target;
        }

        public function getTargetID()
        {
            return $this->_target_id;
        }

        public function getNotificationType()
        {
            return $this->_notification_type;
        }

        public function setNotificationType($notification_type)
        {
            $this->_notification_type = $notification_type;
        }

        public function getCreatedAt()
        {
            return $this->_created_at;
        }

        public function setCreatedAt($created_at)
        {
            $this->_created_at = $created_at;
        }

        public function getTriggeredByUser()
        {
            return $this->_b2dbLazyload('_triggered_by_user_id');
        }

        public function setTriggeredByUser($uid)
        {
            $this->_triggered_by_user_id = $uid;
        }

        public function getUser()
        {
            return $this->_b2dbLazyload('_user_id');
        }

        public function setUser($uid)
        {
            $this->_user_id = $uid;
        }

        public function getModuleName()
        {
            return $this->_module_name;
        }

        public function getIsRead()
        {
            return $this->_is_read;
        }

        public function isRead()
        {
            return $this->getIsRead();
        }

        public function setModuleName($module_name)
        {
            $this->_module_name = $module_name;
        }

        public function setIsRead($is_read)
        {
            $this->_is_read = $is_read;
        }



    }
