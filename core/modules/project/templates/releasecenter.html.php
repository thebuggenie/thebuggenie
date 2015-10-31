<?php

    $tbg_response->addBreadcrumb(__('Release center'), make_url('project_release_center', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" release center', array('%project_name' => $selected_project->getName())));
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Release center')));

?>
<div id="project_release_center" class="project_info_container">
    <div class="project_right_container">
        <div class="project_right only_active" id="project_release_center_container">
            <?php if ($tbg_user->canManageProjectReleases($selected_project)): ?>
                <div class="project_save_container">
                    <div class="button button-silver" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_build', 'project_id' => $selected_project->getId())); ?>');"><?php echo __('Add new project release'); ?></div>
                    <?php if ($selected_project->isEditionsEnabled()): ?>
                        <div class="button button-silver dropper"><?php echo __('Add edition release'); ?></div>
                        <ul class="rounded_box white shadowed dropdown_box rightie popup_box more_actions_dropdown">
                            <?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
                                <li><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_build', 'project_id' => $selected_project->getId(), 'edition_id' => $edition_id)); ?>');"><?php echo __('Add %edition_name release', array('%edition_name' => $edition->getName())); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <h3><?php echo __('Project releases'); ?></h3>
            <ul class="simple_list" id="active_builds_0">
                <?php if (count($selected_project->getNonEditionBuilds())): ?>
                    <?php foreach ($selected_project->getNonEditionBuilds() as $build): ?>
                        <?php include_component('buildbox', array('build' => $build)); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <div class="faded_out" id="no_active_builds_0"<?php if (count($selected_project->getNonEditionBuilds())): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no releases for this project'); ?></div>
            <?php if ($selected_project->isEditionsEnabled()): ?>
                <?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
                    <h3><?php echo __('%edition_name releases', array('%edition_name' => $edition->getName())); ?></h3>
                    <ul class="simple_list" id="active_builds_<?php echo $edition_id; ?>">
                        <?php if (count($edition->getBuilds())): ?>
                            <?php foreach ($edition->getBuilds() as $build): ?>
                                <?php include_component('buildbox', array('build' => $build)); ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <div class="faded_out" id="no_active_builds_<?php echo $edition_id; ?>"<?php if (count($edition->getBuilds())): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no releases for this edition'); ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="project_left_container">
        <div class="project_left">
            <h3><?php echo __('Filters'); ?></h3>
            <ul class="simple_list">
                <li class="selected"><a href="javascript:void(0);" onclick="TBG.Project.clearReleaseCenterFilters(); $('project_release_center_container').addClassName('only_active');TBG.Project.checkAndToggleNoBuildsMessage();TBG.Project.toggleLeftSelection(this);"><?php echo __('Active releases'); ?></a></li>
                <li><a href="javascript:void(0);" onclick="TBG.Project.clearReleaseCenterFilters(); $('project_release_center_container').addClassName('only_archived');TBG.Project.checkAndToggleNoBuildsMessage();TBG.Project.toggleLeftSelection(this);"><?php echo __('Archived releases'); ?></a></li>
                <li><a href="javascript:void(0);" onclick="TBG.Project.clearReleaseCenterFilters(); $('project_release_center_container').addClassName('only_downloads');TBG.Project.checkAndToggleNoBuildsMessage();TBG.Project.toggleLeftSelection(this);"><?php echo __('With downloads'); ?></a></li>
                <li><a href="javascript:void(0);" onclick="TBG.Project.clearReleaseCenterFilters(); TBG.Project.checkAndToggleNoBuildsMessage();TBG.Project.toggleLeftSelection(this);"><?php echo __('Show all releases'); ?></a></li>
            </ul>
        </div>
    </div>
    <br style="clear: both;">
</div>
<?php if ($build_error): ?>
    <script type="text/javascript">
        require(['domReady', 'thebuggenie/tbg'], function (domReady, TBG) {
            domReady(function () {
                TBG.Main.Helpers.Message.error(__('An error occured when adding or updating the release'), $build_error);
            });
        });
    </script>
<?php endif; ?>
