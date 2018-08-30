<?php \thebuggenie\core\framework\Context::loadLibrary('ui'); ?>
<div id="project_box_<?= $project->getID();?>" class="greybox" style="margin: 10px 0px 10px 0px; position: relative;">
    <div style="padding: 3px; font-size: 14px;">
        <?php if ($project->isArchived()): ?>
            <span class="faded_out"><?= __('ARCHIVED'); ?> </span>
        <?php endif; ?>
            <strong><?= link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), $project->getName()); ?></strong>&nbsp;<span class="project_key" style="position: relative;">(<div class="tooltip leftie"><?= __('This is the project key, used in most places when accessing the project'); ?></div><?= $project->getKey(); ?>)</span>
        <?php if ($project->usePrefix()): ?>
            &nbsp;-&nbsp;<i><?= $project->getPrefix(); ?></i>
        <?php endif; ?>
        <?php if ($project->hasParent()): ?>
            &nbsp;-&nbsp;<?= __('Subproject of'); ?> <i><?= $project->getParent()->getName(); ?></i>
        <?php endif; ?>
    </div>
    <div style="display: inline-block; padding-left: 3px; width: 80px;"><b><?= __('Owner: %user_or_team', array('%user_or_team' => '')); ?></b></div>
    <div style="display: inline-block; clear: right; padding-left: 3px; width: auto;">
        <?php if ($project->getOwner() != null): ?>
            <?php if ($project->getOwner() instanceof \thebuggenie\core\entities\User): ?>
                <?= include_component('main/userdropdown', array('user' => $project->getOwner())); ?>
            <?php elseif ($project->getOwner() instanceof \thebuggenie\core\entities\Team): ?>
                <?= include_component('main/teamdropdown', array('team' => $project->getOwner())); ?>
            <?php endif; ?>
        <?php else: ?>
            <div style="color: #AAA; padding: 2px; width: auto;"><?= __('None'); ?></div>
        <?php endif; ?>
    </div>
    <?php if ($project->hasDescription()): ?>
        <div colspan="2" style="padding: 3px;"><?= tbg_parse_text($project->getDescription()); ?></div>
    <?php endif; ?>
    <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
        <div style="position: absolute; right: 20px; top: 20px;">
            <div style="float: left; margin-right: 10px; display: none;" id="project_<?= $project->getID(); ?>_archive_indicator"><?= image_tag('spinning_16.gif'); ?></div>
            <button class="button button-silver dropper" id="project_<?= $project->getID(); ?>_more_actions"><?= __('Actions'); ?></button>
            <ul id="project_<?= $project->getID(); ?>_more_actions_dropdown" style="font-size: 1.1em; width: 200px; top: 23px; margin-top: 0; text-align: right; z-index: 1000;" class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();">
                <?php if (!$project->isArchived()): ?>
                    <li><?= javascript_link_tag((($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL) ? __('Edit project') : __('Show project details')), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $project->getID()))."');")); ?></li>
                <?php endif; ?>
                <li><?= javascript_link_tag((($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL) ? __('Edit project permissions') : __('Show project permissions')), array('onclick' => "$('project_{$project->getID()}_permissions').toggle();")); ?></li>
                <li id="project_<?= $project->getID(); ?>_unarchive" style="<?php if (!$project->isArchived()) echo 'display: none;'; ?>"><a href="javascript:void(0)" onclick="TBG.Project.unarchive('<?= make_url('configure_project_unarchive', array('project_id' => $project->getID())); ?>', <?php print $project->getID(); ?>)"><?= __('Unarchive project');?></a></li>
                <li id="project_<?= $project->getID(); ?>_archive" style="<?php if ($project->isArchived()) echo 'display: none;'; ?>"><a href="javascript:void(0)" onclick="TBG.Main.Helpers.Dialog.show('<?= __('Archive this project?'); ?>', '<?= __('If you archive a project, it is placed into a read only mode, where the project and its issues can no longer be edited. This will also prevent you from creating new issues, and will hide it from project lists (it can be viewed from an Archived Projects list). This will not, however, affect any subprojects this one has.').'<br>'.__('If you need to reactivate this subproject, you can do this from projects configuration.'); ?>', {yes: {click: function() {TBG.Project.archive('<?= make_url('configure_project_archive', array('project_id' => $project->getID())); ?>', <?php print $project->getID(); ?>);}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?= __('Archive project');?></a></li>
                <li><a href="javascript:void(0)" onclick="TBG.Main.Helpers.Dialog.show('<?= __('Really delete project?'); ?>', '<?= __('Deleting this project will prevent users from accessing it or any associated data, such as issues.'); ?>', {yes: {click: function() {TBG.Project.remove('<?= make_url('configure_project_delete', array('project_id' => $project->getID())); ?>', <?= $project->getID(); ?>); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});"><?= __('Delete');?></a></li>
            </ul>
        </div>
    <?php endif; ?>
    <div class="fullpage_backdrop" style="margin: 5px; display: none;" id="project_<?= $project->getID(); ?>_permissions">
        <div class="fullpage_backdrop_content backdrop_box large">
            <div class="backdrop_detail_header">
                <span><?= __('Edit project permissions'); ?></span>
                <a href="javascript:void(0);" class="closer" onclick="$('project_<?= $project->getID(); ?>_permissions').hide();"><?= fa_image_tag('times'); ?></a>
            </div>
            <div class="backdrop_detail_content">
                <?php include_component('project/projectpermissions', array('access_level' => $access_level, 'project' => $project)); ?>
            </div>
        </div>
    </div>
</div>
<?php if ($project->hasChildren()): ?>
    <div class="child_project_container" id="project_<?= $project->getID(); ?>_children">
        <?php foreach ($project->getChildren() as $child_project): ?>
            <?php include_component('projectbox', array('project' => $child_project, 'access_level' => $access_level)); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
