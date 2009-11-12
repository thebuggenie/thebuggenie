Hi, %user_buddyname%!
This email is to notify you that issue <?php echo $issue->getFormattedIssueNo() . ' - ' . $issue->getTitle(); ?> has been created.

* The issue was created with the following description *
<?php echo bugs_BBDecode($issue->getDescription()); ?>
---
You can open the issue by clicking the following link:
<?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo(true))); ?>