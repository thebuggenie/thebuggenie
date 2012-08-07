Hi, %user_buddyname%!
A comment has been added to <?php echo TBGContext::getI18n()->__($issue->getIssueType()->getName()); ?> <?php echo $issue->getFormattedIssueNo(true); ?> - <?php echo $issue->getTitle(); ?> by <?php echo $comment->getPostedBy()->getName(); ?>:

<?php echo tbg_parse_text($comment->getContent()); ?>

Show issue: <?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false); ?>
Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false); ?>