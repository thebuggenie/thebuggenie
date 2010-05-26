<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #333;">
	Hi, %user_buddyname%!<br>
	The following issue was created by <?php echo $issue->getPostedBy()->getName(); ?>:<br>
	<?php echo $issue->getIssuetype()->getName(); ?> <?php echo $issue->getFormattedTitle(true); ?><br>
	<br>
	<b>The issue was created with the following description:</b><br>
	<?php echo tbg_parse_text($issue->getDescription()); ?><br>
	<br>
	<br>
	<div style="color: #888;">
		--
		<br>
		Show issue: <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false)); ?><br>
		Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false)); ?>
	</div>
</div>