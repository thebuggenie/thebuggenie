<?php foreach ($milestone->getNonChildIssues($board->getEpicIssuetypeID()) as $issue): ?>
    <?php include_component('agile/milestoneissue', array('issue' => $issue, 'board' => $board)); ?>
<?php endforeach; ?>
