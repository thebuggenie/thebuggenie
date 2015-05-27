<li id="show_component_<?php print $component->getID(); ?>" class="hover_highlight">
    <div class="component_name">
        <?php echo image_tag('icon_components.png'); ?>
        <span id="component_<?php echo $component->getID(); ?>_name"><?php echo $component->getName(); ?></span>
    </div>
    <div class="component_actions">
        <a href="javascript:void(0);" class="button button-silver dropper" id="component_<?php echo $component->getID(); ?>_more_actions"><?php echo __('Actions'); ?></a>
        <ul class="simple_list rounded_box white shadowed more_actions_dropdown popup_box" onclick="$('component_<?php echo $component->getID(); ?>_more_actions').toggleClassName('button-pressed');$(this).toggle();">
            <li><?php echo javascript_link_tag(__('Edit'), array('class' => 'image', 'onclick' => "TBG.Project.Component.edit('".make_url('configure_project_component', array('project_id' => $component->getProject()->getID(), 'component_id' => $component->getID()))."', '".$component->getID()."');", 'title' => __('Edit component'))); ?></li>
            <li><a href="javascript:void(0);" onclick="$('component_<?php echo $component->getID(); ?>_permissions').toggle();" class="image" title="<?php echo __('Set permissions for this component'); ?>" style="margin-right: 5px;"><?php echo __('Configure permissions'); ?></a></li>
            <li><?php echo javascript_link_tag(__('Remove component'), array('class' => 'image', 'onclick' => "TBG.Main.Helpers.Dialog.show('".__('Please confirm')."', '".__('Do you really want to delete this component?')."', {yes: {click: function() {TBG.Project.Component.remove('".make_url('configure_delete_component', array('project_id' => $component->getProject()->getID(), 'component_id' => $component->getID()))."', ".$component->getID().");}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}})")); ?></li>
        </ul>
    </div>
    <div id="component_<?php echo $component->getID(); ?>_permissions" class="rounded_box white" style="display: none; margin: 5px 0 10px 0; padding: 3px; font-size: 12px;">
        <div class="header"><?php echo __('Permission details for "%itemname"', array('%itemname' => $component->getName())); ?></div>
        <div class="content">
            <?php echo __('Specify who can access this component.'); ?>
            <?php include_component('configuration/permissionsinfo', array('key' => 'canseecomponent', 'mode' => 'project_hierarchy', 'target_id' => $component->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
        </div>
    </div>
</li>
