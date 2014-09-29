<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #333;">
    Buen d&iacute;a %user_buddyname%,<br>
    El siguiente pedido ha sido registrado por <?php echo $issue->getPostedBy()->getName(); ?> :<br>
    <?php echo $issue->getIssuetype()->getName(); ?> <?php echo $issue->getFormattedTitle(true); ?><br>
    <br>
    <b>El pedido ha sido creado con la siguiente descripci&oacute;n :</b><br>
    <?php echo tbg_parse_text($issue->getDescription()); ?><br>
    <br>
    <br>
    <div style="color: #888;">
        --
        <br>
        Ver el pedido: <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false)); ?><br>
        Ver el panel de control del proyecto <?php echo $issue->getProject()->getName(); ?> : <?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false)); ?>
    </div>
</div>
