<?php

    $tbg_response->addBreadcrumb(__('Commits'), make_url('vcs_commitspage', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" commits', array('%project_name' => $selected_project->getName())));
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Project commits')));

?>
<div id="project_commits_center" class="project_info_container">
    <div class="project_right_container">
        <div class="project_right" id="project_commits_center_container">
            <div id="project_commits" style="width: 790px;">
                <p class="faded_out"><?php echo __('Choose branch on the left to filter commits for this project'); ?></p>
            </div>
        </div>
    </div>
    <div class="project_left_container">
        <div class="project_left">
            <?php if (count($gitlab_repos_nss)): ?>
                <?php foreach ($gitlab_repos_nss as $gitlab_repos_nss => $grn_branches): ?>
                    <h3><?php echo $gitlab_repos_nss; ?></h3>
                    <ul class="simple_list">
                        <li><a href="javascript:void(0);" onclick="TBG.Project.showBranchCommits('<?php echo make_url('vcs_commitspage', array('project_key' => $selected_project->getKey())); ?>', undefined, '<?php echo $gitlab_repos_nss; ?>'); TBG.Project.toggleLeftSelection(this);"><?php echo __('All branches'); ?></a></li>
                        <?php foreach ($grn_branches as $branchname): ?>
                            <li><a href="javascript:void(0);" onclick="TBG.Project.showBranchCommits('<?php echo make_url('vcs_commitspage', array('project_key' => $selected_project->getKey())); ?>', '<?php echo $branchname; ?>', '<?php echo $gitlab_repos_nss; ?>'); TBG.Project.toggleLeftSelection(this);"><?php echo $branchname; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            <?php else: ?>
                <h3><?php echo __('Branch filters'); ?></h3>
                <ul class="simple_list">
                    <li><a href="javascript:void(0);" onclick="TBG.Project.showBranchCommits('<?php echo make_url('vcs_commitspage', array('project_key' => $selected_project->getKey())); ?>'); TBG.Project.toggleLeftSelection(this);"><?php echo __('All branches'); ?></a></li>
                    <?php foreach ($branches as $branchname): ?>
                        <li><a href="javascript:void(0);" onclick="TBG.Project.showBranchCommits('<?php echo make_url('vcs_commitspage', array('project_key' => $selected_project->getKey())); ?>', '<?php echo $branchname; ?>'); TBG.Project.toggleLeftSelection(this);"><?php echo $branchname; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <br style="clear: both;">
</div>
