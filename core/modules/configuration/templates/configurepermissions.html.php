<?php

    $tbg_response->setTitle(__('Configure advanced permissions'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => 5)); ?>
        <td valign="top" style="padding-left: 15px;" id="configure_permissions_advanced">
            <div style="width: 730px;">
                <h3>
                    <?php echo link_tag(make_url('configure_roles'), '&lt;&lt;'.__('Back to roles'), array('class' => 'button button-green')); ?>
                    <?php echo __('Configure permissions'); ?>
                </h3>
                <?php include_component('configuration/permissionswarning'); ?>
                <div class="config_permissions greybox" style="margin: 0 0 10px 10px; min-height: 85px; width: 330px; float: right;">
                    <div class="header_div smaller" style="clear: both; margin: 0 0 5px 0;"><?php echo __('Icon legend:'); ?></div>
                    <div style="clear: both;">
                        <?php echo image_tag('icon_project_permissions.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Show more detailed permissions'); ?></span>
                    </div>
                    <div style="clear: both;">
                        <?php echo image_tag('cfg_icon_permissions.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Set permissions'); ?></span>
                    </div>
                    <div class="header_div smaller" style="clear: both; margin: 0 0 5px 0; padding-top: 10px;"><?php echo __('Permissions icon legend:'); ?></div>
                    <div style="clear: both;">
                        <?php echo image_tag('permission_unset_ok.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Not set (permissive system setting)'); ?></span>
                    </div>
                    <div style="clear: both;">
                        <?php echo image_tag('permission_unset_denied.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Not set (restrictive system setting)'); ?></span>
                    </div>
                    <div style="clear: both;">
                        <?php echo image_tag('permission_set_unset.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Not set (uses global permission)'); ?></span>
                    </div>
                    <div style="clear: both;">
                        <?php echo image_tag('permission_set_ok.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Allowed'); ?></span>
                    </div>
                    <div style="clear: both;">
                        <?php echo image_tag('permission_set_denied.png', array('style' => 'float: left; margin: 0 5px 0 10px;')); ?><span style="float: left;"><?php echo __('Denied'); ?></span>
                    </div>
                </div>
                <p>
                    <?php echo tbg_parse_text(__("Edit all global group and team permissions from this page - user-specific permissions are handled from the [[TBG:configure_users|user configuration page]], and permissions for specific issue fields are available from the [[TBG:configure_issuefields|issue fields configuration page]].", array(), true)); ?><br>
                    <br>
                    <?php echo tbg_parse_text(__("The Bug Genie permissions are thoroughly explained in [[ConfigurePermissions]] in the wiki - look it up if you're ever stuck.", array(), true)); ?>
                </p>
                <div id="config_permissions" class="" style="clear: both;">
                    <?php include_component('configuration/permissionsconfigurator', array('access_level' => $access_level, 'base_id' => 'configurator')); ?>
                </div>
            </div>
        </td>
    </tr>
</table>
