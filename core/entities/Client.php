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
         * @Column(type="string", length=255)
         */
        protected $_name;

        /**
         * Email of client
         *
         * @param string
         * @Column(type="string", length=255)
         */
        protected $_email = null;

        /**
         * Telephone number of client
         *
         * @param string
         * @Column(type="string", length=255)
         */
        protected $_telephone = null;

        /**
         * URL for client website
         *
         * @param string
         * @Column(type="string", length=2083)
         */
        protected $_website = null;

        /**
         * Fax number of client
         *
         * @param string
         * @Column(type="string", length=255)
         */
        protected $_fax = null;

        /**
         * Short code of client
         *
         * @param string
         * @Column(type="string", length=255)
         */
        protected $_code = null;

        /**
         * Main contact of client
         *
         * @param string
         * @Column(type="string", length=255)
         */
        protected $_contact = null;

        /**
         * Title of main contact of client
         *
         * @param string
         * @Column(type="string", length=255)
         */
        protected $_title = null;

        /**
         * Mailing address of client
         *
         * @param string
         * @Column(type="string", length=255)
         */
        protected $_address = null;

        /**
         * Additional notes for client
         *
         * @param string
         * @Column(string="string", length=4096)
         */
        protected $_notes = null;


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

        public static function doesClientCodeExist($client_code)
        {
            return tables\Clients::getTable()->doesClientCodeExist($client_code);
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
         * @return string
         */
        public function getTelephone()
        {
            return $this->_telephone;
        }

        /**
         * Get the client's fax number
         *
         * @return string
         */
        public function getFax()
        {
            return $this->_fax;
        }

        /**
         * Get the client's "short" code
         *
         * @return string
         */
        public function getCode()
        {
            return (empty($this->_code) ? $this->getID() : $this->_code);
        }

        /**
         * Get the client's main contact name
         *
         * @return string
         */
        public function getContact()
        {
            return $this->_contact;
        }

        /**
         * Get the client's main contact title
         *
         * @return string
         */
        public function getTitle()
        {
            return $this->_title;
        }

        /**
         * Get the client's main addres
         *
         * @return string
         */
        public function getAddress()
        {
            return $this->_address;
        }

        /**
         * Get the client's additional notes
         *
         * @return string
         */
        public function getNotes()
        {
            return $this->_notes;
        }

        /**
         * Set the client code
         *
         * @return string
         */
        public function setCode($code)
        {
            $this->_code = strtoupper($code);
        }

        /**
         * Set the client main contact
         *
         * @return string
         */
        public function setContact($contact)
        {
            $this->_contact = $contact;
        }

        /**
         * Set the client main contact title
         *
         * @return string
         */
        public function setTitle($title)
        {
            $this->_title = $title;
        }

        /**
         * Set the client main address
         *
         * @return string
         */
        public function setAddress($address)
        {
            $this->_address = $address;
        }

        /**
         * Set the client additional notes
         *
         * @return string
         */
        public function setNotes($notes)
        {
            $this->_notes = $notes;
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
            return (bool) (framework\Context::getUser()->hasPageAccess('clientlist') && framework\Context::getUser()->isMemberOfClient($this));
        }

        /**
         * Return the client name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the client name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = trim($name);
        }

        /**
         * Returns an array of client dashboards
         *
         * @return \thebuggenie\core\entities\Dashboard[]
         */
        public function getDashboards()
        {
            $this->_b2dbLazyLoad('_dashboards');
            return $this->_dashboards;
        }

        /**
         * @return Project[][]
         */
        public function getProjects()
        {
            $projects = Project::getAllByClientID($this->getID());

            $active_projects = [];
            $archived_projects = [];

            foreach ($projects as $project_id => $project)
            {
                if ($project->isArchived()) {
                    $archived_projects[$project_id] = $project;
                } else {
                    $active_projects[$project_id] = $project;
                }
            }

            return [$active_projects, $archived_projects];
        }

    }
