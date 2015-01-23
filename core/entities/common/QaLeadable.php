<?php

    namespace thebuggenie\core\entities\common;

    /**
     * Item class for objects with both QA responsible and Leader properties
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Item class for objects with both QA responsible and Leader properties
     *
     * @package thebuggenie
     * @subpackage core
     */
    class QaLeadable extends Releaseable
    {

        /**
         * The lead type for the project, \thebuggenie\core\entities\common\Identifiable::TYPE_USER or \thebuggenie\core\entities\common\Identifiable::TYPE_TEAM
         *
         * @var \thebuggenie\core\entities\Team
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Team")
         */
        protected $_leader_team;

        /**
         * The lead for the project
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_leader_user;

        /**
         * The QA responsible for the project, \thebuggenie\core\entities\common\Identifiable::TYPE_USER or \thebuggenie\core\entities\common\Identifiable::TYPE_TEAM
         *
         * @var \thebuggenie\core\entities\Team
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Team")
         */
        protected $_qa_responsible_team;

        /**
         * The QA responsible for the project
         *
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_qa_responsible_user;

        public function getLeader()
        {
            $this->_b2dbLazyload('_leader_team');
            $this->_b2dbLazyload('_leader_user');

            if ($this->_leader_team instanceof \thebuggenie\core\entities\Team) {
                return $this->_leader_team;
            } elseif ($this->_leader_user instanceof \thebuggenie\core\entities\User) {
                return $this->_leader_user;
            } else {
                return null;
            }
        }

        public function getLeaderID()
        {
            $leader = $this->getLeader();
            return ($leader instanceof \thebuggenie\core\entities\common\Identifiable) ? $leader->getID() : null;
        }

        public function hasLeader()
        {
            return (bool) ($this->getLeader() instanceof \thebuggenie\core\entities\common\Identifiable);
        }

        public function setLeader(\thebuggenie\core\entities\common\Identifiable $leader)
        {
            if ($leader instanceof \thebuggenie\core\entities\Team) {
                $this->_leader_user = null;
                $this->_leader_team = $leader;
            } else {
                $this->_leader_team = null;
                $this->_leader_user = $leader;
            }
        }

        public function clearLeader()
        {
            $this->_leader_team = null;
            $this->_leader_user = null;
        }

        public function getQaResponsible()
        {
            if (!empty($this->_qa_responsible_team)) {
                $this->_b2dbLazyload('_qa_responsible_team');
            } elseif (!empty($this->_qa_responsible_user)) {
                $this->_b2dbLazyload('_qa_responsible_user');
            }

            if ($this->_qa_responsible_team instanceof \thebuggenie\core\entities\Team) {
                return $this->_qa_responsible_team;
            } elseif ($this->_qa_responsible_user instanceof \thebuggenie\core\entities\User) {
                return $this->_qa_responsible_user;
            } else {
                return null;
            }
        }

        public function getQaResponsibleID()
        {
            $qa_responsible = $this->getQaResponsible();
            return ($qa_responsible instanceof \thebuggenie\core\entities\common\Identifiable) ? $qa_responsible->getID() : null;
        }

        public function hasQaResponsible()
        {
            return (bool) ($this->getQaResponsible() instanceof \thebuggenie\core\entities\common\Identifiable);
        }

        public function setQaResponsible(\thebuggenie\core\entities\common\Identifiable $qa_responsible)
        {
            if ($qa_responsible instanceof \thebuggenie\core\entities\Team) {
                $this->_qa_responsible_user = null;
                $this->_qa_responsible_team = $qa_responsible;
            } else {
                $this->_qa_responsible_team = null;
                $this->_qa_responsible_user = $qa_responsible;
            }
        }

        public function clearQaResponsible()
        {
            $this->_qa_responsible_team = null;
            $this->_qa_responsible_user = null;
        }

    }
