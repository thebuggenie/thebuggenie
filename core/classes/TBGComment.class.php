<?php

	/**
	 * Class used for comments
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	class TBGComment extends TBGIdentifiableClass implements TBGIdentifiable 
	{
		
		/**
		 * Issue comment
		 */
		const TYPE_ISSUE = 1;
		
		protected $_content;
		
		protected $_postedby;
		
		protected $_updatedby;
		
		protected $_posted;

		protected $_updated;
		
		protected $_target_id;
		
		protected $_target_type;
		
		protected $_is_public;
		
		protected $_module;
		
		protected $_systemcomment = false;


		/**
		 * Class constructor
		 *
		 * @param integer $c_id
		 * @param B2DBRow $res
		 */
		public function __construct($c_id, $row = null)
		{
			if ($row == null)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tComments::SCOPE, TBGContext::getScope()->getID());
				$row = B2DB::getTable('B2tComments')->doSelectById($c_id, $crit);
			}
			$this->_itemid = $row->get(B2tComments::ID);
			$this->_name = $row->get(B2tComments::TITLE);
			$this->_content = $row->get(B2tComments::CONTENT);
			$this->_posted = $row->get(B2tComments::POSTED);
			$this->_updated = $row->get(B2tComments::UPDATED);
			$this->_postedby = $row->get(B2tComments::POSTED_BY);
			$this->_updatedby = $row->get(B2tComments::UPDATED_BY);
			$this->_target_id = $row->get(B2tComments::TARGET_ID);
			$this->_target_type = $row->get(B2tComments::TARGET_TYPE);
			$this->_module = $row->get(B2tComments::MODULE);
			$this->_systemcomment = ($row->get(B2tComments::SYSTEM_COMMENT) == 1) ? true : false;
			$this->_is_public = ($row->get(B2tComments::IS_PUBLIC) == 1) ? true : false;
		}
		
		/**
		 *
		 * Returns all comments for a given item
		 *
		 */
		static function getComments($target_id, $target_type, $module = 'core', $from_when = null)
		{
			$retval = array();
			if ($res = B2DB::getTable('B2tComments')->getComments($target_id, $target_type))
			{
				while ($row = $res->getNextRow())
				{
					$comment = TBGFactory::TBGCommentLab($row->get(B2tComments::ID), $row);
					$retval[$comment->getID()] = $comment;
				}
			}
			return $retval;
		}
		
		static function countComments($target_id, $target_type, $module = 'core')
		{
			return 0;
		}
		
		
		static function createNew($title, $content, $uid, $target_id, $target_type, $module, $is_public, $system_comment = 0, $invoke_trigger = true)
		{
			$commentTitle = trim($title);
			$commentContent = trim($content);
			$comment = null;
			if ($commentContent != '' && $commentTitle == '') $commentTitle = __('Untitled comment');
			if ($commentTitle != '' && $commentContent != '')
			{
				$now = $_SERVER["REQUEST_TIME"];
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tComments::TARGET_ID, $target_id);
				$crit->addInsert(B2tComments::TARGET_TYPE, $target_type);
				$crit->addInsert(B2tComments::TITLE, $commentTitle);
				$crit->addInsert(B2tComments::CONTENT, $commentContent);
				$crit->addInsert(B2tComments::POSTED_BY, $uid);
				$crit->addInsert(B2tComments::POSTED, $now);
				$crit->addInsert(B2tComments::UPDATED, $now);
				$crit->addInsert(B2tComments::IS_PUBLIC, (int) $is_public);
				$crit->addInsert(B2tComments::SYSTEM_COMMENT, $system_comment);
				$crit->addInsert(B2tComments::MODULE, $module);
				$crit->addInsert(B2tComments::SCOPE, TBGContext::getScope()->getID());
				$res = B2DB::getTable('B2tComments')->doInsert($crit);
				$comment = new TBGComment($res->getInsertID());
				/*if ($target_type == 1 && $module == 'core')
				{
					try
					{
						TBGFactory::TBGIssueLab((int) $target_id)->updateTime();
					}
					catch (Exception $e) {}
				}*/
				if ($invoke_trigger)
				{
					try
					{
						TBGContext::trigger('core', 'TBGComment::createNew', $comment);
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
			$now = $_SERVER["REQUEST_TIME"];
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tComments::TITLE, $commentTitle);
			$crit->addUpdate(B2tComments::CONTENT, $commentContent);
			$crit->addUpdate(B2tComments::UPDATED, $now);
			$crit->addUpdate(B2tComments::UPDATED_BY, $uid);
			B2DB::getTable('B2tComments')->doUpdateById($crit, $c_id);
			if ($module == 'core' && $target_type == 1)
			{
				TBGFactory::TBGIssueLab($target_id)->updateComment($title, $content, $uid);
			}
		}
		
		public function setTitle($var)
		{
			$this->_name = $var;
			$this->_updated = $_SERVER["REQUEST_TIME"];
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tComments::TITLE, $var);
			$crit->addUpdate(B2tComments::UPDATED, $_SERVER["REQUEST_TIME"]);
			B2DB::getTable('B2tComments')->doUpdateById($crit, $this->getID());
		}

		public function setPublic($var)
		{
			$this->_updated = $_SERVER["REQUEST_TIME"];
			$this->_is_public = ($var == 1) ? true : false;
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tComments::IS_PUBLIC, (int) $var);
			$crit->addUpdate(B2tComments::UPDATED, $_SERVER["REQUEST_TIME"]);
			B2DB::getTable('B2tComments')->doUpdateById($crit, $this->getID());
		}
		
		public function setContent($var)
		{
			$this->_content = $var;
			$this->_updated = $_SERVER["REQUEST_TIME"];
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tComments::CONTENT, $var);
			$crit->addUpdate(B2tComments::UPDATED, $_SERVER["REQUEST_TIME"]);
			B2DB::getTable('B2tComments')->doUpdateById($crit, $this->getID());
		}

		public function setIsPublic($var)
		{
			$this->_is_public = $var;
			$this->_updated = $_SERVER["REQUEST_TIME"];
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tComments::IS_PUBLIC, $var);
			$crit->addUpdate(B2tComments::UPDATED, $_SERVER["REQUEST_TIME"]);
			B2DB::getTable('B2tComments')->doUpdateById($crit, $this->getID());
		}

		public function setUpdatedBy($var)
		{
			$this->_updated = $_SERVER["REQUEST_TIME"];
			$this->_updatedby = $var;
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tComments::UPDATED_BY, $var);
			$crit->addUpdate(B2tComments::UPDATED, $_SERVER["REQUEST_TIME"]);
			B2DB::getTable('B2tComments')->doUpdateById($crit, $this->getID());
		}
		
		static function deleteComment($c_id)
		{
			$now = $_SERVER["REQUEST_TIME"];
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tComments::DELETED, 1);
			$crit->addUpdate(B2tComments::UPDATED, $now);
			$crit->addUpdate(B2tComments::UPDATED_BY, TBGContext::getUser()->getUID());
			B2DB::getTable('B2tComments')->doUpdateById($crit, $c_id);
		}
		
		static function getCommentAccess($target_id, $type = 'view', $cid = 0, $target_type = 1, $module = 'core')
		{
			if (is_numeric($cid) && $cid != 0)
			{
				$comment = TBGFactory::TBGCommentLab($cid);
			}
			switch ($module)
			{
				case 'core':
					switch ($target_type)
					{
						case 1:
							$theIssue = TBGFactory::TBGIssueLab($target_id);
							$ia = $theIssue->hasAccess();
							if ($ia)
							{
								switch ($type)
								{
									case 'view':
										if (TBGContext::getUser()->hasPermission("b2hidecomments", $target_type . ':' . $theIssue->getID(), "core") == false)
										{
											if (TBGContext::getUser()->hasPermission("b2canviewcomments", $target_type . ':' . $theIssue->getProject()->getID(), "core"))
											{
												return true;
											}
										}
										return false;
										break;
									case 'add':
										if (TBGContext::getUser()->hasPermission("canpostcomments") || TBGContext::getUser()->hasPermission("canpostandeditcomments") || TBGContext::getUser()->hasPermission("canpostseeandeditallcomments"))
										{
											$canAddComments = true;
										}
										else
										{
											$canAddComments = false;
										}
										return $canAddComments;
										break;
									case 'edit':
										if (TBGContext::getUser()->hasPermission("caneditcomments") || TBGContext::getUser()->hasPermission("canpostandeditcomments") || TBGContext::getUser()->hasPermission("canpostseeandeditallcomments"))
										{
											$canEditComments = false;
											if ($comment->getPostedBy()->getID() !== TBGContext::getUser()->getID())
											{
												if(TBGContext::getUser()->hasPermission("caneditcomments") || TBGContext::getUser()->hasPermission("canpostseeandeditallcomments"))
												{
													$canEditComments = true;
												}
											}
											else
											{
												if (TBGContext::getUser()->hasPermission("caneditcomments") || TBGContext::getUser()->hasPermission("canpostandeditcomments"))
												{
													$canEditComments = true;
												}
											}
											
											if(TBGContext::getUser()->hasPermission("caneditcomments"))
											{
												$canEditComments = true;
											}
										}
										else
										{
											$canEditComments = false;
										}
										return $canEditComments;
										break;
									case 'delete':
										if (TBGContext::getUser()->hasPermission("candeletecomments") || TBGContext::getUser()->hasPermission("canpostandeditcomments") || TBGContext::getUser()->hasPermission("canpostseeandeditallcomments"))
										{
											$canDeleteComments = false;
											if ($comment->getPostedBy()->getID() !== TBGContext::getUser()->getID())
											{
												if(TBGContext::getUser()->hasPermission("candeletecomments") || TBGContext::getUser()->hasPermission("canpostseeandeditallcomments"))
												{
													$canDeleteComments = true;
												}
											}
											else
											{
												if (TBGContext::getUser()->hasPermission("candeletecomments") || TBGContext::getUser()->hasPermission("canpostandeditcomments"))
												{
													$canDeleteComments = true;
												}
											}
											
											if(TBGContext::getUser()->hasPermission("candeletecomments"))
											{
												$canDeleteComments = true;
											}
										}
										else
										{
											$canDeleteComments = false;
										}
										return $canDeleteComments;
										break;
								}
							}
							return false;
						break;
					}
					break;
				default:
					throw new Exception('eeek!');
					return TBGContext::getModule($module)->getCommentAccess($target_type, $target_id, $type);
			}
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
			return $this->_itemid;
		}
		
		/**
		 * Returns the user who last updated the comment
		 *
		 * @return TBGUser
		 */
		public function getUpdatedBy()
		{
			return TBGFactory::userLab($this->_updatedby);
		}
		
		/**
		 * Returns the user who posted the comment
		 *
		 * @return TBGUser
		 */
		public function getPostedBy()
		{
			return TBGFactory::userLab($this->_postedby);
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
			return $this->_systemcomment;
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
		
	}
