* <?php echo $issue->getFormattedTitle(true); ?> *
Created by <?php echo $issue->getPostedBy()->getNameWithUsername(); ?>

You have been subscribed to this issue and will be notified if and when it changes in the future.
To unsubscribe from this issue, open the issue in your web browser and click the "star" icon in the top left corner, next to the issue title.


Show issue: <?php echo $module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>
Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo $module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey())); ?>

You were sent this notification email because you are related to, subscribed to, or commented on the issue mentioned in this email.
Depending on your notification settings, you may or may not be notified again when this issue is updated in the future.
To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>