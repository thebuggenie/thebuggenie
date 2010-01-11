<?php

	/**
	 * Task class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
				$crit->addWhere(B2tIssueTasks::SCOPE, TBGContext::getScope()->getID());
				$row = B2DB::getTable('B2tIssueTasks')->doSelectById($t_id, $crit);
			}
			
			if (!$row instanceof B2DBRow)
			{
				throw new Exception('The specified task does not exist');
			}
			
			$this->_name = $row->get(B2tIssueTasks::TITLE);
			$this->_assignedto = $row->get(B2tIssueTasks::ASSIGNED_TO);
			$this->_assignedtype = $row->get(B2tIssueTasks::ASSIGNED_TYPE);
			$this->_status = TBGFactory::datatypeLab($row->get(B2tIssueTasks::STATUS), TBGDatatype::STATUS);
			$this->_completed = ($row->get(B2tIssueTasks::COMPLETED) == 1) ? true : false;
			$this->_posted = $row->get(B2tIssueTasks::POSTED);
			$this->_updated = $row->get(B2tIssueTasks::UPDATED);
			$this->_content = $row->get(B2tIssueTasks::CONTENT);
			$this->_issue = $row->get(B2tIssueTasks::ISSUE);
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
			$task_id = B2DB::getTable('B2tIssueTasks')->createNew($title, $content, $issue_id);
			return TBGFactory::taskLab($task_id);
		}
		
		public function delete()
		{
			$tasktitle = B2DB::getTable('B2tIssueTasks')->doSelectById($t_id)->get(B2tIssueTasks::TITLE);
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
				return TBGFactory::userLab($this->_assignedto);
			}
			else
			{
				return TBGFactory::teamLab($this->_assignedto);
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
			TBGFactory::TBGIssueLab($this->_issue)->preserve_relatedUIDs();
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tIssueTasks::ASSIGNED_TO, $a_id);
			$crit->addUpdate(B2tIssueTasks::ASSIGNED_TYPE, $a_type);
			B2DB::getTable('B2tIssueTasks')->doUpdateById($crit, $this->_itemid);
			$this->updateTime();
			TBGFactory::TBGIssueLab($this->_issue)->updateTime();
			$this->_assignedtype = $a_type;
			$this->_assignedto = $a_id;
	
			$the_title = $this->_name;
	
			if ($a_type == 1)
			{
				$newassignee = TBGFactory::userLab($a_id);
			}
			else
			{
				$newassignee = TBGFactory::teamLab($a_id);
			}
	
			TBGFactory::TBGIssueLab($this->_issue)->addLogEntry(LOG_ENTRY_TASK_ASSIGN_TEAM, '\'$the_title\' assigned to ' . $newassignee->getName());
			TBGFactory::TBGIssueLab($this->_issue)->preserve_relatedUIDs();
			TBGFactory::TBGIssueLab($this->_issue)->addSystemComment("Task updated", "Task '$the_title' has been assigned to [b]".$newassignee->getName()."[/b].", TBGContext::getUser()->getUID(), 1);
		}
	
		public function setStatus($sid)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tIssueTasks::STATUS, $sid);
			$res = B2DB::getTable('B2tIssueTasks')->doUpdateById($crit, $this->_itemid);
			$this->_status = TBGFactory::datatypeLab($sid, TBGDatatype::STATUS);
			TBGFactory::TBGIssueLab($this->_issue)->updateTime();
	
			$the_title = $this->_name;
			$status_name = $this->_status->getName();
	
			TBGFactory::TBGIssueLab($this->_issue)->addLogEntry(LOG_ENTRY_TASK_STATUS, "Status for '$the_title' is now '$status_name'");
			TBGFactory::TBGIssueLab($this->_issue)->addSystemComment("Task updated", "Task '$the_title' has been updated. The new status is '$status_name'.", TBGContext::getUser()->getUID(), 1);
		}
	
		public function setCompleted($completed)
		{
			$the_title = $this->_name;
			$this->_completed = ($completed == 1) ? true : false;
			
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tIssueTasks::COMPLETED, $completed);
			B2DB::getTable('B2tIssueTasks')->doUpdateById($crit, $this->_itemid);
			$this->updateTime();
	
			if ($completed == 1)
			{
				TBGFactory::TBGIssueLab($this->_issue)->addLogEntry(LOG_ENTRY_TASK_COMPLETED, "'$the_title' completed");
				TBGFactory::TBGIssueLab($this->_issue)->addSystemComment("Task updated", "Task '$the_title' has been marked as completed.", TBGContext::getUser()->getUID(), 1);
			}
			else
			{
				TBGFactory::TBGIssueLab($this->_issue)->addLogEntry(LOG_ENTRY_TASK_REOPENED, "'$the_title' reopened");
				TBGFactory::TBGIssueLab($this->_issue)->addSystemComment("Task updated", "Task '$the_title' has been reopened.", TBGContext::getUser()->getUID(), 1);
			}
			TBGFactory::TBGIssueLab($this->_issue)->updateTime();
		}

		public function updateDetails($newTitle, $newContent)
		{
			$this->_name = $newTitle;
			$this->_content = $newContent;
	
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tIssueTasks::TITLE, $newTitle);
			$crit->addUpdate(B2tIssueTasks::CONTENT, $newContent);
			B2DB::getTable('B2tIssueTasks')->doUpdateById($crit, $this->_itemid);
			$this->updateTime();
			TBGFactory::TBGIssueLab($this->_issue)->updateTime();
	
			TBGFactory::TBGIssueLab($this->_issue)->addLogEntry(LOG_ENTRY_TASK_UPDATE, "Task details updated");
			TBGFactory::TBGIssueLab($this->_issue)->addSystemComment("Task updated", "Task title and details has been updated.", TBGContext::getUser()->getUID(), 1);
		}

		public function updateTime()
		{
			$theTime = $_SERVER["REQUEST_TIME"];
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tIssueTasks::UPDATED, $theTime);
			$this->_updated = $theTime;
			B2DB::getTable('B2tIssueTasks')->doUpdateById($crit, $this->_itemid);
		}
		
	}
