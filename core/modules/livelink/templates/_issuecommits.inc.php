<?php $first = true; ?>
<?php foreach ($commits as $issue_commit): ?>
    <?php $commit = $issue_commit->getCommit(); ?>
    <?php if ($first || !$commit->getPreviousCommit() instanceof \thebuggenie\core\entities\Commit || date('ymd', $commit->getDate()) != date('ymd', $commit->getPreviousCommit()->getDate())): ?>
        <?php include_component('livelink/commitrowheader', ['commit' => $commit]); ?>
    <?php endif; ?>
    <?php include_component('livelink/commitrow', array('project' => $project, 'commit' => $commit)); ?>
    <?php $first = false; ?>
<?php endforeach; ?>
