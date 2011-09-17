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
				
		public function getConnectionString()
		{
			$conn_string = "{".$this->getServer().":".$this->getPort()."/";
			$conn_string .= ($this->getServerType() == self::SERVER_IMAP) ? "imap" : "pop3";
			
			if ($this->usesSSL()) $conn_string .= "/ssl";
			
			$conn_string .= "}INBOX";
			
			return $conn_string;
		}

	}