<tr id="<?php echo $link['target_type']; ?>_<?php echo $link['target_id']; ?>_links_<?php echo $link_id; ?>">
	<td>
		<?php if ($link['target_type'] == 'wiki' && $link['url'] != ''): ?>
			<?php if ($tbg_routing->getCurrentRouteModule() == 'publish' && $tbg_request['article_name'] == $link['url']): ?>
				<?php echo link_tag(make_url('publish_article', array('article_name' => $link['url'])), (($link['description'] != '') ? $link['description'] : $link['url']), array('class' => 'selected')); ?>
			<?php else: ?>
				<?php echo link_tag(make_url('publish_article', array('article_name' => $link['url'])), (($link['description'] != '') ? $link['description'] : $link['url'])); ?>
			<?php endif; ?>
		<?php elseif (mb_substr($link['url'], 0, 1) == '@'): ?>
			<?php echo link_tag(make_url($link['url']), (($link['description'] != '') ? $link['description'] : $link['url'])); ?>
		<?php elseif ($link['url'] != ''): ?>
			<?php echo link_tag($link['url'], (($link['description'] != '') ? $link['description'] : $link['url'])); ?>
		<?php elseif ($link['description'] != ''): ?>
			<?php echo tbg_parse_text($link['description']); ?>
		<?php else: ?>
			&nbsp;
		<?php endif; ?>
	</td>
	<?php if ($tbg_user->canEditMainMenu()): ?>
		<td style="width: 20px;">
			<?php echo javascript_link_tag(image_tag('action_delete.png'), array('class' => 'image', 'id' => $link['target_type'].'_'.$link['target_id'].'_links_' . $link_id . '_remove_link', 'onclick' => "$('{$link['target_type']}_{$link['target_id']}_links_{$link_id}_remove_confirm').toggle();")); ?>
			<?php echo image_tag('spinning_16.gif', array('id' => "{$link['target_type']}_{$link['target_id']}_links_{$link_id}_remove_indicator", 'style' => 'display: none;')); ?>
		</td>
	<?php endif; ?>
</tr>
<?php if ($tbg_user->canEditMainMenu()): ?>
	<tr id="<?php echo $link['target_type']; ?>_<?php echo $link['target_id']; ?>_links_<?php echo $link_id; ?>_remove_confirm" style="display: none;">
		<td colspan="2">
			<div class="rounded_box white shadowed" style="position: absolute; padding: 0 5px 5px 5px; font-size: 12px; width: 300px; z-index: 10001;">
				<div class="header_div" style="margin-top: 0;"><?php echo __('Are you sure?'); ?></div>
				<div class="content" style="padding: 3px;">
					<?php echo __('Do you really want to remove this item from the menu?'); ?>
					<div style="text-align: right;">
						<?php echo javascript_link_tag(__('Yes'), array('onclick' => "$('{$link['target_type']}_{$link['target_id']}_links_{$link_id}_remove_confirm').toggle();TBG.Main.Link.remove('".make_url('remove_link', array('target_type' => $link['target_type'], 'target_id' => $link['target_id'], 'link_id' => $link_id))."', '{$link['target_type']}', '{$link['target_id']}', ".$link_id.");")); ?> ::
						<?php echo javascript_link_tag('<b>'.__('No').'</b>', array('onclick' => "$('{$link['target_type']}_{$link['target_id']}_links_{$link_id}_remove_confirm').toggle();")); ?>
					</div>
				</div>
			</div>
		</td>
	</tr>
<?php endif; ?>