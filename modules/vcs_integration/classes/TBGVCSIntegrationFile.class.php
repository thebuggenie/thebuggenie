<?php
	/**
	 * File class, vcs_integration
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.2
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * File class, vcs_integration
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */
	class TBGVCSIntegrationFile extends TBGIdentifiableClass
	{
		protected static $_b2dbtablename = 'TBGVCSIntegrationFileTable';
		
		/**
		 * File path
		 * @var string
		 */
		protected $_file_name = null;
		
		/**
		 * Action applied to file (Added, Updated or Deleted)
		 * @var string
		 */
		protected $_action = null;
		
		/**
		 * Associated commit
		 * @var TBGVCSIntegrationCommit
		 * @Class TBGVCSIntegrationCommit
		 */
		protected $_commit_id = null;
		
		/**
		 * Get the file path
		 * @return string
		 */
		public function getFile()
		{
			return $this->_file_name;
		}
		
		/**
		 * Get the file action
		 * @return string
		 */
		public function getAction()
		{
			return $this->_action;
		}
		
		/**
		 * Get the commit with this link
		 * @return TBGVCSIntegrationCommit
		 */
		public function getCommit()
		{
			return $this->_commit_id;
		}
		
	}
