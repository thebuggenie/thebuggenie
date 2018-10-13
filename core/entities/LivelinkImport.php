<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\framework;
    use thebuggenie\core\entities\common\IdentifiableScoped,
        \thebuggenie\core\entities\Project,
        \thebuggenie\core\entities\User,
        \thebuggenie\core\entities\Team,
        \thebuggenie\core\entities\Client;

    /**
     * Dashboard class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Dashboard class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\LivelinkImports")
     */
    class LivelinkImport extends IdentifiableScoped
    {

        /**
         * @var \thebuggenie\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\User")
         */
        protected $_user_id;

        /**
         * @var \thebuggenie\core\entities\Project
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
         */
        protected $_project_id;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_created_at;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_completed_at;

        /**
         * @Column(type="serializable", length=1000)
         */
        protected $_data;

        /**
         * Returns the associated user
         *
         * @return \thebuggenie\core\entities\User
         */
        public function getUser()
        {
            return $this->_b2dbLazyload('_user_id');
        }

        public function setUser($user)
        {
            $this->_user_id = $user;
        }

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if ($is_new) {
                $this->_created_at = NOW;
            }
        }

        /**
         * Returns the associated project
         *
         * @return \thebuggenie\core\entities\Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyload('_project_id');
        }

        public function setProject($project)
        {
            $this->_project_id = $project;
        }

        /**
         * @return array
         */
        public function getData()
        {
            return $this->_data;
        }

        /**
         * @param array $data
         */
        public function setData($data)
        {
            $this->_data = $data;
        }

        /**
         * @return mixed
         */
        public function getCompletedAt()
        {
            return $this->_completed_at;
        }

        /**
         * @param mixed $completed_at
         */
        public function setCompletedAt($completed_at)
        {
            $this->_completed_at = $completed_at;
        }

    }
