<?php

    $tbg_response->addBreadcrumb(__('Commits'), make_url('vcs_commitspage', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" commits', array('%project_name' => $selected_project->getName())));
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Project commits')));

?>
<div id="project_release_center" class="project_info_container">
    <div class="project_right_container">
        <div class="project_right" id="project_release_center_container">
            <div id="project_commits" style="width: 790px;">
                <?php
                if ($commits == false)
                {
                ?>
                    <p class="faded_out"><?php echo __('No commits have been found for this project'); ?></p>
                <?php
                }
                else
                {
                    ?>
                    <div class="project_commits_box">
                        <div id="commits">
                            <?php include_component('vcs_integration/projectcommits', array('selected_project' => $selected_project, 'commits' => $commits)); ?>
                        </div>

                        <div class="commits_next">
                            <input id="commits_offset" value="40" type="hidden">
                            <?php echo image_tag('spinning_16.gif', array('id' => 'commits_indicator', 'style' => 'display: none; float: left; margin-right: 5px;')); ?>
                            <?php echo javascript_link_tag(__('Show more').image_tag('action_add_small.png', array('style' => 'float: left; margin-right: 5px;')), array('onclick' => "TBG.Project.Commits.update('".make_url('vcs_commitspage', array('project_key' => $selected_project->getKey()))."');", 'id' => 'commits_more_link')); ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <div class="project_left_container">
        <div class="project_left">
        </div>
    </div>
    <br style="clear: both;">
</div>
