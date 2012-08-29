* <?php echo $issue->getFormattedTitle(true); ?> *
Created by <?php echo $issue->getPostedBy()->getBuddyname(); ?> (<?php echo $issue->getPostedBy()->getUsername(); ?>)

* Comment by <?php echo $comment->getPostedBy()->getBuddyname(); ?> (<?php echo $comment->getPostedBy()->getUsername(); ?>) *
<?php echo tbg_parse_text($comment->getContent()); ?>

Show issue: <?php echo $module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>
Show comment: <?php echo link_tag($module->generateURL('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())).'#comment_'.$comment->getID()); ?>
Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo $module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey())); ?>

You were sent this notification email because you are related to the issue mentioned in this email.
To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>