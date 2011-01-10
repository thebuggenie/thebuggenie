<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #333;">
	Bonjour %user_buddyname%,<br>
	<?php echo $issue->getIssuetype()->getName(); ?> <?php echo $issue->getFormattedTitle(true); ?> a &eacute;t&eacute; mise &agrave; jour par <?php echo $updated_by->getName(); ?>.<br>
	<br>
	<b>Les informations suivantes ont &eacute;t&eacute; modifi&eacute;es :</b><br>
	<ul>
	<?php foreach ($comment_lines as $comment_line): ?>
		<li><?php echo $comment_line; ?></li>
	<?php endforeach; ?>
	</ul>
	<br>
	<div style="color: #888;">
		--
		<br>
		Affiche la demande : <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false)); ?><br>
		Affiche le tableau de bord du projet <?php echo $issue->getProject()->getName(); ?> : <?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false)); ?>
	</div>
</div>