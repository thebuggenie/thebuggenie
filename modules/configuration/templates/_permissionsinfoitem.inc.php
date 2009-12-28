<?php echo image_tag('spinning_16.gif', array('id' => 'permissions_' . $key . '_' . $target_id . '_' . $type . '_' . $item_id . '_indicator', 'style' => 'display: none;')); ?>
<span id="permissions_<?php echo $key; ?>_<?php echo $target_id; ?>_<?php echo $type; ?>_<?php echo $item_id; ?>">
	<?php if ($mode == 'datatype'): ?>
		<?php $val = BUGScontext::isPermissionSet($type, $key, $item_id, $target_id, $module); ?>
		<?php if (is_bool($val)): ?>
			<?php $image_tag = ($val) ? image_tag('permission_set_ok.png') : image_tag('permission_set_denied.png'); ?>
		<?php else: ?>
			<?php $image_tag = image_tag('permission_set_ok.png'); ?>
		<?php endif; ?>
		<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
			<?php if (is_null($val) || ($val)): ?>
				<a href="javascript:void(0);" onclick="setPermission('<?php echo make_url('permissions_set_denied', array('key' => $key, 'target_id' => $target_id, 'target_type' => $type, 'item_id' => $item_id, 'target_module' => $module, 'template_mode' => $mode)); ?>', 'permissions_<?php echo $key; ?>_<?php echo $target_id; ?>_<?php echo $type; ?>_<?php echo $item_id; ?>');" class="image" title="<?php echo __('Click to remove access'); ?>"><?php echo $image_tag; ?></a>
			<?php elseif (!$val): ?>
				<a href="javascript:void(0);" onclick="setPermission('<?php echo make_url('permissions_set_unset', array('key' => $key, 'target_id' => $target_id, 'target_type' => $type, 'item_id' => $item_id, 'target_module' => $module, 'template_mode' => $mode)); ?>', 'permissions_<?php echo $key; ?>_<?php echo $target_id; ?>_<?php echo $type; ?>_<?php echo $item_id; ?>');" class="image" title="<?php echo __('Click to remove access'); ?>"><?php echo $image_tag; ?></a>
			<?php endif; ?>
		<?php else: ?>
			<?php echo $image_tag; ?>
		<?php endif; ?>
	<?php endif; ?>
</span>