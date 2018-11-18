<?php

    /** @var \thebuggenie\core\entities\Branch $branch */
    /** @var \thebuggenie\core\entities\Commit $commit */
    /** @var \thebuggenie\core\entities\Project $project */

?>
<div class="commit branch_<?= $branch->getName(); ?>" id="commit_<?= $commit->getID(); ?>">
    <div class="message-author-container" style="flex: 1 1 auto;">
        <div class="commit-message"><?= trim($commit->getLog()); ?></div>
        <div class="commit-author"><?= __('Committed by %user %time', ['%user' => get_component_html('main/userdropdown', ['user' => $commit->getAuthor(), 'size' => 'small']), '%time' => tbg_formatTime($commit->getDate(), 20)]); ?></div>
    </div>
    <?php if (isset($branches[$commit->getID()])): ?>
        <div class="commit-branches">
            <?php foreach ($branches[$commit->getID()] as $commit_branch): ?>
                <div class="branch"><?= fa_image_tag('code-branch'); ?><span><?= $commit_branch->getName(); ?></span></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="commit-sha"><?= $commit->getShortRevision(); ?></div>
    <div class="commit-actions"><a class="button button-silver"><?= __('Actions'); ?></a></div>
</div>
