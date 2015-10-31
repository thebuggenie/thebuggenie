<?php use thebuggenie\modules\mailing\Mailing; ?>
<style>
    #mailnotification_settings_container.mailer-type-<?php echo Mailing::MAIL_TYPE_SMTP; ?> #mail_type_smtp_info { display: block; }
    #mailnotification_settings_container.mailer-type-<?php echo Mailing::MAIL_TYPE_SENDMAIL; ?> #mail_type_sendmail_info { display: block; }
    #mail_type_smtp_info, #mail_type_sendmail_info { display: none; }
</style>
<form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
    <div id="mailnotification_settings_container" style="margin: 10px 0 0 0;" class="mailer-type-<?php echo $module->getMailerType(); ?>">
        <div class="content" style="padding-bottom: 10px;"><?php echo __('These are the settings for outgoing emails, such as notification emails and registration emails.'); ?></div>
        <table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0 id="mailnotification_settings_table">
            <tr>
                <td style="width: 300px; padding: 5px;"><label for="enable_outgoing_notifications"><?php echo __('Enable outgoing email notifications'); ?></label></td>
                <td style="width: auto;">
                    <select name="enable_outgoing_notifications" id="enable_outgoing_notifications" onchange="if ($(this).getValue() == 0) { $('mailnotification_settings_container').select('input').each(function (element, index) { element.disable(); }); } else { $('mailnotification_settings_container').select('input').each(function (element, index) { element.enable(); }); }">
                        <option value="1"<?php if ($module->isOutgoingNotificationsEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
                        <option value="0"<?php if (!$module->isOutgoingNotificationsEnabled()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="activation_needed"><?php echo __("Require email activation for new accounts"); ?></label></td>
                <td><input type="checkbox" name="activation_needed" id="activation_needed" value="1" <?php if ($module->isActivationNeeded()): ?>checked<?php endif; ?> style="width: 100%;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __("If this option is ticked, new accounts will require activation by clicking a link in the email. If this is ticked, the user's password will also be provided in the email, instead of in the registration screen"); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="from_name"><?php echo __('Email "from"-name'); ?></label></td>
                <td><input type="text" name="from_name" id="from_name" value="<?php echo $module->getSetting('from_name'); ?>" style="width: 100%;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="from_address"><?php echo __('Email "from"-address'); ?></label></td>
                <td><input type="text" name="from_addr" id="from_address" value="<?php echo $module->getSetting('from_addr'); ?>" style="width: 100%;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('This is the name and email address email notifications from The Bug Genie will be sent from'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="from_address"><?php echo __('Issue tracker URL'); ?></label></td>
                <td><input type="text" name="cli_mailing_url" id="cli_mailing_url" value="<?php echo $module->getMailingUrl(); ?>" placeholder="<?php echo \thebuggenie\core\framework\Context::getScope()->getCurrentHostname(); ?>" style="width: 100%;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2">
                    <?php echo __("This is the full URL to the issue tracker, used when sending outgoing emails. If this isn't configured, you will not be able to use the outgoing email feature."); ?>
                </td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="headcharset"><?php echo __('Email header charset'); ?></label></td>
                <td><input type="text" name="headcharset" id="headcharset" value="<?php echo $module->getSetting('headcharset'); ?>" style="width: 100px;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('The character encoding used in outgoing emails'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="use_queue"><?php echo __('Queue emails for batch processing'); ?></label></td>
                <td>
                    <input type="radio" name="use_queue" value="0" id="use_queue_no"<?php if (!$module->usesEmailQueue()): ?> checked<?php endif; ?> <?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>&nbsp;<label for="use_queue_no"><?php echo __('Send email notifications instantly'); ?></label><br>
                    <input type="radio" name="use_queue" value="1" id="use_queue_yes"<?php if ($module->usesEmailQueue()): ?> checked<?php endif; ?> <?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>&nbsp;<label for="use_queue_yes"><?php echo __('Use email queueing'); ?></label>
                </td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __("If you're using a queue, outgoing emails will not slow down the system. Read more about how to set up email queueing in %email_queueing", array('%email_queueing' => link_tag(make_url('@publish_article?article_name=EmailQueueing'), 'EmailQueueing'))); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="mailing_mail_type"><?php echo __('Email backend selection'); ?></label></td>
                <td>
                    <select name="mail_type" id="mailing_mail_type" <?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>
                        <option value="<?php echo Mailing::MAIL_TYPE_SMTP; ?>" <?php if ($module->getMailerType() == Mailing::MAIL_TYPE_SMTP): ?> selected<?php endif; ?>><?php echo __('SMTP Transport'); ?></option>
                        <option value="<?php echo Mailing::MAIL_TYPE_SENDMAIL; ?>" <?php if ($module->getMailerType() == Mailing::MAIL_TYPE_SENDMAIL): ?> selected<?php endif; ?>><?php echo __('Sendmail Transport'); ?></option>
                        <option value="<?php echo Mailing::MAIL_TYPE_PHP; ?>" <?php if ($module->getMailerType() == Mailing::MAIL_TYPE_PHP): ?> selected<?php endif; ?>><?php echo __('Fallback PHP mail() transport (not recommended)'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('For outgoing emails The Bug Genie uses the %swiftmailer library, which supports several different mail transports. Please see the %swiftmailer_configuration for more information.', array('%swiftmailer_configuration' => '<a href="http://swiftmailer.org/docs/sending.html#transport-types" target="_blank">'.__('Swiftmailer configuration').'</a>', '%swiftmailer' => '<a href="http://swiftmailer.org/docs/sending.html#transport-types" target="_blank">Swiftmailer</a>')); ?></td>
            </tr>
        </table>
        <table style="width: 680px; margin-top: 10px;" class="padded_table" cellpadding=0 cellspacing=0 id="mail_type_sendmail_info">
            <tr>
                <td style="width: 300px; padding: 5px;"><label for="mailing_sendmail_command"><?php echo __('Sendmail command'); ?></label></td>
                <td style="width: auto;"><input type="text" placeholder="<?php echo __("Leave blank for default ('/usr/sbin/sendmail -bs')"); ?>" name="sendmail_command" id="mailing_sendmail_command" value="<?php echo $module->getSendmailCommand(); ?>" style="width: 100%;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('Please see the %swiftmailer_configuration for more information about the sendmail transport.', array('%swiftmailer_configuration' => '<a href="http://swiftmailer.org/docs/sending.html#the-sendmail-transport" target="_blank">'.__('Swiftmailer configuration').'</a>', '%swiftmailer' => '<a href="http://swiftmailer.org/docs/sending.html#transport-types" target="_blank">Swiftmailer</a>')); ?></td>
            </tr>
        </table>
        <table style="width: 680px; margin-top: 10px;" class="padded_table" cellpadding=0 cellspacing=0 id="mail_type_smtp_info">
            <tr>
                <td style="width: 300px; padding: 5px;"><label for="smtp_host"><?php echo __('SMTP server address'); ?></label></td>
                <td style="width: auto;"><input type="text" name="smtp_host" id="smtp_host" value="<?php echo $module->getSetting('smtp_host'); ?>" style="width: 100%;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="smtp_port"><?php echo __('SMTP address port'); ?></label></td>
                <td><input type="text" name="smtp_port" id="smtp_port" value="<?php echo $module->getSetting('smtp_port'); ?>" style="width: 40px;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="timeout"><?php echo __('SMTP server timeout'); ?></label></td>
                <td><input type="text" name="timeout" id="timeout" value="<?php echo $module->getSmtpTimeout(); ?>" style="width: 40px;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>><?php echo __('%number_of seconds', array('%number_of' => '')); ?></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('Connection information for the outgoing email server'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="smtp_user"><?php echo __('SMTP username'); ?></label></td>
                <td><input type="text" name="smtp_user" id="smtp_user" value="<?php echo $module->getSmtpUsername(); ?>" style="width: 300px;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('The username used for sending emails'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="mailing_smtp_password"><?php echo __('SMTP password'); ?></label></td>
                <td><input type="password" name="smtp_pwd" id="mailing_smtp_password" value="<?php echo $module->getSmtpPassword(); ?>" style="width: 150px;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('The password used for sending emails'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="mailing_encryption"><?php echo __('Connection encryption'); ?></label></td>
                <td>
                    <select name="smtp_encryption" id="mailing_encryption" <?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>
                        <option value="" <?php if (!$module->getSmtpEncryption()): ?> selected<?php endif; ?>><?php echo __('No encryption'); ?></option>
                        <option value="ssl" <?php if (!$module->isSSLEncryptionAvailable()): ?> disabled<?php elseif ($module->getSmtpEncryption() == 'ssl'): ?> selected<?php endif; ?>><?php echo __('Use SSL encryption'); ?></option>
                        <option value="tls" <?php if (!$module->isTLSEncryptionAvailable()): ?> disabled<?php elseif ($module->getSmtpEncryption() == 'tls'): ?> selected<?php endif; ?>><?php echo __('Use TLS encryption'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
            </tr>
        </table>
    </div>
<?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
    <script>
        require(['domReady', 'jquery'], function (domReady, $) {
            domReady(function () {
                $('#mailing_mail_type').change(function () {
                    var classname = 'mailer-type-' + $(this).val();
                    ['mailer-type-<?php echo Mailing::MAIL_TYPE_PHP; ?>', 'mailer-type-<?php echo Mailing::MAIL_TYPE_SMTP; ?>', 'mailer-type-<?php echo Mailing::MAIL_TYPE_SENDMAIL; ?>'].forEach(function (mailertype) {
                        (mailertype == classname) ? $('#mailnotification_settings_container').addClass(mailertype) : $('#mailnotification_settings_container').removeClass(mailertype);
                    });
                })
            });
        });
    </script>
    <div class="bluebox" style="margin: 0 0 5px 0;">
        <?php echo __('Click "%save" to save email notification settings', array('%save' => __('Save'))); ?>
        <input type="submit" id="submit_settings_button" style="margin: -3px -3px 0 0; float: right; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
    </div>
<?php endif; ?>
</form>
<?php if ($module->isEnabled()): ?>
    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('mailing_testemail'); ?>" method="post">
        <div class="greybox" style="margin: 5px 0 30px 0;">
            <table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
                <tr>
                    <td style="width: 125px; padding: 5px;"><label for="test_email_to"><?php echo __('Send test email'); ?></label></td>
                    <td style="width: auto;"><input type="text" name="test_email_to" id="test_email_to" value="" style="width: 300px;"<?php echo ($access_level != \thebuggenie\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2" style="font-size: 13px;">
                        <span class="faded_out">
                            <?php echo __('Enter an email address, and click "%send_test_email" to check if the email module is configured correctly', array('%send_test_email' => __('Send test email'))); ?>
                        </span>
                    </td>
                </tr>
            </table>
            <div style="text-align: right;">
                <input type="submit" id="submit_settings_button" style="padding: 0 10px 0 10px; font-size: 13px; font-weight: bold;" value="<?php echo __('Send test email'); ?>">
            </div>
        </div>
    </form>
<?php endif; ?>
