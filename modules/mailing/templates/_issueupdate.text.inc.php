Hi, %user_buddyname%!
<?php echo $issue->getFormattedTitle(true); ?> was updated by <?php echo $updated_by->getName(); ?>.

<?php echo $comment; ?>

---
Show issue: <?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false); ?>
Show <?php echo $issue->getProject()->getName(); ?> project dashboard: <?php echo make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false); ?>