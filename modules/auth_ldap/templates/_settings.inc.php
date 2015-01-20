<p><?php echo __('Use this page to set up the connection details for your LDAP or Active Directory server. It is highly recommended that you read the online help before use, as misconfiguration may prevent you from accessing configuration pages to rectify issues.'); ?></p>
<p><b><?php echo link_tag('http://issues.thebuggenie.com/wiki/Category%3ATheBugGenie%3AUserGuide%3AModules%3ALDAP', __('View the online documentation')); ?></b></p>
<?php if ($noldap): ?>
<div class="rounded_box red" style="margin-top: 5px">
    <div class="header"><?php echo __('LDAP support is not installed'); ?></div>
    <p><?php echo __('The PHP LDAP extension is required to use this functionality. As this module is not installed, all functionality on this page has been disabled.'); ?></p>
</div>
<?php endif; ?>
<div class="rounded_box yellow" style="margin-top: 5px">
    <div class="header"><?php echo __('Important information'); ?></div>
    <p><?php echo __('When you enable LDAP as your authentication backend in Authentication configuration, you will lose access to all accounts which do not also exist in the LDAP database. This may mean you lose administrative access.'); ?></p>
    <p style="font-weight: bold; padding-top: 5px"><?php echo __('To resolve this issue, either import all users using the tool on this page and make one an administrator using Users configuration, or create a user with the same username as one in LDAP and make that one an administrator.'); ?></p>
