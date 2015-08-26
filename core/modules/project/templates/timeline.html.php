<?php

    $tbg_response->addBreadcrumb(__('Timeline'), make_url('project_timeline', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" project timeline', array('%project_name' => $selected_project->getName())));
    $tbg_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name" project timeline', array('%project_name' => $selected_project->getName())));
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Timeline')));

?>
<div id="project_release_center" class="project_info_container">
    <div class="project_right_container">
        <div class="project_right" id="project_release_center_container">
            <div style="width: 790px;" id="timeline">
                <?php if (count($recent_activities) > 0): ?>
                    <?php include_component('project/timeline', array('activities' => $recent_activities)); ?>
                <?php else: ?>
                    <div class="faded_out dark" style="font-size: 13px; padding-top: 3px;"><b><?php echo __('No recent activity registered for this project.'); ?></b><br><?php echo __('As soon as something important happens it will appear here.'); ?></div>
                <?php endif; ?>
            </div>
            <?php if (count($recent_activities) > 0): ?>
                <div class="project_timeline_more_button_container">
                    <?php echo image_tag('spinning_32.gif', array('id' => 'timeline_indicator', 'style' => 'display: none;')); ?>
                    <?php echo javascript_link_tag(__('Show more'), array('class' => 'button button-silver', 'onclick' => "TBG.Project.Timeline.update('".make_url(($important) ? 'project_timeline_important' : 'project_timeline', array('project_key' => $selected_project->getKey()))."');", 'id' => 'timeline_more_link')); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="project_left_container">
        <div class="project_left">
            <input id="timeline_offset" value="40" type="hidden">
            <h3><?php echo __('Timeline actions'); ?></h3>
            <ul class="simple_list">
                <li class="<?php if ($important) echo 'selected'; ?>"><?php echo link_tag(make_url('project_timeline_important', array('project_key' => $selected_project->getKey())), image_tag('icon_important.png') . __('Only important items')); ?></li>
                <li class="<?php if (!$important) echo 'selected'; ?>"><?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey())), image_tag('icon_timeline.png') . __('All timeline items')); ?></li>
            </ul>
            <ul class="simple_list">
                <li><?php echo link_tag(make_url('project_timeline_important', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), image_tag('icon_rss.png') . __('Only important items')); ?></li>
                <li><?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), image_tag('icon_rss.png') . __('All timeline items')); ?></li>
            </ul>
        </div>
    </div>
    <br style="clear: both;">
</div>
