<?php
	/**
	 * Issue to Commit link class, vcs_integration
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.2
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * Issue to Commit link class, vcs_integration
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */
	class TBGVCSIntegrationIssueLink extends TBGIdentifiableClass
	{
		protected static $_b2dbtablename = 'TBGVCSIntegrationIssueLinksTable';
		
		/**
		 * Affected issue
		 * @var TBGIssue
		 * @Class TBGIssue
		 */
		protected $_issue_no = null;
		
		/**
		 * Associated commit
		 * @var TBGVCSIntegrationCommit
		 * @Class TBGVCSIntegrationCommit
		 */
		protected $_commit_id = null;
		
		/**
		 * Get the issue for this link
		 * @return TBGIssue
		 */
		public function getIssue()
		{
			return $this->_issue_no;
		}
		
		/**
		 * Get the commit with this link
		 * @return TBGVCSIntegrationCommit
		 */
		public function getCommit()
		{
			return $this->_commit_id;
		}
		
		/**
		 * Set the issue in this link
		 * @param TBGIssue $issue
		 */
		public function setIssue(TBGIssue $issue)
		{
			$this->_issue_no = $issue;
		}
		
		/**
		 * Set the commit in this link
		 * @param TBGVCSIntegrationCommit $commit
		 */
		public function setCommit(TBGVCSIntegrationCommit $commit)
		{
			$this->_commit_id = $commit;
		}
	}
