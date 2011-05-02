<tr id="edition_box_<?php echo $edition->getID(); ?>" class="hover_highlight">
	<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_edition.png'); ?></td>
	<td style="width: auto; padding: 2px;"><?php echo $edition->getName(); ?><?php if ($edition->isDefault()): echo __('%edition_name% (default)', array('%edition_name%' => '')); endif; ?></td>
	<td style="width: 60px; padding: 2px;">
		<?php echo javascript_link_tag(image_tag('icon_edit.png'), array('class' => 'image', 'onclick' => "editEdition('".make_url('configure_project_edition', array('project_id' => $edition->getProject()->getID(), 'edition_id' => $edition->getID()))."', '".$edition->getID()."');", 'title' => __('Edit edition'))); ?>
		<a href="javascript:void(0);" onclick="$('edition_<?php echo $edition->getID(); ?>_permissions').toggle();" class="image" title="<?php echo __('Set permissions for this edition'); ?>" style="margin-right: 5px;"><?php echo image_tag('cfg_icon_permissions.png'); ?></a>
		<?php echo javascript_link_tag(image_tag('action_cancel_small.png'), array('class' => 'image', 'onclick' => "\$('del_edition_{$edition->getID()}').toggle();")); ?>
		<div id="del_edition_<?php echo $edition->getID(); ?>" style="display: none; position: absolute; width: 200px; padding: 10px; border: 1px solid #DDD; background-color: #FFF;"><b><?php echo __('Please confirm'); ?></b><br><?php echo __('Do you really want to delete this edition?'); ?><br>
			<div style="text-align: right; padding-top: 5px;"><?php echo image_tag('spinning_16.gif', array('id' => 'edition_'.$edition->getID().'_delete_indicator', 'style' => 'margin-right: 5px; display: none;')); ?> <a href="javascript:void(0);" onclick="deleteEdition('<?php echo make_url('configure_delete_edition', array('edition_id' => $edition->getID())); ?>', <?php print $edition->getID(); ?>);return false;"><?php echo __('Yes'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="Effect.Fade('del_edition_<?php echo $edition->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a></div>
		</div>
	</td>
</tr>
<?php if ($edition->hasDescription()): ?>
	<tr>
		<td style="padding: 2px;" colspan=3>
			<div style="padding-bottom: 10px; color: #AAA;"><?php print $edition->getDescription(); ?></div>
		</td>
	</tr>
<?php endif; ?>
<tr id="edition_<?php echo $edition->getID(); ?>_permissions" style="display: none;">
	<td colspan="3">
		<div class="rounded_box white" style="margin: 5px 0 10px 0; padding: 3px; font-size: 12px;">
			<div class="header"><?php echo __('Permission details for "%itemname%"', array('%itemname%' => $edition->getName())); ?></div>
			<div class="content">
				<?php echo __('Specify who can access this edition.'); ?>
				<?php include_component('configuration/permissionsinfo', array('key' => 'canseeedition', 'mode' => 'project_hierarchy', 'target_id' => $edition->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
			</div>
		</div>
	</td>
</tr>