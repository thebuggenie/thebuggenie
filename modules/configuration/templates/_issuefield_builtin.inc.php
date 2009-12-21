<div class="header_div" style="margin-top: 15px;">
	<?php echo __('Existing choices'); ?>
</div>
<table style="width: 100%;" cellpadding="0" cellspacing="0">
	<?php if ($type == 'status'): ?>
		<?php foreach ($items as $item): ?>
			<tr class="canhover_light" id="item_<?php echo $item->getID(); ?>">
				<td style="width: 30px;"><div style="border: 0; background-color: <?php echo $item->getColor(); ?>; font-size: 1px; width: 25px; height: 8px; margin-right: 2px;">&nbsp;</div></td>
				<td style="padding: 2px; font-size: 12px;"><?php echo $item->getName(); ?></td>
				<td style="width: 60px; padding: 2px; text-align: right;">
					<a href="javascript:void(0);" onclick="getIssuefieldEdit('<?php echo make_url('configure_issuefields_getedit', array('type' => 'status', 'id' => $item->getID())); ?>', <?php echo $item->getID(); ?>);" class="image" title="<?php echo __('Edit this item'); ?>"><?php echo image_tag('icon_edit.png'); ?></a>
					<a href="javascript:void(0);" onclick="getIssuefieldPermissionsEditor('<?php echo make_url('configure_issuefields_getpermissions', array('type' => 'status', 'id' => $item->getID())); ?>', <?php echo $item->getID(); ?>);" class="image" title="<?php echo __('Set permissions for this item'); ?>" style="margin-right: 5px;"><?php echo image_tag('cfg_icon_permissions.png'); ?></a>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	<tr>
		<td colspan="3">
			<div class="header_div" style="margin-top: 15px;"><?php echo __('Add another'); ?></div>
		</td>
	</tr>
	<tr>
		<?php if ($type == 'status'): ?>
			<td style="width: 24px;">
				<select name="color" style="width: 100%;">

				</select>
			</td>
			<td>
				<input type="text" name="status_name" style="width: 400px;">
			</td>
			<td style="text-align: right;">
				<input type="submit" value="<?php echo __('Add'); ?>" style="margin-right: 5px; font-weight: bold;">
			</td>
		<?php endif; ?>
	</tr>
</table>