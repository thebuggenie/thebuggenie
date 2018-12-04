<?php

    $tbg_response->setTitle(__('Configure authentication'));
    
?>

<?php include_component('leftmenu', array('selected_section' => \thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_LICENSE)); ?>
<div valign="top" class="main_area main_configuration_content">
    <div style="width: 730px;" id="config_authentication">
        <h3><?php echo __('Configure subscription license'); ?></h3>
        <div class="message-box type-info">
            <?= fa_image_tag('receipt') . '<span>'.__('A valid subscription license enables additional features such as automatic module installation and upgrades.').'</span>'; ?>
            <span class="actions"><a href="https://thebuggenie.com/register/self-hosted" target="_blank" class="button button-silver button-purchase"><?= __('Purchase a subscription'); ?></a></span>
        </div>
        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
            <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_authentication_pt2'); ?>" method="post" id="config_auth">
        <?php endif; ?>
        <table style="clear: both; width: 100%; margin-top: 15px;" class="padded_table" cellpadding=0 cellspacing=0>
            <tr>
                <td><label for="auth_backend"><?php echo __('License key'); ?></label></td>
                <td>
                </td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('Copy the license key exactly as it is displayed on your account dashboard, and input it here.'); ?></td>
            </tr>
        </table>
<?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
        <div class="greybox" style="margin: 5px 0px 5px 0px; height: 23px; padding: 5px 10px 5px 10px;">
            <div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save" to save your changes in all categories', array('%save' => __('Save'))); ?></div>
            <input type="submit" id="config_auth_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
        </div>
    </form>
<?php endif; ?>
</div>
</div>
