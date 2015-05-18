<?php if ($issue instanceof \thebuggenie\core\entities\Issue): ?>
    <h3>
        <?php echo $issue->getFormattedTitle(true); ?><br>
        <span style="font-size: 0.8em; font-weight: normal;">Created by <?php echo $issue->getPostedBy()->getNameWithUsername(); ?></span>
    </h3>
    You have been subscribed to this issue and will be notified if and when it changes in the future.<br>
    To unsubscribe from this issue, open the issue in your web browser and click the "star" icon in the top left corner, next to the issue title.
    <br>
    <br>
    <div style="color: #888;">
        Show issue: <?php echo link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()))); ?><br>
        Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo link_tag($module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey()))); ?><br>
        <br>
        You were sent this notification email because you are related to, subscribed to, or commented on the issue mentioned in this email.<br>
        Depending on your notification settings, you may or may not be notified again when this issue is updated in the future.<br>
        To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
    </div>
<?php endif; ?>