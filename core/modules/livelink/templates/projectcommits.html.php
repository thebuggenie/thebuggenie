<?php

    use thebuggenie\core\entities\Branch;

    /** @var Branch[] $branches */
    /** @var \thebuggenie\core\entities\Project $selected_project */
    /** @var \thebuggenie\core\framework\Response $tbg_response */

    $tbg_response->setTitle(__('"%project_name" commits', array('%project_name' => $selected_project->getName())));
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Project commits')));

?>
<div id="project_commits_overview" class="project_info_container">
    <div class="project_left_container">
        <div class="project_left">
            <h3><?php echo __('Branch filters'); ?></h3>
            <ul class="simple_list branch-list">
                <?php foreach ($branches as $branch): ?>
                    <li>
                        <a href="javascript:void(0);" onclick="TBG.Project.showBranchCommits('<?php echo make_url('livelink_project_commits', array('project_key' => $selected_project->getKey())); ?>', '<?php echo $branch->getName(); ?>'); TBG.Project.toggleLeftSelection(this);">
                            <span class="branch-name"><?php echo $branch->getName(); ?></span>
                            <span class="branch-last-updated"><?= fa_image_tag('clock') . __('Last commit: %date', ['%date' => tbg_formatTime($branch->getLatestCommit()->getDate(), 20)]); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="project_right_container">
        <div class="project_right" id="project_commits_center_container">
            <?php if ($is_importing): ?>
                <div class="message-box type-warning">
                    <span class="message">
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin']) . __('This repository is still being imported and may not be fully up-to-date yet.'); ?>
                    </span>
                </div>
            <?php endif; ?>
            <div id="project_commits">
                <p class="faded_out"><?php echo __('Choose branch on the left to filter commits for this project'); ?></p>
            </div>
        </div>
    </div>
</div>
