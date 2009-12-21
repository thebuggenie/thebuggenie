<tr class="canhover_light" id="item_<?php echo $type; ?>_<?php echo $item->getID(); ?>">
	<?php if ($type == 'status'): ?>
		<td style="width: 30px;"><div style="border: 0; background-color: <?php echo $item->getColor(); ?>; font-size: 1px; width: 25px; height: 8px; margin-right: 2px;">&nbsp;</div></td>
	<?php endif; ?>
	<td style="padding: 2px; font-size: 12px;"><?php echo $item->getName(); ?></td>
	<td style="width: 60px; padding: 2px; text-align: right;">
		<a href="javascript:void(0);" onclick="getIssuefieldEdit('<?php echo make_url('configure_issuefields_getedit', array('type' => 'status', 'id' => $item->getID())); ?>', <?php echo $item->getID(); ?>);" class="image" title="<?php echo __('Edit this item'); ?>"><?php echo image_tag('icon_edit.png'); ?></a>
		<a href="javascript:void(0);" onclick="getIssuefieldPermissionsEditor('<?php echo make_url('configure_issuefields_getpermissions', array('type' => 'status', 'id' => $item->getID())); ?>', <?php echo $item->getID(); ?>);" class="image" title="<?php echo __('Set permissions for this item'); ?>" style="margin-right: 5px;"><?php echo image_tag('cfg_icon_permissions.png'); ?></a>
		<a href="javascript:void(0);" onclick="$('delete_item_<?php echo $item->getID(); ?>').toggle();" class="image" id="delete_<?php echo $item->getID(); ?>_link"><?php echo image_tag('icon_delete.png'); ?></a>
		<?php echo image_tag('spinning_16.gif', array('id' => 'delete_' . $type . '_' . $item->getID() . '_indicator', 'style' => 'display: none;')); ?>
	</td>
</tr>
<tr id="delete_item_<?php echo $item->getID(); ?>" style="display: none;">
	<td colspan="3">
		<div class="rounded_box white_borderless" style="margin: 5px 0 10px 0;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="padding: 3px; font-size: 12px;">
				<div class="header"><?php echo __('Really delete "%itemname%"?', array('%itemname%' => $item->getName())); ?></div>
				<div class="content">
					<?php echo __('Are you really sure you want to delete this item?'); ?>
					<div style="text-align: right; font-size: 13px;">
						<a href="javascript:void(0);" onclick="deleteIssuefieldOption('<?php echo make_url('configure_issuefields_delete', array('type' => $type, 'id' => $item->getID())); ?>', '<?php echo $type; ?>', <?php echo $item->getID(); ?>);"><?php echo __('Yes'); ?></a> ::
						<a href="javascript:void(0);" onclick="$('delete_item_<?php echo $item->getID(); ?>').toggle();"><b><?php echo __('No'); ?></b></a>
					</div>
				</div>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
	</td>
</tr>