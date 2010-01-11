<?php

	if (!($theTask instanceof TBGTask && $theIssue instanceof TBGIssue))
	{
		exit();
	}
	if ($include_table)
	{
		echo '<span id="issuetask_' . $theTask->getID() . '">';
	}
	
?>
<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0 id="taskslist">
<tr>
<?php

if ($theIssue->canEditTexts())
{
	?>
	<td class="issuedetailscontentsleft" style="width: 25px;"><a href="javascript:void(0);" onclick="Effect.Appear('edit_task_<?php echo $theTask->getID(); ?>_details', { duration: 0.5 });" class="image"><?php echo image_tag('icon_title_small.png'); ?></a>
	<br>
	<div id="edit_task_<?php echo $theTask->getID(); ?>_details" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 500px; display: none; background-color: #F1F1F1;">
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="viewissue.php" enctype="multipart/form-data" method="post" id="edit_task_<?php echo $theTask->getID(); ?>_form" onsubmit="return false">
	<input type="hidden" name="issue_no" value="<?php echo $theIssue->getFormattedIssueNo(true); ?>">
	<input type="hidden" name="issue_update_task" value="1">
	<input type="hidden" name="t_id" value="<?php echo $theTask->getID(); ?>">
	<div style="border-bottom: 1px solid #DDD; padding: 2px;"><b><?php echo __('Edit task'); ?></b></div>
	<div style="padding: 3px;"><?php echo __('Enter a new title and a new description, then press "Update" to update the task.'); ?></div>
	<input type="text" name="task_new_title" value="<?php echo $theTask->getTitle(); ?>" style="width: 100%;"><br>
	<?php echo bugs_newTextArea('task_new_content', '80px', '100%', $theTask->getContent()) ?>
	<div style="padding: 3px; padding-right: 0px; text-align: right;"><button style="width: 70px;" onclick="updateTask(<?php echo $theTask->getID(); ?>);"><?php echo __('Update'); ?></button></div>
	</form>
	<div style="border-bottom: 1px solid #DDD; padding: 2px;"><b><?php echo __('Promote task'); ?></b></div>
	<div style="padding: 3px;"><?php echo __('If you want to promote this task to a separate issue report, select it below. The task will then be converted from a task to an issue report, which will be attached to this issue report.'); ?></div>
	<div style="padding: 3px; padding-right: 0px; text-align: right;"><a href="javascript:void(0);" onclick="Effect.Appear('promote_task_<?php echo $theTask->getID(); ?>', { duration: 0.5 });"><b><?php echo __('Promote this task'); ?></b></a></div>
	<div style="padding: 3px; text-align: right; display: none;" id="promote_task_<?php echo $theTask->getID(); ?>"><a href="javascript:void(0);" onclick="promoteTask(<?php echo $theTask->getID(); ?>);"><?php echo __('Yes, promote it'); ?></a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="Effect.Fade('promote_task_<?php echo $theTask->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No, never mind'); ?></b></a></div>
	<div style="border-bottom: 1px solid #DDD; padding: 2px;"><b><?php echo __('Delete task'); ?></b></div>
	<div style="padding: 3px;"><?php echo __('If you want to delete this task, confirm it below.'); ?></div>
	<div style="padding: 3px; padding-right: 0px; text-align: right;"><a href="javascript:void(0);" onclick="Effect.Appear('delete_task_<?php echo $theTask->getID(); ?>', { duration: 0.5 });"><b><?php echo __('Delete this task'); ?></b></a></div>
	<div style="padding: 3px; text-align: right; display: none;" id="delete_task_<?php echo $theTask->getID(); ?>"><a href="javascript:void(0);" onclick="deleteTask(<?php echo $theTask->getID(); ?>);Effect.Fade('edit_task_<?php echo $theTask->getID(); ?>_details', { duration: 0.5 });"><?php echo __('Yes, delete it'); ?></a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="Effect.Fade('delete_task_<?php echo $theTask->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No, never mind'); ?></b></a></div>
	<div style="padding: 3px; padding-right: 0px; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Effect.Fade('edit_task_<?php echo $theTask->getID(); ?>_details', { duration: 0.5 });">Close menu</a></div>
	</div>
	</td>
	<td class="issuedetailscontentscenter" style="padding-left: 0px;" id="task_<?php echo $theTask->getID(); ?>_title">
	<?php

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

	?></td>
	<?php
}
else
{
	?>
	<td class="issuedetailscontentsleft" style="width: 0px;">&nbsp;</td>
	<td class="issuedetailscontentscenter" style="width: auto;"><?php

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

	?></td>
	<?php
}
?>
<td class="issuedetailscontentscenter" id="task_<?php echo $theTask->getID(); ?>_assignee" style="width: 150px;"><?php

	if ($theTask->getAssignedType())
	{
		?><table style="width: 100%;" cellpadding=0 cellspacing=0><?php
		if ($theTask->getAssignedType() == TBGIdentifiableClass::TYPE_USER)
		{
			if ($theIssue->canEditUsers())
			{
				$thetr = bugs_userDropdown($theTask->getAssignee()->getID(), 1);
				echo $thetr[0];
				?><td style="width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.Appear('task_<?php echo $theTask->getID(); ?>_edit_assignee', { duration: 0.5 });"><?php echo image_tag('icon_switchassignee.png'); ?></a></td></tr><?php
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
				?><td style="width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.Appear('task_<?php echo $theTask->getID(); ?>_edit_assignee', { duration: 0.5 });"><?php echo image_tag('icon_switchassignee.png'); ?></a></td></tr><?php
				echo $thetr[1];
			}
			else
			{
				echo bugs_teamDropdown($theTask->getAssignee()->getID());
			}
		}
		?>
		</table><?php
	}
	else
	{
		if ($theIssue->canEditUsers())
		{
			?><div style="padding: 4px;"><a href="javascript:void(0);" onclick="Effect.Appear('task_<?php echo $theTask->getID(); ?>_edit_assignee', { duration: 0.5 });"><?php echo __('Assign this task'); ?></a></div><?php
		}
		else
		{
			echo __('Nobody');
		}
	}
	?>
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
</td>
<td class="issuedetailscontentscenter" style="font-size: 10px; width: 80px;" id="task_<?php echo $theTask->getID(); ?>_lastupdated"><?php echo bugs_formatTime($theTask->getUpdated(), 4); ?></td>
<td class="issuedetailscontentscenter" style="width: 190px;">
	<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
	<tr id="task_status_inline_<?php echo $theTask->getID(); ?>">
	<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: <?php echo $theTask->getStatus()->getColor(); ?>; font-size: 1px; width: 13px; height: 13px;">&nbsp;</div></td>
	<td><?php echo $theTask->getStatus()->getName(); ?></td>
	<td style="width: 30px; text-align: right;">
	<?php
	if($theIssue->canEditTexts() ||
		($theIssue->getPostedBy()->getID() == TBGContext::getUser()->getID() && !TBGContext::getUser()->isGuest()) ||
		($theTask->getAssignedType() == TBGIdentifiableClass::TYPE_USER && $theTask->getAssignee()->getID() == TBGContext::getUser()->getID() && !TBGContext::getUser()->isGuest()) || 
		($theTask->getAssignedType() == TBGIdentifiableClass::TYPE_TEAM && TBGContext::getUser()->isMemberOf($theTask->getAssignee()->getID()) === true && !TBGContext::getUser()->isGuest()))
	{
		?><a href="javascript:void(0);" onclick="Effect.Appear('task_status_<?php echo $theTask->getID(); ?>', { duration: 0.5 });getTaskStatusList(<?php echo $theTask->getID(); ?>);" style="font-size: 9px;" class="image"><?php echo image_tag('icon_switchassignee.png'); ?></a><?php
	}
	?></td>
	</tr>
	</table>
	<div id="task_status_<?php echo $theTask->getID(); ?>" style="position: absolute; right: 10px; padding: 5px; border: 1px solid #DDD; width: 250px; display: none; background-color: #FFF;">
	<div style="text-align: left; padding-bottom: 5px;"><b><?php echo __('Change status'); ?></b><br><?php echo __('Select the status of this task, from the list below.'); ?></div>
	<span id="task_status_list_<?php echo $theTask->getID(); ?>"><?php echo __('Please wait, loading list'); ?> ...</span>
	<div style="width: auto; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Effect.Fade('task_status_<?php echo $theTask->getID(); ?>', { duration: 0.5 });"><?php echo __('Close menu'); ?></a></div>
	</div>
</td>
<td class="issuedetailscontentsright" style="text-align: center; width: 60px;" id="task_closed_<?php echo $theTask->getID(); ?>">
<?php

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

?>
</td>
</tr>
<tr id="task_<?php echo $theTask->getID(); ?>" style="display: none;">
<td colspan=6 class="issuedetailscontentsleft" style="border-bottom: 1px solid #DDD;" id="task_<?php echo $theTask->getID(); ?>_description">
<?php echo bugs_BBDecode($theTask->getContent()); ?>
<div style="font-size: 10px; text-align: left;"><a href="javascript:void(0);" onclick="Element.hide('task_<?php echo $theTask->getID(); ?>');"><?php echo __('Hide description'); ?></a></div>
</td>
</tr>
</table>
<?php

	if ($include_table)
	{
		echo '</span>';
	}

?>
