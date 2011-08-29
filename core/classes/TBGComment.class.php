<?php

	/**
	 * Class used for comments
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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
		
		static protected $_b2dbtablename = 'TBGCommentsTable';
		
		/**
		 * Issue comment
		 */
		const TYPE_ISSUE = 1;
		
		/**
		 * Article comment
		 */
		const TYPE_ARTICLE = 2;

		protected $_content;
		
		/**
		 * Who posted the comment
		 * 
		 * @var TBGUser
		 * @Class TBGUser
		 */
		protected $_posted_by;
		
		/**
		 * Who last updated the comment
		 * 
		 * @var TBGUser
		 * @Class TBGUser
		 */
		protected $_updated_by;
		
		protected $_posted;

		protected $_updated;
		
		protected $_target_id;
		
		protected $_target_type = self::TYPE_ISSUE;
		
		protected $_is_public = true;
		
		protected $_module = 'core';
		
		protected $_deleted = false;
		
		protected $_system_comment = false;

		protected $_comment_number = 0;

		protected static $_comment_count = array();

		/**
		 *
		 * Returns all comments for a given item
		 *
		 */
		static function getComments($target_id, $target_type, $sort_order = \b2db\Criteria::SORT_ASC)
		{
			$retval = array();
			if ($res = TBGCommentsTable::getTable()->getComments($target_id, $target_type, $sort_order))
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
		
		static function getRecentCommentsByAuthor($user_id, $target_type = self::TYPE_ISSUE, $limit = 10)
		{
			$retval = array();
			if ($res = TBGCommentsTable::getTable()->getRecentCommentsByUserIDandTargetType($user_id, $target_type, $limit))
			{
				while ($row = $res->getNextRow())
				{
					$comment = TBGContext::factory()->TBGComment($row->get(TBGCommentsTable::ID), $row);
					$retval[$comment->getID()] = $comment;
				}
			}
			return $retval;
		}
		
		static function countComments($target_id, $target_type, $include_system_comments = true)
		{
			if (!array_key_exists($target_type, self::$_comment_count))
				self::$_comment_count[$target_type] = array();

			if (!array_key_exists($target_id, self::$_comment_count[$target_type]))
				self::$_comment_count[$target_type][$target_id] = array();

			if (!array_key_exists((int) $include_system_comments, self::$_comment_count[$target_type][$target_id]))
				self::$_comment_count[$target_type][$target_id][(int) $include_system_comments] = (int) TBGCommentsTable::getTable()->countComments($target_id, $target_type, $include_system_comments);

			return (int) self::$_comment_count[$target_type][$target_id][(int) $include_system_comments];
		}
		
		public function setTitle($var)
		{
			$this->_name = $var;
		}

		public function setPublic($var)
		{
			$this->_is_public = (bool) $var;
		}
		
		public function setContent($var)
		{
			$this->_content = $var;
		}

		public function setIsPublic($var)
		{
			$this->_is_public = (bool) $var;
		}

		public function setUpdatedBy($var)
		{
			$this->_updated = NOW;
			$this->_updated_by = $var;
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

		protected function _preSave($is_new)
		{
			if ($is_new)
			{
				if (!$this->_posted)
				{
					$this->_posted = NOW;
				}
				if (!$this->_comment_number)
				{
					$this->_comment_number = TBGCommentsTable::getTable()->getNextCommentNumber($this->_target_id, $this->_target_type);
				}
			}
		}

		protected function _postSave($is_new)
		{
			if ($is_new)
			{
				$tty = $this->getTargetType();
				$tid = $this->getTargetID();
				if (array_key_exists($tty, self::$_comment_count) && array_key_exists($tid, self::$_comment_count[$tty]) && array_key_exists((int) $this->isSystemComment(), self::$_comment_count[$tty][$tid]))
					self::$_comment_count[$tty][$tid][(int) $this->isSystemComment()]++;
			}
		}
		
		/**
		 * Return if the user can edit this comment
		 *
		 * @return boolean
		 */
		public function canUserEditComment()
		{
			if ($this->isSystemComment()) return false;
			return (bool) ($this->getPostedByID() == TBGContext::getUser()->getID() || $this->_permissionCheck('caneditcomments') || $this->_permissionCheck('canpostseeandeditallcomments', true));
		}

		/**
		 * Return if the user can delete this comment
		 *
		 * @return boolean
		 */
		public function canUserDeleteComment()
		{
			return (bool) ($this->getPostedByID() == TBGContext::getUser()->getID() || $this->_permissionCheck('candeletecomments') || $this->_permissionCheck('canpostseeandeditallcomments', true));
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
			return ($this->_updated_by instanceof TBGUser) ? $this->_updated_by : TBGContext::factory()->TBGUser($this->_updated_by);
		}
		
		/**
		 * Returns the user who posted the comment
		 *
		 * @return TBGUser
		 */
		public function getPostedBy()
		{
			try
			{
				return ($this->_posted_by instanceof TBGUser) ? $this->_posted_by : TBGContext::factory()->TBGUser($this->_posted_by);
			}
			catch (Exception $e)
			{
				return null;
			}
		}

		/**
		 * Return the poster id
		 *
		 * @return integer
		 */
		public function getPostedByID()
		{
			$poster = null;
			try
			{
				$poster = $this->getPostedBy();
			}
			catch (Exception $e) {}
			return ($poster instanceof TBGIdentifiable) ? $poster->getID() : null;
		}

		public function setPostedBy($var)
		{
			if (is_object($var))
			{
				$var = $var->getID();
			}
			$this->_posted_by = $var;
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

		public function setTargetID($var)
		{
			$this->_target_id = $var;
		}
		
		public function getTargetType()
		{
			return $this->_target_type;
		}
		
		public function setTargetType($var)
		{
			$this->_target_type = $var;
		}
		
		public function setSystemComment($val = true)
		{
			$this->_system_comment = $val;
		}

		public function getModuleName()
		{
			return $this->_module;
		}

		public function setModuleName($var)
		{
			$this->_module = $var;
		}

		public function getCommentNumber()
		{
			return (int) $this->_comment_number;
		}

		public function toJSON()
		{
			$return_values = array(
				'id' => $this->getID(),
				'created_at' => $this->getPosted(),
				'comment_number' => $this->getCommentNumber(),
				'posted_by' => ($this->getPostedBy() instanceof TBGIdentifiable) ? $this->getPostedBy()->toJSON() : null,
				'content' => $this->getContent(),
				'system_comment' => $this->isSystemComment(),
			);

			return $return_values;
		}

	}
