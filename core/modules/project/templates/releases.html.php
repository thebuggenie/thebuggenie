<?php

    $tbg_response->addBreadcrumb(__('Releases'), make_url('project_releases', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" releases', array('%project_name' => $selected_project->getName())));
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Releases')));

?>
<div id="project_releases" class="project_info_container">
    <div class="project_right_container">
        <div class="project_right" id="project_releases_container">
            <div class="active_releases releases_list">
                <h3><?php echo __('Active project releases'); ?></h3>
                <?php if (count($active_builds[0])): ?>
                    <ul class="simple_list">
                    <?php foreach ($active_builds[0] as $build): ?>
                        <?php include_component('project/release', array('build' => $build)); ?>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="faded_out"><?php echo __('There are no active releases for this project'); ?></div>
                <?php endif; ?>
                <?php if ($selected_project->isEditionsEnabled()): ?>
                    <?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
                        <h4><?php echo __('Active %edition_name releases', array('%edition_name' => $edition->getName())); ?></h4>
                        <?php if (count($active_builds[$edition_id])): ?>
                            <ul class="simple_list">
                            <?php foreach ($active_builds[$edition_id] as $build): ?>
                                <?php include_component('project/release', array('build' => $build)); ?>
                            <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="faded_out"><?php echo __('There are no active releases for this edition'); ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="archived_releases releases_list" style="display: none;">
                <h3><?php echo __('Archived project releases'); ?></h3>
                <?php if (count($archived_builds[0])): ?>
                    <ul class="simple_list">
                    <?php foreach ($archived_builds[0] as $build): ?>
                        <?php include_component('project/release', array('build' => $build)); ?>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="faded_out"><?php echo __('There are no archived releases for this project'); ?></div>
                <?php endif; ?>
                <?php if ($selected_project->isEditionsEnabled()): ?>
                    <?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
                        <h4><?php echo __('Archived %edition_name releases', array('%edition_name' => $edition->getName())); ?></h4>
                        <?php if (count($archived_builds[$edition_id])): ?>
                            <ul class="simple_list">
                            <?php foreach ($archived_builds[$edition_id] as $build): ?>
                                <?php include_component('project/release', array('build' => $build)); ?>
                            <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="faded_out"><?php echo __('There are no archived releases for this edition'); ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="project_left_container">
        <div class="project_left">
            <h3><?php echo __('Release selection'); ?></h3>
            <ul class="simple_list">
                <li class="selected"><a href="javascript:void(0);" onclick="$$('.releases_list').each(function (r) { (r.hasClassName('active_releases')) ? r.show() : r.hide() }); TBG.Project.toggleLeftSelection(this);"><?php echo __('All active releases'); ?></a></li>
                <li ><a href="javascript:void(0);" onclick="$$('.releases_list').each(function (r) { (r.hasClassName('archived_releases')) ? r.show() : r.hide() }); TBG.Project.toggleLeftSelection(this);"><?php echo __('Archived releases'); ?></a></li>
            </ul>
        </div>
    </div>
    <br style="clear: both;">
</div>
