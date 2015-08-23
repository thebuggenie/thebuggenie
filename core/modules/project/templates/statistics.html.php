<?php

    $tbg_response->addBreadcrumb(__('Statistics'), make_url('project_statistics', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" project team', array('%project_name' => $selected_project->getName())));
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Statistics')));

?>
<div id="project_statistics" class="project_info_container">
    <div class="project_right_container">
        <div class="project_right" id="project_statistics_container">
            <div style="width: 790px;">
            <div style="display: none;" id="statistics_main">
                <div style="width: 695px; height: 310px; padding: 0;" id="statistics_main_image_div">
                    <img src="#" id="statistics_main_image" alt="<?php echo __('Loading, please wait'); ?>">
                </div>
                <div style="padding: 5px; text-align: center;"><b><?php echo __('Click one of the graphs below to show details'); ?></b></div>
                <table style="width: 697px; margin-top: 5px;" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="width: 33%; height: 150px; padding: 1px;"><img src="#" onclick="TBG.Project.Statistics.toggleImage(1);" id="statistics_mini_image_1" alt="<?php echo __('Loading, please wait'); ?>" style="cursor: pointer;" title="<?php echo __('Show details'); ?>"></td>
                        <td style="width: 33%; padding: 1px;"><img src="#" onclick="TBG.Project.Statistics.toggleImage(2);" id="statistics_mini_image_2" alt="<?php echo __('Loading, please wait'); ?>" style="cursor: pointer;" title="<?php echo __('Show details'); ?>"></td>
                        <td style="width: 34%; padding: 1px;"><img src="#" onclick="TBG.Project.Statistics.toggleImage(3);" id="statistics_mini_image_3" alt="<?php echo __('Loading, please wait'); ?>" style="cursor: pointer;" title="<?php echo __('Show details'); ?>"></td>
                    </tr>
                </table>
                <input type="hidden" id="statistics_mini_1_main" value="">
                <input type="hidden" id="statistics_mini_2_main" value="">
                <input type="hidden" id="statistics_mini_3_main" value="">
                <?php \thebuggenie\core\framework\Event::createNew('core', 'projectstatistics_image', $selected_project)->trigger(); ?>
            </div>
            <div class="rounded_box verylightgrey borderless" style="width: 690px; text-align: center; padding: 150px 5px 150px 5px; color: #AAA; font-size: 19px;" id="statistics_help">
                <?php echo __('Select an item in the left menu to show more details'); ?>
            </div>
            </div>
        </div>
    </div>
    <div class="project_left_container">
        <div class="project_left">
            <h3><?php echo __('Number of issues per:'); ?></h3>
            <ul class="simple_list" id="statistics_selector">
                <li id="statistics_per_state_selector"><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_state')); ?>', 'state');"><?php echo __('%number_of_issues_per State (open / closed)', array('%number_of_issues_per' => '')); ?></a></li>
                <li id="statistics_per_category_selector"><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_category')); ?>', 'category');"><?php echo __('%number_of_issues_per Category', array('%number_of_issues_per' => '')); ?></a></li>
                <li id="statistics_per_priority_selector"><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_priority')); ?>', 'priority');"><?php echo __('%number_of_issues_per Priority level', array('%number_of_issues_per' => '')); ?></a></li>
                <li id="statistics_per_resolution_selector"><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_resolution')); ?>', 'resolution');"><?php echo __('%number_of_issues_per Resolution', array('%number_of_issues_per' => '')); ?></a></li>
                <li id="statistics_per_reproducability_selector"><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_reproducability')); ?>', 'reproducability');"><?php echo __('%number_of_issues_per Reproducability', array('%number_of_issues_per' => '')); ?></a></li>
                <li id="statistics_per_status_selector"><a href="javascript:void(0);" onclick="TBG.Project.Statistics.get('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_status')); ?>', 'status');"><?php echo __('%number_of_issues_per Status type', array('%number_of_issues_per' => '')); ?></a></li>
            </ul>
            <?php \thebuggenie\core\framework\Event::createNew('core', 'projectstatistics_links', $selected_project)->trigger(); ?>
        </div>
    </div>
    <br style="clear: both;">
</div>
