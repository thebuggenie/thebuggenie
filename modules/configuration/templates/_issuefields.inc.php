<?php if ($showitems): ?>
	<div class="header_div" style="margin-top: 15px;">
		<?php echo __('Existing choices'); ?>
	</div>
	<table style="width: 100%;" cellpadding="0" cellspacing="0">
		<tbody id="<?php echo $type; ?>_list">
			<?php if (count($items) > 0): ?>
				<?php foreach ($items as $item): ?>
					<?php include_template('issuefield', array('item' => $item, 'type' => $type, 'access_level' => $access_level)); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<div class="faded_out dark" id="no_<?php echo $type; ?>_items" style="<?php if (count($items) > 0): ?>display: none; <?php endif; ?>padding: 3px;"><?php echo __('There are no items'); ?></div>
	<div class="header_div" style="margin: 15px 0 2px 0;"><?php echo __('Add an option'); ?></div>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_add', array('type' => $type)); ?>" onsubmit="TBG.Issues.Field.Options.add('<?php echo make_url('configure_issuefields_add', array('type' => $type)); ?>', '<?php echo $type; ?>');return false;" id="add_<?php echo $type; ?>_form">
		<?php if ($type == 'status'): ?>
			<label for="add_option_<?php echo $type; ?>_itemdata"><?php echo __('Color'); ?></label>
			#<input type="text" id="add_option_<?php echo $type; ?>_itemdata" name="itemdata" style="width: 45px;" onclick="picker.show(this);">
		<?php endif; ?>
		<?php if (!array_key_exists($type, TBGDatatype::getTypes())): ?>
			<label for="add_option_<?php echo $type; ?>_itemdata"><?php echo __('Value'); ?></label>
			<input type="text" id="add_option_<?php echo $type; ?>_itemdata" name="value" style="width: 45px;">
		<?php endif; ?>
		<label for="add_option_<?php echo $type; ?>_name"><?php echo __('Name'); ?></label>
		<input type="text" id="add_option_<?php echo $type; ?>_name" name="name" style="width: 400px;">
		<input type="submit" value="<?php echo __('Add'); ?>" style="margin-right: 5px; font-weight: bold;">
		<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_' . $type . '_indicator')); ?>
	</form>
<?php endif; ?>