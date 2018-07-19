<?php foreach ($issue->getAccessibleChildIssues() as $child_issue): ?>
    <?php include_component('main/relatedissue', array('issue' => $child_issue, 'related_issue' => $issue)); ?>
<?php endforeach; ?>