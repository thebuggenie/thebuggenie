Hi, %user_buddyname%!
<?php echo $issue->getIssuetype()->getName(); ?> <?php echo $issue->getFormattedTitle(true); ?> was updated by <?php echo $updated_by->getName(); ?>.

* The following details were changed: *
<?php foreach ($comment_lines as $comment_line): ?>
    <?php echo $comment_line; ?>
<?php endforeach; ?>

---
Show issue: <?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false); ?>
Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false); ?>