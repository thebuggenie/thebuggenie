<?php

    namespace thebuggenie\core\entities\common;

    /**
     * Ownable item class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Ownable item class
     *
     * @package thebuggenie
     * @subpackage core
     */
    class Ownable extends IdentifiableScoped
    {

        /**
         * The project owner if team
         *
         * @var \thebuggenie\core\entities\Team
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Team")
         */
        protected $_owner_team;

        /**
         * The project owner if user
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_owner_user;

        public function getOwner()
        {
            $this->_b2dbLazyload('_owner_team');
            $this->_b2dbLazyload('_owner_user');

            if ($this->_owner_team instanceof \thebuggenie\core\entities\Team) {
                return $this->_owner_team;
            } elseif ($this->_owner_user instanceof \thebuggenie\core\entities\User) {
                return $this->_owner_user;
            } else {
                return null;
            }
        }

        public function hasOwner()
        {
            return (bool) ($this->getOwner() instanceof \thebuggenie\core\entities\common\Identifiable);
        }

        public function setOwner(Identifiable $owner)
        {
            if ($owner instanceof \thebuggenie\core\entities\Team) {
                $this->_owner_user = null;
                $this->_owner_team = $owner;
            } else {
                $this->_owner_team = null;
                $this->_owner_user = $owner;
            }
        }

        public function clearOwner()
        {
            $this->_owner_team = null;
            $this->_owner_user = null;
        }

    }