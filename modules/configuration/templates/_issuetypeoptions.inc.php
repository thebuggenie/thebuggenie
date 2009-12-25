<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuetypes_update_choices', array('id' => $issuetype->getID())); ?>" onsubmit="updateIssuetypeChoices('<?php echo make_url('configure_issuetypes_update_choices', array('id' => $issuetype->getID())); ?>', <?php echo $issuetype->getID(); ?>);return false;" id="update_<?php echo $issuetype->getID(); ?>_choices_form">
	<div class="rounded_box white_borderless" style="margin: 5px 0 0 0;">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="padding: 3px; font-size: 12px;">
			<div class="header_div" style="margin-top: 0;">
				<?php echo __('Available issue fields'); ?>
			</div>
			<table style="width: 100%;" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th style="padding: 2px;"><?php echo __('Field'); ?></th>
						<th style="padding: 2px; text-align: center;"><?php echo __('Visible'); ?></th>
						<th style="padding: 2px; text-align: center;"><?php echo __('Reportable'); ?></th>
						<th style="padding: 2px; text-align: center;"><?php echo __('Additional'); ?></th>
						<th style="padding: 2px; text-align: center;"><?php echo __('Required'); ?></th>
					</tr>
				</thead>
				<tbody id="<?php echo $issuetype->getID(); ?>_list">
					<?php foreach ($builtinfields as $item): ?>
						<?php include_template('issuetypeoption', array('issuetype' => $issuetype, 'key' => $item, 'item' => $item, 'visiblefields' => $visiblefields)); ?>
					<?php endforeach; ?>
					<?php if (count($customtypes) > 0): ?>
						<?php foreach ($customtypes as $key => $item): ?>
							<?php include_template('issuetypeoption', array('issuetype' => $issuetype, 'key' => $key, 'item' => $item->getDescription(), 'visiblefields' => $visiblefields)); ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
			<div style="margin: 10px 0 0 0; height: 25px;">
				<div style="float: left; font-size: 13px; padding-top: 2px; font-weight: bold;"><?php echo __('Click "Save" to save your changes'); ?></div>
				<input type="submit" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
				<span id="update_<?php echo $issuetype->getID(); ?>_choices_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
			</div>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
</form>