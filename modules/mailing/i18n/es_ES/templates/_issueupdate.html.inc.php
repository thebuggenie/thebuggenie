<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #333;">
    Buen d&iacute;a %user_buddyname%,<br>
    <?php echo $issue->getIssuetype()->getName(); ?> <?php echo $issue->getFormattedTitle(true); ?> ha sido actualizada por <?php echo $updated_by->getName(); ?>.<br>
    <br>
    <?php echo tbg_parse_text($comment); ?>
    <br>
    <div style="color: #888;">
        --
        <br>
        Ver el problema : <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false)); ?><br>
        Ver el panel de control del proyecto <?php echo $issue->getProject()->getName(); ?> : <?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false)); ?>
    </div>
</div>
