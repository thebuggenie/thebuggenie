<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #646464;">
	Hi, %user_buddyname%!<br>
	This email is to notify you that issue <?php echo $issue->getFormattedIssueNo() . ' - ' . $issue->getTitle(); ?> has been created.<br>
	<br>
	<b>The issue was created with the following description:</b><br>
	<?php echo tbg_parse_text($issue->getDescription()); ?><br>
	<br>
	You can open the issue by clicking the following link:<br>
	<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo(true)))); ?>
</div>
