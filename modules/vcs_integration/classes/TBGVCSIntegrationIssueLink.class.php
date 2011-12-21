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
	 *
	 * @Table(name="TBGVCSIntegrationIssueLinksTable")
	 */
	class TBGVCSIntegrationIssueLink extends TBGIdentifiableScopedClass
	{
		
		/**
		 * Affected issue
		 * @var TBGIssue
		 * @Column(type="integer", name="issue_no")
		 * @Relates(class="TBGIssue")
		 */
		protected $_issue = null;
		
		/**
		 * Associated commit
		 * @var TBGVCSIntegrationCommit
		 * @Column(type="integer", name="commit_id")
		 * @Relates(class="TBGVCSIntegrationCommit")
		 */
		protected $_commit = null;
		
		/**
		 * Get the issue for this link
		 * @return TBGIssue
		 */
		public function getIssue()
		{
			return $this->_b2dbLazyload('_issue');
		}
		
		/**
		 * Get the commit with this link
		 * @return TBGVCSIntegrationCommit
		 */
		public function getCommit()
		{
			return $this->_b2dbLazyload('_commit');
		}
		
		/**
		 * Set the issue in this link
		 * @param TBGIssue $issue
		 */
		public function setIssue(TBGIssue $issue)
		{
			$this->_issue = $issue;
		}
		
		/**
		 * Set the commit in this link
		 * @param TBGVCSIntegrationCommit $commit
		 */
		public function setCommit(TBGVCSIntegrationCommit $commit)
		{
			$this->_commit = $commit;
		}
		
		/**
		 * Return all commits for a given issue
		 * @param TBGIssue $issue
		 * @return array
		 */
		public static function getCommitsByIssue(TBGIssue $issue)
		{
			$data = array();

			if (!is_object(TBGVCSIntegrationIssueLinksTable::getTable()->getByIssueID($issue->getID())))
			{
				return false;
			}
			
			foreach (TBGVCSIntegrationIssueLinksTable::getTable()->getByIssueID($issue->getID())->getAllRows() as $row)
			{
				$data[] = TBGContext::factory()->TBGVCSIntegrationIssueLink($row->get(TBGVCSIntegrationIssueLinksTable::ID), $row);
			}

			return $data;
		}
		
		/**
		 * Return all issues for a given commit
		 * @param TBGVCSIntegrationCommit $commit
		 * @return array
		 */
		public static function getIssuesByCommit(TBGVCSIntegrationCommit $commit)
		{
			$rows = TBGVCSIntegrationIssueLinksTable::getTable()->getByCommitID($commit->getID());
			$data = array();
			
			if (!is_object($rows))
			{
				return false;
			}
			
			foreach ($rows->getAllRows() as $row)
			{
				$data[] = TBGContext::factory()->TBGVCSIntegrationIssueLink($row->get(TBGVCSIntegrationIssueLinksTable::ID), $row);
			}

			return $data;
		}
	}
