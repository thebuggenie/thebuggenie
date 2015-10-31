<?php

    namespace thebuggenie\modules\mailing\entities;

    use thebuggenie\modules\mailing\entities\tables\IncomingEmailAccounts;

    /**
     * @Table(name="\thebuggenie\modules\mailing\entities\tables\IncomingEmailAccounts")
     */
    class IncomingEmailAccount extends \thebuggenie\core\entities\common\IdentifiableScoped
    {

        const SERVER_IMAP = 0;
        const SERVER_POP3 = 1;

        /**
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @Column(type="string", length=200)
         */
        protected $_server;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_port;

        /**
         * @Column(type="string", length=200)
         */
        protected $_folder;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_server_type;

        /**
         * @Column(type="boolean")
         */
        protected $_ssl;

        /**
         * @Column(type="boolean")
         */
        protected $_ignore_certificate_validation = false;

        /**
         * @Column(type="boolean")
         */
        protected $_plaintext_authentication = false;

        /**
         * @Column(type="boolean")
         */
        protected $_keep_email;

        /**
         * @Column(type="string", length=200)
         */
        protected $_username;

        /**
         * @Column(type="string", length=200)
         */
        protected $_password;
        protected $_connection;

        /**
         * @var \thebuggenie\core\entities\Project
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
         */
        protected $_project;

        /**
         * @var \thebuggenie\core\entities\Issuetype
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Issuetype")
         */
        protected $_issuetype;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_num_last_fetched = 0;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_time_last_fetched = 0;

        public static function getAll()
        {
            return tables\IncomingEmailAccounts::getTable()->getAll();
        }

        public static function getAllByProjectID($project_id)
        {
            return tables\IncomingEmailAccounts::getTable()->getAllByProjectID($project_id);
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

        public function getServer()
        {
            return $this->_server;
        }

        public function setServer($server)
        {
            $this->_server = $server;
        }

        public function getFoldername()
        {
            return $this->_folder;
        }

        public function setFoldername($folder)
        {
            $this->_folder = $folder;
        }

        public function getPort()
        {
            return $this->_port;
        }

        public function setPort($port)
        {
            $this->_port = $port;
        }

        public function getServerType()
        {
            return $this->_server_type;
        }

        public function setServerType($server_type)
        {
            $this->_server_type = $server_type;
        }

        public function isImap()
        {
            return (bool) $this->getServerType() == self::SERVER_IMAP;
        }

        public function isPop3()
        {
            return (bool) $this->getServerType() == self::SERVER_POP3;
        }

        public function usesSSL()
        {
            return (boolean) $this->_ssl;
        }

        public function setSSL($ssl)
        {
            $this->_ssl = $ssl;
        }

        public function doesKeepEmails()
        {
            return (boolean) $this->_keep_email;
        }

        public function setKeepEmails($keep_emails)
        {
            $this->_keep_email = $keep_emails;
        }

        public function getUsername()
        {
            return $this->_username;
        }

        public function setUsername($username)
        {
            $this->_username = $username;
        }

        public function getPassword()
        {
            return $this->_password;
        }

        public function setPassword($password)
        {
            $this->_password = $password;
        }

        public function setProject($project)
        {
            $this->_project = $project;
        }

        public function setIssuetype($issuetype)
        {
            $this->_issuetype = $issuetype;
        }

        public function getNumberOfEmailsLastFetched()
        {
            return $this->_num_last_fetched;
        }

        public function setNumberOfEmailsLastFetched($num_last_fetched)
        {
            $this->_num_last_fetched = $num_last_fetched;
        }

        public function getTimeLastFetched()
        {
            return $this->_time_last_fetched;
        }

        public function setTimeLastFetched($time_last_fetched)
        {
            $this->_time_last_fetched = $time_last_fetched;
        }

        /**
         * Retrieve the imap connection string for this account
         *
         * @return string
         */
        public function getConnectionString()
        {
            $conn_string = "{" . $this->getServer() . ":" . $this->getPort() . "/";
            $conn_string .= ($this->getServerType() == self::SERVER_IMAP) ? "imap" : "pop3";

            if ($this->usesSSL())
                $conn_string .= "/ssl";
            if ($this->doesIgnoreCertificateValidation())
                $conn_string .= "/novalidate-cert";

            $conn_string .= "}";
            $conn_string .= ($this->getFoldername() == '') ? "INBOX" : $this->getFoldername();

            return $conn_string;
        }

        /**
         * Create an imap connection for this account
         */
        public function connect()
        {
            if ($this->_connection === null)
            {
                $options = array();
                if ($this->usesPlaintextAuthentication())
                    $options['DISABLE_AUTHENTICATOR'] = 'GSSAPI';

                $this->_connection = imap_open($this->getConnectionString(), $this->getUsername(), $this->getPassword(), 0, 0, $options);
            }
            if (!is_resource($this->_connection))
            {
                $error = imap_last_error();
                $error = ($error === false) ? \thebuggenie\core\framework\Context::getI18n()->__('No error message provided') : $error;
                throw new \Exception(\thebuggenie\core\framework\Context::getI18n()->__('Could not connect to the specified email server(%connection_string): %error_message', array('%connection_string' => $this->getConnectionString(), '%error_message' => $error)));
            }
        }

        /**
         * Disconnects this account from the imap resource         *
         */
        public function disconnect()
        {
            if (!$this->doesKeepEmails())
            {
                imap_expunge($this->connection);
            }
            imap_close($this->_connection);
            $this->_connection = null;
        }

        /**
         * Returns the imap connection resource
         *
         * @return resource
         */
        public function getConnection()
        {
            return $this->_connection;
        }

        /**
         * Returns imap overview objects for all unread emails on this account
         *
         * @return array
         */
        public function getUnprocessedEmails()
        {
            $this->connect();
            $uids = imap_search($this->_connection, 'UNSEEN');
            if ($uids)
            {
                return imap_fetch_overview($this->_connection, join(",", $uids), 0);
            }
            else
            {
                return array();
            }
        }

        /**
         * Takes an email object and looks up details for this particular email
         * Returns the primary body and the mime type
         * Sets the message for deletion when chosen to do not keep emails
         *
         * @param stdObject $email
         * @return \thebuggenie\modules\mailing\entities\IncomingEmailMessage the message
         */
        public function getMessage($email)
        {
            $message = new IncomingEmailMessage($this->_connection, $email->msgno);
            $is_structure = $message->fetch();
            if ($is_structure && !$this->doesKeepEmails())
            {
                imap_delete($this->_connection, $email->msgno);
            }
            return $message;
        }

        /**
         * Returns number of unread emails on this account
         *
         * @return integer
         */
        public function getUnreadCount()
        {
            $this->connect();
            $result = imap_search($this->_connection, "UNSEEN");
            return ($result !== false) ? count($result) : 0;
        }

        /**
         * Returns the project associated with this account
         *
         * @return \thebuggenie\core\entities\Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyload('_project');
        }

        /**
         * Returns the issuetype associated with this account
         *
         * @return \thebuggenie\core\entities\Issuetype
         */
        public function getIssuetype()
        {
            return $this->_b2dbLazyload('_issuetype');
        }

        public function getIssuetypeID()
        {
            $issuetype = $this->getIssuetype();
            return ($issuetype instanceof \thebuggenie\core\entities\Issuetype) ? $issuetype->getID() : null;
        }

        public function setIgnoreCertificateValidation($value = true)
        {
            $this->_ignore_certificate_validation = $value;
        }

        public function doesIgnoreCertificateValidation()
        {
            return $this->_ignore_certificate_validation;
        }

        public function setUsePlaintextAuthentication($value = true)
        {
            $this->_plaintext_authentication = $value;
        }

        public function usesPlaintextAuthentication()
        {
            return $this->_plaintext_authentication;
        }

        public function doesUsePlaintextAuthentication()
        {
            return $this->usesPlaintextAuthentication();
        }

    }
