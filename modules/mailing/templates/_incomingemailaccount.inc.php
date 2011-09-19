<div id="incoming_email_account_<?php echo $account->getID(); ?>">
	<h5>
		<div class="button button-silver" style="float: right; margin: 10px 0 0 5px;"><span onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'mailing_editincomingemailaccount', 'project_id' => $account->getProject()->getId(), 'account_id' => $account->getID())); ?>');"><?php echo __('Edit'); ?></span></div>
		<div class="button button-green" style="float: right; margin-top: 10px;"><span onclick="TBG.Modules.mailing.checkIncomingAccount('<?php echo make_url('mailing_check_account', array('account_id' => $account->getID())); ?>');"><?php echo __('Check now'); ?></span></div>
		<span id="mailing_account_<?php echo $account->getID(); ?>_name"><?php echo $account->getName(); ?></span> <span class="faded_out" style="font-size: 0.8em; font-weight: normal;"><?php echo $account->getServer(); ?></span>
		<div style="font-size: 0.9em; font-weight: normal;">
			<span><?php echo __('Last checked: %time%', array('%time%' => '')); ?><span id="mailing_account_<?php echo $account->getID(); ?>_time" style="font-style: italic;"><?php echo ($account->getTimeLastFetched()) ? tbg_formatTime($account->getTimeLastFetched(), 6) : __('%last_checked% never', array('%last_checked%' => '')); ?></span>.</span>
			<span><?php echo __('Email(s) processed: %number%', array('%number%' => '<span id="mailing_account_'.$account->getID().'_count" style="font-style: italic;">'.$account->getNumberOfEmailsLastFetched().'</span>')); ?></span>
		</div>
	</h5>
</div>