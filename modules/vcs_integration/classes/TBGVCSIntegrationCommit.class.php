<?php
	/**
	 * Commit class, vcs_integration
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.2
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * Commit class, vcs_integration
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */
	class TBGVCSIntegrationCommit extends TBGIdentifiableClass
	{
		protected static $_b2dbtablename = 'TBGVCSIntegrationCommitsTable';
		
		/**
		 * Commit log.
		 * @var string
		 */
		protected $_log = null;
		
		/**
		 * Revision number/hash of previous commit
		 * @var string/integer
		 */
		protected $_old_rev = null;
		
		/**
		 * Revision number/hash of this commit
		 * @var string/integer
		 */
		protected $_new_rev = null;
		
		/**
		 * Commit author
		 * @var TBGUser
		 * @Class TBGUser
		 */
		protected $_author = null;
		
		/**
		 * POSIX timestamp of commit
		 * @var integer
		 */
		protected $_date = null;
		
		/**
		 * Misc data
		 * @var string
		 */
		protected $_data = null;
		
		/**
		 * Affected files
		 * @var array
		 */
		protected $_files = null;
		
		/**
		 * Affected issues
		 * @var array
		 */
		protected $_issues = null;
		
		/**
		 * Get the commit log for this commit
		 * @return string
		 */
		public function getLog()
		{
			return $this->_log;
		}
		
		/**
		 * Get this commit's revision number or hash
		 * @return string/integer
		 */
		public function getRevision()
		{
			return $this->_new_rev;
		}
		
		/**
		 * Get the preceeding commit's revision number or hash
		 * @return string/integer
		 */
		public function getPreviousRevision()
		{
			return $this->_old_rev;
		}
		
		/**
		 * Get the previous commit
		 * @return TBGVCSIntegrationCommit
		 */
		public function getPreviousCommit()
		{
			// FIXME
		}
		
		/**
		 * Get the author of this commit
		 * @return TBGAuthor
		 */
		public function getAuthor()
		{
			return $this->_author;
		}
		
		/**
		 * Get the POSIX timestamp of this comment
		 * @return integer
		 */
		public function getDate()
		{
			return $this->_date;
		}
		
		/**
		 * Get any other data for this comment, will need parsing
		 * @return string
		 */
		public function getMiscData()
		{
			return $this->_data;
		}
		
		/**
		 * Get an array of TBGVCSIntegrationFiles affected by this commit
		 * @return array
		 */
		public function getFiles()
		{
			$this->_populateAffectedFiles();
			return $this->_files;
		}
		
		/**
		 * Get an array of TBGIssues affected by this commit
		 * @return string
		 */
		public function getIssues()
		{
			$this->_populateAffectedIssues();
			return $this->_issues;
		}
		
		private function _populateAffectedFiles()
		{
			if ($this->_files == null)
			{
				$this->_files = array();
				
				foreach (TBGVCSIntegrationFilesTable::getByCommitID($this->id) as $row)
				{
					$this->_files[] = TBGContext::getFactory()->TBGVCSIntegrationFile($row->get(TBGVCSIntegrationFilesTable::ID), $row);
				}
			}
		}
		
		private function _populateAffectedIssues()
		{
			if ($this->_issues == null)
			{
				$this->_issues = array();
		
				foreach (TBGVCSIntegrationIssueLinksTable::getByCommitID($this->id) as $row)
				{
					$this->_issues[] = TBGContext::getFactory()->TBGIssue($row->get(TBGVCSIntegrationIssueLinksTable::ISSUE_ID));
				}
			}
		}
	}
