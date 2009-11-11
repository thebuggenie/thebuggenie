<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #646464;">
	Hi, %user_buddyname%!<br>
	This email is to notify you that issue <?php echo $issue->getFormattedIssueNo() . ' - ' . $theIssue->getTitle(); ?> has been created.<br>
	<br>
	<b>The issue was created with the following description:</b><br>
	<?php echo bugs_BBDecode($theIssue->getDescription()); ?><br>
	<br>
	You can open the issue by clicking the following link:<br>
	<?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(true)))); ?>
</div>