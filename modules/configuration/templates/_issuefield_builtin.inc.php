<?php if (strtolower(get_class($item)) == 'bugsstatus'): ?>
	<tr class="canhover_light" id="item_<?php echo $item->getID(); ?>">
		<td style="width: 30px;"><div style="border: 0; background-color: <?php echo $item->getColor(); ?>; font-size: 1px; width: 25px; height: 8px; margin-right: 2px;">&nbsp;</div></td>
		<td style="padding: 2px; font-size: 12px;"><?php echo $item->getName(); ?></td>
		<td style="width: 60px; padding: 2px; text-align: right;">
			<a href="javascript:void(0);" onclick="getIssuefieldEdit('<?php echo make_url('configure_issuefields_getedit', array('type' => 'status', 'id' => $item->getID())); ?>', <?php echo $item->getID(); ?>);" class="image" title="<?php echo __('Edit this item'); ?>"><?php echo image_tag('icon_edit.png'); ?></a>
			<a href="javascript:void(0);" onclick="getIssuefieldPermissionsEditor('<?php echo make_url('configure_issuefields_getpermissions', array('type' => 'status', 'id' => $item->getID())); ?>', <?php echo $item->getID(); ?>);" class="image" title="<?php echo __('Set permissions for this item'); ?>" style="margin-right: 5px;"><?php echo image_tag('cfg_icon_permissions.png'); ?></a>
		</td>
	</tr>
<?php endif; ?>