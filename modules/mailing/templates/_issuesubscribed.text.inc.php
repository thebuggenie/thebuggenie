* <?php echo $issue->getFormattedTitle(true); ?> *
<?php echo __('Created by %name', array('%name' => $issue->getPostedBy()->getNameWithUsername()))."\n"; ?>

<?php echo __('You have been subscribed to this issue and will be notified if and when it changes in the future.')."\n"; ?>
<?php echo __('To unsubscribe from this issue, open the issue in your web browser and click the "star" icon in the top left corner, next to the issue title.')."\n"; ?>

<?php echo __('Show issue:') . ' ' . $module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>

<?php echo __('Show %project project dashboard:', array('%project' => $issue->getProject()->getName())) . ' ' . $module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey())); ?>


<?php echo __('You were sent this notification email because you are related to, subscribed to, or commented on the issue mentioned in this email.'); ?>

<?php echo __('Depending on your notification settings, you may or may not be notified again when this issue is updated in the future.'); ?>

<?php echo __('To change when and how often we send these emails, update your account settings:') . ' ' . $module->generateURL('account'); ?>
