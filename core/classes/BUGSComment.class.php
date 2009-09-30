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
	class BUGSComment extends BUGSidentifiableclass implements BUGSidentifiable 
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
				$crit->addWhere(B2tComments::SCOPE, BUGScontext::getScope()->getID());
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
		 * Returns all comments for a given item, or a specified comment ($c_id)
		 *
		 */
		static function getComments($target_id, $target_type, $module = 'core', $from_when = null)
		{
			$theComments = array();
			
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tComments::TARGET_ID, $target_id);
			$crit->addWhere(B2tComments::TARGET_TYPE, $target_type);
			$crit->addWhere(B2tComments::MODULE, $module);
			$crit->addWhere(B2tComments::DELETED, 0);
			if ($from_when != null)
			{
				$crit->addWhere(B2tComments::POSTED, $from_when, B2DBCriteria::DB_GREATER_THAN);
			}
			if (!BUGScontext::getUser()->hasPermission('b2canreadallcomments'))
			{
				$crit->addWhere(B2tComments::IS_PUBLIC, 1);
			}
			$crit->addOrderBy(B2tComments::UPDATED, 'desc');
	
			$res = B2DB::getTable('B2tComments')->doSelect($crit);
			
			while ($row = $res->getNextRow())
			{
				$theComments[] = new BUGSComment($row->get(B2tComments::ID), $row);
			}
	
			return $theComments;
		}
		
		static function countComments($target_id, $target_type, $module = 'core')
		{
			
		}
		
		static function getCommentForm($target_id, $target_type = 1, $module = 'core', $c_id = null)
		{
			if ($c_id != null)
			{
				$theComment = new BUGSComment($c_id);
			}
			$retval = '';
			$form_id = ($c_id == null) ? "new_comment_form_{$module}_{$target_type}_{$target_id}" : "edit_comment_form_{$module}_{$target_type}_{$target_id}";
			$form_action = ($c_id == null) ? 'addComment(\'' . $module . '\', \'' . $target_type . '\', \'' . $target_id . '\');' : 'updateComment(\'' . $theComment->getID() . '\', \'' . $theComment->getModuleName() . '\', \'' . $theComment->getTargetType() . '\', \'' . $theComment->getTargetID() . '\');'; 
			if ($c_id == null)
			{
				$retval .= '<a name="add_comment_location_' . $module . '_' . $target_type . '_' . $target_id . '"></a>';
				$retval .= '<div id="addCommentLink_' . $module . '_' . $target_type . '_' . $target_id . '" style="margin-top: 5px;">';
				$retval .= '<div style="padding: 5px; width: auto; float: left; margin-bottom: 10px; text-align: left; background-color: #F7F7F7; border: 1px solid #EEE;">';
				$retval .= '<a href="javascript:void(0);" onclick="Element.show(\'addComment_' . $module . '_' . $target_type . '_' . $target_id . '\');Element.hide(\'addCommentLink_' . $module . '_' . $target_type . '_' . $target_id . '\');">';
				$retval .= image_tag('icon_title_small.png', 'align="left" style="margin-right: 5px;"');
				$retval .= __('Click here to add a comment');
				$retval .= '</a>';
				$retval .= '</div>';
				$retval .= '</div>';
				$retval .= '<div style="padding: 5px; padding-top: 0px; display: none;" id="addComment_' . $module . '_' . $target_type . '_' . $target_id . '">';
				$retval .= '<div class="commentheadertop"><b>' . __('Add a comment') . '</b></div>';
				$retval .= '<form accept-charset="' . BUGScontext::getI18n()->getCharset() . '" action="" enctype="multipart/form-data" method="post" id="'.$form_id.'" onsubmit="'.$form_action.'return false">';
			}
			else
			{
				$retval .= '<div style="padding: 5px; padding-top: 0px;" id="editComment_' . $c_id . '">';
				$retval .= '<form accept-charset="' . BUGScontext::getI18n()->getCharset() . '" action="" enctype="multipart/form-data" method="post" id="'.$form_id.'" onsubmit="'.$form_action.'return false">';
			}
			$retval .= '<input type="hidden" name="target_id" value="' . $target_id . '">';
			$retval .= '<input type="hidden" name="target_type" value="' . $target_type . '">';
			$retval .= '<input type="hidden" name="module" value="' . $module . '">';
			if ($c_id == null)
			{
				$retval .= '<input type="hidden" name="add_comment" value="true">';
			}
			else
			{
				$retval .= '<input type="hidden" name="comment_id" value="' . $c_id . '">';
			}
			$retval .= '<table style="width: 100%" cellpadding=0 cellspacing=0>';
			$retval .= '<tr>';
			$retval .= '<td style="width: 70px; padding: 2px;"><b>' . __('Title') . '</b></td>';
			$retval .= '<td style="width: auto; padding: 2px;">';
			if ($c_id != null)
			{
				$retval .= '<input type="text" style="width: 100%;" name="new_comment_title_' . $module . '_' . $target_type . '_' . $target_id . '" value="' . $theComment->getTitle() . '">';
			}
			else
			{
				$retval .= '<input type="text" style="width: 100%;" name="new_comment_title">';
			}
			$retval .= '</td>';
			$retval .= '</tr>';
			$retval .= '<tr>';
			$retval .= '<td style="width: 70px; padding: 2px;"><b>' . __('Visibility') . '</b></td>';
			$retval .= '<td style="width: auto; padding: 2px;">';
			$retval .= '<select name="is_public" style="width: 100%;">';
			
			$retval .= '<option value="1" ';
			if ($c_id !== null && $theComment->isPublic()) $retval .= 'selected';
			$retval .= '>' . __('This comment is visible to everyone') . '</option>';
			
			$retval .= '<option value="0" ';
			if ($c_id !== null && !$theComment->isPublic()) $retval .= 'selected';
			$retval .= '>' . __('This comment is visible only to the developers / staff') . '</option>';
			
			$retval .= '</select>';
			$retval .= '</td>';
			$retval .= '</tr>';
			$retval .= '<tr>';
			$retval .= '<td style="padding: 2px; padding-top: 4px;" valign="top"><b>' . __('Comment') . '</b></td>';
			$retval .= '<td style="padding: 2px;">';
			$area_id = ($c_id != null) ? "new_comment_comment_{$module}_{$target_type}_{$target_id}" : 'new_comment_comment';
			if ($c_id != null)
			{
				$retval .= bugs_newTextArea($area_id, '100px', '100%', $theComment->getContent());
			}
			else
			{
				$retval .= bugs_newTextArea($area_id, '100px', '100%');
			}
			$retval .= '</td>';
			$retval .= '</tr>';
			$retval .= '</table><br>';
			if ($c_id == null)
			{
				$retval .= '<input type="submit" value="' . __('Post comment') . '">&nbsp;&nbsp;<a style="font-size: 10px;" href="javascript:void(0);" onclick="Element.hide(\'addComment_' . $module . '_' . $target_type . '_' . $target_id . '\');Element.show(\'addCommentLink_' . $module . '_' . $target_type . '_' . $target_id . '\');">' . __('Cancel') . '</a>';
			}
			else
			{
				$retval .= '<input type="submit" value="' . __('Post comment') . '">&nbsp;&nbsp;<a style="font-size: 10px;" href="javascript:void(0);" onclick="getComment(' . $c_id . ', \'' . $module . '\', \'' . $target_type . '\', \'' . $target_id . '\');">' . __('Cancel') . '</a>';
			}
			$retval .= '</form>';
			$retval .= '<div style="padding: 10px 0 10px 0; display: none;" id="add_comment_indicator_' . $module . '_' . $target_type . '_' . $target_id . '"><span style="float: left;">' . image_tag('spinning_16.gif') . '</span>&nbsp;' . __('Please wait') . '</div>';
			$retval .= '</div>';
			if (BUGScontext::getRequest()->isAjaxCall())
			{
				$retval .= '<script type="text/javascript">';
				$retval .= 'tinyMCE.init({';
				$retval .= 'theme : "advanced",';
				$retval .= 'mode : "none",';
				$retval .= 'plugins : "bbcode",';
				$retval .= 'theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,blockquote,|,undo,redo,|,link,unlink",';
				$retval .= 'theme_advanced_buttons2 : "",';
				$retval .= 'theme_advanced_buttons3 : "",';
				$retval .= 'theme_advanced_toolbar_location : "bottom",';
				$retval .= 'theme_advanced_toolbar_align : "left",';
				$retval .= 'theme_advanced_styles : "Code=bb_code;Quote=bb_quote",';
				$retval .= 'content_css : "' . BUGScontext::getTBGPath() . 'themes/' . BUGSsettings::getThemeName() . '/' . BUGSsettings::getThemeName() . '.css",';
				$retval .= 'entity_encoding : "raw",';
				$retval .= 'valid_elements : "a,br,b,i,u,span,p,img,ul,li",';
				$retval .= 'add_unload_trigger : false,';
				$retval .= 'remove_linebreaks : false';
				$retval .= '});';
				$retval .= '</script>';
			}
			
			$retval .= '<script type="text/javascript">
							Event.observe(window, \'load\', function() {
								tinyMCE.get(\''.$area_id.'\').onKeyPress.add(function(ed, k) { 
									if (k.ctrlKey && k.keyCode == 13)
									{
										'.$form_action.'
									}
								});
							});
						</script>';
			echo $retval;
		}
		
		static function createNew($title, $content, $uid, $target_id, $target_type, $module, $is_public, $system_comment = 0, $invoke_trigger = true)
		{
			$commentTitle = trim($title);
			$commentContent = trim($content);
			$comment = null;
			if ($commentContent != '' && $commentTitle == '') $commentTitle = __('Untitled comment');
			if ($commentTitle != '' && $commentContent != '' && $commentContent != '[p] [/p]')
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
				$crit->addInsert(B2tComments::SCOPE, BUGScontext::getScope()->getID());
				$res = B2DB::getTable('B2tComments')->doInsert($crit);
				$comment = new BUGSComment($res->getInsertID());
				if ($target_type == 1 && $module == 'core')
				{
					try
					{
						BUGSfactory::BUGSissueLab((int) $target_id)->updateTime();
					}
					catch (Exception $e) {}
				}
				if ($invoke_trigger)
				{
					try
					{
						BUGScontext::trigger('core', 'BUGSComment::createNew', $comment);
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
				BUGSfactory::BUGSissueLab($target_id)->updateComment($title, $content, $uid);
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
			$crit->addUpdate(B2tComments::UPDATED_BY, BUGScontext::getUser()->getUID());
			B2DB::getTable('B2tComments')->doUpdateById($crit, $c_id);
		}
		
		static function getCommentAccess($target_id, $type = 'view', $target_type = 1, $module = 'core')
		{
			switch ($module)
			{
				case 'core':
					switch ($target_type)
					{
						case 1:
							$ia = BUGSissue::hasAccess($target_id);
							if ($ia['allowed'] || $ia['explicit'])
							{
								$theIssue = BUGSfactory::BUGSissueLab($target_id);
								switch ($type)
								{
									case 'view':
										if (BUGScontext::getUser()->hasPermission("b2hidecomments", $target_type . ':' . $theIssue->getID(), "core") == false)
										{
											if (BUGScontext::getUser()->hasPermission("b2canviewcomments", $target_type . ':' . $theIssue->getProject()->getID(), "core"))
											{
												return true;
											}
										}
										return false;
										break;
									case 'add':
										if (BUGScontext::getUser()->hasPermission("b2canaddcomments", $target_type . ':' . $theIssue->getProject()->getID(), "core"))
										{
											$canAddComments = true;
										}
										if (BUGScontext::getUser()->hasPermission("b2notaddcomments", $theIssue->getID(), "core") == true)
										{
											$canAddComments = false;
										}
										return $canAddComments;
										break;
									case 'edit':
										if (BUGScontext::getUser()->hasPermission("b2caneditcomments", $target_type . ':' . $theIssue->getProject()->getID(), "core"))
										{
											$canEditComments = true;
										}
										if (BUGScontext::getUser()->hasPermission("b2noteditcomments", $theIssue->getID(), "core") == true)
										{
											$canEditComments = false;
										}
										return $canEditComments;
										break;
								}
							}
							return false;
							/*if (BUGSsettings::get('b2filtercommentsuser', 'core', BUGScontext::getScope()->getID(), BUGScontext::getUser()->getUID()) == 1)
							{
								$doFilterUserComments = true;
							}
							if (BUGSsettings::get('b2filtercommentssystem', 'core', BUGScontext::getScope()->getID(), BUGScontext::getUser()->getUID()) == 1)
							{
								$doFilterSystemComments = true;
							}*/
						break;
					}
					break;
				default:
					throw new Exception('eeek!');
					return BUGScontext::getModule($module)->getCommentAccess($target_type, $target_id, $type);
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
		 * @return BUGSuser
		 */
		public function getUpdatedBy()
		{
			return BUGSfactory::userLab($this->_updatedby);
		}
		
		/**
		 * Returns the user who posted the comment
		 *
		 * @return BUGSuser
		 */
		public function getPostedBy()
		{
			return BUGSfactory::userLab($this->_postedby);
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
