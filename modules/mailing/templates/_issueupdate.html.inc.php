<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #333;">
	Hi, %user_buddyname%!<br>
	<?php echo $issue->getFormattedTitle(true); ?> was updated by <?php echo $updated_by->getName(); ?>.<br>
	<br>
	<?php echo tbg_parse_text($comment); ?>
	<br>
	<div style="color: #888;">
	<br>
	Show issue: <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false)); ?><br>
	Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false)); ?>
	</div>
</div>