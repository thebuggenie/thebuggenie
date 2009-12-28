<td style="padding: 2px;">
	<?php echo $item_name; ?>
</td>
<?php if ($mode == 'datatype'): ?>
	<td style="padding: 2px; text-align: center;">
		<?php $val = BUGScontext::isPermissionSet($type, $key, $item_id, $target_id, $module); ?>
		<?php if (is_bool($val)): ?>
			<?php $image_tag = ($val) ? image_tag('permission_set_ok.png') : image_tag('permission_set_unset.png'); ?>
		<?php else: ?>
			<?php $image_tag = image_tag('permission_set_ok.png'); ?>
		<?php endif; ?>
		<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
			<?php if (is_null($val)): ?>
				<?php echo link_tag(make_url('permissions_set_denied', array('key' => $key, 'target_id' => $target_id, 'target_type' => $type, 'item_id' => $item_id)), $image_tag, array('class' => 'image')); ?>
			<?php elseif ($val): ?>
				<?php echo link_tag(make_url('permissions_set_denied', array('key' => $key, 'target_id' => $target_id, 'target_type' => $type, 'item_id' => $item_id)), $image_tag, array('class' => 'image')); ?>
			<?php elseif (!$val): ?>
				<?php echo link_tag(make_url('permissions_set_allowed', array('key' => $key, 'target_id' => $target_id, 'target_type' => $type, 'item_id' => $item_id)), $image_tag, array('class' => 'image')); ?>
			<?php endif; ?>
		<?php else: ?>
			<?php echo $image_tag; ?>
		<?php endif; ?>
	</td>
<?php endif; ?>