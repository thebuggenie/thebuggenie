<?php foreach ($issue->getChildIssues() as $child_issue): ?>
    <?php if ($child_issue->hasAccess()): ?>
        <?php include_component('main/relatedissue', array('issue' => $child_issue, 'related_issue' => $issue)); ?>
    <?php endif; ?>
<?php endforeach; ?>