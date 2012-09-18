<?php if ($showitems): ?>
<?php if (isset($customtype) && $customtype->getType() == TBGCustomDatatype::CALCULATED_FIELD): ?>
	<div class="header_div" style="margin-top: 15px;">
		<?php echo __('Formula'); ?>
	</div>
	<p><?php echo __('To use a custom field in the formula, enter the field key (displayed in light gray text next to the name) between curly braces.'); ?></p>
	<p><?php echo __('Example: ({myfield}+{otherfield})/({thirdfield}*2)'); ?></p>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_add', array('type' => $type)); ?>" onsubmit="TBG.Config.Issuefields.Options.add('<?php echo make_url('configure_issuefields_add', array('type' => $type)); ?>', '<?php echo $type; ?>');return false;" id="add_<?php echo $type; ?>_form">
		<label for="add_option_<?php echo $type; ?>_itemdata"><?php echo __('Value'); ?></label>
		<input type="hidden" id="add_option_<?php echo $type; ?>_name" name="name" value="Formula">
		<?php $value = (!empty($items) ? array_pop($items)->getValue() : ''); ?>
		<input type="text" id="add_option_<?php echo $type; ?>_itemdata" name="value" value="<?php echo $value ?>" style="width: 400px;">
		<input type="submit" value="<?php echo __('Save'); ?>" style="margin-right: 5px; font-weight: bold;">
		<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_' . $type . '_indicator')); ?>
	</form>
<?php else: ?>
	<div class="header_div" style="margin-top: 15px;">
		<?php echo __('Existing choices'); ?>
		<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none; float: right;', 'id' => $type . '_sort_indicator')); ?>
	</div>
	<ul class="simple_list" id="<?php echo $type; ?>_list">
		<?php if (count($items) > 0): ?>
			<?php foreach ($items as $item): ?>
				<?php include_template('issuefield', array('item' => $item, 'type' => $type, 'access_level' => $access_level)); ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	<div class="faded_out dark" id="no_<?php echo $type; ?>_items" style="<?php if (count($items) > 0): ?>display: none; <?php endif; ?>padding: 3px;"><?php echo __('There are no items'); ?></div>
	<div class="header_div" style="margin: 15px 0 2px 0;"><?php echo __('Add an option'); ?></div>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_add', array('type' => $type)); ?>" onsubmit="TBG.Config.Issuefields.Options.add('<?php echo make_url('configure_issuefields_add', array('type' => $type)); ?>', '<?php echo $type; ?>');return false;" id="add_<?php echo $type; ?>_form">
		<?php if ($type == 'status'): ?>
			<label for="add_option_<?php echo $type; ?>_itemdata"><?php echo __('Color'); ?></label>
			#<input type="text" id="add_option_<?php echo $type; ?>_itemdata" name="itemdata" style="width: 45px;">
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
	<script>
		Sortable.create('<?php echo $type; ?>_list', {constraint: '', onUpdate: function(container) { TBG.Config.Issuefields.saveOrder(container, '<?php echo $type; ?>', '<?php echo make_url('configure_issuefields_saveorder', array('type' => $type)); ?>'); }});
	</script>
<?php endif; ?>
<?php endif; ?>