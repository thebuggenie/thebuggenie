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
<h3>Editing email settings</h3>
	<?php if ($access_level != TBGSettings::ACCESS_FULL): ?>
		<div class="rounded_box red" style="margin-top: 10px;">
			<?php echo __('You do not have the relevant permissions to access email settings'); ?>
		</div>
	<?php else: ?>
		<h4>
			<div class="button button-green" style="float: right;"><span onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'mailing_editincomingemailaccount', 'project_id' => $project->getId())); ?>');"><?php echo __('Add new account'); ?></span></div>
			<?php echo __('Incoming email accounts'); ?>
		</h4>
		<div id="mailing_incoming_accounts">
			<?php foreach (TBGContext::getModule('mailing')->getIncomingEmailAccountsForProject(TBGContext::getCurrentProject()) as $account): ?>
				<?php include_template('mailing/incomingemailaccount', array('account' => $account)); ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>