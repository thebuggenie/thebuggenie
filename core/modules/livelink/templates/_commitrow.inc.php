<?php

    /** @var \thebuggenie\core\entities\Branch $branch */
    /** @var \thebuggenie\core\entities\Branch[] $branches */
    /** @var \thebuggenie\core\entities\Commit $commit */
    /** @var \thebuggenie\core\entities\Project $project */

?>
<div class="commit <?php if (isset($branch)) echo ' branch_' . $branch->getName(); ?>" id="commit_<?= $commit->getID(); ?>">
    <div class="message-author-container" style="flex: 1 1 auto;">
        <?php if (isset($branch)): ?>
            <a class="commit-message" href="<?= make_url('livelink_project_commit', ['commit_hash' => $commit->getRevision(), 'project_key' => $project->getKey(), 'branch' => $branch->getName()]); ?>"><?= trim($commit->getTitle()); ?></a>
        <?php else: ?>
            <a class="commit-message" href="<?= make_url('livelink_project_commit', ['commit_hash' => $commit->getRevision(), 'project_key' => $project->getKey()]); ?>"><?= trim($commit->getTitle()); ?></a>
        <?php endif; ?>
        <div class="commit-author"><?= __('Committed by %user %time', ['%user' => get_component_html('main/userdropdown', ['user' => $commit->getAuthor(), 'size' => 'small']), '%time' => tbg_formatTime($commit->getDate(), 20)]); ?></div>
    </div>
    <?php if (isset($branches[$commit->getID()])): ?>
        <div class="commit-branches">
            <?php foreach ($branches[$commit->getID()] as $commit_branch): ?>
                <div class="branch"><?= fa_image_tag('code-branch'); ?><span><?= $commit_branch->getName(); ?></span></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($commit->isImported()): ?>
        <div class="commit-diff-summary"><?php include_component('livelink/diff_summary', ['diffable' => $commit]); ?></div>
    <?php endif; ?>
    <div class="commit-sha"><?= $commit->getShortRevision(); ?></div>
    <div class="commit-details">
        <?php if (!$commit->isImported()): ?>
            <?= fa_image_tag('exclamation-triangle', ['class' => 'not-imported', 'title' => __('This commit has been imported and is missing some information')]); ?>
        <?php endif; ?>
        <span class="commit-comments"><?= fa_image_tag('comments'); ?><?= $commit->getCommentCount(); ?></span>
        <a class="button button-silver"><?= __('Actions'); ?></a>
    </div>
</div>
