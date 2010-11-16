<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #333;">
	Bonjour %user_buddyname%,<br>
	La demande suivante a &eacute;t&eacute; cr&eacute;er par <?php echo $issue->getPostedBy()->getName(); ?> :<br>
	<?php echo $issue->getIssuetype()->getName(); ?> <?php echo $issue->getFormattedTitle(true); ?><br>
	<br>
	<b>La demande a &eacute;t&eacute; cr&eacute;er avec la description suivante :</b><br>
	<?php echo tbg_parse_text($issue->getDescription()); ?><br>
	<br>
	<br>
	<div style="color: #888;">
		--
		<br>
		Afficher la demande : <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false)); ?><br>
		Afficher le tableau de bord du projet <?php echo $issue->getProject()->getName(); ?> : <?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false)); ?>
	</div>
</div>