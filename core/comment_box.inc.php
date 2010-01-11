<?php

	if (!$aComment instanceof TBGComment)
	{
		exit();
	}

	$notFiltered = true;
	$theTitle = ($aComment->getTitle() != "") ? $aComment->getTitle() : substr($aComment->getContent(), 0, 45);
	$viewuser_string = "window.open('" . TBGContext::getTBGPath() . "viewuser.php?uid=" . $aComment->getPostedBy()->getUname() . "','mywindow','menubar=0,toolbar=0,location=0,status=0,scrollbars=0,width=600,height=400');";
	if ($canEditComments && !$aComment->isSystemComment() && !$doFilterUserComments)
	{
		?>
		<table style="width: 100%;" cellpadding=0 cellspacing=0 id="commentheader_<?php echo $aComment->getID(); ?>">
		<tr>
		<td class="commentheader" style="padding: 2px; width: 30px; text-align: center;"><div style="padding: 2px; background-color: #FFF; border: 1px solid #DDD;"><?php echo image_tag('avatars/' . $aComment->getPostedBy()->getAvatar() . '_small.png', '', '', '', 0, 0, 0, true); ?></div></td>
		<td style="width: auto;">
		<?php $commentusername = '<a href="javascript:void(0);" onclick="' . $viewuser_string . '"><b>' . $aComment->getPostedBy()->getBuddyname() . '</b></a>&nbsp;(' . $aComment->getPostedBy()->getUname() . '), &nbsp;'; ?>
		<div class="commentheader"><?php echo $theTitle; ?><br>
		<?php echo ($aComment->isSystemComment()) ? __('Posted automatically on behalf of %username%', array('%username%' => $commentusername)) : __('Posted by %username%', array('%username%' => $commentusername));

		echo tbg_formatTime($aComment->getUpdated(), 12);
		
		if ($aComment->getUpdated() != $aComment->getPosted())
		{
			echo ', ' . __('edited by %username%', array('%username%' => '<b>' . $aComment->getUpdatedBy()->getBuddyname() . '</b>')); ?>&nbsp;(<?php echo $aComment->getUpdatedBy()->getUname(); ?>), &nbsp;<?php echo tbg_formatTime($aComment->getUpdated(), 12);
		}
	
		?></div>
		</td>
		<td style="width: 20px;" valign="middle" class="commentheader" style="text-align: right;"><a class="image" href="javascript:void(0);" onclick="editComment(<?php echo $aComment->getID(); ?>, '<?php echo $aComment->getModuleName(); ?>', '<?php echo $aComment->getTargetType(); ?>', '<?php echo $aComment->getTargetID(); ?>');"><?php echo image_tag('icon_edit.png'); ?></a></td>
		<td style="width: 20px;" valign="middle" class="commentheader" style="text-align: right;"><a class="image" href="javascript:void(0);" onclick="Effect.Appear('delete_comment_<?php echo $aComment->getID(); ?>', { duration: 0.5 });"><?php echo image_tag('icon_comment_delete.png'); ?></a><br>
		<div style="position: relative;">
			<div id="delete_comment_<?php echo $aComment->getID(); ?>" style="border: 1px solid #E5E5E5; width: 150px; padding: 5px; background-color: #FFF; display: none; margin-top: 5px; position: absolute; right: 30px; top: -30px;"><?php echo __('Please confirm that you want to delete this comment'); ?>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteComment(<?php echo $aComment->getID(); ?>, '<?php echo $aComment->getModuleName(); ?>', '<?php echo $aComment->getTargetType(); ?>', '<?php echo $aComment->getTargetID(); ?>');"><?php echo __('Yes'); ?></a>&nbsp;|&nbsp;<a href="javascript:void(0)" onclick="Effect.Fade('delete_comment_<?php echo $aComment->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a></div>
		</div>
		</td>
		</tr>
		</table>
		<?php
	}
	elseif (($aComment->isSystemComment() && !$doFilterSystemComments) || (!$aComment->isSystemComment() && !$doFilterUserComments))
	{
		?>
		<table style="width: 100%;" cellpadding=0 cellspacing=0 id="commentheader_<?php echo $aComment->getID(); ?>">
		<tr>
		<td class="commentheader" style="padding: 2px; width: 30px; text-align: center;"><div style="padding: 2px; background-color: #FFF; border: 1px solid #DDD;"><?php echo image_tag('avatars/' . $aComment->getPostedBy()->getAvatar() . '_small.png', '', '', '', 0, 0, 0, true); ?></div></td>
		<td style="width: auto;">
		<?php $commentusername = '<a href="javascript:void(0);" onclick="' . $viewuser_string . '"><b>' . $aComment->getPostedBy()->getBuddyname() . '</b></a>&nbsp;(' . $aComment->getPostedBy()->getUname() . '), &nbsp;'; ?>
		<div class="commentheader"><?php echo $theTitle; ?><br>
		<?php echo ($aComment->isSystemComment()) ? __('Posted automatically on behalf of %username%', array('%username%' => $commentusername)) : __('Posted by %username%', array('%username%' => $commentusername));
		
		echo tbg_formatTime($aComment->getUpdated(), 12);
		
		if ($aComment->getUpdated() != $aComment->getPosted())
		{
			echo ', ' . __('edited by %username%', array('%username%' => '<b>' . $aComment->getUpdatedBy()->getBuddyname() . '</b>')); ?>&nbsp;(<?php echo $aComment->getUpdatedBy()->getUname(); ?>), &nbsp;<?php echo tbg_formatTime($aComment->getUpdated(), 12);
		}
			
		?></div>
		</td>
		<?php
	
			if ($canEditComments)
			{
				?>
				<td style="width: 20px;" valign="middle" class="commentheader" style="text-align: right;"><a class="image" href="javascript:void(0);" onclick="Effect.Appear('delete_comment_<?php echo $aComment->getID(); ?>', { duration: 0.5 })"><?php echo image_tag('icon_comment_delete.png'); ?></a><br>
				<div style="position: relative;">
				<div id="delete_comment_<?php echo $aComment->getID(); ?>" style="border: 1px solid #E5E5E5; width: 150px; padding: 5px; background-color: #FFF; display: none; margin-top: 5px; position: absolute; right: 30px; top: -30px;"><?php echo __('Please confirm that you want to delete this comment'); ?>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteComment(<?php echo $aComment->getID(); ?>, '<?php echo $aComment->getModuleName(); ?>', '<?php echo $aComment->getTargetType(); ?>', '<?php echo $aComment->getTargetID(); ?>');"><?php echo __('Yes'); ?></a>&nbsp;|&nbsp;<a href="javascript:void(0)" onclick="Effect.Fade('delete_comment_<?php echo $aComment->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a></div>
				</div>
				</td>
				<?php
			}
	
		?>
		</tr>
		</table>
		<?php
	}
	else
	{
		$notFiltered = false;
		$filteredComments++;
	}
	if ($notFiltered)
	{
		?>
		<div class="comment" id="commentbody_<?php echo $aComment->getID(); ?>"><?php echo tbg_BBDecode($aComment->getContent(), true); ?></div>
		<div id="deletedcomment_<?php echo $aComment->getID(); ?>" style="display: none; text-align: left; background-color: #F1F1F1; padding: 3px; border: 1px solid #E5E5E5; border-top: 1px solid #FFF;"><b><?php echo __('The comment was deleted'); ?></b>&nbsp;&nbsp;[<a href="javascript:void(0);" onclick="Effect.Fade('deletedcomment_<?php echo $aComment->getID(); ?>');"><?php echo __('OK'); ?></a>]</div>
		<?php
	}						

?>
