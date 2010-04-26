<tr id="main_menu_links_<?php echo $link_id; ?>">
	<td>
		<?php echo link_tag($link['url'], (($link['description'] != '') ? $link['description'] : $link['url'])); ?><br>
	</td>
	<?php if (true): ?>
		<td style="width: 20px;">
			<?php echo javascript_link_tag(image_tag('action_delete.png'), array('class' => 'image', 'id' => 'main_menu_links_' . $link_id . '_remove_link', 'onclick' => "$('main_menu_links_{$link_id}_remove_confirm').toggle();")); ?>
			<?php echo image_tag('spinning_16.gif', array('id' => 'main_menu_links_' . $link_id . '_remove_indicator', 'style' => 'display: none;')); ?>
		</td>
	<?php endif; ?>
</tr>
<?php if (true): ?>
	<tr id="main_menu_links_<?php echo $link_id; ?>_remove_confirm" style="display: none;">
		<td colspan="2">
			<div class="rounded_box white" style="position: relative; clear: both; left: auto; top: auto; margin: 0; width: auto;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 0 5px 5px 5px; font-size: 12px; width: auto;">
					<div class="header_div" style="margin-top: 0;"><?php echo __('Are you sure?'); ?></div>
					<div class="content" style="padding: 3px;">
						<?php echo __('Do you really want to remove this item from the menu?'); ?>
						<div style="text-align: right;">
							<?php echo javascript_link_tag(__('Yes'), array('onclick' => "$('main_menu_links_{$link_id}_remove_confirm').toggle();removeMainMenuLink('".make_url('main_remove_link', array('link_id' => $link_id))."', ".$link_id.");")); ?> ::
							<?php echo javascript_link_tag('<b>'.__('No').'</b>', array('onclick' => "$('main_menu_links_{$link_id}_remove_confirm').toggle();")); ?>
						</div>
					</div>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		</td>
	</tr>
<?php endif; ?>