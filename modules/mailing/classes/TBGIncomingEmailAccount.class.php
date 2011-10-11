<?php


	class TBGIncomingEmailAccount extends TBGIdentifiableClass
	{
		
		const SERVER_IMAP = 0;
		const SERVER_POP3 = 1;
		
		protected $_id;
		
		protected $_name;
		
		protected $_server;
		
		protected $_port;
		
		protected $_server_type;
		
		protected $_ssl;
		
		protected $_keep_email;
		
		protected $_username;
		
		protected $_password;
		
		protected $_connection;

		/**
		 * @Class TBGProject
		 * @var TBGProject
		 */
		protected $_project;
		
		/**
		 * @Class TBGIssuetype
		 * @var TBGIssuetype
		 */
		protected $_issuetype;
		
		protected $_num_last_fetched = 0;
		
		protected $_time_last_fetched = 0;
		
		static public function getAll()
		{
			$accounts = array();
			if ($res = TBGIncomingEmailAccountTable::getTable()->doSelectAll())
			{
				while ($row = $res->getNextRow())
				{
					$accounts[] = TBGContext::factory()->TBGIncomingEmailAccount($row->get(TBGIncomingEmailAccountTable::ID), $row);
				}
			}
			
			return $accounts;
		}

		static public function getAllByProjectID($project_id)
		{
			$accounts = array();
			if ($res = TBGIncomingEmailAccountTable::getTable()->getAllByProjectID($project_id))
			{
				while ($row = $res->getNextRow())
				{
					$accounts[] = TBGContext::factory()->TBGIncomingEmailAccount($row->get(TBGIncomingEmailAccountTable::ID), $row);
				}
			}
			
			return $accounts;
		}

		public function getId()
		{
			return $this->_id;
		}

		public function setID($id)
		{
			$this->_id = $id;
		}

		public function getName()
		{
			return $this->_name;
		}

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
			$conn_string = "{".$this->getServer().":".$this->getPort()."/";
			$conn_string .= ($this->getServerType() == self::SERVER_IMAP) ? "imap" : "pop3";
			
			if ($this->usesSSL()) $conn_string .= "/ssl";
			
			$conn_string .= "}INBOX";
			
			return $conn_string;
		}
		
		/**
		 * Create an imap connection for this account
		 */
		public function connect()
		{
			if ($this->_connection === null)
			{
				$this->_connection = imap_open($this->getConnectionString(), $this->getUsername(), $this->getPassword(), OP_READONLY);
			}
		}
		
		/**
		 * Disconnects this account from the imap resource
		 */
		public function disconnect()
		{
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
		 * 
		 * @param stdObject $email
		 * @return array An array($type, $body)
		 */
		public function getEmailDetails($email)
		{
			$structure = imap_fetchstructure($this->_connection, $email->msgno);
			$type = TBGContext::getModule('mailing')->getMailMimeType($structure);

			$data = TBGContext::getModule('mailing')->getMailPart($this->_connection, $email->msgno, $type, $structure);
			return array($type, $data);
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
		 * @return TBGProject
		 */
		public function getProject()
		{
			return $this->_getPopulatedObjectFromProperty('_project');
		}
		
		/**
		 * Returns the issuetype associated with this account
		 * 
		 * @return TBGIssuetype
		 */
		public function getIssuetype()
		{
			return $this->_getPopulatedObjectFromProperty('_issuetype');
		}
		
		public function getIssuetypeID()
		{
			$issuetype = $this->getIssuetype();
			return ($issuetype instanceof TBGIssuetype) ? $issuetype->getID() : null;
		}

	}