<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;

    /**
     * Log item class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Log item class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\Log")
     */
    class LogItem extends IdentifiableScoped
    {

        /**
         * @Column(type="integer", length=10)
         */
        protected $_target;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_target_type;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_change_type;

        /**
         * @Column(type="text")
         */
        protected $_previous_value;

        /**
         * @Column(type="text")
         */
        protected $_current_value;

        /**
         * @Column(type="text")
         */
        protected $_text;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_time;

        /**
         * Who posted the comment
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_uid;

        /**
         * Related comment
         *
         * @var \thebuggenie\core\entities\Comment
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Comment")
         */
        protected $_comment_id;

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if ($is_new)
            {
                $this->_time = NOW;
            }
        }

        public function getTarget()
        {
            return $this->_target;
        }

        public function setTarget($target)
        {
            $this->_target = $target;
        }

        public function getTargetType()
        {
            return $this->_target_type;
        }

        public function setTargetType($target_type)
        {
            $this->_target_type = $target_type;
        }

        public function getChangeType()
        {
            return $this->_change_type;
        }

        public function setChangeType($change_type)
        {
            $this->_change_type = $change_type;
        }

        public function getPreviousValue()
        {
            return $this->_previous_value;
        }

        public function setPreviousValue($previous_value)
        {
            $this->_previous_value = $previous_value;
        }

        public function getCurrentValue()
        {
            return $this->_current_value;
        }

        public function setCurrentValue($current_value)
        {
            $this->_current_value = $current_value;
        }

        public function getText()
        {
            return $this->_text;
        }

        public function setText($text)
        {
            $this->_text = $text;
        }

        public function getTime()
        {
            return $this->_time;
        }

        public function setTime($time)
        {
            $this->_time = $time;
        }

        public function getUser()
        {
            return $this->_b2dbLazyload('_uid');
        }

        public function setUser($uid)
        {
            $this->_uid = $uid;
        }

        public function getComment()
        {
            return $this->_b2dbLazyload('_comment_id');
        }

        public function setComment($comment_id)
        {
            $this->_comment_id = $comment_id;
        }

        public function hasChangeDetails()
        {
            return ($this->_comment_id !== null);
        }

    }
