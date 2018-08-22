<?php foreach ($child_issues as $child_issue): ?>
    <?php include_component('main/relatedissue', array('issue' => $child_issue, 'related_issue' => $issue)); ?>
<?php endforeach; ?>