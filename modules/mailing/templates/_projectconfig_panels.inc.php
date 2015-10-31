<script type="text/javascript">
    var TBG;

    require(['domReady', 'thebuggenie/tbg'], function (domReady, tbgjs) {
        domReady(function () {
            TBG = tbgjs;
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
                    loading: {
                        indicator: 'fullpage_backdrop',
                        clear: 'fullpage_backdrop_content',
                        show: 'fullpage_backdrop_indicator',
                        hide: 'dialog_backdrop'
                    },
                    success: {
                        remove: 'incoming_email_account_' + account_id,
                        callback: TBG.Main.Helpers.Dialog.dismiss
                    }
                });
            };
        });
    });
</script>
<div id="tab_mailing_settings_pane"<?php if ($selected_tab != 'mailing_settings'): ?> style="display: none;"<?php endif; ?>>
    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('mailing_configure_settings', array('project_id' => $project->getID())); ?>" method="post" onsubmit="TBG.Main.Helpers.formSubmit('<?php echo make_url('mailing_configure_settings', array('project_id' => $project->getID())); ?>', 'mailing'); return false;" id="mailing">
        <div class="project_save_container">
            <span id="mailing_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
            <input type="submit" class="button button-silver" id="mailing_button" value="<?php echo __('Save outgoing email settings'); ?>">
        </div>
        <input type="hidden" name="project_id" value="<?php echo $project->getID(); ?>">
        <table style="clear: both; width: 780px; margin-bottom: 25px;" class="padded_table" cellpadding=0 cellspacing=0>
            <tr>
                <td style="width: 200px;"><label for="mailing_from_address"><?php echo __('Project from-address'); ?></label></td>
                <td style="width: 580px;">
                    <input type="email" name="mailing_from_address" style="width: 300px;" id="mailing_from_address" value="<?php echo \thebuggenie\core\framework\Settings::get('project_from_address_'.$project->getID(), 'mailing'); ?>">
                </td>
            </tr>
            <tr>
                <td style="width: 200px;"><label for="mailing_from_name"><?php echo __('Project from-name'); ?></label></td>
                <td style="width: 580px;">
                    <input type="text" name="mailing_from_name" style="width: 300px;" id="mailing_from_name" value="<?php echo \thebuggenie\core\framework\Settings::get('project_from_name_'.$project->getID(), 'mailing'); ?>">
                </td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('By specifying an email address here, users can hit the "Reply" button on email notifications, and replies will be sent to the specified address instead of the usual generic no-reply address.'); ?></td>
            </tr>
        </table>
    </form>
</div>
<div id="tab_mailing_other_pane"<?php if ($selected_tab != 'mailing_other'): ?> style="display: none;"<?php endif; ?>>
    <div class="project_save_container">
        <span class="content">
            <?php echo __('The Bug Genie can check email accounts and create issues from incoming emails. Set up a new account here, and check the %online_documentation for more information.', array('%online_documentation' => link_tag('http://issues.thebuggenie.com/wiki/TheBugGenie:IncomingEmail', '<b>'.__('online documentation').'</b>'))); ?>
        </span>
        <div class="button button-silver" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'mailing_editincomingemailaccount', 'project_id' => $project->getId())); ?>');"><?php echo __('Add new account'); ?></div>
    </div>
    <?php if ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
        <div class="rounded_box red" style="margin-top: 10px;">
            <?php echo __('You do not have the relevant permissions to access email settings'); ?>
        </div>
    <?php else: ?>
        <h4>
            <?php echo __('Incoming email accounts'); ?>
        </h4>
        <div id="mailing_incoming_accounts">
            <?php foreach (\thebuggenie\core\framework\Context::getModule('mailing')->getIncomingEmailAccountsForProject(\thebuggenie\core\framework\Context::getCurrentProject()) as $account): ?>
                <?php include_component('mailing/incomingemailaccount', array('account' => $account)); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
