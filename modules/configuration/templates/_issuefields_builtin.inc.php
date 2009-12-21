<div class="header_div" style="margin-top: 15px;">
	<?php echo __('Existing choices'); ?>
</div>
<?php if (count($items) > 0): ?>
	<table style="width: 100%;" cellpadding="0" cellspacing="0">
		<tbody id="<?php echo $type; ?>_list">
			<?php foreach ($items as $item): ?>
				<?php include_template('issuefield_builtin', array('item' => $item, 'type' => $type)); ?>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
<div class="faded_dark" id="no_<?php echo $type; ?>_items" style="<?php if (count($items) > 0): ?>display: none; <?php endif; ?>padding: 3px;"><?php echo __('There are no items'); ?></div>
<div class="header_div" style="margin-top: 15px;"><?php echo __('Add another'); ?></div>
<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_add', array('type' => $type)); ?>" onsubmit="addIssuefieldOption('<?php echo make_url('configure_issuefields_add', array('type' => $type)); ?>', '<?php echo $type; ?>');return false;" id="add_<?php echo $type; ?>_form">
	<table style="width: 100%;" cellpadding="0" cellspacing="0">
		<thead class="borderless">
			<tr>
				<?php if ($type == 'status'): ?>
					<th style="width: 70px;"><?php echo __('Color'); ?></th>
				<?php endif; ?>
				<th style="width: auto;"><?php echo __('Name'); ?></th>
				<th style="width: 100px;">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<?php if ($type == 'status'): ?>
					<td style="font-size: 14px;">
						#<input type="text" name="color" style="width: 45px;">
					</td>
				<?php endif; ?>
				<td>
					<input type="text" name="name" style="width: 400px;">
				</td>
				<td style="text-align: right;">
					<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_' . $type . '_indicator')); ?>
					<input type="submit" value="<?php echo __('Add'); ?>" style="margin-right: 5px; font-weight: bold;">
				</td>
			</tr>
		</tbody>
	</table>
</form>