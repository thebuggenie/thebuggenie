Buen d&iacute;a %user_buddyname%,<br>
El siguiente pedido ha sido creado por <?php echo $issue->getPostedBy()->getName(); ?> :
<?php echo $issue->getIssuetype()->getName(); ?> <?php echo $issue->getFormattedTitle(true); ?>
    
* El pedido ha sido creado con la siguiente descripci&oacute;n *
<?php echo tbg_parse_text($issue->getDescription()); ?>
    
--
Ver el pedido : <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false)); ?>
Ver el panel de control del proyecto <?php echo $issue->getProject()->getName(); ?> : <?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false)); ?>
