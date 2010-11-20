<?php

	/**
	 * Class used for comments
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Class used for comments
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGComment extends TBGIdentifiableClass 
	{
		
		protected $_b2dbtablename = 'TBGCommentsTable';
		
		/**
		 * Issue comment
		 */
		const TYPE_ISSUE = 1;
		
		protected $_content;
		
		protected $_posted_by;
		
		protected $_updated_by;
		
		protected $_posted;

		protected $_updated;
		
		protected $_target_id;
		
		protected $_target_type;
		
		protected $_is_public;
		
		protected $_module;
		
		protected $_deleted;
		
		protected $_system_comment = false;

		protected $_comment_number = 0;

		protected static $_comment_count = array();

		/**
		 *
		 * Returns all comments for a given item
		 *
		 */
		static function getComments($target_id, $target_type, $module = 'core', $from_when = null)
		{
			$retval = array();
			if ($res = TBGCommentsTable::getTable()->getComments($target_id, $target_type))
			{
				while ($row = $res->getNextRow())
				{
					$comment = TBGContext::factory()->TBGComment($row->get(TBGCommentsTable::ID), $row);
					$retval[$comment->getID()] = $comment;
				}
				self::$_comment_count[$target_type][$target_id] = count($retval);
			}
			return $retval;
		}
		
		static function countComments($target_id, $target_type, $module = 'core')
		{
			if (!array_key_exists($target_type, self::$_comment_count))
			{
				self::$_comment_count[$target_type] = array();
			}
			if (!array_key_exists($target_id, self::$_comment_count[$target_type]))
			{
				self::$_comment_count[$target_type][$target_id] = TBGCommentsTable::getTable()->countComments($target_id, $target_type);
			}
			return self::$_comment_count[$target_type][$target_id];
		}
		
		static function createNew($title, $content, $uid, $target_id, $target_type, $module = 'core', $is_public = true, $system_comment = false, $invoke_trigger = true)
		{
			$commentTitle = trim($title);
			$commentContent = trim($content);
			$comment = null;
			if ($commentContent != '' && $commentTitle == '') $commentTitle = TBGContext::getI18n()->__('Untitled comment');
			if ($commentTitle != '' && $commentContent != '')
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(TBGCommentsTable::TARGET_ID, $target_id);
				$crit->addInsert(TBGCommentsTable::TARGET_TYPE, $target_type);
				$crit->addInsert(TBGCommentsTable::TITLE, $commentTitle);
				$crit->addInsert(TBGCommentsTable::CONTENT, $commentContent);
				$crit->addInsert(TBGCommentsTable::POSTED_BY, $uid);
				$crit->addInsert(TBGCommentsTable::POSTED, NOW);
				$crit->addInsert(TBGCommentsTable::UPDATED, NOW);
				$crit->addInsert(TBGCommentsTable::IS_PUBLIC, (int) $is_public);
				$crit->addInsert(TBGCommentsTable::SYSTEM_COMMENT, $system_comment);
				$crit->addInsert(TBGCommentsTable::MODULE, $module);
				$crit->addInsert(TBGCommentsTable::COMMENT_NUMBER, TBGCommentsTable::getTable()->getNextCommentNumber($target_id, $target_type));
				$crit->addInsert(TBGCommentsTable::SCOPE, TBGContext::getScope()->getID());
				$res = TBGCommentsTable::getTable()->doInsert($crit);
				$comment = new TBGComment($res->getInsertID());
				if (!$system_comment)
				{
					TBGLogTable::getTable()->createNew($target_id, TBGLogTable::TYPE_ISSUE, TBGLogTable::LOG_COMMENT, $comment->getID(), $uid);
				}
				/*if ($target_type == 1 && $module == 'core')
				{
					try
					{
						TBGContext::factory()->TBGIssue((int) $target_id)->updateTime();
					}
					catch (Exception $e) {}
				}*/
				if ($invoke_trigger)
				{
					try
					{
						TBGEvent::createNew('core', 'TBGComment::createNew', $comment)->trigger();
					}
					catch (Exception $e) {}
				}
			}
			return $comment;
		}
	
		static function updateComment($title, $content, $uid, $c_id, $is_public, $module, $target_type, $target_id)
		{
			$commentTitle = trim($title);
			$commentContent = trim($content);
			$now = NOW;
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCommentsTable::TITLE, $commentTitle);
			$crit->addUpdate(TBGCommentsTable::CONTENT, $commentContent);
			$crit->addUpdate(TBGCommentsTable::UPDATED, $now);
			$crit->addUpdate(TBGCommentsTable::UPDATED_BY, $uid);
			TBGCommentsTable::getTable()->doUpdateById($crit, $c_id);
			if ($module == 'core' && $target_type == 1)
			{
				TBGContext::factory()->TBGIssue($target_id)->updateComment($title, $content, $uid);
			}
		}
		
		public function setTitle($var)
		{
			$this->_name = $var;
			$this->_updated = NOW;
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCommentsTable::TITLE, $var);
			$crit->addUpdate(TBGCommentsTable::UPDATED, NOW);
			TBGCommentsTable::getTable()->doUpdateById($crit, $this->getID());
		}

		public function setPublic($var)
		{
			$this->_updated = NOW;
			$this->_is_public = ($var == 1) ? true : false;
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCommentsTable::IS_PUBLIC, (int) $var);
			$crit->addUpdate(TBGCommentsTable::UPDATED, NOW);
			TBGCommentsTable::getTable()->doUpdateById($crit, $this->getID());
		}
		
		public function setContent($var)
		{
			$this->_content = $var;
			$this->_updated = NOW;
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCommentsTable::CONTENT, $var);
			$crit->addUpdate(TBGCommentsTable::UPDATED, NOW);
			TBGCommentsTable::getTable()->doUpdateById($crit, $this->getID());
		}

		public function setIsPublic($var)
		{
			$this->_is_public = $var;
			$this->_updated = NOW;
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCommentsTable::IS_PUBLIC, $var);
			$crit->addUpdate(TBGCommentsTable::UPDATED, NOW);
			TBGCommentsTable::getTable()->doUpdateById($crit, $this->getID());
		}

		public function setUpdatedBy($var)
		{
			$this->_updated = NOW;
			$this->_updated_by = $var;
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCommentsTable::UPDATED_BY, $var);
			$crit->addUpdate(TBGCommentsTable::UPDATED, NOW);
			TBGCommentsTable::getTable()->doUpdateById($crit, $this->getID());
		}

		/**
		 * Perform a permission check based on a key, and whether or not to
		 * check if the permission is explicitly set
		 *
		 * @param string $key The permission key to check for
		 * @param boolean $exclusive Whether to make sure the permission is explicitly set
		 *
		 * @return boolean
		 */
		protected function _permissionCheckWithID($key, $explicit = false)
		{
			$retval = TBGContext::getUser()->hasPermission($key, $this->getID(), 'core', true, null);
			if ($explicit)
			{
				$retval = ($retval !== null) ? $retval : TBGContext::getUser()->hasPermission($key, 0, 'core', true, null);
			}
			else
			{
				$retval = ($retval !== null) ? $retval : TBGContext::getUser()->hasPermission($key);
			}

			return $retval;
		}

		/**
		 * Perform a permission check based on a key, and whether or not to
		 * check for the equivalent "*own" permission if the comment is posted
		 * by the same user
		 *
		 * @param string $key The permission key to check for
		 * @param boolean $exclusive Whether to perform a similar check for "own"
		 *
		 * @return boolean
		 */
		protected function _permissionCheck($key, $exclusive = false)
		{
			$retval = null;
			if ($this->getPostedByID() == TBGContext::getUser()->getID() && !$exclusive)
			{
				$retval = $this->_permissionCheckWithID($key.'own', true);
			}
			return ($retval !== null) ? $retval : $this->_permissionCheckWithID($key);
		}

		/**
		 * Return if the user can edit this comment
		 *
		 * @return boolean
		 */
		public function canUserEditComment()
		{
			return (bool) ($this->_permissionCheck('caneditcomments') || $this->_permissionCheck('canpostseeandeditallcomments', true));
		}

		/**
		 * Return if the user can delete this comment
		 *
		 * @return boolean
		 */
		public function canUserDeleteComment()
		{
			return (bool) ($this->_permissionCheck('candeletecomments') || $this->_permissionCheck('canpostseeandeditallcomments', true));
		}

		static function deleteComment($c_id)
		{
			$now = NOW;
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGCommentsTable::DELETED, 1);
			$crit->addUpdate(TBGCommentsTable::UPDATED, $now);
			$crit->addUpdate(TBGCommentsTable::UPDATED_BY, TBGContext::getUser()->getUID());
			TBGCommentsTable::getTable()->doUpdateById($crit, $c_id);
		}
		
		public function __toString()
		{
			return $this->_name;
		}
		
		public function getName()
		{
			return $this->_name;
		}
		
		public function getID()
		{
			return $this->_id;
		}
		
		/**
		 * Returns the user who last updated the comment
		 *
		 * @return TBGUser
		 */
		public function getUpdatedBy()
		{
			return TBGContext::factory()->TBGUser($this->_updated_by);
		}
		
		/**
		 * Returns the user who posted the comment
		 *
		 * @return TBGUser
		 */
		public function getPostedBy()
		{
			return TBGContext::factory()->TBGUser($this->_posted_by);
		}

		/**
		 * Return the poster id
		 *
		 * @return integer
		 */
		public function getPostedByID()
		{
			$poster = $this->getPostedBy();
			return ($poster instanceof TBGIdentifiable) ? $poster->getID() : null;
		}

		public function getTitle()
		{
			return $this->_name;
		}
		
		public function isPublic()
		{
			return $this->_is_public;
		}
		
		public function getContent()
		{
			return $this->_content;
		}
		
		public function getUpdated()
		{
			return $this->_updated;
		}
		
		public function getPosted()
		{
			return $this->_posted;
		}
		
		public function isSystemComment()
		{
			return $this->_system_comment;
		}
		
		public function getTargetID()
		{
			return $this->_target_id;
		}
		
		public function getTargetType()
		{
			return $this->_target_type;
		}
		
		public function getModuleName()
		{
			return $this->_module;
		}

		public function getCommentNumber()
		{
			return (int) $this->_comment_number;
		}
		
	}
