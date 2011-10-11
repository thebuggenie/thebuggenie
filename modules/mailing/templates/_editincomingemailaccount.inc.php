<script type="text/javascript">
	TBG.Modules.mailing.saveIncomingEmailAccount = function(url) {
		TBG.Main.Helpers.ajax(url, {
			form: 'incoming_email_account_form',
			loading: {indicator: 'add_account_indicator'},
			success: {
				update: {element: <?php echo ($account->getID()) ? "'mailing_account_{$account->getID()}_name', from: 'name'" : "'mailing_incoming_accounts', insertion: true"; ?>},
				callback: function() {
					TBG.Main.Helpers.Backdrop.reset();
				}
			}
		});
	};
</script>
<div class="backdrop_box large">
	<div class="backdrop_detail_header">
		<?php echo ($account->getId()) ? __('Edit incoming email account') : __('Add new incoming email account'); ?>
	</div>
	<div id="backdrop_detail_content">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" id="incoming_email_account_form" action="<?php echo make_url('mailing_save_incoming_account', array('project_key' => $project->getKey())); ?>" method="post" id="build_form" onsubmit="TBG.Modules.mailing.saveIncomingEmailAccount('<?php echo make_url('mailing_save_incoming_account', array('project_key' => $project->getKey())); ?>');return false;">
			<input type="hidden" name="account_id" value="<?php echo $account->getID(); ?>">
			<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
				<tr>
					<td style="width: 200px;"><label for="account_name"><?php echo __('Account name:'); ?></label></td>
					<td style="width: 580px;"><input type="text" name="name" id="account_name" style="width: 570px;" value="<?php echo $account->getName(); ?>"></td>
				</tr>
				<tr>
					<td><label for="account_servername"><?php echo __('Server name:'); ?></label></td>
					<td><input type="text" name="servername" id="account_servername" style="width: 300px;" value="<?php echo $account->getServer(); ?>"></td>
				</tr>
				<tr>
					<td><label for="account_port"><?php echo __('Port number:'); ?></label></td>
					<td><input type="text" name="port" id="account_port" style="width: 100px;" value="<?php echo $account->getPort(); ?>"></td>
				</tr>
				<tr>
					<td><label for="account_username"><?php echo __('Email username:'); ?></label></td>
					<td><input type="text" name="username" id="account_username" style="width: 200px;" value="<?php echo $account->getUsername(); ?>"></td>
				</tr>
				<tr>
					<td><label for="account_password"><?php echo __('Email password:'); ?></label></td>
					<td><input type="password" name="password" id="account_password" style="width: 200px;" value="<?php echo $account->getPassword(); ?>"></td>
				</tr>
				<tr>
					<td><label for="account_type_imap"><?php echo __('Account type'); ?></label></td>
					<td>
						<input type="radio" name="account_type" id="account_type_imap" value="<?php echo TBGIncomingEmailAccount::SERVER_IMAP; ?>"<?php if ($account->isImap()) echo ' checked'; ?>><label for="account_type_imap" style="font-weight: normal;"><?php echo __('Microsoft Exchange / IMAP'); ?></label>
						<input type="radio" name="account_type" id="account_type_pop3" value="<?php echo TBGIncomingEmailAccount::SERVER_POP3; ?>"<?php if ($account->isPop3()) echo ' checked'; ?>><label for="account_type_pop3" style="font-weight: normal;"><?php echo __('POP3'); ?></label>
					</td>
				</tr>
				<tr>
					<td><label for="account_ssl"><?php echo __('Use secure connection (SSL)'); ?></label></td>
					<td>
						<input type="radio" name="ssl" id="ssl_yes" value="1"<?php if ($account->usesSSL()) echo ' checked'; ?>><label for="ssl_yes" style="font-weight: normal;"><?php echo __('Yes'); ?></label>
						<input type="radio" name="ssl" id="ssl_no" value="0"<?php if (!$account->usesSSL()) echo ' checked'; ?>><label for="ssl_no" style="font-weight: normal;"><?php echo __('No'); ?></label>
					</td>
				</tr>
				<tr>
					<td><label for="account_issuetype"><?php echo __('Issuetype'); ?></label></td>
					<td>
						<select id="account_issuetype" name="issuetype">
							<?php foreach ($project->getIssuetypeScheme()->getReportableIssuetypes() as $issuetype): ?>
							<option value="<?php echo $issuetype->getID(); ?>"<?php if ($account->getIssuetypeID() == $issuetype->getID()) echo " selected"; ?>><?php echo $issuetype->getName(); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</table>
			<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="2" style="padding: 10px 0 10px 10px; text-align: right;">
						<div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation">
							<?php if ($account->getId()): ?>
								<?php echo __('When you are done, click "%save_changes%" to update the details for this account', array('%save_changes%' => __('Save changes'))); ?>
							<?php else: ?>
								<?php echo __('When you are done, click "%add_account%" to add this account', array('%add_account%' => __('Add account'))); ?>
							<?php endif; ?>
						</div>
						<?php if ($account->getID()): ?>
							<input type="hidden" name="account_id" value="<?php echo $account->getID(); ?>">
							<input type="hidden" name="project_id" value="<?php echo $project->getID(); ?>">
						<?php endif; ?>
						<div class="button button-green" style="float: right;">
							<input type="submit" value="<?php echo ($account->getId()) ? __('Save changes') : __('Add account'); ?>">
						</div>
						<span id="add_account_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div class="backdrop_detail_footer">
		<?php echo javascript_link_tag(__('Close popup'), array('onclick' => 'TBG.Main.Helpers.Backdrop.reset();')); ?>
	</div>
</div>