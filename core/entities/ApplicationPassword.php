<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;

    /**
     * Application password class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Application password class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\ApplicationPasswords")
     */
    class ApplicationPassword extends IdentifiableScoped
    {

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * Hashed password
         *
         * @var string
         * @Column(type="string", length=100)
         */
        protected $_password = '';

        /**
         * @Column(type="integer", length=10)
         */
        protected $_created_at;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_last_used_at;

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

        /**
         * Return the application password name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the application password name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        /**
         * Returns a hash of the user password
         *
         * @return string
         */
        public function getHashPassword()
        {
            return $this->_password;
        }

        /**
         * Returns a hash of the user password
         *
         * @see \thebuggenie\core\entities\User::getHashPassword
         * @return string
         */
        public function getPassword()
        {
            return $this->getHashPassword();
        }

        /**
         * Set password
         *
         * @param string $newpassword
         *
         * @see \thebuggenie\core\entities\User::changePassword
         */
        public function setPassword($newpassword)
        {
            $this->_password = \thebuggenie\core\entities\User::hashPassword($newpassword, $this->getUser()->getSalt());
        }

        public function getCreatedAt()
        {
            return $this->_created_at;
        }

        public function setCreatedAt($created_at)
        {
            $this->_created_at = $created_at;
        }

        public function getLastUsedAt()
        {
            return $this->_last_used_at;
        }

        public function isUsed()
        {
            return (bool) $this->_last_used_at;
        }

        public function setLastUsedAt($last_used_at)
        {
            $this->_last_used_at = $last_used_at;
        }

        public function useOnce()
        {
            $this->_last_used_at = time();
        }

        public function getUser()
        {
            return $this->_b2dbLazyload('_user_id');
        }

        public function setUser($uid)
        {
            $this->_user_id = $uid;
        }

    }
