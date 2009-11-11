Hi, %user_buddyname%!
This email is to notify you that issue <?php echo $issue->getFormattedIssueNo() . ' - ' . $theIssue->getTitle(); ?> has been created.

* The issue was created with the following description *
<?php echo bugs_BBDecode($theIssue->getDescription()); ?>
---
You can open the issue by clicking the following link:
<?php echo make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(true))); ?>