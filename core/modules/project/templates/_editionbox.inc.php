<li id="edition_<?php echo $edition->getID(); ?>_box" class="rounded_box invisible borderless">
    <div class="edition_name">
        <?php echo image_tag('icon_edition.png'); ?>
        <span id="edition_<?php echo $edition->getID(); ?>_name"><?php echo $edition->getName(); ?></span>
    </div>
    <div class="edition_actions">
        <a href="javascript:void(0);" class="button button-silver dropper" id="edition_<?php echo $edition->getID(); ?>_more_actions"><?php echo __('Actions'); ?></a>
        <ul class="simple_list rounded_box white shadowed more_actions_dropdown popup_box" onclick="$('edition_<?php echo $edition->getID(); ?>_more_actions').toggleClassName('button-pressed');$(this).toggle();">
            <li><?php echo javascript_link_tag(__('Edit'), array('class' => 'image', 'onclick' => "TBG.Project.Edition.edit('".make_url('configure_project_edition', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID()))."', '".$edition->getID()."');", 'title' => __('Edit edition'))); ?></li>
            <li><a href="javascript:void(0);" onclick="$('edition_<?php echo $edition->getID(); ?>_permissions').toggle();" class="image" title="<?php echo __('Set permissions for this edition'); ?>" style="margin-right: 5px;"><?php echo __('Configure permissions'); ?></a></li>
            <li><?php echo javascript_link_tag(__('Remove edition'), array('class' => 'image', 'onclick' => "TBG.Main.Helpers.Dialog.show('".__('Please confirm')."', '".__('Do you really want to delete this edition?')."', {yes: {click: function() {TBG.Project.Edition.remove('".make_url('configure_delete_edition', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID()))."', ".$edition->getID().");}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}})")); ?></li>
        </ul>
    </div>
    <?php if ($edition->hasDescription()): ?>
        <div style="padding: 0 0 10px 20px; margin-top: 0;" class="faded_out"><?php print $edition->getDescription(); ?></div>
    <?php endif; ?>
    <div id="edition_<?php echo $edition->getID(); ?>_permissions" style="display: none;">
        <div class="rounded_box white" style="margin: 5px 0 10px 0; padding: 3px; font-size: 12px;">
            <div class="header"><?php echo __('Permission details for "%itemname"', array('%itemname' => $edition->getName())); ?></div>
            <div class="content">
                <?php echo __('Specify who can access this edition.'); ?>
                <?php include_component('configuration/permissionsinfo', array('key' => 'canseeedition', 'mode' => 'project_hierarchy', 'target_id' => $edition->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
            </div>
        </div>
    </div>
</li>
