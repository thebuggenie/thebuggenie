<?php

	/**
	 * Task class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Task class
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGTask extends TBGIdentifiableClass implements TBGAssignable 
	{
		
		protected $_assignedto = 0;
		
		protected $_assignedtype = 0;
		
		protected $_status = 0;
		
		protected $_completed = false;
		
		protected $_updated = 0;
		
		protected $_posted = 0;
		
		protected $_content = 0;
		
		protected $_issue = 0;
		
		/**
		 * Constructor function
		 *
		 * @param integer $t_id
		 */
		public function __construct($t_id, $row = null)
		{
			$this->_itemid = $t_id;

			if ($row === null)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGIssueTasksTable::SCOPE, TBGContext::getScope()->getID());
				$row = B2DB::getTable('TBGIssueTasksTable')->doSelectById($t_id, $crit);
			}
			
			if (!$row instanceof B2DBRow)
			{
				throw new Exception('The specified task does not exist');
			}
			
			$this->_name = $row->get(TBGIssueTasksTable::TITLE);
			$this->_assignedto = $row->get(TBGIssueTasksTable::ASSIGNED_TO);
			$this->_assignedtype = $row->get(TBGIssueTasksTable::ASSIGNED_TYPE);
			$this->_status = TBGContext::factory()->datatype($row->get(TBGIssueTasksTable::STATUS), TBGDatatype::STATUS);
			$this->_completed = ($row->get(TBGIssueTasksTable::COMPLETED) == 1) ? true : false;
			$this->_posted = $row->get(TBGIssueTasksTable::POSTED);
			$this->_updated = $row->get(TBGIssueTasksTable::UPDATED);
			$this->_content = $row->get(TBGIssueTasksTable::CONTENT);
			$this->_issue = $row->get(TBGIssueTasksTable::ISSUE);
		}
		
		public function __toString()
		{
			return $this->_name;
		}
		
		/**
		 * Create a new task and return it
		 * 
		 * @param string $title The task title
		 * @param string $content The task content
		 * @param integer $issue_id The issue the task is related to
		 * 
		 * @return TBGTask
		 */
		static function createNew($title, $content, $issue_id)
		{
			$task_id = B2DB::getTable('TBGIssueTasksTable')->createNew($title, $content, $issue_id);
			return TBGContext::factory()->task($task_id);
		}
		
		public function delete()
		{
			$tasktitle = B2DB::getTable('TBGIssueTasksTable')->doSelectById($t_id)->get(TBGIssueTasksTable::TITLE);
			unset($this->_tasks[$t_id]);
	
			TBGTask::deleteTask($t_id);
			$this->updateTime();
	
			$this->addLogEntry(LOG_ENTRY_TASK_DELETE, "Delete task '$tasktitle'");
			$this->addSystemComment("Task deleted", "[s]The task '$tasktitle' has been deleted[/s].", TBGContext::getUser()->getUID());
		}
	
		public function getName()
		{
			return $this->_name;
		}
		
		public function getID()
		{
			return $this->_itemid;
		}
		
		public function isAssigned()
		{
			return ($this->_assignedto == 0) ? false : true;
		}
		
		/**
		 * Returns the assigned type
		 *
		 * @return integer
		 */
		public function getAssignedType()
		{
			return $this->_assignedtype;
		}
		
		/**
		 * Returns the assignee
		 *
		 * @return TBGIdentifiable
		 */
		public function getAssignee()
		{
			if ($this->_assignedtype == TBGIdentifiableClass::TYPE_USER)
			{
				return TBGContext::factory()->TBGUser($this->_assignedto);
			}
			else
			{
				return TBGContext::factory()->TBGTeam($this->_assignedto);
			}
		}
		
		public function getTitle()
		{
			return $this->_name;
		}
		
		public function getContent()
		{
			return $this->_content;
		}
		
		public function isCompleted()
		{
			return $this->_completed;
		}
		
		public function getUpdated()
		{
			return $this->_updated;
		}
		
		public function getPosted()
		{
			return $this->_posted;
		}
		
		/**
		 * Returns the status for this task
		 *
		 * @return TBGDatatype
		 */
		public function getStatus()
		{
			return $this->_status;
		}
		
		public function setAssignee($a_id, $a_type)
		{
			TBGContext::factory()->TBGIssue($this->_issue)->preserve_relatedUIDs();
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGIssueTasksTable::ASSIGNED_TO, $a_id);
			$crit->addUpdate(TBGIssueTasksTable::ASSIGNED_TYPE, $a_type);
			B2DB::getTable('TBGIssueTasksTable')->doUpdateById($crit, $this->_itemid);
			$this->updateTime();
			TBGContext::factory()->TBGIssue($this->_issue)->updateTime();
			$this->_assignedtype = $a_type;
			$this->_assignedto = $a_id;
	
			$the_title = $this->_name;
	
			if ($a_type == 1)
			{
				$newassignee = TBGContext::factory()->TBGUser($a_id);
			}
			else
			{
				$newassignee = TBGContext::factory()->TBGTeam($a_id);
			}
	
			TBGContext::factory()->TBGIssue($this->_issue)->addLogEntry(LOG_ENTRY_TASK_ASSIGN_TEAM, '\'$the_title\' assigned to ' . $newassignee->getName());
			TBGContext::factory()->TBGIssue($this->_issue)->preserve_relatedUIDs();
			TBGContext::factory()->TBGIssue($this->_issue)->addSystemComment("Task updated", "Task '$the_title' has been assigned to [b]".$newassignee->getName()."[/b].", TBGContext::getUser()->getUID(), 1);
		}
	
		public function setStatus($sid)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGIssueTasksTable::STATUS, $sid);
			$res = B2DB::getTable('TBGIssueTasksTable')->doUpdateById($crit, $this->_itemid);
			$this->_status = TBGContext::factory()->datatype($sid, TBGDatatype::STATUS);
			TBGContext::factory()->TBGIssue($this->_issue)->updateTime();
	
			$the_title = $this->_name;
			$status_name = $this->_status->getName();
	
			TBGContext::factory()->TBGIssue($this->_issue)->addLogEntry(LOG_ENTRY_TASK_STATUS, "Status for '$the_title' is now '$status_name'");
			TBGContext::factory()->TBGIssue($this->_issue)->addSystemComment("Task updated", "Task '$the_title' has been updated. The new status is '$status_name'.", TBGContext::getUser()->getUID(), 1);
		}
	
		public function setCompleted($completed)
		{
			$the_title = $this->_name;
			$this->_completed = ($completed == 1) ? true : false;
			
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGIssueTasksTable::COMPLETED, $completed);
			B2DB::getTable('TBGIssueTasksTable')->doUpdateById($crit, $this->_itemid);
			$this->updateTime();
	
			if ($completed == 1)
			{
				TBGContext::factory()->TBGIssue($this->_issue)->addLogEntry(LOG_ENTRY_TASK_COMPLETED, "'$the_title' completed");
				TBGContext::factory()->TBGIssue($this->_issue)->addSystemComment("Task updated", "Task '$the_title' has been marked as completed.", TBGContext::getUser()->getUID(), 1);
			}
			else
			{
				TBGContext::factory()->TBGIssue($this->_issue)->addLogEntry(LOG_ENTRY_TASK_REOPENED, "'$the_title' reopened");
				TBGContext::factory()->TBGIssue($this->_issue)->addSystemComment("Task updated", "Task '$the_title' has been reopened.", TBGContext::getUser()->getUID(), 1);
			}
			TBGContext::factory()->TBGIssue($this->_issue)->updateTime();
		}

		public function updateDetails($newTitle, $newContent)
		{
			$this->_name = $newTitle;
			$this->_content = $newContent;
	
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGIssueTasksTable::TITLE, $newTitle);
			$crit->addUpdate(TBGIssueTasksTable::CONTENT, $newContent);
			B2DB::getTable('TBGIssueTasksTable')->doUpdateById($crit, $this->_itemid);
			$this->updateTime();
			TBGContext::factory()->TBGIssue($this->_issue)->updateTime();
	
			TBGContext::factory()->TBGIssue($this->_issue)->addLogEntry(LOG_ENTRY_TASK_UPDATE, "Task details updated");
			TBGContext::factory()->TBGIssue($this->_issue)->addSystemComment("Task updated", "Task title and details has been updated.", TBGContext::getUser()->getUID(), 1);
		}

		public function updateTime()
		{
			$theTime = NOW;
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGIssueTasksTable::UPDATED, $theTime);
			$this->_updated = $theTime;
			B2DB::getTable('TBGIssueTasksTable')->doUpdateById($crit, $this->_itemid);
		}
		
	}
