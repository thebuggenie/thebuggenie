<?php

	if (!defined('BUGS2_INCLUDE_PATH'))
	{
		define ('BUGS2_INCLUDE_PATH', '../');
	}
	
	require_once(BUGS2_INCLUDE_PATH . 'include/checkcookie.inc.php');
	require_once(BUGS2_INCLUDE_PATH . 'include/b2_engine.inc.php');
	require_once(BUGScontext::getIncludePath() . 'include/ui_functions.inc.php');
	
	if (BUGScontext::getRequest()->getParameter('issue_no'))
	{
		$_SESSION['issue_no'] = BUGScontext::getRequest()->getParameter('issue_no');
	}

	$issue_no = (isset($_SESSION['issue_no'])) ? $_SESSION['issue_no'] : null;

	if (!$issue_no)
	{
		bugs_moveTo('index.php');
		exit();
	}
	else
	{
		try
		{
			$theIssue = BUGSissue::getIssueFromLink($issue_no);
		}
		catch (Exception $e)
		{
			echo nl2br($e);
		}
	}
	
	if ($theIssue instanceof BUGSissue)
	{

		bugs_removeIssueNotification(BUGScontext::getUser()->getUID(), $theIssue->getID());

		if (!BUGScontext::getUser()->hasPermission("b2cantvote", $theIssue->getID(), "core"))
		{
			if (is_numeric(BUGScontext::getRequest()->getParameter('votetype'))) 
			{ 
				echo $theIssue->vote(BUGScontext::getRequest()->getParameter('votetype'), BUGScontext::getUser()->getUID()); 
			}
		}

		$theIssue->canDeleteIssue(false);
		if (BUGScontext::getUser()->hasPermission("b2caneditissuetext", $theIssue->getProject()->getID(), "core"))
		{
			$theIssue->canEditTexts(true);
		}
		if (BUGScontext::getUser()->hasPermission("b2caneditissueusers", $theIssue->getProject()->getID(), "core"))
		{
			$theIssue->canEditUsers(true);
		}
		if (BUGScontext::getUser()->hasPermission("b2caneditissuefields", $theIssue->getProject()->getID(), "core"))
		{
			$theIssue->canEditFields(true);
		}
		if (BUGScontext::getUser()->hasPermission("b2candeleteissues", $theIssue->getProject()->getID(), "core"))
		{
			$theIssue->canDeleteIssue(true);
		}

		if (!BUGScontext::getRequest()->getParameter('hide_comments') && BUGSuser::isThisGuest())
		{
			switch (BUGScontext::getRequest()->getParameter('hide_comments'))
			{
				case 'system':
					BUGSsettings::saveSetting('b2filtercommentssystem', 1, 'core', BUGScontext::getScope()->getID(), BUGScontext::getUser()->getUID());
					break;
				case 'user':
					BUGSsettings::saveSetting('b2filtercommentsuser', 1, 'core', BUGScontext::getScope()->getID(), BUGScontext::getUser()->getUID());
					break;
			}
		}
		if (!BUGScontext::getRequest()->getParameter('show_comments') && BUGSuser::isThisGuest())
		{
			switch (BUGScontext::getRequest()->getParameter('show_comments'))
			{
				case 'system':
					BUGSsettings::saveSetting('b2filtercommentssystem', 0, 'core', BUGScontext::getScope()->getID(), BUGScontext::getUser()->getUID());
					break;
				case 'user':
					BUGSsettings::saveSetting('b2filtercommentsuser', 0, 'core', BUGScontext::getScope()->getID(), BUGScontext::getUser()->getUID());
					break;
			}
		}
		
		if (BUGScontext::getRequest()->getParameter('getvotes'))
		{
			echo '<table cellpadding=0 cellspacing=0>
				<tr>
				<td style="width: 20px;">' . image_tag('icon_votes.png') . '</td>
				<td><b>' . $theIssue->getVotesByType(BUGSissue::VOTE_REVIEW) . '</b>' . __('%number% votes for reviewing', array('%number%' => '')) . '</td>
				</tr>
				<tr>
				<td style="width: 20px;">' . image_tag('icon_votes.png') . '</td>
				<td><b>' . $theIssue->getVotesByType(BUGSissue::VOTE_STATUS) . '</b>' . __('%number% votes for critical status', array('%number%' => '')) . '</td>
				</tr>
				<tr>
				<td style="width: 20px;">' . image_tag('icon_votes.png') . '</td>
				<td><b>' . $theIssue->getVotesByType(BUGSissue::VOTE_CLOSING) . '</b>' . __('%number% votes for closing this issue', array('%number%' => '')) . '</td>
				</tr>
				</table>';
		}
		
		if ($theIssue->canEditFields() || ($theIssue->getPostedBy()->getID() == BUGScontext::getUser()->getID() && !BUGSuser::isThisGuest()))
		{
			if (BUGScontext::getRequest()->getParameter('issue_setstate') !== null)
			{
				$theIssue->setState(BUGScontext::getRequest()->getParameter('issue_setstate'));
			}
		}
		if ($theIssue->canDeleteIssue())
		{
			if (BUGScontext::getRequest()->getParameter('delete_issue'))
			{
				$theIssue->deleteIssue();
			}
		}
		if ($theIssue->canEditTexts())
		{
			if (BUGScontext::getRequest()->getParameter('promotetask') && is_numeric(BUGScontext::getRequest()->getParameter('t_id')))
			{
				$theTask = $theIssue->getTask(BUGScontext::getRequest()->getParameter('t_id'));
				$e_id = ($theIssue->getEdition() instanceof BUGSedition) ? $theIssue->getEdition()->getID() : 0;
				$b_id = ($theIssue->getBuild() instanceof BUGSbuild) ? $theIssue->getBuild()->getID() : 0;
				$c_id = ($theIssue->getComponent() instanceof BUGScomponent) ? $theIssue->getComponent()->getID() : 0; 
				$theNewIssue = BUGSissue::createNew($theIssue->getProject()->getID(), $e_id, $b_id, BUGSissuetype::getTask(), $c_id, 0, 0, $theTask->getTitle(), $theTask->getContent(), '', array());
				$theNewIssue->setStatus($theTask->getStatus()->getID());
				$theNewIssue->setIssuetype(BUGSissuetype::getTask());
				if ($theTask->getAssignedType() > 0)
				{
					$theNewIssue->setAssignee($theTask->getAssignee()->getID(), $theTask->getAssignedType());
				}
				if ($theTask->isCompleted())
				{
					$theNewIssue->setState(BUGSissue::STATE_CLOSED);
				}
				$theIssue->addDependantIssue($theNewIssue->getID(), 1);
				$theIssue->deleteTask($theTask->getID());
				BUGScontext::getUser()->addUserIssue($theNewIssue->getID());
			}
			if (BUGScontext::getRequest()->getParameter('issue_update_task'))
			{
				if (BUGScontext::getRequest()->getParameter('task_new_title'))
				{
					$newTitle = stripcslashes(BUGScontext::getRequest()->getParameter('task_new_title'));
					$newContent = stripcslashes(BUGScontext::getRequest()->getParameter('task_new_content'));
					$theTask = BUGSfactory::taskLab(BUGScontext::getRequest()->getParameter('t_id'));
					$theTask->updateDetails($newTitle, $newContent);
				}
			}
		}
		if ($theIssue->canEditFields())
		{
			if (BUGScontext::getRequest()->getParameter('add_dependant_issue'))
			{
				if (is_numeric(BUGScontext::getRequest()->getParameter('d_id')))
				{
					$theIssue->addDependantIssue(BUGScontext::getRequest()->getParameter('d_id'), BUGScontext::getRequest()->getParameter('this_depends'));
				}
			}
			if (BUGScontext::getRequest()->getParameter('remove_depends'))
			{
				if (is_numeric(BUGScontext::getRequest()->getParameter('p_id')))
				{
					$theIssue->removeDependantIssue(BUGScontext::getRequest()->getParameter('p_id'));
				}
			}
			if (BUGScontext::getRequest()->getParameter('task_setassignee'))
			{
				$theTask = BUGSfactory::taskLab(BUGScontext::getRequest()->getParameter('t_id'));
				$theTask->setAssignee(BUGScontext::getRequest()->getParameter('id'), BUGScontext::getRequest()->getParameter('assigned_type'));
			}
			if (BUGScontext::getRequest()->getParameter('task_setstatus'))
			{
				$theTask = BUGSfactory::taskLab(BUGScontext::getRequest()->getParameter('t_id'));
				$theTask->setStatus(BUGScontext::getRequest()->getParameter('task_newstatus'));
			}
			if (BUGScontext::getRequest()->getParameter('task_setclosed') && is_numeric(BUGScontext::getRequest()->getParameter('t_id')))
			{
				$theTask = BUGSfactory::taskLab(BUGScontext::getRequest()->getParameter('t_id'));
				$theTask->setCompleted(BUGScontext::getRequest()->getParameter('closed'));
				if ($theIssue->canEditFields())
				{
					if ($theTask->isCompleted())
					{
						?><a href="javascript:void(0);" onclick="setTaskClosed(<?php echo $theTask->getID(); ?>, 0);" class="image"><?php echo image_tag('action_ok_small.png'); ?></a><?php
					}
					else
					{
						?><a href="javascript:void(0);" onclick="setTaskClosed(<?php echo $theTask->getID(); ?>, 1);" class="image"><?php echo image_tag('action_cancel_small.png'); ?></a><?php
					}
				}
				else
				{
					echo ($theTask->isCompleted()) ? image_tag('action_ok_small.png') : image_tag('action_cancel_small.png'); ?><?php
				}
			}
			if (BUGScontext::getRequest()->getParameter('issue_setconfirmed'))
			{
				$theIssue->setAffectedConfirmed(BUGScontext::getRequest()->getParameter('a_id'), BUGScontext::getRequest()->getParameter('a_type'), BUGScontext::getRequest()->getParameter('confirmed'));
				if ($theIssue->canEditFields())
				{
					if (BUGScontext::getRequest()->getParameter('confirmed'))
					{
						echo '<a href="javascript:void(0);" onclick="setAffectedConfirmed(0, ' . BUGScontext::getRequest()->getParameter('a_id') . ', \'' . BUGScontext::getRequest()->getParameter('a_type') . '\')" class="image">' . image_tag('action_ok_small.png') . '</a>';
					}
					else
					{
						echo '<a href="javascript:void(0);" onclick="setAffectedConfirmed(1, ' . BUGScontext::getRequest()->getParameter('a_id') . ', \'' . BUGScontext::getRequest()->getParameter('a_type') . '\')" class="image">' . image_tag('action_cancel_small.png') . '</a>';
					}
				}
			}
			if (BUGScontext::getRequest()->getParameter('deletetask') && is_numeric(BUGScontext::getRequest()->getParameter('t_id')))
			{
				$theIssue->deleteTask(BUGScontext::getRequest()->getParameter('t_id'));
			}
			if (BUGScontext::getRequest()->getParameter('links') && BUGScontext::getUser()->hasPermission('b2addlinks'))
			{
				switch (BUGScontext::getRequest()->getParameter('action'))
				{
					case "add":
						$theIssue->attachLink(BUGScontext::getRequest()->getParameter('url'), BUGScontext::getRequest()->getParameter('desc'));
						break;
					case "remove":
						$theIssue->removeLink(BUGScontext::getRequest()->getParameter('l_id'));
						break;
				}
			}
		}
		if (BUGScontext::getRequest()->getParameter('files') && BUGScontext::getUser()->hasPermission('b2uploadfiles'))
		{
			switch (BUGScontext::getRequest()->getParameter('action'))
			{
				case "add":
					$thefile = &$_FILES['file'];
					try
					{
						$new_filename = BUGSrequest::handleUpload($thefile);
						if ($new_filename)
						{
							$description = BUGScontext::getRequest()->getParameter('desc', basename($thefile['name']));
							$theIssue->attachFile($new_filename, $description);
							$theIssue->addSystemComment(__('File attached'), __('The file %filename% was attached to the issue', array('%filename%' => '[url=files/' . $new_filename . ']' . $description . '[/url]')), BUGScontext::getUser()->getUID(), true);
						}
						else
						{
							throw new Exception(__('An error occured when saving the file'));
						}
					}
					catch (Exception $e)
					{
						$upload_error = $e->getMessage();
					}
					break;
				case "remove":
					$theIssue->removeFile(BUGScontext::getRequest()->getParameter('f_id'));
					break;
			}
		}
		if (BUGScontext::getUser()->hasPermission("b2caneditissuetext", $theIssue->getProject()->getID(), "core"))
		{
			$theIssue->canEditTexts(true);
		}
		if (BUGScontext::getUser()->hasPermission("b2caneditissueusers", $theIssue->getProject()->getID(), "core"))
		{
			$theIssue->canEditUsers(true);
		}
		if (BUGScontext::getUser()->hasPermission("b2caneditissuefields", $theIssue->getProject()->getID(), "core"))
		{
			$theIssue->canEditFields(true);
		}

		if ((BUGScontext::getUser()->getUID() != 0) && ((BUGScontext::getUser()->getUname() != BUGSsettings::get('defaultuname')) || (BUGSsettings::get('defaultisguest') == 0)))
		{
			if (BUGScontext::getRequest()->getParameter('watchlist'))
			{
				switch (BUGScontext::getRequest()->getParameter('action'))
				{
					case "add":
						BUGScontext::getUser()->addUserIssue($theIssue->getID());
						break;
					case "remove":
						BUGScontext::getUser()->removeUserIssue($theIssue->getID());
						break;
				}
				if (in_array($theIssue->getID(), BUGScontext::getUser()->getStarredIssues()))
				{
					?>
					<tr>
					<td class="imgtd"><?php echo image_tag('icon_issue_followup_stop.png'); ?></td>
					<td><a href="javascript:void(0);" onclick="removeUserIssue();"><?php echo __('Remove this issue from the watchlist'); ?></a></td>
					</tr>
					<?php
				}
				else
				{
					?>
					<tr>
					<td class="imgtd"><?php echo image_tag('icon_issue_followup.png'); ?></td>
					<td><a href="javascript:void(0);" onclick="addUserIssue();"><?php echo __('Add this issue to the watchlist'); ?></a></td>
					</tr>
					<?php
				}
			}
		}

	}

	if ($theIssue instanceof BUGSissue)
	{
		$header_title = 'Issue ' . $theIssue->getFormattedIssueNo() . ' - ' . $theIssue->getTitle();
	}
	elseif (BUGScontext::getRequest()->isAjaxCall())
	{
		echo __('You do not have access to this issue');
		exit();
	}
	else
	{
		require_once(BUGScontext::getIncludePath() . 'include/header.inc.php');
		require_once(BUGScontext::getIncludePath() . 'include/menu.inc.php');
		bugs_msgbox(false, __('The specified issue is not available'), __('The specified issue report is not available due to one of the following reasons:') . '<br><br><li>' . __('You have specified an issue id that does not exist, or has been deleted') . '</li><li>' . __('You do not have permission to view this issue report'));
	}
	if ($theIssue instanceof BUGSissue)
	{
		if ($theIssue->isDeleted() && !BUGScontext::getRequest()->isAjaxCall())
		{
			require_once(BUGScontext::getIncludePath() . 'include/header.inc.php');
			require_once(BUGScontext::getIncludePath() . 'include/menu.inc.php');
			bugs_msgbox(false, __('The specified issue has been deleted'), __('The specified issue has been deleted'));
			require_once(BUGScontext::getIncludePath() . 'include/footer.inc.php');
			exit();
		}
		elseif (!$theIssue->isDeleted())
		{
			if (BUGScontext::getRequest()->isAjaxCall())
			{
				header ("Content-Type: text/html; charset=" . BUGScontext::getI18n()->getCharset());
				if (BUGScontext::getRequest()->getParameter('getstatuslisttask') || BUGScontext::getRequest()->getParameter('settaskstatus'))
				{
					$theTask = BUGSfactory::taskLab(BUGScontext::getRequest()->getParameter('t_id'));
					if($theIssue->canEditTexts() ||
						($theIssue->getPostedBy()->getID() == BUGScontext::getUser()->getID() && !BUGScontext::getUser()->isGuest()) ||
						($theTask->getAssignedType() == BUGSidentifiableclass::TYPE_USER && $theTask->getAssignee()->getID() == BUGScontext::getUser()->getID() && !BUGScontext::getUser()->isGuest()) || 
						($theTask->getAssignedType() == BUGSidentifiableclass::TYPE_TEAM && BUGScontext::getUser()->isMemberOf($theTask->getAssignee()->getID()) === true && !BUGScontext::getUser()->isGuest()))
					{
						if (BUGScontext::getRequest()->getParameter('getstatuslisttask'))
						{
							$statusTypes = BUGScontext::getDatatypes(BUGSdatatype::STATUS);
							$retval = '';
							$retval .= '<table cellpadding=0 cellspacing=0 border=0 style="width: 100%;">';
							$statusCC = 0;
							foreach ($statusTypes as $aStatus)
							{
								$retval .= '<tr>';
								$retval .= '<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: ' . $aStatus->getItemdata() . '; font-size: 1px; width: 13px; height: 13px;">&nbsp;</div></td>';
								$retval .= '<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="setTaskStatus(' . BUGScontext::getRequest()->getParameter('t_id') . ', ' . $aStatus->getID() . ');">' . $aStatus->getName() . '</a></td>';
								$retval .= '</tr>';
								$statusCC++;
							}
							if (count($statusTypes) == 0)
							{
								$retval .= '<tr><td style="padding: 2px; color: #BBB;">' . __('There is no available status to select') . '</td></tr>';
							}
							$retval .= '</table>';
							echo $retval;
						}
						if (BUGScontext::getRequest()->getParameter('settaskstatus'))
						{
							$theTask->setStatus(BUGScontext::getRequest()->getParameter('status'));
							$retval = '';
							$retval .= '<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: ' . $theTask->getStatus()->getColor() . '; font-size: 1px; width: 13px; height: 13px;">&nbsp;</div></td>';
							$retval .= '<td>' . $theTask->getStatus()->getName() . '</td>';
							$retval .= '<td style="width: 30px; text-align: right;"><a href="javascript:void(0);" onclick="Effect.Appear(\'task_status_' . BUGScontext::getRequest()->getParameter('t_id') . '\');getTaskStatusList(' . BUGScontext::getRequest()->getParameter('t_id') . ');" style="font-size: 9px;" class="image">' . image_tag('icon_switchassignee.png') . '</a></td>';
							echo $retval;
						}
					}
				}
				if ($theIssue->canEditUsers())
				{
					if (BUGScontext::getRequest()->getParameter('gettasks'))
					{
						echo '<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0 id="taskslist">';
						if (count($theIssue->getTasks()) == 0)
						{
							?>
							<tr>
							<td class="issuedetailscontentsleft" colspan=5 style="padding-top: 4px; padding-bottom: 4px; color: #BBB;"><?php echo __('No tasks are specified for this issue'); ?></td>
							</tr>
							</table>
							<?php
						}
						else
						{
							echo '</table>';
							$include_table = true;
							foreach ($theIssue->getTasks() as $theTask)
							{
								require BUGScontext::getIncludePath() . 'include/issue_taskbox.inc.php';
							}
						}
					}
					if (BUGScontext::getRequest()->getParameter('gettask_title') && is_numeric(BUGScontext::getRequest()->getParameter('t_id')))
					{
						$theTask = new BUGStask(BUGScontext::getRequest()->getParameter('t_id'));
						if ($theTask->getContent() != "")
						{
							?>
							<a href="javascript:void(0);" onclick="Effect.SlideDown('task_<?php echo $theTask->getID(); ?>')"><?php echo $theTask->getTitle(); ?></a>
							<?php
						}
						else
						{
							echo $theTask->getTitle();
						}
					}
					if (BUGScontext::getRequest()->getParameter('gettask_description') && is_numeric(BUGScontext::getRequest()->getParameter('t_id')))
					{
						$theTask = new BUGStask(BUGScontext::getRequest()->getParameter('t_id'));
						echo bugs_BBDecode($theTask->getContent());
						?><div style="font-size: 10px; text-align: left;"><a href="javascript:void(0);" onclick="Element.hide('task_<?php echo $theTask->getID(); ?>');"><?php echo __('Hide description'); ?></a></div><?php
					}
					if (BUGScontext::getRequest()->getParameter('gettask_lastupdated') && is_numeric(BUGScontext::getRequest()->getParameter('t_id')))
					{
						$theTask = new BUGStask(BUGScontext::getRequest()->getParameter('t_id'));
						echo bugs_formatTime($theTask->getUpdated(), 4);
					}
					if (BUGScontext::getRequest()->getParameter('setowner'))
					{
						$theIssue->setOwner(BUGScontext::getRequest()->getParameter('id'), BUGScontext::getRequest()->getParameter('owned_type'));
						echo $theIssue->getOwner();
					}
					if (BUGScontext::getRequest()->getParameter('getowner'))
					{
						if ($theIssue->isOwned())
						{
							BUGScontext::setIncludePath('');
							echo '<table style="width: 100%;" cellpadding=0 cellspacing=0>';
							echo ($theIssue->getOwnerType() == BUGSidentifiableclass::TYPE_USER) ? bugs_userDropdown($theIssue->getOwner()->getID()) : bugs_teamDropdown($theIssue->getOwner()->getID());
							echo '</table>';
						}
						else
						{
							 echo '<div style="color: #BFBFBF;">' . __('Not owned by anyone') . '</div>';
						}
					}
					if (BUGScontext::getRequest()->getParameter('setassignee'))
					{
						$theIssue->setAssignee( BUGScontext::getRequest()->getParameter('id'), BUGScontext::getRequest()->getParameter('assigned_type'));
						echo $theIssue->getAssignee();
					}
					if (BUGScontext::getRequest()->getParameter('deleteaccess'))
					{
						B2DB::getTable('B2tPermissions')->doDeleteById(BUGScontext::getRequest()->getParameter('id'));
					}
					if (BUGScontext::getRequest()->getParameter('sethidden'))
					{
						$tid = 0;
						$uid = 0;
						$gid = 0;
						switch (BUGScontext::getRequest()->getParameter('hidden_type'))
						{
							case 1:
								$uid = BUGScontext::getRequest()->getParameter('id');
								break;
							case 2:
								$tid = BUGScontext::getRequest()->getParameter('id');
								break;
							case 3:
								$gid = BUGScontext::getRequest()->getParameter('id');
								break;
						}
						BUGScontext::setPermission('b2notviewissue', $theIssue->getID(), 'core', $uid, $gid, $tid, 1);
					}
					if (BUGScontext::getRequest()->getParameter('setvisible'))
					{
						$tid = 0;
						$uid = 0;
						$gid = 0;
						switch (BUGScontext::getRequest()->getParameter('visible_type'))
						{
							case 1:
								$uid = BUGScontext::getRequest()->getParameter('id');
								break;
							case 2:
								$tid = BUGScontext::getRequest()->getParameter('id');
								break;
							case 3:
								$gid = BUGScontext::getRequest()->getParameter('id');
								break;
						}
						BUGScontext::setPermission('b2viewissue', $theIssue->getID(), 'core', $uid, $gid, $tid, 1);
					}
					if (BUGScontext::getRequest()->getParameter('gethiddenfrom') || BUGScontext::getRequest()->getParameter('getavailableto'))
					{
						if (BUGScontext::getRequest()->getParameter('gethiddenfrom'))
						{
							$permissions = BUGScontext::getAllPermissions('b2notviewissue', 0, 0, 0, $theIssue->getID(), true);
						}
						else
						{
							$permissions = BUGScontext::getAllPermissions('b2viewissue', 0, 0, 0, $theIssue->getID(), true);
						}
						foreach ($permissions as $permission)
						{
							if (($permission['uid'] + $permission['gid'] + $permission['tid']) == 0)
							{
								echo 'Everyone';
							}
							else
							{
								switch (true)
								{
									case ($permission['uid'] != 0):
										echo BUGSfactory::userLab($permission['uid']);
										break;
									case ($permission['gid'] != 0):
										echo '<b>' . __('Group:') . '</b> ' . BUGSfactory::groupLab($permission['gid']);
										break;
									case ($permission['tid'] != 0):
										echo '<b>' . __('Team:') . '</b> ' . BUGSfactory::teamLab($permission['tid']);
										break;
								}
							}
							echo '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deletePermission(\'' . $permission['id'] . '\')" style="font-size: 9px;">' . __('Remove') . '</a><br>';
						}
						if (count($permissions) == 0)
						{
							echo '<div style="color: #BBB;">' . __('No restrictions set') . '</div>';
						}
					}
					if (BUGScontext::getRequest()->getParameter('getassignee'))
					{
						if ($theIssue->isAssigned())
						{
							BUGScontext::setIncludePath('');
							echo '<table style="width: 100%;" cellpadding=0 cellspacing=0>';
							echo ($theIssue->getAssigneeType() == BUGSidentifiableclass::TYPE_USER) ? bugs_userDropdown($theIssue->getAssignee()->getID()) : bugs_teamDropdown($theIssue->getAssignee()->getID());
							echo '</table>';
						}
						else
						{
							 echo '<div style="color: #BFBFBF;">' . __('Not assigned to anyone') . '</div>';
						}
					}
					if (BUGScontext::getRequest()->getParameter('task_getassignee'))
					{
						BUGScontext::setIncludePath('');
						$theTask = BUGSfactory::taskLab(BUGScontext::getRequest()->getParameter('t_id'));
						?><table style="width: 100%;" cellpadding=0 cellspacing=0><?php
						if ($theTask->getAssignedType() == BUGSidentifiableclass::TYPE_USER)
						{
							if ($theIssue->canEditUsers())
							{
								$thetr = bugs_userDropdown($theTask->getAssignee()->getID(), 1);
								echo $thetr[0];
								?><td style="width: 20px;"><a href="javascript:void(0);" class="image" onclick="javascript:showHide('task_<?php echo $theTask->getID(); ?>_edit_assignee')"><?php echo image_tag('icon_switchassignee.png'); ?></a></td></tr><?php
								echo $thetr[1];
							}
							else
							{
								echo bugs_userDropdown($theTask->getAssignee()->getID());
							}
						}
						else
						{
							if ($theIssue->canEditUsers())
							{
								$thetr = bugs_teamDropdown($theTask->getAssignee()->getID(), 1);
								echo $thetr[0];
								?><td style="width: 20px;"><a href="javascript:void(0);" class="image" onclick="javascript:showHide('task_<?php echo $theTask->getID(); ?>_edit_assignee')"><?php echo image_tag('icon_switchassignee.png'); ?></a></td></tr><?php
								echo $thetr[1];
							}
							else
							{
								echo bugs_teamDropdown($theTask->getAssignee()->getID());
							}
						}
						?>
						</table>
						<span id="task_<?php echo $theTask->getID(); ?>_edit_assignee" style="display: none;">
						<?php bugs_AJAXuserteamselector(__('Assign to a user'), 
														__('Assign to a team'),
														'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&t_id=' . $theTask->getID() . '&task_setassignee=true&assigned_type=1', 
														'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&t_id=' . $theTask->getID() . '&task_setassignee=true&assigned_type=2',
														'task_' . $theTask->getID() . '_assignee',
														'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&t_id=' . $theTask->getID() . '&task_getassignee=true', 
														'task_' . $theTask->getID() . '_assignee', 
														'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&t_id=' . $theTask->getID() . '&task_getassignee=true',
														'task_' . $theTask->getID() . '_edit_assignee'
														); ?>
						</span>
						<?php
					}
				}
				if ($theIssue->canEditTexts() || ($theIssue->getPostedBy()->getID() == BUGScontext::getUser()->getID() && !BUGScontext::getUser()->isGuest()))
				{
					if (BUGScontext::getRequest()->getParameter('issue_newtitle'))
					{
						$newTitle = stripcslashes(BUGScontext::getRequest()->getParameter('issue_newtitle'));
						$theIssue->setTitle($newTitle);
						echo $theIssue->getTitle();
					}
					if (BUGScontext::getRequest()->getParameter('issue_newdescription') || BUGScontext::getRequest()->getParameter('issue_newdescription_inline'))
					{
						if (BUGScontext::getRequest()->getParameter('issue_newdescription_inline'))
						{
							$newDesc = trim(BUGScontext::getRequest()->getParameter('issue_newdescription_inline', null, false));
						}
						else
						{
							$newDesc = trim(BUGScontext::getRequest()->getParameter('issue_newdescription', null, false));
						}
						if ($newDesc != "")
						{
							$theIssue->setDescription($newDesc);
						}
						echo bugs_BBDecode($theIssue->getDescription());
					}
				}
				if ($theIssue->canEditFields())
				{
					if (BUGScontext::getRequest()->getParameter('getstatuslistaffected'))
					{
						$statusTypes = BUGSdatatype::getAll(BUGSdatatype::STATUS);
						$retval = '';
						$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
						$statusCC = 0;
						foreach ($statusTypes as $aStatus)
						{
							$aStatus = BUGSfactory::datatypeLab($aStatus, BUGSdatatype::STATUS);
							$retval .= '<tr>';
							$retval .= '<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: ' . $aStatus->getItemdata() . '; font-size: 1px; width: 13px; height: 13px;">&nbsp;</div></td>';
							$retval .= '<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="setAffectedStatus(' . BUGScontext::getRequest()->getParameter('a_id') . ', \'' . BUGScontext::getRequest()->getParameter('a_type') . '\', ' . $aStatus->getID() . ');">' . $aStatus->getName() . '</a></td>';
							$retval .= '</tr>';
							$statusCC++;
						}
						if (count($statusTypes) == 0)
						{
							$retval .= '<tr><td style="padding: 2px; color: #BBB;">' . __('There is no available status to select') . '</td></tr>';
						}
						$retval .= '</table>';
						echo $retval;
					}
					if (BUGScontext::getRequest()->getParameter('setaffectedstatus'))
					{
						$theIssue->setAffectedStatus(BUGScontext::getRequest()->getParameter('a_id'), BUGScontext::getRequest()->getParameter('a_type'), BUGScontext::getRequest()->getParameter('status'));
						$theStatus = BUGSfactory::datatypeLab(BUGScontext::getRequest()->getParameter('status'), BUGSdatatype::STATUS);
						$retval = '';
						$retval .= '<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: ' . $theStatus->getItemdata() . '; font-size: 1px; width: 13px; height: 13px;">&nbsp;</div></td>';
						$retval .= '<td>' . $theStatus->getName() . '</td>';
						$retval .= '<td style="width: 30px; text-align: right;"><a href="javascript:void(0);" onclick="Element.show(\'affected_status_' . BUGScontext::getRequest()->getParameter('a_id') . '_' . BUGScontext::getRequest()->getParameter('a_type') . '\');getAffectedStatusList(' . BUGScontext::getRequest()->getParameter('a_id') . ', \'' . BUGScontext::getRequest()->getParameter('a_type') . '\');" style="font-size: 9px;" class="image">' . image_tag('icon_switchassignee.png') . '</a></td>';
						echo $retval;
					}
					if (BUGScontext::getRequest()->getParameter('issue_new_task'))
					{
						$taskTitle = BUGScontext::getRequest()->getParameter('issue_new_task_title');
						$taskDesc = BUGScontext::getRequest()->getParameter('issue_new_task_description');
						$theIssue->addTask($taskTitle, $taskDesc);
					}
					if (BUGScontext::getRequest()->getParameter('issue_addmilestone'))
					{
						$theIssue->setMilestone(BUGScontext::getRequest()->getParameter('m_id'));
					}
					if (BUGScontext::getRequest()->getParameter('issue_removemilestone'))
					{
						$theIssue->removeMilestone(BUGScontext::getRequest()->getParameter('m_id'));
					}
					if (BUGScontext::getRequest()->getParameter('issue_addaffects'))
					{
						$anAffected = array();
						$retval = '';
						if (is_numeric(BUGScontext::getRequest()->getParameter('build')) && BUGScontext::getRequest()->getParameter('build') != 0)
						{
							$anAffected['a_id'] = $theIssue->addAffectedBuild(BUGScontext::getRequest()->getParameter('build'));
							$anAffected['build'] = BUGSfactory::buildLab(BUGScontext::getRequest()->getParameter('build'))->getName();
							require BUGScontext::getIncludePath() . 'include/issue_affected_build_menu.inc.php';
						}
						if (is_numeric(BUGScontext::getRequest()->getParameter('component')) && BUGScontext::getRequest()->getParameter('component') != 0)
						{
							$anAffected['a_id'] = $theIssue->addAffectedComponent(BUGScontext::getRequest()->getParameter('component'));
							$anAffected['component'] = BUGSfactory::componentLab(BUGScontext::getRequest()->getParameter('component'))->getName();
							require BUGScontext::getIncludePath() . 'include/issue_affected_component_menu.inc.php';
						}
						if (is_numeric(BUGScontext::getRequest()->getParameter('edition')) && BUGScontext::getRequest()->getParameter('edition') != 0)
						{
							$anAffected['a_id'] = $theIssue->addAffectedEdition(BUGScontext::getRequest()->getParameter('edition'));
							$anAffected['edition'] = BUGSfactory::editionLab(BUGScontext::getRequest()->getParameter('edition'))->getName();
							require BUGScontext::getIncludePath() . 'include/issue_affected_edition_menu.inc.php';
						}
						echo $retval;
					}
					if (BUGScontext::getRequest()->hasParameter('issue_removeaffects'))
					{
						$theIssue->removeAffects(BUGScontext::getRequest()->getParameter('issue_removeaffects'), BUGScontext::getRequest()->getParameter('issue_removeaffects_type'));
						switch(BUGScontext::getRequest()->getParameter('issue_removeaffects_type'))
						{
							case 'edition':
								if (count($theIssue->getEditions()) == 0)
								{
									echo '<script type="text/javascript">$(\'affects_no_editions_menu\').show();</script>';
								}
								break;
							case 'component':
								if (count($theIssue->getComponents()) == 0)
								{
									echo '<script type="text/javascript">$(\'affects_no_components_menu\').show();</script>';
								}
								break;
							case 'build':
								if (count($theIssue->getBuilds()) == 0)
								{
									echo '<script type="text/javascript">$(\'affects_no_builds_menu\').show();</script>';
								}
								break;
						}
					}
					if (BUGScontext::getRequest()->hasParameter('issue_setpercent'))
					{
						$theIssue->setPercentCompleted(floor(BUGScontext::getRequest()->getParameter('issue_setpercent')));
						$retval = '';
						$retval .= '<td style="font-size: 3px; width: ' . $theIssue->getPercentCompleted() . '%; height: 13px; background-color: #8C8;"><b>&nbsp;</b></td>';
						$retval .= '<td style="font-size: 3px; width: ' . (100 - $theIssue->getPercentCompleted()) . '%; height: 13px; background-color: #AFA;"><b>&nbsp;</b></td>';
						
						echo $retval;
					}
					if (BUGScontext::getRequest()->hasParameter('issue_setestimatedweeks') || BUGScontext::getRequest()->hasParameter('issue_setestimateddays') || BUGScontext::getRequest()->hasParameter('issue_setestimatedhours'))
					{
						$hrs = $theIssue->getProject()->getHoursPerDay();
						$theIssue->setEstimatedtime((($hrs * BUGScontext::getRequest()->getParameter('issue_setestimatedweeks')) * 7) + ($hrs * BUGScontext::getRequest()->getParameter('issue_setestimateddays')) + BUGScontext::getRequest()->getParameter('issue_setestimatedhours'));
						echo ($theIssue->getEstimatedTime() != 0) ? $theIssue->getFormattedTime($theIssue->getTimeDetails($theIssue->getEstimated$_SERVER["REQUEST_TIME"])) : '<div style="color: #BFBFBF;">' . __('Not determined') . '</div>';
					}
					if (BUGScontext::getRequest()->hasParameter('issue_setelapsedweeks') || BUGScontext::getRequest()->hasParameter('issue_setelapseddays') || BUGScontext::getRequest()->hasParameter('issue_setelapsedhours'))
					{
						$hrs = $theIssue->getProject()->getHoursPerDay();
						$theIssue->setElapsedtime((($hrs * BUGScontext::getRequest()->getParameter('issue_setelapsedweeks')) * 7) + ($hrs * BUGScontext::getRequest()->getParameter('issue_setelapseddays')) + BUGScontext::getRequest()->getParameter('issue_setelapsedhours'));
						echo ($theIssue->getElapsedTime() != 0) ? $theIssue->getFormattedTime($theIssue->getTimeDetails($theIssue->getElapsed$_SERVER["REQUEST_TIME"])) : '<div style="color: #BFBFBF;">' . __('Not determined') . '</div>';
					}
					if (BUGScontext::getRequest()->getParameter('getstatuslist'))
					{
						$retval = '';
						$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
		
						$statusTypes = BUGSdatatype::getAll(BUGSdatatype::STATUS);
						$statusCC = 0;
						foreach ($statusTypes as $aStatus)
						{
							if ($aStatus != $theIssue->getStatus()->getID())
							{
								$aStatus = BUGSfactory::datatypeLab($aStatus, BUGSdatatype::STATUS);
								$retval .= '<tr>';
								$retval .= '<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: ' . $aStatus->getItemdata() . '; font-size: 1px; width: 13px; height: 13px;">&nbsp;</div></td>';
								$retval .= '<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="setStatus(' . $aStatus->getID() . ');">' . $aStatus->getName() . '</a></td>';
								$retval .= '</tr>';
								$statusCC++;
							}
						}
						if ($statusCC == 0)
						{
							$retval .= '<tr><td style="padding: 2px; color: #BBB;">' . __('There is no available status to select') . '</td></tr>';
						}
		
						$retval .= '</table>';
						echo $retval;
					}
					if (BUGScontext::getRequest()->getParameter('setstatus'))
					{
						$theIssue->setStatus(BUGScontext::getRequest()->getParameter('setstatus'));
						$retval = '';
						$retval .= '<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>';
						$retval .= '<tr>';
						$retval .= '<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: ' . $theIssue->getStatus()->getColor() . '; font-size: 1px; width: 13px; height: 13px;">&nbsp;</div></td>';
						$retval .= '<td>';
						$retval .= ($theIssue->getStatus()->getID() != 0) ? $theIssue->getStatus()->getName() : '<div style="color: #AAA;>' . __('Not determined') . '</div>';
						$retval .= '</td>';
						$retval .= '</tr>';
						$retval .= '</table>';
						echo $retval;
					}
					if (BUGScontext::getRequest()->getParameter('getseverities'))
					{
						$severityTypes = BUGSdatatype::getAll(BUGSdatatype::SEVERITY);
						$retval = '';
						$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
						$sevCC = 0;
						foreach ($severityTypes as $aSev)
						{
							if ($aSev != $theIssue->getSeverity()->getID())
							{
								$aSev = BUGSfactory::datatypeLab($aSev, BUGSdatatype::SEVERITY);
								$retval .= '<tr>';
								$retval .= '<td style="width: 20px; padding: 2px;">' . image_tag('icon_severity.png') . '</td>';
								$retval .= '<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="setSeverity(' . $aSev->getID() . ');">' . $aSev->getName() . '</a></td>';
								$retval .= '</tr>';
								$sevCC++;
							}
						}
						if ($sevCC == 0)
						{
							$retval .= '<tr><td style="padding: 2px; color: #BBB;" colspan=2>' . __('There is no available severity to select') . '</td></tr>';
						}
						$retval .= '</table>';
						echo $retval;
					}
					if (BUGScontext::getRequest()->getParameter('setseverity'))
					{
						$theIssue->setSeverity(BUGScontext::getRequest()->getParameter('setseverity'));
						$retval = '';
						$retval .= ($theIssue->getSeverity()->getID() != 0) ? $theIssue->getSeverity() : '<div style="color: #BFBFBF;">' . __('Not determined') . '</div>';
						echo $retval;					
					}
					if (BUGScontext::getRequest()->getParameter('getpriorities'))
					{
						$priorityTypes = BUGSdatatype::getAll(BUGSdatatype::PRIORITY);
						$retval = '';
						$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
						$prioCC = 0;
						foreach ($priorityTypes as $aPrio)
						{
							if ($aPrio != $theIssue->getPriority()->getID())
							{
								$aPrio = BUGSfactory::datatypeLab($aPrio, BUGSdatatype::PRIORITY);
								$retval .= '<tr>';
								$retval .= '<td style="width: 20px; padding: 2px;">' . image_tag('icon_priority.png') . '</td>';
								$retval .= '<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="setPriority(' . $aPrio->getID() . ');">' . $aPrio->getItemdata() . ' - ' . $aPrio->getName() . '</a></td>';
								$retval .= '</tr>';
								$prioCC++;
							}
						}
						if ($prioCC == 0)
						{
							$retval .= '<tr><td style="padding: 2px; color: #BBB;" colspan=2>' . __('There are no available priorities to select') . '</td></tr>';
						}
						$retval .= '</table>';
						echo $retval;
					}
					if (BUGScontext::getRequest()->getParameter('setpriority'))
					{
						$theIssue->setPriority(BUGScontext::getRequest()->getParameter('setpriority'));
						$retval = '';
						$retval .= ($theIssue->getPriority()->getID() != 0) ? $theIssue->getPriority()->getItemdata() . '-' . $theIssue->getPriority()->getName() : '<div style="color: #BFBFBF;">' . __('Not determined') . '</div>';
						echo $retval;					
					}
					if (BUGScontext::getRequest()->getParameter('setblocking'))
					{
						$retval = '';
						$theIssue->setBlocking(BUGScontext::getRequest()->getParameter('setblocking'));
						if ($theIssue->isBlocking())
						{
							$retval .= '<div style="margin: 5px; margin-bottom: 0px; padding: 5px; border: 1px solid #B22; background-color: #E55; color: #FFF; font-weight: bold;">' . __('This issue is blocking the next release') . '</div>';
						}
						echo $retval;
					}
					if (BUGScontext::getRequest()->getParameter('updateblockingmenu'))
					{
						$retval = '';
						$retval .= '<a href="javascript:void(0);" onclick="setBlocking(';
						$retval .= ($theIssue->isBlocking()) ? 2 : 1;
						$retval .= ');">';
						$retval .= ($theIssue->isBlocking()) ? __('Mark as "Not blocking"') : __('Mark as "Blocking"');
						$retval .= '</a>';
						echo $retval;
					}
					if (BUGScontext::getRequest()->getParameter('getaffectedinline'))
					{
						require BUGS2_INCLUDE_PATH . 'include/issue_affected_inline.inc.php';
					}
					if (BUGScontext::getRequest()->getParameter('getaffectedbuildinline') && is_numeric(BUGScontext::getRequest()->getParameter('b_id')))
					{
						foreach ($theIssue->getBuilds() as $anAffected)
						{
							if ($anAffected['build']->getId() == BUGScontext::getRequest()->getParameter('b_id'))
							{
								require BUGScontext::getIncludePath() . 'include/issue_affected_itemline.inc.php';
								break;
							}
						}
					}
					if (BUGScontext::getRequest()->getParameter('getaffectedcomponentinline') && is_numeric(BUGScontext::getRequest()->getParameter('c_id')))
					{
						foreach ($theIssue->getComponents() as $anAffected)
						{
							if ($anAffected['component']->getId() == BUGScontext::getRequest()->getParameter('c_id'))
							{
								require BUGScontext::getIncludePath() . 'include/issue_affected_itemline.inc.php';
								break;
							}
						}
					}
					if (BUGScontext::getRequest()->getParameter('getrelatedissuesinline'))
					{
						BUGScontext::setIncludePath('');
						$issueRelations = $theIssue->getRelatedIssues();
						$retval = '';
						if (BUGScontext::getRequest()->getParameter('p_issues'))
						{
							$p_issues = $issueRelations[1];
							foreach ($p_issues as $p_issue)
							{
								$p_id = $p_issue['rel_id'];
								$p_issue = BUGSfactory::BUGSissueLab($p_issue['issue_id']);
								$p_ia = BUGSissue::hasAccess($p_issue);
								if ($p_ia['allowed'] || $p_ia['explicit'])
								{
									?>
									<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
									<tr>
									<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: <?php echo $p_issue->getStatus()->getColor(); ?>; font-size: 1px; width: 13px; height: 13px;" title="<?php echo $p_issue->getStatus()->getName(); ?>">&nbsp;</div></td>
									<td style="padding: 1px; width: auto;" valign="middle"><a href="viewissue.php?issue_no=<?php echo $p_issue->getFormattedIssueNo(true); ?>"><?php echo $p_issue->getFormattedIssueNo() . "</a> - " . $p_issue->getTitle(); ?><br></td>
									<td style="padding: 1px; width: 20px;" valign="middle"><?php echo image_tag('action_' . (($p_issue->getState() == BUGSissue::STATE_CLOSED) ? "ok" : "cancel") . '_small.png', '', __('All these issues must be fixed before the issue relation is solved')); ?></td>
									</tr>
									</table>
									<?php
								}
							}
								
							if (count($p_issues) == 0)
							{
								?>
								<div style="color: #BBB;"><?php echo __('None'); ?></div>
								<?php
							}
						}
						elseif (BUGScontext::getRequest()->getParameter('c_issues'))
						{
							$c_issues = $issueRelations[0];
						
							foreach ($c_issues as $c_issue)
							{
								$c_id = $c_issue['rel_id'];
								$c_issue = BUGSfactory::BUGSissueLab($c_issue['issue_id']);
								$c_ia = BUGSissue::hasAccess($c_issue);
								if ($c_ia['allowed'] || $c_ia['explicit'])
								{
									?>
									<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
									<tr>
									<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: <?php echo $c_issue->getStatus()->getColor(); ?>; font-size: 1px; width: 13px; height: 13px;" title="<?php echo $c_issue->getStatus()->getName(); ?>">&nbsp;</div></td>
									<td style="padding: 1px; width: auto;" valign="middle"><a href="viewissue.php?issue_no=<?php echo $c_issue->getFormattedIssueNo(true); ?>"><?php echo $c_issue->getFormattedIssueNo() . "</a> - " . $c_issue->getTitle(); ?><br></td>
									<td style="padding: 1px; width: 20px;" valign="middle"><?php echo image_tag('action_' . (($c_issue->getState() == BUGSissue::STATE_CLOSED) ? "ok" : "cancel") . '_small.png', '', __('All these issues must be fixed before the issue relation is solved')); ?></td>
									</tr>
									</table>
									<?php
								}
							}
						
							if (count($c_issues) == 0)
							{
								echo '<div style="color: #BBB;">' . __('None') . '</div>';
							}
						}
					}
					if (BUGScontext::getRequest()->getParameter('getrelatedissues'))
					{
						$issueRelations = $theIssue->getRelatedIssues();
						$retval = '';
						if (BUGScontext::getRequest()->getParameter('p_issues'))
						{
							$p_issues = $issueRelations[1];
							if (count($p_issues) == 0)
							{
								$retval .= '<div style="color: #BBB;">' . __('None') . '</div>';
							}
							else
							{
								$retval .= '<table style="table-layout: fixed; margin-top: 4px; width: 100%;" cellpadding=0 cellspacing=0>';
								foreach ($p_issues as $p_issue)
								{
									$p_id = $p_issue['rel_id'];
									$p_issue = BUGSfactory::BUGSissueLab($p_issue['issue_id']);
									$retval .= '<tr>';
									$retval .= '<td style="width: auto;"><a href="viewissue.php?issue_no=' . $p_issue->getFormattedIssueNo(true) . '">' . $p_issue->getFormattedIssueNo() . '</a>&nbsp;-&nbsp;' . $p_issue->getTitle() . '</td>';
									$retval .= '<td style="width: 50px; text-align: right;"><a href="javascript:void(0);" onclick="removeRelatedIssue(' . $p_id . ');" style="font-size: 9px;">' . __('Remove') . '</a></td>';
									$retval .= '</tr>';
								}
								$retval .= '</table>';
							}
						}
						elseif (BUGScontext::getRequest()->getParameter('c_issues'))
						{
							$c_issues = $issueRelations[0];
							if (count($c_issues) == 0)
							{
								$retval .= '<div style="color: #BBB;">' . __('None') . '</div>';
							}
							else
							{
								$retval .= '<table style="table-layout: fixed; margin-top: 4px; width: 100%;" cellpadding=0 cellspacing=0>';
								foreach ($c_issues as $c_issue)
								{
									$c_id = $c_issue['rel_id'];
									$c_issue = BUGSfactory::BUGSissueLab($c_issue['issue_id']);
									$retval .= '<tr>';
									$retval .= '<td style="width: auto;"><a href="viewissue.php?issue_no=' . $c_issue->getFormattedIssueNo(true) . '">' . $c_issue->getFormattedIssueNo() . '</a>&nbsp;-&nbsp;' . $c_issue->getTitle() . '</td>';
									$retval .= '<td style="width: 50px; text-align: right;"><a href="javascript:void(0);" onclick="removeRelatedIssue(' . $c_id . ');" style="font-size: 9px;">' . __('Remove') . '</a></td>';
									$retval .= '</tr>';
								}
								$retval .= '</table>';
							}
						}
						echo $retval;
					}
					if (BUGScontext::getRequest()->getParameter('find_issue') != "")
					{
						$retval = '';
						$retval .= '<div style="border-bottom: 1px solid #DDD; padding-bottom: 3px;"><b>' . __('Add an issue') . '</b></div>';
						$retval .= '<div style="padding: 2px;">' . __('The following issue can be added:') . '</div>';
	
						BUGScontext::getRequest()->setParameter('find_issue', str_replace('#', '', BUGScontext::getRequest()->getParameter('find_issue')));
						
						try
						{
							$f_uniqueid = BUGSissue::getIssueIDfromLink(BUGScontext::getRequest()->getParameter('find_issue'));
						}
						catch (Exception $e) {}
						if ($f_uniqueid)
						{
							try
							{
								$theIssue = BUGSfactory::BUGSissueLab($f_uniqueid);
								$retval .= '<b>' . $theIssue->getFormattedIssueNo() . '</b> - ' . $theIssue->getTitle();
								$retval .= '&nbsp;&nbsp;&nbsp;';
								if (BUGScontext::getRequest()->getParameter('this_depends') == 1)
								{
									$retval .= '<a href="javascript:void(0);" onclick="addRelatedIssue(1, ' . $theIssue->getID() . ');getRelatedIssuesSearchBox(true, false);" style="font-size: 9px;">' . __('Add this') . '</a> | <a href="javascript:void(0);" onclick="getRelatedIssuesSearchBox(true, false);" style="font-size: 9px;">' . __('Search again') . '</a>';
								}
								else
								{
									$retval .= '<a href="javascript:void(0);" onclick="addRelatedIssue(0, ' . $theIssue->getID() . ');getRelatedIssuesSearchBox(false, true);" style="font-size: 9px;">' . __('Add this') . '</a> | <a href="javascript:void(0);" onclick="getRelatedIssuesSearchBox(false, true);" style="font-size: 9px;">' . __('Search again') . '</a>';								
								}
							}
							catch (Exception $e)
							{
								if (BUGScontext::getRequest()->getParameter('this_depends') == 1)
								{
									$retval .= '<div style="color: #BBB;">' . __('None') . ' (<a href="javascript:void(0);" onclick="getRelatedIssuesSearchBox(true, false);" style="font-size: 9px;">' . __('Search again') . '</a>)</div>';
								}
								else
								{
									$retval .= '<div style="color: #BBB;">' . __('None') . ' (<a href="javascript:void(0);" onclick="getRelatedIssuesSearchBox(false, true);" style="font-size: 9px;">' . __('Search again') . '</a>)</div>';								
								}
							}
						}
						else
						{
							if (BUGScontext::getRequest()->getParameter('this_depends') == 1)
							{
								$retval .= '<div style="color: #BBB;">' . __('None') . ' (<a href="javascript:void(0);" onclick="getRelatedIssuesSearchBox(true, false);" style="font-size: 9px;">' . __('Search again') . '</a>)</div>';
							}
							else
							{
								$retval .= '<div style="color: #BBB;">' . __('None') . ' (<a href="javascript:void(0);" onclick="getRelatedIssuesSearchBox(false, true);" style="font-size: 9px;">' . __('Search again') . '</a>)</div>';								
							}
						}
						echo $retval;
					}
					if (BUGScontext::getRequest()->getParameter('getrelatedissues_searchform'))
					{
						$retval = '';
						if (BUGScontext::getRequest()->getParameter('this_depends') == 1)
						{
							$retval .= '<form accept-charset="' . BUGScontext::getI18n()->getCharset() . '" action="viewissue.php" enctype="multipart/form-data" method="post" id="issue_find_related_p" onsubmit="return false">';
						}
						else
						{
							$retval .= '<form accept-charset="' . BUGScontext::getI18n()->getCharset() . '" action="viewissue.php" enctype="multipart/form-data" method="post" id="issue_find_related_c" onsubmit="return false">';
						}
						$retval .= '<input type="hidden" name="this_depends" value=' . BUGScontext::getRequest()->getParameter('this_depends') . '>';
						$retval .= '<input type="hidden" name="issue_no" value="' . $theIssue->getFormattedIssueNo(true) . '">';
						$retval .= '<input type="hidden" name="find_dependant_issue" value="true">';
						$retval .= '<div style="border-bottom: 1px solid #DDD; padding-bottom: 3px;"><b>' . __('Add an issue') . '</b></div>';
						$retval .= '<div style="padding: 2px;">' . __('Enter the issue number for an issue you would like to add') . '</div>';
						$retval .= '<table>';
						$retval .= '<tr>';
						$retval .= '<td style="width: 60px;"><input type="text" name="find_issue" style="width: 100%;"></td>';
						if (BUGScontext::getRequest()->getParameter('this_depends') == 1)
						{
							$retval .= '<td><button onclick="findRelatedIssue(' . BUGScontext::getRequest()->getParameter('this_depends') . ', \'issue_find_related_p\', \'related_p_issues_search\');">' . __('Find') . '</button></td>';
						}
						else
						{
							$retval .= '<td><button onclick="findRelatedIssue(' . BUGScontext::getRequest()->getParameter('this_depends') . ', \'issue_find_related_c\', \'related_c_issues_search\');">' . __('Find') . '</button></td>';
						}
						$retval .= '</tr>';
						$retval .= '</table>';
						$retval .= '</form>';
						echo $retval;
					}
				}
				if (BUGScontext::getRequest()->getParameter('getissuetypes'))
				{
					$issueTypes = BUGSissuetype::getAll($theIssue->getProject()->getID(),);
					$retval = '';
					$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
		
					foreach($issueTypes as $anIssuetype)
					{
						$retval .= '<tr>';
						$retval .= '<td style="width: 20px; padding: 2px;">';
						$retval .= image_tag('icon_issuetypes.png');
						$retval .= '<td style="width: auto; padding: 2px;"><a ';
						$retval .= 'href="javascript:setIssueType(';
						$retval .= $anIssuetype->getID();
						$retval .= ')">';
						$retval .= $anIssuetype->getName();
						$retval .= '</a></td>';
						$retval .= '</tr>';
					}
					if (count($issueTypes) == 0)
					{
						$retval .= '<tr>';
						$retval .= '<td style="width: auto; padding: 2px;" colspan=2><div style="color: #BBB; padding: 2px;">' . __('There are no available issue types') . '</div></td>';
						$retval .= '</tr>';
					}
		
					$retval .= '</table>';
					echo $retval;
				}
				if (BUGScontext::getRequest()->getParameter('setissuetype'))
				{
					$theIssue->setIssuetype(BUGScontext::getRequest()->getParameter('setissuetype'));
					echo $theIssue->getIssueType()->getName();
				}
				if (BUGScontext::getRequest()->getParameter('issue_newrepro'))
				{
					$newRepro = trim(BUGScontext::getRequest()->getParameter('issue_newrepro', null, false));
					$theIssue->setReproduction($newRepro);
					if ($theIssue->getReproduction() == '')
					{
						echo '<div style="color: #BBB;">' . __('Nothing entered.') . '</div>';
					}
					else
					{
						echo bugs_BBDecode($theIssue->getReproduction());
					}
				}
				if (BUGScontext::getRequest()->getParameter('getcategories'))
				{
					$categories = BUGSdatatype::getAll(BUGSdatatype::CATEGORY);
					$retval = '';
					$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
		
					foreach($categories as $aCategory)
					{
						$aCategory = BUGSfactory::datatypeLab($aCategory, BUGSdatatype::CATEGORY);
						$retval .= '<tr>';
						$retval .= '<td style="width: 20px; padding: 2px;">';
						$retval .= image_tag('icon_issuetypes.png');
						$retval .= '<td style="width: auto; padding: 2px;"><a ';
						$retval .= 'href="javascript:setCategory(';
						$retval .= $aCategory->getID();
						$retval .= ')">';
						$retval .= $aCategory->getName();
						$retval .= '</a></td>';
						$retval .= '</tr>';
					}
					if (count($categories) == 0)
					{
						$retval .= '<tr>';
						$retval .= '<td style="width: auto; padding: 2px;" colspan=2><div style="color: #BBB; padding: 2px;">' . __('There are no available categories') . '</div></td>';
						$retval .= '</tr>';
					}
		
					$retval .= '</table>';
					echo $retval;
				}
				if (BUGScontext::getRequest()->getParameter('setcategory'))
				{
					$theIssue->setCategory(BUGScontext::getRequest()->getParameter('setcategory'));
					echo $theIssue->getCategory()->getName();
				}
				if (BUGScontext::getRequest()->getParameter('getrepros'))
				{
					$repros = BUGSdatatype::getAll(BUGSdatatype::REPRO);
					$retval = '';
					$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
		
					foreach($repros as $aRepro)
					{
						$aRepro = BUGSfactory::datatypeLab($aRepro, BUGSdatatype::REPRO);
						$retval .= '<tr>';
						$retval .= '<td style="width: 20px; padding: 2px;">';
						$retval .= image_tag('icon_issuetypes.png');
						$retval .= '<td style="width: auto; padding: 2px;"><a ';
						$retval .= 'href="javascript:setReproID(';
						$retval .= $aRepro->getID();
						$retval .= ')">';
						$retval .= $aRepro->getName();
						$retval .= '</a></td>';
						$retval .= '</tr>';
					}
					if (count($repros) == 0)
					{
						$retval .= '<tr>';
						$retval .= '<td style="width: auto; padding: 2px;" colspan=2><div style="color: #BBB; padding: 2px;">' . __('There are no reproduction types') . '</div></td>';
						$retval .= '</tr>';
					}
					$retval .= '</table>';
					echo $retval;
				}
				if (BUGScontext::getRequest()->getParameter('setreproid'))
				{
					$theIssue->setReproducability(BUGScontext::getRequest()->getParameter('setreproid'));
					echo $theIssue->getReproducability()->getName();
				}
				if (BUGScontext::getRequest()->getParameter('getresolutions'))
				{
					$resolutionTypes = BUGSdatatype::getAll(BUGSdatatype::RESOLUTION);
					$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
					$resCC = 0;
					foreach ($resolutionTypes as $aRes)
					{
						if ($aRes != $theIssue->getResolution()->getID())
						{
							$aRes = BUGSfactory::datatypeLab($aRes, BUGSdatatype::RESOLUTION);
							$retval .= '<tr>';
							$retval .= '<td style="width: 20px; padding: 2px;">' . image_tag('icon_resolution.png') . '</td>';
							$retval .= '<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="setResolution(' . $aRes->getID() . ');">' . $aRes->getName() . '</a></td>';
							$retval .= '</tr>';
							$resCC++;
						}
					}
					if ($resCC == 0)
					{
						$retval .= '<tr><td style="padding: 2px; color: #BBB;" colspan=2>' . __('There is no available resolution to select') . '</td></tr>';
					}
		
					$retval .= '</table>';
					echo $retval;
				}
				if (BUGScontext::getRequest()->getParameter('setresolution'))
				{
					$theIssue->setResolution(BUGScontext::getRequest()->getParameter('setresolution'));
					echo $theIssue->getResolution()->getName();
				}
				if (BUGScontext::getRequest()->getParameter('markasduplicate') && is_numeric(BUGScontext::getRequest()->getParameter('d_id')))
				{
					$theIssue->setDuplicateOf(BUGScontext::getRequest()->getParameter('d_id'));
					$theIssue->setState(BUGSissue::STATE_CLOSED);
					$retval = '';
					if ($theIssue->isDuplicate())
					{
						$retval .= '<div style="margin: 5px; margin-bottom: 0px; padding: 5px; border: 1px solid #DDD; background-color: #F5F5F5;"><b style="font-size: 13px;">' . image_tag('icon_info_big.png', 'align="left"');
						$retval .= __('This issue is a duplicate of issue %link_to_duplicate_issue% For more information you should visit the issue mentioned above, as this issue is not likely to be updated.', array('%link_to_duplicate_issue%' => '<a href="viewissue.php?issue_no=' . $theIssue->getDuplicateOf()->getFormattedIssueNo(true) . '"><b>' . $theIssue->getDuplicateOf()->getFormattedIssueNo() . '</b></a> - ' . $theIssue->getDuplicateOf()->getTitle() . '</b><br>'));
						$retval .= '</div>';
					}
					echo $retval;
				}
				if (BUGScontext::getRequest()->getParameter('getduplicateof'))
				{
					if ($theIssue->isDuplicate())
					{
						echo '<a href="viewissue.php?issue_no=' . $theIssue->getDuplicateOf()->getFormattedIssueNo(true) . '" target="_blank"><b>' . $theIssue->getDuplicateOf()->getFormattedIssueNo() . '</b></a> - ' . $theIssue->getDuplicateOf()->getTitle() . '&nbsp;&nbsp;(<a href="javascript:void(0);" onclick="markAsDuplicateOf(0);" style="font-size: 9px;">' . __('Remove') . '</a>)';
					}
					else
					{
						echo '<div style="color: #AAA;">' . __('This issue is not a duplicate of any other issues') . '</div>';
					}
				}
				if (BUGScontext::getRequest()->getParameter('getduplicatesearchbox'))
				{
					$retval .= '<form accept-charset="' . BUGScontext::getI18n()->getCharset() . '" action="viewissue.php" enctype="multipart/form-data" method="post" id="issue_find_duplicated_form" onsubmit="return false">';
					$retval .= '<input type="hidden" name="issue_no" value="' . $theIssue->getFormattedIssueNo(true) . '">';
					$retval .= '<input type="hidden" name="find_duplicated_issue" value="true">';
					$retval .= '<div style="border-bottom: 1px solid #DDD; padding-bottom: 3px;"><b>' . __('Mark issue as a duplicate') . '</b></div>';
					$retval .= '<div style="padding: 2px;">' . __('Enter the issue number for the issue you would like to mark this issue as a duplicate of') . '</div>';
					$retval .= '<table>';
					$retval .= '<tr>';
					$retval .= '<td style="width: 60px;"><input type="text" name="find_duped_issue" style="width: 100%;"></td>';
					$retval .= '<td><button onclick="findDuplicatedIssue();">' . __('Find') . '</button></td>';
					$retval .= '</tr>';
					$retval .= '</table>';
					$retval .= '</form>';
					echo $retval;
				}
				if (BUGScontext::getRequest()->getParameter('find_duplicated_issue'))
				{
					$retval = '';
					$retval .= '<div style="border-bottom: 1px solid #DDD; padding-bottom: 3px;"><b>' . __('Mark issue as a duplicate') . '</b></div>';
					$retval .= '<div style="padding: 2px;">' . __('The following issue was found:') . '</div>';
	
					$find_issue = str_replace('#', '', BUGScontext::getRequest()->getParameter('find_duped_issue'));
					
					$f_uniqueid = BUGSissue::getIssueIDfromLink($find_issue);
					if ($f_uniqueid != 0)
					{
						try
						{
							$theIssue = BUGSfactory::BUGSissueLab($f_uniqueid);
							$retval .= '<a href="viewissue.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '" target="_blank"><b>' . $theIssue->getFormattedIssueNo() . '</b></a> - ' . $theIssue->getTitle();
							$retval .= '&nbsp;&nbsp;&nbsp;';
							$retval .= '<a href="javascript:void(0);" onclick="markAsDuplicateOf(' . $theIssue->getID() . ')" style="font-size: 9px;">' . __('Mark as duplicate of this issue') . '</a> | <a href="javascript:void(0);" onclick="getDuplicateSearchBox();" style="font-size: 9px;">' . __('Search again') . '</a>';								
						}
						catch (Exception $e)
						{
							$retval .= '<div style="color: #BBB;">None1 (<a href="javascript:void(0);" onclick="getDuplicateSearchBox();" style="font-size: 9px;">' . __('Search again') . '</a>)</div>';								
						}
					}
					else
					{
						$retval .= '<div style="color: #BBB;">None2 (<a href="javascript:void(0);" onclick="getDuplicateSearchBox();" style="font-size: 9px;">' . __('Search again') . '</a>)</div>';								
					}
					echo $retval;
					
				}
				if (BUGScontext::getRequest()->getParameter('getbuilds'))
				{
					$retval = '';
					if ($theIssue->getProject()->isBuildsEnabled())
					{
						$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
			
						$builds = array();
						foreach ($theIssue->getEditions() as $anEdition)
						{
							$builds = array_merge($builds, $anEdition['edition']->getBuilds());
						}
						
						$bds = 0;
						foreach ($builds as $aBuild)
						{
							$alreadyHasit = false;
							foreach ($theIssue->getBuilds() as $aF)
							{
								if ($aF['build']->getID() == $aBuild->getID())
								{
									$alreadyHasit = true;
								}
							}
							if (!$alreadyHasit)
							{
								$retval .= '<tr>';
								$retval .= '<td style="width: 20px; padding: 2px;">' . image_tag('icon_build.png') . '</td>';
								$retval .= '<td style="width: auto; padding: 2px;">';
								$retval .= '<a href="javascript:void(0);" onclick="addBuild(' . $aBuild->getID() . ');';
								if (BUGScontext::getRequest()->getParameter('inline'))
								{
									$retval .= 'Effect.Fade(\'builds_table_inline_div\')';
								}
								$retval .= '">' . $aBuild . '</a>';
								$retval .= '</td>';
								$retval .= '</tr>';
								$bds++;
							}
						}
						if ($bds == 0)
						{
							$retval .= '<tr><td style="padding: 2px; color: #BBB;">' . __('There are no available builds') . '</td></tr>';
						}
						$retval .= '</table>';
					}
					else
					{
						$retval .= '<div style="padding: 2px; color: #BBB;">' . __('This project does not have builds enabled') . '</div>';
					}
					echo $retval;
				}
				if (BUGScontext::getRequest()->getParameter('getcomponents'))
				{
					$retval = '';
					$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
					
					$components = array();
					foreach ($theIssue->getEditions() as $anEdition)
					{
						$components = array_merge($components, $anEdition['edition']->getComponents());
					}
					$components = array_merge($components, $theIssue->getProject()->getComponents());
					$components = array_unique($components);
					
					$cps = 0;
					foreach ($components as $aComponent)
					{
						$alreadyHasit = false;
						foreach ($theIssue->getComponents() as $aF)
						{
							if ($aF['component']->getID() == $aComponent->getID())
							{
								$alreadyHasit = true;
							}
						}
						if (!$alreadyHasit)
						{
							$retval .= '<tr>';
							$retval .= '<td style="width: 20px; padding: 2px;">' . image_tag('icon_components.png') . '</td>';
							$retval .= '<td style="width: auto; padding: 2px;">';
							$retval .= '<a href="javascript:void(0);" onclick="addComponent(' . $aComponent->getID() . ');';
							if (BUGScontext::getRequest()->getParameter('inline'))
							{
								$retval .= 'Effect.Fade(\'components_table_inline_div\')';
							}
							$retval .= '">' . $aComponent . '</a>';
							$retval .= '</td>';
							$retval .= '</tr>';
							$cps++;
						}
					}
					if ($cps == 0)
					{
						$retval .= '<tr><td style="padding: 2px; color: #BBB;">' . __('There are no available components') . '</td></tr>';
					}
					$retval .= '</table>';
					echo $retval;
				}
				if (BUGScontext::getRequest()->getParameter('geteditionsinline'))
				{
					echo '<div style="color: #BBB;';
					echo (count($theIssue->getEditions())) ? 'display: none;' : '';
					echo '" id="affects_no_editions_inline">' . __('This issue does not affect any editions') . '</div>';
					if (count($theIssue->getEditions()) > 0)
					{
						foreach ($theIssue->getEditions() as $ea)
						{
							echo '<span id="issue_affected_edition_' . $ea['a_id'] . '_inline">' . $ea['edition'] . '<br></span>';
						}
					}
				}
				if (BUGScontext::getRequest()->getParameter('geteditions'))
				{
					$retval = '';
					$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
					
					$eds = 0;
					foreach ($theIssue->getProject()->getEditions() as $anEdition)
					{
						$alreadyHasit = false;
						foreach ($theIssue->getEditions() as $aF)
						{
							if ($aF['edition']->getID() == $anEdition->getID())
							{
								$alreadyHasit = true;
							}
						}
						if (!$alreadyHasit)
						{
							$retval .= '<tr>';
							$retval .= '<td style="width: 20px; padding: 2px;">' . image_tag('icon_edition.png') . '</td>';
							$retval .= '<td style="width: auto; padding: 2px;">';
							$retval .= '<a href="javascript:void(0);" onclick="addEdition(' . $anEdition->getID() . ');">' . $anEdition . '</a>';
							$retval .= '</td>';
							$retval .= '</tr>';
							$eds++;
						}
					}
					if ($eds == 0)
					{
						$retval .= '<tr><td style="padding: 2px; color: #BBB;">' . __('There are no available editions') . '</td></tr>';
					}
					$retval .= '</table>';
					echo $retval;
				}

				function getEditionsInMenu($theIssue)
				{
					$retval = '';
					if (count($theIssue->getEditions()) > 0)
					{
						foreach ($theIssue->getEditions() as $anAffected)
						{
							require BUGScontext::getIncludePath() . 'include/issue_affected_edition_menu.inc.php';
						}
					}
					$retval .= '<div style="padding: 2px; color: #BBB;';
					$retval .= (count($theIssue->getEditions())) ? 'display: none;' : '';
					$retval .= '" id="affects_no_editions_menu">' . __('This issue does not affect any editions') . '</div>';
					return $retval;
				}
				
				function getBuildsInMenu($theIssue)
				{
					$retval = '';
					if (count($theIssue->getBuilds()) > 0)
					{
						foreach ($theIssue->getBuilds() as $anAffected)
						{
							require BUGScontext::getIncludePath() . 'include/issue_affected_build_menu.inc.php';
						}
					}
					$retval .= '<div style="padding: 2px; color: #BBB;';
					$retval .= (count($theIssue->getBuilds())) ? 'display: none;' : '';
					$retval .= '" id="affects_no_builds_menu">' . __('This issue does not affect any builds') . '</div>';
					return $retval;
				}
				
				function getComponentsInMenu($theIssue)
				{
					$retval = '';
					if (count($theIssue->getComponents()) > 0)
					{
						foreach ($theIssue->getComponents() as $anAffected)
						{
							require BUGScontext::getIncludePath() . 'include/issue_affected_component_menu.inc.php';
						}
					}
					$retval .= '<div style="padding: 2px; color: #BBB;';
					$retval .= (count($theIssue->getComponents())) ? 'display: none;' : '';
					$retval .= '" id="affects_no_components_menu">' . __('This issue does not affect any components') . '</div>';
					return $retval;
				}
				
				if (BUGScontext::getRequest()->getParameter('getaffected'))
				{
					$retval = '';
					$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
					$retval .= '<tr>';
					$retval .= '<td><b>' . __('Editions') . '</b></td>';
					$retval .= '<td><b>' . __('Builds') . '</b></td>';
					$retval .= '<td><b>' . __('Components') . '</b></td>';
					$retval .= '</tr>';
					$retval .= '<tr>';
					$retval .= '<td valign="top" id="affected_editions_menu">';
					$retval .= getEditionsInMenu($theIssue);
					$retval .= '</td>';
					$retval .= '<td valign="top" id="affected_builds_menu">';
					$retval .= getBuildsInMenu($theIssue);
					$retval .= '</td>';
					$retval .= '<td valign="top" id="affected_components_menu">';
					$retval .= getComponentsInMenu($theIssue);
					$retval .= '</td>';
					$retval .= '</tr>';
					$retval .= '</table>';
					
					echo $retval;
				}
				
				if (BUGScontext::getRequest()->getParameter('getaffectededitions'))
				{
					echo getEditionsInMenu($theIssue);
				}

				if (BUGScontext::getRequest()->getParameter('getaffectedbuilds'))
				{
					echo getBuildsInMenu($theIssue);
				}
				
				if (BUGScontext::getRequest()->getParameter('getaffectedcomponents'))
				{
					echo getComponentsInMenu($theIssue);
				}
				
				if (BUGScontext::getRequest()->getParameter('getavailablemilestones'))
				{
					$retval = '';
					$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
					foreach ($theIssue->getProject()->getMilestones(true) as $aMilestone)
					{
						$aMilestone = BUGSfactory::milestoneLab($aMilestone['id']);
						$retval .= '<tr>';
						$retval .= '<td style="width: 20px; padding: 2px;">' .image_tag('icon_milestones.png') . '</td>';
						$retval .= '<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="addMilestone(' . $aMilestone->getID() . ');">' . $aMilestone->getName() . '</a></td>';
						$retval .= '</tr>';
					}
					if (count($theIssue->getProject()->getMilestones()) == 0)
					{
						$retval .= '<tr><td style="padding: 2px; color: #BBB;">' . __('There are no available milestones') . '</td></tr>';
					}
					$retval .= '</table>';
					echo $retval;
				}
				if (BUGScontext::getRequest()->getParameter('getassignedmilestones'))
				{
					$retval = '';
					$retval .= '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
					if ($theIssue->getMilestone() instanceof BUGSmilestone)
					{
						$retval .= '<tr>';
						$retval .= '<td style="width: 20px; padding: 2px;">' . image_tag('icon_milestones.png') . '</td>';
						$retval .= '<td style="width: auto; padding: 2px;">' . $theIssue->getMilestone()->getName() . '</td>';
						if (!BUGScontext::getRequest()->getParameter('nolink'))
						{
							$retval .= '<td style="width: 40px; padding: 2px;" valign="top"><div style="font-size: 10px; padding-top: 5px; text-align: center;"><a href="javascript:void(0);" onclick="removeMilestone(' . $aMilestone['id'] . ');">' . __('Remove') . '</a></div></td>';
						}
						$retval .= '</tr>';
					}
					else
					{
						$retval .= '<tr><td style="padding: 2px; color: #BBB;">' . __('This issue is not assigned to any milestone') . '</td></tr>';
					}
					$retval .= '</table>';
					echo $retval;
				}
				if (BUGScontext::getRequest()->getParameter('getlogentries'))
				{
					$log_entries = $theIssue->getLogEntries();
	
					BUGScontext::setIncludePath('');
					echo '<table cellpadding=0 cellspacing=0 style="width: 100%; table-layout: fixed;">';
					echo "<tr>";
					echo "<td style=\"width: 35px; padding: 2px; text-align: right; vertical-align: middle;\">";
					echo image_tag('icon_plus.png');
					echo "</td>";
					echo "<td>&nbsp;" . __('Issue registered') . "</td>";
					echo "<td style=\"width: 150px;\">";
					echo "<table cellpadding=0 cellspacing=0 style=\"width: 100%;\">";
					echo bugs_userDropdown($theIssue->getPostedBy()->getUID());
					echo "</table>";
					echo "</td>";
					echo "<td style=\"width: 70px; font-size: 9px; text-align: right;\">";
					echo bugs_formatTime($theIssue->getPosted(), 4);
					echo "</td>";
					echo "</tr>";
					foreach($log_entries as $anEntry)
					{
						$anEntry['text'] = "&nbsp;" . bugs_BBDecode($anEntry['text']);
						echo "<tr>";
						echo "<td style=\"padding: 2px; text-align: right; vertical-align: middle;\">";
						switch ($anEntry['change_type'])
						{
							case LOG_ENTRY_AFF_ADD:
							case LOG_ENTRY_AFF_UPDATE:
								echo image_tag('icon_edition.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_AFF_DELETE:
								echo image_tag('icon_edition.png');
								echo "</td>";
								echo "<td style=\"text-decoration: line-through;\">" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_CLOSE:
								echo image_tag('icon_close.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_CATEGORY:
								echo image_tag('icon_category.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_COMMENT:
								echo image_tag('comments.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_ISSUETYPE:
								echo image_tag('icon_issuetypes.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_DEPENDS:
								echo image_tag('issue_depend_add.png');
								echo "</td>";
								echo "<td style=\"text-decoration: line-through;\">" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_PCT:
								echo image_tag('icon_percent.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_PRIO:
								echo image_tag('icon_priority.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_REOPEN:
								echo image_tag('icon_reopen.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_REPRO:
								echo image_tag('icon_repro.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_RES:
								echo image_tag('icon_resolution.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_STATUS:
								echo image_tag('icon_status.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_TEAM:
								echo image_tag('icon_team.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_TIME:
								echo image_tag('icon_time.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_UPDATE:
								echo image_tag('icon_title.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_ISSUE_USERS:
								echo image_tag('icon_user.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_MILESTONE_ADD:
								echo image_tag('icon_milestones.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_MILESTONE_REMOVE:
								echo image_tag('icon_milestones.png');
								echo "</td>";
								echo "<td style=\"text-decoration: line-through;\">" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_TASK_ADD:
								echo image_tag('icon_newtask.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_TASK_DELETE:
								echo image_tag('icon_newtask.png');
								echo "</td>";
								echo "<td style=\"text-decoration: line-through;\">" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_TASK_UPDATE:
								echo image_tag('icon_title.png');
								echo image_tag('icon_newtask.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_TASK_ASSIGN_TEAM:
								echo image_tag('icon_team.png');
								echo image_tag('icon_newtask.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_TASK_ASSIGN_USER:
								echo image_tag('icon_user.png');
								echo image_tag('icon_newtask.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_TASK_STATUS:
								echo image_tag('icon_status.png');
								echo image_tag('icon_newtask.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_TASK_COMPLETED:
								echo image_tag('icon_close.png');
								echo image_tag('icon_newtask.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
							case LOG_ENTRY_TASK_REOPENED:
								echo image_tag('icon_reopen.png');
								echo image_tag('icon_newtask.png');
								echo "</td>";
								echo "<td>" . $anEntry['text'] . "</td>";
								break;
						}
						echo "<td style=\"text-align: left;\">";
						if ($anEntry['uid'] != 0)
						{
							echo "<table cellpadding=0 cellspacing=0 style=\"width: 100%;\">";
							echo bugs_userDropdown($anEntry['uid']);
							echo "</table>";
						}
						else
						{
							echo "<div style=\"color: #BBB; padding-left: 5px;\">" . __('System') . "</div>";
						}
						echo "</td>";
						echo "<td style=\"width: 70px; font-size: 9px; text-align: right;\">";
						echo bugs_formatTime($anEntry['time'], 4);
						echo "</td>";
						echo "</tr>";
					}
					echo '</table>';
				}
			}
			
			if (BUGScontext::getRequest()->isAjaxCall())
			{
				exit();
			}
			
			$issueRelations = $theIssue->getRelatedIssues();
			
			$p_issues = $issueRelations[1];
			$c_issues = $issueRelations[0];
			
			$theTime_estim = $theIssue->getTimeDetails($theIssue->getEstimatedTime());
			$theTime_elap = $theIssue->getTimeDetails($theIssue->getElapsedTime());
			
		}
	}
	
?>
