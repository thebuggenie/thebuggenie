<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\core\framework;

    /**
     * Client class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Client class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\Clients")
     */
    class Client extends IdentifiableScoped
    {

        protected $_members = null;

        protected $_num_members = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * Email of client
         *
         * @param string
         * @Column(type="string", length=200)
         */
        protected $_email = null;

        /**
         * Telephone number of client
         *
         * @param integer
         * @Column(type="string", length=200)
         */
        protected $_telephone = null;

        /**
         * URL for client website
         *
         * @param string
         * @Column(type="string", length=200)
         */
        protected $_website = null;

        /**
         * Fax number of client
         *
         * @param integer
         * @Column(type="string", length=200)
         */
        protected $_fax = null;

        /**
         * List of client's dashboards
         *
         * @var array|\thebuggenie\core\entities\Dashboard
         * @Relates(class="\thebuggenie\core\entities\Dashboard", collection=true, foreign_column="client_id", orderby="name")
         */
        protected $_dashboards = null;

        protected static $_clients = null;

        public static function doesClientNameExist($client_name)
        {
            return tables\Clients::getTable()->doesClientNameExist($client_name);
        }

        public static function getAll()
        {
            if (self::$_clients === null)
            {
                self::$_clients = tables\Clients::getTable()->getAll();
            }
            return self::$_clients;
        }

        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {

        }

        public function __toString()
        {
            return "" . $this->_name;
        }

        /**
         * Get the client's website
         *
         * @return string
         */
        public function getWebsite()
        {
            return $this->_website;
        }

        /**
         * Get the client's email address
         *
         * @return string
         */
        public function getEmail()
        {
            return $this->_email;
        }

        /**
         * Get the client's telephone number
         *
         * @return integer
         */
        public function getTelephone()
        {
            return $this->_telephone;
        }

        /**
         * Get the client's fax number
         *
         * @return integer
         */
        public function getFax()
        {
            return $this->_fax;
        }

        /**
         * Set the client's website
         *
         * @param string
         */
        public function setWebsite($website)
        {
            $this->_website = $website;
        }

        /**
         * Set the client's email address
         *
         * @param string
         */
        public function setEmail($email)
        {
            $this->_email = $email;
        }

        /**
         * Set the client's telephone number
         *
         * @param integer
         */
        public function setTelephone($telephone)
        {
            $this->_telephone = $telephone;
        }

        /**
         * Set the client's fax number
         *
         * @param integer
         */
        public function setFax($fax)
        {
            $this->_fax = $fax;
        }

        /**
         * Adds a user to the client
         *
         * @param \thebuggenie\core\entities\User $user
         */
        public function addMember(\thebuggenie\core\entities\User $user)
        {
            if (!$user->getID()) throw new \Exception('Cannot add user object to client until the object is saved');

            tables\ClientMembers::getTable()->addUserToClient($user->getID(), $this->getID());

            if (is_array($this->_members))
                $this->_members[$user->getID()] = $user->getID();
        }

        public function getMembers()
        {
            if ($this->_members === null)
            {
                $this->_members = array();
                foreach (tables\ClientMembers::getTable()->getUIDsForClientID($this->getID()) as $uid)
                {
                    $this->_members[$uid] = \thebuggenie\core\entities\User::getB2DBTable()->selectById($uid);
                }
            }
            return $this->_members;
        }

        public function removeMember(\thebuggenie\core\entities\User $user)
        {
            if ($this->_members !== null)
            {
                unset($this->_members[$user->getID()]);
            }
            if ($this->_num_members !== null)
            {
                $this->_num_members--;
            }
            tables\ClientMembers::getTable()->removeUserFromClient($user->getID(), $this->getID());
        }

        protected function _preDelete()
        {
            tables\ClientMembers::getTable()->removeUsersFromClient($this->getID());
        }

        public static function findClients($details)
        {
            $crit = new \b2db\Criteria();
            $crit->addWhere(tables\Clients::NAME, "%$details%", \b2db\Criteria::DB_LIKE);
            $clients = array();
            if ($res = tables\Clients::getTable()->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $clients[$row->get(tables\Clients::ID)] = new \thebuggenie\core\entities\Client($row->get(tables\Clients::ID), $row);
                }
            }
            return $clients;
        }

        public function getNumberOfMembers()
        {
            if ($this->_members !== null)
            {
                return count($this->_members);
            }
            elseif ($this->_num_members === null)
            {
                $this->_num_members = tables\ClientMembers::getTable()->getNumberOfMembersByClientID($this->getID());
            }

            return $this->_num_members;
        }

        public function hasAccess()
        {
            return (bool) (framework\Context::getUser()->hasPageAccess('clientlist') || framework\Context::getUser()->isMemberOfClient($this));
        }

        /**
         * Return the items name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the edition name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        /**
         * Returns an array of client dashboards
         *
         * @return array|\thebuggenie\core\entities\Dashboard
         */
        public function getDashboards()
        {
            $this->_b2dbLazyload('_dashboards');
            return $this->_dashboards;
        }

    }
