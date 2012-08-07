Hi, %user_buddyname%!
The following issue was created by <?php echo $issue->getPostedBy()->getName(); ?>:
<?php echo $issue->getIssuetype()->getName(); ?> <?php echo $issue->getFormattedTitle(true); ?>

* The issue was created with the following description *
<?php echo tbg_parse_text($issue->getDescription()); ?>

Show issue: <?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false); ?>
Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false); ?>