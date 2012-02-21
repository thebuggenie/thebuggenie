<?php if ($too_short): ?>
	<div style="padding: 3px; margin-top: 5px; font-weight: normal; font-size: 14px;" class="faded_out">
		<?php echo __('Please enter something to search for'); ?>
	</div>
<?php else: ?>
	<div style="padding: 3px; margin-top: 5px; font-weight: normal; font-size: 14px;" class="faded_out">
		<?php if (isset($title)): ?>
			<?php echo $title; ?>
		<?php else: ?>
			<?php echo __('%count% users found when searching for "%searchstring%"', array('%count%' => "<span class=\"find_users_num_results\">{$total_results}</span>", '%searchstring%' => $findstring)); ?>
		<?php endif ?>
	</div>
	<?php if ($total_results > 0): ?>
		<table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-top: 5px;">
			<thead>
				<tr>
					<th style="width: 20px;">&nbsp;</th>
					<th><?php echo __('ID'); ?></th>
					<th><?php echo __('Username'); ?></th>
					<th><?php echo __('Real name (nickname)'); ?></th>
					<th><?php echo __('E-mail'); ?></th>
					<th><?php echo __('Actv.'); ?></th>
					<th style="width: 80px;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($users as $user): ?>
				<tr class="hover_highlight" id="users_results_user_<?php echo $user->getID(); ?>" onclick="$('users_results_user_<?php echo $user->getID(); ?>').toggleClassName('selected_green');">
					<?php include_template('finduser_row', array('user' => $user)); ?>
				</tr>
				<tr id="user_<?php echo $user->getID(); ?>_edit_spinning" class="selected_green" style="display: none;">
					<td style="padding: 3px;" colspan="7">
						<?php echo image_tag('spinning_32.gif'); ?>
					</td>
				</tr>
				<tr id="user_<?php echo $user->getID(); ?>_edit_tr" class="selected_green" style="display: none;">
					<td style="padding: 3px;" colspan="7" id="user_<?php echo $user->getID(); ?>_edit_td">
						&nbsp;
					</td>
				</tr>
				<tr id="users_results_user_<?php echo $user->getID(); ?>_permissions_row" style="display: none;">
					<td id="users_results_user_<?php echo $user->getID(); ?>_permissions" colspan="7" class="config_permissions" style="padding-bottom: 5px;"></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
<?php endif; ?>
<?php if (isset($more_available)): ?>
	<script type="text/javascript">
		<?php if (!$more_available): ?>
			$('adduser_div').hide();
		<?php else: ?>
			$('adduser_div').show();
		<?php endif; ?>
		<?php if (TBGContext::getScope()->getMaxUsers()): ?>
			$('current_user_num_count').update(<?php echo $total_count; ?>);
		<?php endif; ?>
	</script>
<?php endif; ?>