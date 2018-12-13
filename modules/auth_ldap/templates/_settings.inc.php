<p><?= __('Use this page to set up the connection details for your LDAP or Active Directory server. It is highly recommended that you read the online help before use, as misconfiguration may prevent you from accessing configuration pages to rectify issues.'); ?></p>

<p><b><?= link_tag('https://issues.thebuggenie.com/wiki/Category%3ATheBugGenie%3AUserGuide%3AModules%3ALDAP', __('View the online documentation')); ?></b></p>

<?php if ($noldap): ?>
    <div class="rounded_box red" style="margin-top: 5px">
        <div class="header"><?= __('LDAP support is not installed'); ?></div>
        <p><?= __('The PHP LDAP extension is required to use this functionality. As this module is not installed, all functionality on this page has been disabled.'); ?></p>
    </div>
<?php else: ?>
    <div class="rounded_box yellow" style="margin-top: 5px">
        <div class="header"><?= __('Important information'); ?></div>
        <p><?= __('When you enable LDAP as your authentication backend in Authentication configuration, you will lose access to all accounts which do not also exist in the LDAP database. This may mean you lose administrative access.'); ?></p>
        <p style="font-weight: bold; padding-top: 5px"><?= __('To resolve this issue, either import all users using the tool on this page and make one an administrator using Users configuration, or create a user with the same username as one in LDAP and make that one an administrator.'); ?></p>
    </div>
    <form accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
        <div class="rounded_box borderless mediumgrey<?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> cut_bottom<?php endif; ?>" style="margin: 10px 0 0 0; width: 700px;<?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
            <div class="header"><?= __('Connection details'); ?></div>
            <table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0 id="ldap_settings_table">
                <tr>
                    <td style="padding: 5px;"><label for="hostname"><strong><?= __('LDAP connection URI'); ?></strong></label></td>
                    <td><input type="text" name="hostname" id="hostname" value="<?= $module->getSetting('hostname'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('URI used for connecting to the LDAP server. Format is <schema://[name[:port]]/>, where schema is one of: ldap (plain-text), ldaps (TLS connection), or ldapi (UNIX domain socket). For example: ldap://hostname/, ldap://hostname:1389/, ldaps://hostname:636/, ldaps://hostname:1636/, ldapi:///.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="b_dn"><strong><?= __('Base DN'); ?></strong></label></td>
                    <td><input type="text" name="b_dn" id="b_dn" value="<?= $module->getSetting('b_dn'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('Base DN under which all user and group entires can be found. For example: dc=ldap,c=example,dc=com.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="control_user"><?= __('Control user DN'); ?></label></td>
                    <td><input type="text" name="control_user" id="control_user" value="<?= $module->getSetting('control_user'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('LDAP distinguished name (DN) for control user. Control user needs to be able to access all relevant user and allowed group entries in the LDAP directory. Read-only access is sufficient. The user does not need to be able to read any credentials (passwords). To use anonymous bind for control user, leave the setting blank. For example: cn=tbg,ou=services,c=example,dc=com.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="control_pass"><?= __('Control user password'); ?></label></td>
                    <td><input type="password" name="control_pass" id="control_pass" value="<?= $module->getSetting('control_pass'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('Password for logging in into LDAP directory using the control user DN. To use anonymous bind for control user, leave the setting blank.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="integrated_auth"><?= __('Use HTTP Integrated Authentication'); ?></label></td>
                    <td><input type="checkbox" name="integrated_auth" id="integrated_auth" value="1" value="1" <?php if ($module->getSetting('integrated_auth')): ?>checked<?php endif; ?> style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('Enable automated login by using username passed-in into application via HTTP header. This requires that the web server performs authentication on behalf of The Bug Genie (e.g. HTTP Basic Authentication, Kerberos etc).'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="integrated_auth_header"><?= __('HTTP header field'); ?></label></td>
                    <td><input type="text" name="integrated_auth_header" id="integrated_auth_header" value="<?= $module->getSetting('integrated_auth_header'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('HTTP header field containing the username if HTTP Integrated Authentication is enabled. For example: REMOTE_USER.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="dn_attr"><strong><?= __('Object DN attribute'); ?></strong></label></td>
                    <td><input type="text" name="dn_attr" id="dn_attr" value="<?= $module->getSetting('dn_attr'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('LDAP attribute that contains the distinguished name of an object. On most LDAP servers this shouild be set to entrydn. If using Active Directory, set the value to distinguishedName.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="u_type"><strong><?= __('User object class'); ?></strong></label></td>
                    <td><input type="text" name="u_type" id="u_type" value="<?= $module->getSetting('u_type'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('Object class used for locating valid user entries. For example: person, inetOrgPerson.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="u_attr"><strong><?= __('User username attribute'); ?></strong></label></td>
                    <td><input type="text" name="u_attr" id="u_attr" value="<?= $module->getSetting('u_attr'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('LDAP attribute where the username is stored. For example: uid.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="f_attr"><?= __('User full name attribute'); ?></label></td>
                    <td><input type="text" name="f_attr" id="f_attr" value="<?= $module->getSetting('f_attr'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('LDAP attribute where the full name of the user is stored. If not specified, cn will be used if available. For example: displayName.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="b_attr"><?= __('User buddy name attribute'); ?></label></td>
                    <td><input type="text" name="b_attr" id="b_attr" value="<?= $module->getSetting('b_attr'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('LDAP attribute where the buddy name is stored. For example: gn.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="e_attr"><?= __('User e-mail address attribute'); ?></label></td>
                    <td><input type="text" name="e_attr" id="e_attr" value="<?= $module->getSetting('e_attr'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('LDAP attribute where the e-mail is stored. For example: mail.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="groups"><?= __('Allowed groups'); ?></label></td>
                    <td><input type="text" name="groups" id="groups" value="<?= $module->getSetting('groups'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('Comma-separated list of allowed groups. Each group should be specified by its common name (cn) attribute. If specified, only users that are members of these groups will be able to access The Bug Genie. Leave this option blank to disable group membership checks and allow any valid LDAP user to access The Bug Genie. For example: tbg,mygroup1,mygroup2.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="g_type"><?= __('Group object class'); ?></label></td>
                    <td><input type="text" name="g_type" id="g_type" value="<?= $module->getSetting('g_type'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('Object class used for locating valid allowed group entries. For example: groupOfNames, groupOfUniqueNames.'); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px;"><label for="g_attr"><?= __('Group member attribute'); ?></label></td>
                    <td><input type="text" name="g_attr" id="g_attr" value="<?= $module->getSetting('g_attr'); ?>" style="width: 100%;"></td>
                </tr>
                <tr>
                    <td class="config_explanation" colspan="2"><?= __('LDAP attribute in allowed group entries where member users are stored. For example: member, uniqueMember.'); ?></td>
                </tr>
            </table>
        </div>
        <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
            <div class="save-button-container">
                <div class="message"><?= __('Click "%save" to save the settings', array('%save' => __('Save'))); ?></div>
                <input type="submit" id="submit_settings_button" value="<?= __('Save'); ?>">
            </div>
        <?php endif; ?>
    </form>

    <form accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('ldap_test'); ?>" method="post">
        <div class="rounded_box borderless mediumgrey cut_bottom" style="margin: 10px 0 0 0; width: 700px; padding: 5px;">
            <div class="header"><?= __('Test connection'); ?></div>
            <div class="content"><?= __('After configuring and saving your connection settings, you should test your connection to the LDAP server. This test does not check whether the DN and attributes can allow The Bug Genie to correctly find users, but it will give an indication if The Bug Genie can talk to your LDAP server, and if any groups you specify exist. If HTTP integrated authentication is enabled, this will also test that your web server is providing the configured header.'); ?></div>
        </div>
        <div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 700px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
            <input type="submit" id="test_button" style="float: right; padding: 0 10px 0 10px; font-size: 13px; font-weight: bold;" value="<?= __('Test connection'); ?>">
        </div>
    </form>

    <form accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('ldap_import'); ?>" method="post">
        <div class="rounded_box borderless mediumgrey cut_bottom" style="margin: 10px 0 0 0; width: 700px; padding: 5px;">
            <div class="header"><?= __('Import all users'); ?></div>
            <div class="content"><?= __('You can import all users who can log in from LDAP into The Bug Genie with this tool. This will not let them log in without switching to LDAP Authentication. We recomemnd you do this before switching over, and make at least one of the new users an administrator. Already existing users with the same username will be updated.'); ?></div>
        </div>
        <div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 700px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
            <input type="submit" id="import_button" style="float: right; padding: 0 10px 0 10px; font-size: 13px; font-weight: bold;" value="<?= __('Import users'); ?>">
        </div>
    </form>

    <form accept-charset="<?= \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('ldap_prune'); ?>" method="post">
        <div class="rounded_box borderless mediumgrey cut_bottom" style="margin: 10px 0 0 0; width: 700px; padding: 5px;">
            <div class="header"><?= __('Prune users'); ?></div>
            <div class="content"><?= __('To remove the data from The Bug Genie of users who can no longer log in via LDAP, run this tool. These users would not be able to log in anyway, but it will keep your user list clean. The guest user is not affected, but it may affect your current user - if this is deleted you will be logged out.'); ?></div>
        </div>
        <div class="rounded_box red borderless cut_top" style="margin: 0 0 5px 0; width: 700px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
            <input type="submit" id="prune_button" style="float: right; padding: 0 10px 0 10px; font-size: 13px; font-weight: bold;" value="<?= __('Prune users'); ?>">
        </div>
    </form>
<?php endif; ?>
