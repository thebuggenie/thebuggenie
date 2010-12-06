<div class="rounded_box mediumgrey borderless" style="margin: 5px 0 0 0; font-size: 12px;" id="issuetype_<?php echo $type->getID(); ?>_box">
	<?php echo image_tag('spinning_32.gif', array('style' => 'float: right; margin-left: 5px; display: none;', 'id' => 'issuetype_' . $type->getID() . '_indicator')); ?>
	<div class="header">
		<?php echo $type->getName(); ?>
	</div>
	<?php if (isset($scheme)): ?>
		<a title="<?php echo __('Show and edit available choices'); ?>" href="javascript:void(0);" onclick="showIssuetypeOptions('<?php echo make_url('configure_issuetypes_getoptions_for_scheme', array('id' => $type->getID(), 'scheme_id' => $scheme->getID())); ?>', <?php echo $type->getID(); ?>);" class="image" style="float: right; margin-right: 5px;"><?php echo image_tag('action_dropdown_small.png'); ?></a>
	<?php endif; ?>
	<a title="<?php echo __('Edit issue type settings'); ?>" href="javascript:void(0);" onclick="$('edit_issuetype_<?php echo $type->getID(); ?>_form').toggle();$('issuetype_<?php echo $type->getID(); ?>_info').toggle();" class="image" style="float: right; margin-right: 5px;"><?php echo image_tag('icon_edit.png'); ?></a>
	<div id="issuetype_<?php echo $type->getID(); ?>_info">
		<b><?php echo __('Description'); ?>:</b>&nbsp;<span id="issuetype_<?php echo $type->getID(); ?>_description_span"><?php echo $type->getDescription(); ?></span><br>
	</div>
	<?php if (!isset($scheme)): ?>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuetypes_update_issuetype', array('id' => $type->getID())); ?>" onsubmit="updateIssuetype('<?php echo make_url('configure_issuetypes_update_issuetype', array('id' => $type->getID())); ?>', <?php echo $type->getID(); ?>);return false;" id="edit_issuetype_<?php echo $type->getID(); ?>_form" style="display: none;">
	<?php else: ?>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuetypes_update_issuetype_for_scheme', array('id' => $type->getID(), 'scheme_id' => $scheme->getID())); ?>" onsubmit="updateIssuetype('<?php echo make_url('configure_issuetypes_update_issuetype_for_scheme', array('id' => $type->getID(), 'scheme_id' => $scheme->getID())); ?>', <?php echo $type->getID(); ?>);return false;" id="edit_issuetype_<?php echo $type->getID(); ?>_form" style="display: none;">
	<?php endif; ?>
			<div class="rounded_box white borderless" style="clear: both; margin: 5px; font-size: 12px;">
			<table cellpadding="0" cellspacing="0">
				<?php if (!isset($scheme)): ?>
					<tr>
						<td style="vertical-align: top; padding-top: 5px;"><label for="issuetype_<?php echo $type->getID(); ?>_name"><?php echo __('Name'); ?></label></td>
						<td><input type="text" name="name" id="issuetype_<?php echo $type->getID(); ?>_name" value="<?php echo $type->getName(); ?>" style="width: 300px;"><br></td>
					</tr>
					<tr>
						<td style="vertical-align: top; padding-top: 5px;"><label for="issuetype_<?php echo $type->getID(); ?>_description"><?php echo __('Description'); ?></label></td>
						<td>
							<input type="text" name="description" id="issuetype_<?php echo $type->getID(); ?>_description" value="<?php echo $type->getDescription(); ?>" style="width: 600px;">
							<div class="faded_out" style="margin-bottom: 10px; padding: 2px; font-size: 12px;"><?php echo __('Users see this description when choosing an issue type to report'); ?>.</div>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top; padding-top: 5px;"><label for="issuetype_<?php echo $type->getID(); ?>_icon"><?php echo __('Issue type'); ?></label></td>
						<td>
							<select name="icon" id="issuetype_<?php echo $type->getID(); ?>_icon">
								<?php foreach ($icons as $icon => $description): ?>
									<option value="<?php echo $icon; ?>"<?php if ($type->getIcon() == $icon): ?> selected<?php endif; ?>><?php echo $description; ?></option>
								<?php endforeach; ?>
							</select>
							<div class="faded_out" style="margin-bottom: 10px; padding: 2px; font-size: 12px;"><?php echo __('Select what kind of issue type this is'); ?></div>
						</td>
					</tr>
				<?php else: ?>
					<tr>
						<td style="vertical-align: top; padding-top: 5px;"><label for="issuetype_<?php echo $type->getID(); ?>_reportable"><?php echo __('Reportable'); ?></label></td>
						<td>
							<select name="reportable" id="issuetype_<?php echo $type->getID(); ?>_reportable">
								<option value="1"<?php if ($scheme->isIssuetypeReportable($type)): ?> selected<?php endif; ?>><?php echo __('Users can report new issues with this issue type'); ?></option>
								<option value="0"<?php if (!$scheme->isIssuetypeReportable($type)): ?> selected<?php endif; ?>><?php echo __('Users cannot report new issues with this issue type'); ?></option>
							</select>
							<div class="faded_out" style="margin-bottom: 10px; padding: 2px; font-size: 12px;"><?php echo __('Whether this issue type is enabled for reporting or not'); ?>.</div>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top; padding-top: 5px;"><label for="issuetype_<?php echo $type->getID(); ?>_redirect"><?php echo __('Redirect'); ?></label></td>
						<td>
							<select name="redirect_after_reporting" id="issuetype_<?php echo $type->getID(); ?>_redirect">
								<option value="1"<?php if ($scheme->isIssuetypeRedirectedAfterReporting($type)): ?> selected<?php endif; ?>><?php echo __('Redirect to the reported issue after it has been reported'); ?></option>
								<option value="0"<?php if (!$scheme->isIssuetypeRedirectedAfterReporting($type)): ?> selected<?php endif; ?>><?php echo __('Reload a blank "%report_issue%" page with a link to the reported issue at the top', array('%report_issue%' => __('Report issue'))); ?></option>
							</select>
							<div class="faded_out" style="margin-bottom: 10px; padding: 2px; font-size: 12px;"><?php echo __('Whether to forward the user to the reported issue after it has been reported'); ?>.</div>
						</td>
					</tr>
				<?php endif; ?>
			</table>
			<input type="submit" value="<?php echo __('Update details'); ?>" style="font-weight: bold; font-size: 13px;">
			<?php echo __('%update_details% or %cancel%', array('%update_details%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'edit_issuetype_' . $type->getID() . '_form\').toggle();$(\'issuetype_' . $type->getID() . '_info\').toggle();"><b>' . __('cancel') . '</b></a>')); ?>
			<?php echo image_tag('spinning_20.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'edit_issuetype_' . $type->getID() . '_indicator')); ?>
		</div>
	</form>
	<div class="content" id="issuetype_<?php echo $type->getID(); ?>_content" style="display: none;"> </div>
</div>