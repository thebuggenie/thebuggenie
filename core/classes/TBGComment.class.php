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
	 *
	 * @Table(name="TBGCommentsTable")
	 */
	class TBGComment extends TBGIdentifiableScopedClass
	{
		
		/**
		 * Issue comment
		 */
		const TYPE_ISSUE = 1;
		
		/**
		 * Article comment
		 */
		const TYPE_ARTICLE = 2;

		/**
		 * @Column(type="text")
		 */
		protected $_content;
		
		/**
		 * Who posted the comment
		 * 
		 * @var TBGUser
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGUser")
		 */
		protected $_posted_by;
		
		/**
		 * Who last updated the comment
		 * 
		 * @var TBGUser
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGUser")
		 */
		protected $_updated_by;

		/**
		 * @Column(type="integer", length=10)
		 */
		protected $_posted;

		/**
		 * @Column(type="integer", length=10)
		 */
		protected $_updated;
		
		/**
		 * @Column(type="integer", length=10)
		 */
		protected $_target_id;
		
		/**
		 * @Column(type="integer", length=5)
		 */
		protected $_target_type = self::TYPE_ISSUE;
		
		/**
		 * @Column(type="boolean")
		 */
		protected $_is_public = true;
		
		/**
		 * @Column(type="string", length=100)
		 */
		protected $_module = 'core';
		
		/**
		 * @Column(type="boolean")
		 */
		protected $_deleted = false;
		
		/**
		 * @Column(type="boolean")
		 */
		protected $_system_comment = false;

		/**
		 * @Column(type="integer", length=10)
		 */
		protected $_comment_number = 0;

		/**
		 * @Column(type="integer", length=10)
		 * @Relates(class="TBGComment")
		 */
		protected $_reply_to_comment = 0;

		protected static $_comment_count = array();

		/**
		 *
		 * Returns all comments for a given item
		 *
		 */
		static function getComments($target_id, $target_type, $sort_order = \b2db\Criteria::SORT_ASC)
		{
			$comments = TBGCommentsTable::getTable()->getComments($target_id, $target_type, $sort_order);
			self::$_comment_count[$target_type][$target_id] = count($comments);

			return $comments;
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
		protected function _permissionCheckWithID($key)
		{
			$retval = TBGContext::getUser()->hasPermission($key, $this->getID(), 'core', true, null);
			$retval = ($retval !== null) ? $retval : TBGContext::getUser()->hasPermission($key, 0, 'core', true, null);

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
			$retval = ($this->getPostedByID() == TBGContext::getUser()->getID() && !$exclusive) ? $this->_permissionCheckWithID($key.'own') : null;
			$retval = ($retval !== null) ? $retval : $this->_permissionCheckWithID($key);
			return ($retval !== null) ? $retval : null;
		}

		protected function _preSave($is_new)
		{
			parent::_preSave($is_new);
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
		
		protected function _canPermissionOrSeeAndEditAllComments($permission)
		{
			$retval = $this->_permissionCheck($permission);
			$retval = ($retval === null) ? $this->_permissionCheck('canpostseeandeditallcomments', true) : $retval;

			return $retval;
		}

		protected function _canPermissionOrSeeAndEditComments($permission)
		{
			$retval = $this->_permissionCheck($permission);
			$retval = ($retval === null) ? $this->_permissionCheck('canpostandeditcomments', true) : $retval;

			return $retval;
		}

		/**
		 * Return if the user can edit this comment
		 *
		 * @return boolean
		 */
		public function canUserEditComment()
		{
			if ($this->isSystemComment()) return false;
			$retval = $this->_canPermissionOrSeeAndEditAllComments('caneditcomments');

			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}

		/**
		 * Return if the user can edit own comment
		 *
		 * @return boolean
		 */
		public function canUserEditOwnComment()
		{
			$retval = $this->_canPermissionOrSeeAndEditComments('caneditcommentsown');

			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}

		/**
		 * Return if the user can delete this comment
		 *
		 * @return boolean
		 */
		public function canUserDeleteComment()
		{
			if ($this->isSystemComment()) return false;
			$retval = $this->_canPermissionOrSeeAndEditAllComments('candeletecomments');

			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}

		/**
		 * Return if the user can delete own comment
		 *
		 * @return boolean
		 */
		public function canUserDeleteOwnComment()
		{
			$retval = $this->_canPermissionOrSeeAndEditComments('candeletecommentsown');

			return ($retval !== null) ? $retval : TBGSettings::isPermissive();
		}

		/**
		 * Return if the user can delete comment
		 *
		 * @param TBGUser $user A User
		 *
		 * @return boolean
		 */
		public function canUserDelete(TBGUser $user)
		{
			$can_delete = false;
			
			try
			{
				// Delete comment if valid user and... 
				if ($user instanceof TBGUser)
				{
					if (($this->postedByUser($user->getID()) && $this->canUserDeleteOwnComment()) // the user posted the comment AND the user can delete own comments
						|| $this->canUserDeleteComment()) // OR the user can delete all comments
					{
						$can_delete = true;
					}//endif
				}//endif
			}//endtry
			catch (Exception $e){ }
			return $can_delete;
		}

		/**
		 * Return if the user can edit comment
		 *
		 * @param TBGUser $user A User
		 *
		 * @return boolean
		 */
		public function canUserEdit(TBGUser $user)
		{
			$can_edit = false;
			
			try
			{
				// Edit comment if valid user and... 
				if ($user instanceof TBGUser)
				{
					if (($this->postedByUser($user->getID()) && $this->canUserEditOwnComment()) // the user posted the comment AND the user can edit own comments
						|| $this->canUserEditComment()) // OR the user can edit all comments
					{
						$can_edit = true;
					}//endif
				}//endif
			}//endtry
			catch (Exception $e){ }
			return $can_edit;
		}
		
		/**
		 * Return if the specified user can view this comment
		 *
		 * @param TBGUser $user A User
		 *
		 * @return boolean
		 */
		public function isViewableByUser(TBGUser $user)
		{
			$can_view = false;
			
			try
			{
				// Show comment if valid user and... 
				if ($user instanceof TBGUser)
				{
					
					if ((!$this->isPublic() && $user->canSeeNonPublicComments()) // the comment is hidden and the user can see hidden comments
						|| ($this->isPublic() && $user->canViewComments()) // OR the comment is public and  user can see public comments
						|| ($this->postedByUser($user->getID()))) // OR the user posted the comment
					{
						$can_view = true;
					}//endif
					
				}//endif
			}//endtry
			catch (Exception $e){ }
			return $can_view;
		}

		public function __toString()
		{
			return $this->_name;
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

		/**
		 * Return the whether or not the user owns this comment
		 *
		 * @param int $user_id A user's ID
		 *
		 * @return bool
		 */
		public function postedByUser($user_id)
		{
			$posted_by_id = null;
			
			try
			{
				$posted_by_id = $this->getPostedByID();
				
				if (!empty($posted_by_id) && !empty($user_id))
				{
					if ($posted_by_id == $user_id)
					{
						return true;
					}//endif
				}//endif
				else
				{
					return false;
				}//endelse
			}//endtry
			catch (Exception $e) { }
			return false;
		}//end postedByUser

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

		public function setReplyToComment($reply_to_comment_id)
		{
			$this->_reply_to_comment = $reply_to_comment_id;
		}

		public function getReplyToComment()
		{
			if (!is_object($this->_reply_to_comment) && $this->_reply_to_comment)
			{
				$this->_b2dbLazyload('_reply_to_comment');
			}
			return $this->_reply_to_comment;
		}

		public function isReply()
		{
			return (bool) ($this->getReplyToComment() instanceof TBGComment);
		}

	}
