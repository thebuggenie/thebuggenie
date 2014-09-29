<?php

    namespace thebuggenie\core\entities;

    /**
     * Notification setting class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Notification setting class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\b2db\NotificationSettings")
     */
    class NotificationSetting extends \TBGIdentifiableClass
    {

        /**
         * The module name
         *
         * @var string
         * @Column(type="string", length=50)
         */
        protected $_module_name;

        /**
         * The setting name
         *
         * @var string
         * @Column(type="string", length=50)
         */
        protected $_name;

        /**
         * Setting value
         *
         * @var string
         * @Column(type="string", length=100)
         */
        protected $_value = '';

        /**
         * Who the notification is for
         *
         * @var \TBGUser
         * @Column(type="integer", length=10)
         * @Relates(class="\TBGUser")
         */
        protected $_user_id;

        /**
         * Return the module name
         *
         * @return string
         */
        public function getModuleName()
        {
            return $this->_module_name;
        }

        /**
         * Set the module name
         *
         * @param string $module_name
         */
        public function setModuleName($module_name)
        {
            $this->_module_name = $module_name;
        }

        /**
         * Return the notification settings name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the notification settings name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        public function getValue()
        {
            return $this->_value;
        }

        public function setValue($value)
        {
            $this->_value = $value;
        }

        public function getUser()
        {
            return $this->_b2dbLazyload('_user_id');
        }

        public function setUser($uid)
        {
            $this->_user_id = $uid;
        }

        public function isOn()
        {
            return (bool) $this->getValue();
        }

        public function isOff()
        {
            return !(bool) $this->isOn();
        }

    }
