* <?php echo $issue->getFormattedTitle(true); ?> *
<?php echo __('Created by %name', array('%name' =>  $issue->getPostedBy()->getNameWithUsername())); ?>


* <?php echo __('Comment by %name', array('%name' => $comment->getPostedBy()->getNameWithUsername())); ?> *
<?php echo $comment->getContent(); ?>


<?php echo __('Show issue:') . ' ' . $module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>

<?php echo __('Show comment:') . ' ' . $module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())) . '#comment_' . $comment->getID();?>

<?php echo __('Show %project project dashboard:', array('%project' => $issue->getProject()->getName())) . ' ' .  $module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey())); ?>


<?php echo __('You were sent this notification email because you are related to the issue mentioned in this email.'); ?>

<?php echo __('To change when and how often we send these emails, update your account settings:') . ' ' . $module->generateURL('account'); ?>

