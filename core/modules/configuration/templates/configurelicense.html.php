<?php

    $tbg_response->setTitle(__('Configure authentication'));
    
?>

<?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_LICENSE)); ?>
<div valign="top" class="main_area main_configuration_content">
    <div style="width: 730px;" id="config_authentication">
        <h3><?= __('Configure subscription license'); ?></h3>
        <div class="message-box type-info">
            <span class="message"><?= fa_image_tag('receipt') . '<span>'.__('A valid subscription license enables additional features such as automatic module installation and upgrades.').'</span>'; ?></span>
            <span class="actions"><a href="https://thebuggenie.com/register/self-hosted" target="_blank" class="button button-silver button-purchase"><?= __('Purchase a subscription'); ?></a></span>
        </div>
        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
            <form accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_license'); ?>" method="post" id="configure_license_form" onsubmit="TBG.Main.Helpers.formSubmit('<?= make_url('configure_license'); ?>', 'configure_license_form'); return false;">
        <?php endif; ?>
            <table style="clear: both; width: 100%; margin-top: 15px;" class="padded_table" cellpadding=0 cellspacing=0>
                <tr>
                    <td style="vertical-align: middle;"><label for="license_key"><?= __('License key'); ?></label></td>
                    <td>
                        <input type="text" name="license_key" id="license_key" value="<?= thebuggenie\core\framework\Settings::getLicenseIdentifier(); ?>" class="padded">
                    </td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('Copy the license key exactly as it is displayed on your account dashboard, and input it here.'); ?></td>
                </tr>
            </table>
            <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                <div class="save-button-container">
                    <div class="message"><?= __('Click "%save" to save your license key', array('%save' => __('Save'))); ?></div>
                    <span id="configure_license_form_indicator" style="display: none;"><?= image_tag('spinning_20.gif'); ?></span>
                    <input type="submit" id="configure_license_form_button" value="<?= __('Save'); ?>">
                </div>
            <?php endif; ?>
        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
            </form>
        <?php endif; ?>
    </div>
</div>
