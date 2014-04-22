<script type="text/javascript">
	TBG.Modules.mailing = {};
	TBG.Modules.mailing.checkIncomingAccount = function(url, account_id) {
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: 'mailing_account_' + account_id + '_indicator'},
			success: {
				callback: function(json) {
					$('mailing_account_' + account_id + '_time').update(json.time);
					$('mailing_account_' + account_id + '_count').update(json.count);
				}
			}
		});
	};
	
	TBG.Modules.mailing.deleteIncomingAccount = function(url, account_id) {
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: 'mailing_account_' + account_id + '_indicator'},
			success: {
				remove: 'incoming_email_account_' + account_id,
				callback: TBG.Main.Helpers.Dialog.dismiss
			}
		});
	};
</script>
<div id="tab_mailing_pane"<?php if ($selected_tab != 'mailing'): ?> style="display: none;"<?php endif; ?>>
<h3><?php echo __('Editing email settings');?></h3>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_mailing_settings', array('project_id' => $project->getID())); ?>" method="post" onsubmit="TBG.Main.Helpers.formSubmit('<?php echo make_url('configure_mailing_settings', array('project_id' => $project->getID())); ?>', 'vcs'); return false;" id="vcs">
		<input type="hidden" name="project_id" value="<?php echo $project->getID(); ?>">
		<table style="clear: both; width: 780px; margin-bottom: 25px;" class="padded_table" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 200px;"><label for="mailing_from_address"><?php echo __('Project from-address'); ?></label></td>
				<td style="width: 580px;">
					<input type="email" name="mailing_from_address" style="width: 300px;" id="mailing_from_address" value="<?php echo TBGSettings::get('project_from_address_'.$project->getID(), 'mailing'); ?>">
				</td>
			</tr>
			<tr>
				<td style="width: 200px;"><label for="mailing_from_name"><?php echo __('Project from-name'); ?></label></td>
				<td style="width: 580px;">
					<input type="text" name="mailing_from_name" style="width: 300px;" id="mailing_from_name" value="<?php echo TBGSettings::get('project_from_name_'.$project->getID(), 'mailing'); ?>">
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('By specifying an email address here, users can hit the "Reply" button on email notifications, and replies will be sent to the specified address instead of the usual generic no-reply address.'); ?></td>
			</tr>
		</table>
		<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan="2" style="padding: 10px 0 10px 10px; text-align: right;">
					<div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation"><?php echo __('When you are done, click "%save" to save your changes', array('%save' => __('Save'))); ?></div>
					<div id="mailing_button" style="float: right; font-size: 14px; font-weight: bold;">
						<input type="submit" class="button button-green" value="<?php echo __('Save'); ?>">
					</div>
					<span id="mailing_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
				</td>
			</tr>
		</table>
	</form>
	<div class="content">
		<?php echo __('The Bug Genie can check email accounts and create issues from incoming emails. Set up a new account here, and check the %online_documentation for more information.', array('%online_documentation' => link_tag('http://issues.thebuggenie.com/wiki/TheBugGenie:IncomingEmail', '<b>'.__('online documentation').'</b>'))); ?>
	</div>
	<?php if ($access_level != TBGSettings::ACCESS_FULL): ?>
		<div class="rounded_box red" style="margin-top: 10px;">
			<?php echo __('You do not have the relevant permissions to access email settings'); ?>
		</div>
	<?php else: ?>
		<h4>
			<div class="button button-green" style="float: right;" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'mailing_editincomingemailaccount', 'project_id' => $project->getId())); ?>');"><?php echo __('Add new account'); ?></div>
			<?php echo __('Incoming email accounts'); ?>
		</h4>
		<div id="mailing_incoming_accounts">
			<?php foreach (TBGContext::getModule('mailing')->getIncomingEmailAccountsForProject(TBGContext::getCurrentProject()) as $account): ?>
				<?php include_template('mailing/incomingemailaccount', array('account' => $account)); ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
