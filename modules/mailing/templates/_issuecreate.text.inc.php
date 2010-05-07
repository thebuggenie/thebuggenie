Hi, %user_buddyname%!
The following issue was created by <?php echo $issue->getPostedBy()->getName(); ?>:
<?php echo $issue->getFormattedIssueNo() . ' - ' . $issue->getTitle(); ?>

* The issue was created with the following description *
<?php echo tbg_parse_text($issue->getDescription()); ?>
---
You can open the issue by clicking the following link:
<?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo(true))); ?>
