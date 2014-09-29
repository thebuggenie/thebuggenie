<div id="incoming_email_account_<?php echo $account->getID(); ?>">
    <h5>
        <div class="button-group" style="float: right; margin: 10px 0 0 5px;">
            <div class="button button-green">
                <span onclick="TBG.Modules.mailing.checkIncomingAccount('<?php echo make_url('mailing_check_account', array('account_id' => $account->getID())); ?>', <?php echo $account->getID(); ?>);"><?php echo __('Check now'); ?></span>
            </div>
            <div class="button button-silver"><span onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'mailing_editincomingemailaccount', 'project_id' => $account->getProject()->getId(), 'account_id' => $account->getID())); ?>');"><?php echo __('Edit'); ?></span></div>
            <div class="button button-silver"><span onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Really delete this incoming email account?'); ?>', '<?php echo __('Deleting this incoming email account will stop emails being retrieved from this account. All existing issues are still kept.'); ?>', { yes: { click: function() { TBG.Modules.mailing.deleteIncomingAccount('<?php echo make_url('mailing_delete_account', array('account_id' => $account->getID())); ?>', <?php echo $account->getID(); ?>);}}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});"><?php echo __('Delete'); ?></span></div>
        </div>
        <?php echo image_tag('spinning_16.gif', array('style' => 'float: right; margin: 13px 10px 0 0; display: none;', 'id' => 'mailing_account_'.$account->getID().'_indicator')); ?>
        <span id="mailing_account_<?php echo $account->getID(); ?>_name"><?php echo $account->getName(); ?></span> <span class="faded_out" style="font-size: 0.8em; font-weight: normal;"><?php echo $account->getServer(); ?></span>
        <div style="font-size: 0.9em; font-weight: normal;">
            <span><?php echo __('Last checked: %time', array('%time' => '')); ?><span id="mailing_account_<?php echo $account->getID(); ?>_time" style="font-style: italic;"><?php echo ($account->getTimeLastFetched()) ? tbg_formatTime($account->getTimeLastFetched(), 6) : __('%last_checked never', array('%last_checked' => '')); ?></span>.</span>
            <span><?php echo __('Email(s) processed: %number', array('%number' => '<span id="mailing_account_'.$account->getID().'_count" style="font-style: italic;">'.$account->getNumberOfEmailsLastFetched().'</span>')); ?></span>
        </div>
    </h5>
</div>
