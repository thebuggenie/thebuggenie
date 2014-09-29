<?php
echo '<li>'.link_tag(make_url('viewissue', array('issue_no' => $duplicate_issue->getFormattedIssueNo(), 'project_key' => $duplicate_issue->getProject()->getKey())), ($duplicate_issue->getIssueType()->isTask() ? $duplicate_issue->getTitle() : $duplicate_issue->getFormattedTitle())).'</li>';
?>