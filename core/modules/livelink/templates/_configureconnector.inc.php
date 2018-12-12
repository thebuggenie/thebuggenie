<?php

/**
 * @var \thebuggenie\core\entities\User $tbg_user
 * @var \thebuggenie\core\modules\livelink\Livelink $module
 */

?>
<h3><?php echo __('Github authentication details'); ?></h3>
<p><?php echo __('Specify your Github login details here to enable github integration with your account.'); ?></p>
<table class="padded_table" cellpadding=0 cellspacing=0>
    <tr>
        <td style="width: 300px; vertical-align: middle;"><label for="<?= $module->getName(); ?>_github_token">* <?php echo __('Github token'); ?></label></td>
        <td>
            <input type="text" name="github_token" id="<?= $module->getName(); ?>_github_token" value="<?php echo $module->getUserToken($tbg_user); ?>" style="padding: 7px; font-size: 1.1em; min-width: 300px;">
        </td>
    </tr>
    <tr>
        <td class="config_explanation" colspan="2"><?php echo __('This is required to access private repositories, and features such as TBG Link.'); ?></td>
    </tr>
</table>
<?php include_component('main/myaccountsettingsformsubmit', array('module' => $module)); ?>