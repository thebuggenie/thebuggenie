<div class="rounded_box mediumgrey borderless" style="padding: 0; margin-top: 5px;" id="clientbox_<?php echo $client->getID(); ?>">
	<div style="padding: 5px;">
		<?php echo image_tag('client_large.png', array('style' => 'float: left; margin-right: 5px;')); ?>
		<?php echo javascript_link_tag(image_tag('action_delete.png'), array('title' => __('Delete this user client'), 'onclick' => '$(\'confirm_client_'.$client->getID().'_delete\').toggle();', 'style' => 'float: right;', 'class' => 'image')); ?>
		<?php echo javascript_link_tag(image_tag('icon_edit.png'), array('title' => __('Edit this user client'), 'onclick' => '$(\'edit_client_'.$client->getID().'\').toggle();', 'style' => 'float: right; margin-right: 5px;', 'class' => 'image')); ?>
		<?php echo javascript_link_tag(image_tag('client_list_users.png'), array('title' => __('List users in this client'), 'onclick' => 'TBG.Config.Client.showMembers(\''.make_url('configure_users_get_client_members', array('client_id' => $client->getID())).'\', '.$client->getID().');', 'style' => 'float: right; margin-right: 5px;', 'class' => 'image')); ?>
		<p class="clientbox_header"><?php echo $client->getName(); ?></p>
		<p class="clientbox_membercount"><?php echo __('ID: %id%', array('%id%' => $client->getID())); ?> - <?php echo __('%number_of% member(s)', array('%number_of%' => '<span id="client_'.$client->getID().'_membercount">'.$client->getNumberOfMembers().'</span>')); ?></p>
		<div class="rounded_box white shadowed" style="margin: 5px; display: none;" id="edit_client_<?php echo $client->getID(); ?>">
			<div class="dropdown_header"><?php echo __('Edit client settings'); ?></div>
			<div class="dropdown_content">
				<form id="edit_client_<?php echo $client->getID(); ?>_form" action="<?php echo make_url('configure_users_edit_client', array('client_id' => $client->getID())); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="editClient('<?php echo make_url('configure_users_edit_client', array('client_id' => $client->getID())); ?>', '<?php echo $client->getID(); ?>');return false;">
				<input type="hidden" name="client_id" value="<?php echo $client->getID(); ?>">
					<div id="edit_client">
						<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
							<tr>
								<td style="width: 120px;">
									<label for="client_<?php echo $client->getID(); ?>_new_name"><?php echo __('Client name'); ?></label>
								</td>
								<td>
									<input style="width: 580px;" type="text" id="edit_name" name="client_name" value="<?php echo $client->getName(); ?>">
								</td>
							</tr>
							<tr>
								<td>
									<label for="edit_client_<?php echo $client->getID(); ?>_new_email"><?php echo __('Email Address'); ?></label>
								</td>
								<td>
									<input style="width: 580px;" type="text" id="client_email" name="client_email" value="<?php echo $client->getEmail(); ?>">
								</td>
							</tr>
							<tr>
								<td>
									<label for="edit_client_<?php echo $client->getID(); ?>_new_website"><?php echo __('Website'); ?></label>
								</td>
								<td>
									<input style="width: 580px;" type="text" id="client_website" name="client_website" value="<?php echo $client->getWebsite(); ?>">
								</td>
							</tr>
							<tr>
								<td>
									<label for="edit_client_<?php echo $client->getID(); ?>_new_telephone"><?php echo __('Telephone number'); ?></label>
								</td>
								<td>
									<input style="width: 580px;" type="text" id="client_telephone" name="client_telephone" value="<?php echo $client->getTelephone(); ?>">
								</td>
							</tr>
							<tr>
								<td>
									<label for="edit_client_<?php echo $client->getID(); ?>_new_fax"><?php echo __('Fax number'); ?></label>
								</td>
								<td>
									<input style="width: 580px;" type="text" id="client_fax" name="client_fax" value="<?php echo $client->getFax(); ?>">
								</td>
							</tr>
						</table>
					</div>
				<div style="text-align: right;">
					<input type="submit" id="edit_client_<?php echo $client->getID(); ?>_save_button" style="padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>"> <?php echo javascript_link_tag(__('or cancel'), array('onclick' => '$(\'edit_client_'.$client->getID().'\').toggle();')); ?>
				</div>
				</form>
				<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="edit_client_<?php echo $client->getID(); ?>_indicator">
					<tr>
						<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
						<td style="padding: 0px; text-align: left;"><?php echo __('Saving, please wait'); ?>...</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="rounded_box white shadowed" style="margin: 5px; display: none;" id="confirm_client_<?php echo $client->getID(); ?>_delete">
			<div class="dropdown_header"><?php echo __('Do you really want to delete this client?'); ?></div>
			<div class="dropdown_content">
				<?php echo __('If you delete this client, any projects this client is assigned to will be set to having no client.'); ?>
				<div style="text-align: right;">
					<?php echo javascript_link_tag(__('Yes'), array('onclick' => 'TBG.Config.Client.remove(\''.make_url('configure_users_delete_client', array('client_id' => $client->getID())).'\', '.$client->getID().');')); ?> :: <b><?php echo javascript_link_tag(__('No'), array('onclick' => '$(\'confirm_client_'.$client->getID().'_delete\').toggle();')); ?></b>
				</div>
				<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="delete_client_<?php echo $client->getID(); ?>_indicator">
					<tr>
						<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
						<td style="padding: 0px; text-align: left;"><?php echo __('Deleting client, please wait'); ?>...</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div class="rounded_box lightgrey" style="margin-bottom: 5px; display: none;" id="client_members_<?php echo $client->getID(); ?>_container">
		<div class="dropdown_header"><?php echo __('Users in this client'); ?></div>
		<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="client_members_<?php echo $client->getID(); ?>_indicator">
			<tr>
				<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
				<td style="padding: 0px; text-align: left;"><?php echo __('Retrieving members, please wait'); ?>...</td>
			</tr>
		</table>
		<div id="client_members_<?php echo $client->getID(); ?>_list"></div>
	</div>
</div>