<?php $first = true; ?>
<?php foreach ($commits as $commit): ?>
    <?php if ($first || !$commit->getPreviousCommit() instanceof \thebuggenie\core\entities\Commit || date('ymd', $commit->getDate()) != date('ymd', $commit->getPreviousCommit()->getDate())): ?>
        <?php include_component('livelink/commitrowheader', ['commit' => $commit]); ?>
    <?php endif; ?>
    <?php include_component('livelink/commitrow', array('project' => $selected_project, 'commit' => $commit, 'branch' => $branch, 'branches' => $branches)); ?>
    <?php $first = false; ?>
<?php endforeach; ?>
