<?php

    /** @var \thebuggenie\core\entities\Branch $branch */
    /** @var \thebuggenie\core\entities\Commit $commit */
    /** @var \thebuggenie\core\entities\Project $project */
    /** @var \thebuggenie\core\entities\Project $selected_project */
    /** @var \thebuggenie\core\framework\Response $tbg_response */

    $tbg_response->setTitle(__('"%project_name" commits', ['%project_name' => $selected_project->getName()]));
    $branch_string = (isset($branch)) ? '&nbsp;&raquo;&nbsp;' . $branch->getName() : '';
    include_component('project/projectheader', ['selected_project' => $selected_project, 'subpage' => __('Project commits') . $branch_string . '&nbsp;&raquo;&nbsp;' . $commit->getRevisionString()]);

?>
<div id="project_commits_overview" class="project_info_container">
    <div class="project_left_container">
        <div class="project_left commit-details-list">
            <ul class="property-list">
                <li>
                    <h1><?= __('Commit details'); ?></h1>
                </li>
                <li>
                    <div class="property"><?= __('Commit id'); ?></div>
                    <div class="value"><div class="commit-sha"><?= $commit->getShortRevision(); ?></div></div>
                </li>
                <li>
                    <div class="property"><?= __('Committed by'); ?></div>
                    <div class="value"><?php include_component('main/userdropdown', ['user' => $commit->getAuthor()]); ?></div>
                </li>
                <li>
                    <div class="property"><?= __('Committed at'); ?></div>
                    <div class="value"><?= tbg_formatTime($commit->getDate(), 25); ?></div>
                </li>
                <li>
                    <div class="property"><?= __('Branch(es)'); ?></div>
                    <div class="value">
                        <?php foreach ($commit->getBranches() as $branch): ?>
                            <div class="branch-badge"><?= fa_image_tag('code-branch') . $branch->getName(); ?></div>
                        <?php endforeach; ?>
                    </div>
                </li>
            </ul>
            <ul class="related-issues-list related_issues_list">
                <li>
                    <h1><?= __('Affected issues (%count)', ['%count' => count($commit->getIssues())]); ?></h1>
                </li>
                <?php if ($commit->hasIssues()): ?>
                    <?php foreach ($commit->getIssues() as $issue): ?>
                        <?php include_component('main/relatedissue', ['issue' => $issue]); ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><div class="disabled"><?= __('No issues affected by this commit'); ?></div></li>
                <?php endif; ?>
            </ul>
            <?php if ($commit->isImported()): ?>
                <ul class="files-list">
                    <li>
                        <h1>
                            <span class="header-text">
                                <?= __('Files committed (%count)', ['%count' => count($commit->getFiles())]); ?>
                            </span>
                            <span class="action-buttons">
                                <?= fa_image_tag('plus-square', ['title' => __('Expand all'), 'class' => 'expand-all-icon action-button'], 'far'); ?>
                                <?= fa_image_tag('minus-square', ['title' => __('Collapse all'), 'class' => 'collapse-all-icon action-button'], 'far'); ?>
                            </span>
                        </h1>
                    </li>
                    <?php include_component('livelink/tree', ['structure' => $commit->getStructure()]); ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <div class="project_right_container">
        <div class="project_right branch_<?php echo $branch->getName(); ?>" id="commit_<?php echo $commit->getID(); ?>">
            <?php if ($is_importing): ?>
                <div class="message-box type-warning">
                    <span class="message">
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin']) . __('This repository is still being imported and may not be fully up-to-date yet.'); ?>
                    </span>
                </div>
            <?php elseif (!$commit->isImported()): ?>
                <div class="message-box type-warning">
                    <span class="message">
                        <?= fa_image_tag('exclamation-triangle') . __('This commit was imported and does not contain all information. Press the "%update_commit"-button to load details.', ['%update_commit' => __('Update commit')]); ?>
                    </span>
                    <span class="actions">
                        <?php if (isset($branch)): ?>
                            <a class="button button-silver" href="<?= make_url('livelink_project_commit_import', ['project_key' => $selected_project->getKey(), 'commit_hash' => $commit->getRevision(), 'branch' => $branch->getName()]); ?>"><?= __('Update commit'); ?></a>
                        <?php else: ?>
                            <a class="button button-silver" href="<?= make_url('livelink_project_commit_import', ['project_key' => $selected_project->getKey(), 'commit_hash' => $commit->getRevision()]); ?>"><?= __('Update commit'); ?></a>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>
            <div class="commit-message">
                <h1><?= $commit->getTitle(); ?></h1>
                <?php if ($commit->getMessage()): ?>
                    <div class="overflow"><?= tbg_parse_text($commit->getMessage(), false, null, [], \thebuggenie\core\framework\Settings::SYNTAX_MD); ?></div>
                <?php endif; ?>
            </div>
            <?php if ($commit->isImported()): ?>
                <div class="commit-files-summary">
                    <?= fa_image_tag('file-invoice'); ?>
                    <span class="summary">
                        <?php if ($commit->getLinesAdded() && $commit->getLinesRemoved()): ?>
                            <?= __('This commit has %num_additions_and_num_deletions across %num_files', ['%num_additions_and_num_deletions' => '<span class="num_changes">' . __('%num_a addition(s) and %num_d deletion(s)', ['%num_a' => $commit->getLinesAdded(), '%num_d' => $commit->getLinesRemoved()]) . '</span>', '%num_files' => '<span class="num_files">' . __('%num file(s)', ['%num' => count($commit->getFiles())]) . '</span>']); ?>
                        <?php elseif ($commit->getLinesAdded()): ?>
                            <?= __('This commit has %num_additions across %num_files', ['%num_additions' => '<span class="num_changes">' . __('%num addition(s)', ['%num' => $commit->getLinesAdded()]) . '</span>', '%num_files' => '<span class="num_files">' . __('%num file(s)', ['%num' => count($commit->getFiles())]) . '</span>']); ?>
                        <?php else: ?>
                            <?= __('This commit has %num_deletions across %num_files', ['%num_deletions' => '<span class="num_changes">' . __('%num deletion(s)', ['%num' => $commit->getLinesRemoved()]) . '</span>', '%num_files' => '<span class="num_files">' . __('%num file(s)', ['%num' => count($commit->getFiles())]) . '</span>']); ?>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>
            <div class="commit-files">
                <?php foreach ($commit->getFiles() as $file): ?>
                    <a class="file-anchor" name="file_<?= $file->getID(); ?>"></a>
                    <div class="file-preview action_<?= $file->getAction(); ?>">
                        <?php if ($file->getAction() == \thebuggenie\core\entities\CommitFile::ACTION_DELETED): ?>
                            <div class="filename"><?= fa_image_tag('trash-alt') . $file->getPath(); ?></div>
                            <div class="diffs">
                                <div class="message-box type-warning too-long"><?= fa_image_tag('trash') . __('This file was deleted in this commit'); ?></div>
                            </div>
                        <?php elseif ($file->getAction() == \thebuggenie\core\entities\CommitFile::ACTION_RENAMED): ?>
                            <div class="filename"><?= fa_image_tag('edit', [], 'far') . $file->getData()['previous_filename'] . fa_image_tag('arrow-right-alt') . $file->getPath(); ?></div>
                        <?php else: ?>
                            <div class="filename">
                                <?= fa_image_tag($file->getFontAwesomeIcon(), [], $file->getFontAwesomeIconStyle()) . $file->getPath(); ?>
                                <?php include_component('livelink/diff_summary', ['diffable' => $file]); ?>
                                <?php if ($file->getAction() == \thebuggenie\core\entities\CommitFile::ACTION_ADDED): ?>
                                    <div class="added-badge"><?= __('Added in this commit'); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="diffs">
                                <?php foreach ($file->getDiffs() as $diff): ?>
                                    <?php include_component('livelink/diff', ['diff' => $diff]); ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script>
    require(['domReady', 'jquery'], function (domReady, jquery) {
        domReady(function () {
            jquery('body').on('click', '.collapse-all-icon', function (e) {
                jquery('.folder .foldername').addClass('collapsed');
            });
            jquery('body').on('click', '.expand-all-icon', function (e) {
                jquery('.folder .foldername').removeClass('collapsed');
            });
        });
    });
</script>
