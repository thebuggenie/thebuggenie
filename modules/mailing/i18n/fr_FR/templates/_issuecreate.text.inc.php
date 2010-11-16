Bonjour %user_buddyname%,<br>
La demande suivante a &eacute;t&eacute; cr&eacute;er par <?php echo $issue->getPostedBy()->getName(); ?> :
<?php echo $issue->getIssuetype()->getName(); ?> <?php echo $issue->getFormattedTitle(true); ?>
	
* La demande a &eacute;t&eacute; cr&eacute;er avec la description suivante *
<?php echo tbg_parse_text($issue->getDescription()); ?>
	
--
Afficher la demande : <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false)); ?>
Afficher le tableau de bord du projet <?php echo $issue->getProject()->getName(); ?> : <?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false)); ?>