<div class="container_div menu_links" id="<?php echo $target_type; ?>_<?php echo $target_id; ?>_container" style="margin: 0 0 5px 5px;">
	<div class="header">
		<?php if ($tbg_user->canEditMainMenu() && ((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || !TBGContext::isProjectContext())): ?>
			<?php echo javascript_link_tag(image_tag('action_add_link.png'), array('style' => 'float: right;', 'class' => 'image', 'onclick' => "$('attach_link_{$target_type}_{$target_id}').toggle();", 'title' => __('Add an item to the menu'))); ?>
			<?php echo javascript_link_tag(image_tag('icon_edit.png'), array('style' => 'float: right;', 'class' => 'image', 'onclick' => "$('{$target_type}_{$target_id}_container').toggleClassName('menu_editing');", 'title' => __('Toggle menu edit mode'))); ?>
		<?php endif; ?>
		<?php echo $title; ?>
	</div>
	<?php if ($tbg_user->canEditMainMenu() && ((TBGContext::isProjectContext() && !TBGContext::getCurrentProject()->isArchived()) || !TBGContext::isProjectContext())): ?>
		<div class="rounded_box lightgrey shadowed" id="attach_link_<?php echo $target_type; ?>_<?php echo $target_id; ?>" style="position: absolute; width: 300px; z-index: 10001; margin: 5px 0 5px 5px; display: none">
			<div class="header_div" style="margin: 0 0 5px 0;"><?php echo __('Add a link'); ?>:</div>
			<form action="<?php echo make_url('attach_link', array('target_type' => $target_type, 'target_id' => $target_id)); ?>" method="post" onsubmit="TBG.Main.Link.add('<?php echo make_url('attach_link', array('target_type' => $target_type, 'target_id' => $target_id)); ?>', '<?php echo $target_type; ?>', '<?php echo $target_id; ?>');return false;" id="attach_link_<?php echo $target_type; ?>_<?php echo $target_id; ?>_form">
				<dl style="margin: 0;">
					<dt style="width: 80px; padding-top: 3px;"><label for="attach_link_<?php echo $target_type; ?>_<?php echo $target_id; ?>_url"><?php echo ($target_type == 'wiki') ? __('Article name') : __('URL'); ?>:</label></dt>
					<dd style="margin-bottom: 0px;"><input type="text" name="link_url" id="attach_link_<?php echo $target_type; ?>_<?php echo $target_id; ?>_url" style="width: 95%;"></dd>
					<dt style="width: 80px; font-size: 10px; padding-top: 4px;"><label for="attach_link_<?php echo $target_type; ?>_<?php echo $target_id; ?>_description"><?php echo __('Description'); ?>:</label></dt>
					<dd style="margin-bottom: 0px;"><input type="text" name="description" id="attach_link_<?php echo $target_type; ?>_<?php echo $target_id; ?>_description" style="width: 95%;"></dd>
				</dl>
				<div style="font-size: 12px; padding: 15px 2px 10px 2px;" class="faded_out" id="attach_link_<?php echo $target_type; ?>_<?php echo $target_id; ?>_submit">
					<?php if ($target_type == 'wiki'): ?>
						<?php echo __('Enter the name of the article to link to here, along with an (optional) description, and press "%add_link%" to add it to the menu.', array('%add_link%' => __('Add link'))); ?><br /><br />
					<?php else: ?>
						<?php echo __('Enter the link URL here, along with an (optional) description, and press "%add_link%" to add it to the menu.', array('%add_link%' => __('Add link'))); ?><br /><br />
					<?php endif; ?>
					<?php echo __('To add free text, just enter text in the description - without any url - and press the "%add_link%" button (Text will be parsed according to the %wiki_formatting%).', array('%add_link%' => __('Add link'), '%wiki_formatting%' => link_tag(make_url('publish_article', array('article_name' => 'WikiFormatting')), 'WikiFormatting'))); ?><br /><br />
					<?php echo __('To add a spacer, just press "%add_link%", without any url or description.', array('%add_link%' => __('Add link'))); ?>
					<div style="text-align: center; padding: 10px; display: none;" id="attach_link_<?php echo $target_type; ?>_<?php echo $target_id; ?>_indicator"><?php echo image_tag('spinning_26.gif'); ?></div>
					<div style="text-align: center;"><input type="submit" value="<?php echo __('Add link'); ?>" style="font-weight: bold;"><?php echo __('%attach_link% or %cancel%', array('%attach_link%' => '', '%cancel%' => '<b>'.javascript_link_tag(__('cancel'), array('onclick' => "$('attach_link_{$target_type}_{$target_id}').toggle();")).'</b>')); ?></div>
				</div>
			</form>
		</div>
	<?php endif; ?>
	<div class="content">
		<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
			<tbody id="<?php echo $target_type; ?>_<?php echo $target_id; ?>_links">
				<?php foreach ($links as $link_id => $link): ?>
					<?php include_template('main/menulink', array('link_id' => $link_id, 'link' => $link)); ?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<div style="padding-left: 5px;<?php if (count($links) > 0): ?> display: none;<?php endif; ?>" class="no_items" id="<?php echo $target_type; ?>_<?php echo $target_id; ?>_no_links"><?php echo __('There are no links in this menu'); ?></div>
	</div>
</div>
