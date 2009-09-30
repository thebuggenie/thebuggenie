<?php

	if (!$posted_issue instanceof BUGSissue)
	{
		die();
	}

?>
<div style="padding: 5px;">
<table style="width: 950px;" cellpadding=0 cellspacing=0 align="left">
<tr>
<td style="border: 1px dotted #DDD; padding: 1px;">
<div style="width: auto; padding: 2px; border-bottom: 1px solid #DDD; background-color: #F5F5F5;"><b><?php echo __('"Report an issue"-wizard'); ?></b></div>
<div style="padding: 3px;">
<?php echo __('Your issue has been filed, and a developer will attend to it at first available opportunity.'); ?><br>
<br>
<b><?php echo __('You can view your issue here:'); ?></b><br>
<div style="font-size: 14px;"><a href="viewissue.php?issue_no=<?php print $posted_issue->getFormattedIssueNo(true); ?>"><?php print $posted_issue->getFormattedIssueNo(); ?>&nbsp;-&nbsp;<b><?php print stripcslashes($posted_issue->getTitle()); ?></b></a></div>
<br>
<?php echo __('You can also right-click on the link above if you want to save it as a bookmark.'); ?><br>
<?php
						
   if (BUGScontext::getUser()->addUserIssue($posted_issue->getID()))
   {
	   ?><?php echo __('This issue has also be added to your list of monitored issues.'); ?><br><?php
   }

?>
<br>
<?php echo __('You can choose to'); ?> <a href="index.php"><?php echo __('go back to the frontpage'); ?></a>, <?php echo __('or feel free to'); ?> <a href="reportissue.php"><?php echo __('report another issue'); ?></a>.
<br>
</tr>
</table>
</div>