</div>
<form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
    <div class="rounded_box borderless mediumgrey<?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> cut_bottom<?php endif; ?>" style="margin: 10px 0 0 0; width: 700px;<?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
        <div class="header"><?php echo __('Connection details'); ?></div>
        <table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0 id="ldap_settings_table">
            <tr>
                <td style="padding: 5px;"><label for="hostname"><?php echo __('Hostname'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="hostname" id="hostname" value="<?php echo $module->getSetting('hostname'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('Use URL syntax (ldap://hostname:port). If your server requires SSL, use ldaps://hostname/ in this field.'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="b_dn"><?php echo __('Base DN'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="b_dn" id="b_dn" value="<?php echo $module->getSetting('b_dn'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('This should be the DN string for an OU where all user and group OUs can be found. For example, DC=ldap,DC=example,DC=com.'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="dn_attr"><?php echo __('Object DN attribute'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="dn_attr" id="dn_attr" value="<?php echo $module->getSetting('dn_attr'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('Enter the name of the property containing the distinguished name of an object. On Linux systems this may be entrydn (which is the default value if this is left blank), on Active Directory it is distinguishedName.'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="u_type"><?php echo __('User class'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="u_type" id="u_type" value="<?php echo $module->getSetting('u_type'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('Enter the value to check for in objectClass for users. Leave blank to use the default of person'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="u_attr"><?php echo __('Username attribute'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="u_attr" id="u_attr" value="<?php echo $module->getSetting('u_attr'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('This field should contain the name of the attribute where the username is stored, such as uid.'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="f_attr"><?php echo __('Full name attribute'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="f_attr" id="f_attr" value="<?php echo $module->getSetting('f_attr'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="b_attr"><?php echo __('Given name attribute'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="b_attr" id="b_attr" value="<?php echo $module->getSetting('b_attr'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="e_attr"><?php echo __('Email address attribute'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="e_attr" id="e_attr" value="<?php echo $module->getSetting('e_attr'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="g_type"><?php echo __('Group class'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="g_type" id="g_type" value="<?php echo $module->getSetting('g_type'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('Enter the value to check for in objectClass for groups. Leave blank to use the default of group'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="g_attr"><?php echo __('Group members attribute'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="g_attr" id="g_attr" value="<?php echo $module->getSetting('g_attr'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('This field should contain the name of the attribute where the list of members of a group is stored, such as uniqueMember.'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="groups"><?php echo __('Allowed groups'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="groups" id="groups" value="<?php echo $module->getSetting('groups'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('You may wish to restrict access to users who belong to certain groups in LDAP. If so, write a comma separated list of group names here. Leave blank to disable this feature.'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="control_user"><?php echo __('Control username'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="control_user" id="control_user" value="<?php echo $module->getSetting('control_user'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="control_pass"><?php echo __('Control user password'); ?></label></td>
                <td><input type="password"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="control_pass" id="control_pass" value="<?php echo $module->getSetting('control_pass'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('Please insert the authentication details for a user who can access all LDAP records. Only read only access is necessary, and for an anonyous bind leave this blank.'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="integrated_auth"><?php echo __('Use HTTP Integrated Authentication'); ?></label></td>
                <td><input type="checkbox"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="integrated_auth" id="integrated_auth" value="1" value="1" <?php if ($module->getSetting('integrated_auth')): ?>checked<?php endif; ?> style="width: 100%;"></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('Activate to enabled automatic user login using HTTP integrated authentication. This requires your web server to be authenticating the user (e.g. HTTP Basic Authentication, Kerberos etc).'); ?></td>
            </tr>
            <tr>
                <td style="padding: 5px;"><label for="integrated_auth_header"><?php echo __('HTTP header field'); ?></label></td>
                <td><input type="text"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> name="integrated_auth_header" id="integrated_auth_header" value="<?php echo $module->getSetting('integrated_auth_header'); ?>" style="width: 100%;"></td>
            </tr>
            <tr>
                <td class="config_explanation" colspan="2"><?php echo __('If using HTTP integrated authentication specify the HTTP header field that will contain the user name.'); ?></td>
            </tr>                                    
        </table>
    </div>
<?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
    <div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 700px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
        <div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save" to save the settings', array('%save' => __('Save'))); ?></div>
        <input type="submit" id="submit_settings_button"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
    </div>
<?php endif; ?>
</form>

<form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('ldap_test'); ?>" method="post">
    <div class="rounded_box borderless mediumgrey cut_bottom" style="margin: 10px 0 0 0; width: 700px; padding: 5px;">
        <div class="header"><?php echo __('Test connection'); ?></div>
        <div class="content"><?php echo __('After configuring and saving your connection settings, you should test your connection to the LDAP server. This test does not check whether the DN and attributes can allow The Bug Genie to correctly find users, but it will give an indication if The Bug Genie can talk to your LDAP server, and if any groups you specify exist. If HTTP integrated authentication is enabled, this will also test that your web server is providing the REMOTE_USER header.'); ?></div>
    </div>
    <div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 700px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
        <input type="submit" id="test_button"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> style="float: right; padding: 0 10px 0 10px; font-size: 13px; font-weight: bold;" value="<?php echo __('Test connection'); ?>">
    </div>
</form>

<form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('ldap_import'); ?>" method="post">
    <div class="rounded_box borderless mediumgrey cut_bottom" style="margin: 10px 0 0 0; width: 700px; padding: 5px;">
        <div class="header"><?php echo __('Import all users'); ?></div>
        <div class="content"><?php echo __('You can import all users who can log in from LDAP into The Bug Genie with this tool. This will not let them log in without switching to LDAP Authentication. We recomemnd you do this before switching over, and make at least one of the new users an administrator. Already existing users with the same username will be updated.'); ?></div>
    </div>
    <div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 700px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
        <input type="submit" id="import_button"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> style="float: right; padding: 0 10px 0 10px; font-size: 13px; font-weight: bold;" value="<?php echo __('Import users'); ?>">
    </div>
</form>

<form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('ldap_prune'); ?>" method="post">
    <div class="rounded_box borderless mediumgrey cut_bottom" style="margin: 10px 0 0 0; width: 700px; padding: 5px;">
        <div class="header"><?php echo __('Prune users'); ?></div>
        <div class="content"><?php echo __('To remove the data from The Bug Genie of users who can no longer log in via LDAP, run this tool. These users would not be able to log in anyway, but it will keep your user list clean. The guest user is not affected, but it may affect your current user - if this is deleted you will be logged out.'); ?></div>
    </div>
    <div class="rounded_box red borderless cut_top" style="margin: 0 0 5px 0; width: 700px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
        <input type="submit"<?php if ($noldap): echo ' disabled="disabled"'; endif; ?> id="prune_button" style="float: right; padding: 0 10px 0 10px; font-size: 13px; font-weight: bold;" value="<?php echo __('Prune users'); ?>">
    </div>
</form>
