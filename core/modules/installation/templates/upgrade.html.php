<?php include_component('installation/header', array('mode' => 'upgrade')); ?>
<div class="installation_box">
    <?php if ($upgrade_available): ?>
        <?php if (version_compare($current_version, '3.2', '<')): ?>
            <div class="rounded_box shadowed padded_box yellow" style="font-size: 1.1em; margin-bottom: 10px;">
                <u>You are performing an update from an older version of The Bug Genie (<?php echo $current_version; ?>), and not 3.2.x or newer</u>.<br>
                This is a valid upgrade, but if you really are upgrading from <?php echo $current_version; ?>, you need to upgrade to the latest 3.2.x release first, and then upgrade to <?php echo \thebuggenie\core\framework\Settings::getVersion(false, true); ?>.
            </div>
        <?php elseif (isset($permissions_ok) && $permissions_ok): ?>
            <div class="fullpage_backdrop" id="upgrading_popup" style="display: none;">
                <div class="backdrop_box login_page login_popup">
                    <div class="backdrop_detail_content" style="text-align: center;">
                        <div style="font-size: 1.5em; font-weight: bold;">Upgrading, please wait ...</div>
                        <div style="font-size: 1.2em;">This can take a little while</div>
                        <img src="iconsets/oxygen/spinning_32.gif" style="margin: 25px auto;">
                    </div>
                </div>
            </div>
            <form accept-charset="utf-8" action="<?php echo make_url('upgrade'); ?>" method="post" onsubmit="if (!$('confirm_backup').checked) { return false; } else { $('upgrading_popup').show(); }">
                <div class="donate padded_box rounded_box shadowed installpage" id="install_page_1" style="margin-bottom: 15px;">
                    <h2>Get involved with The Bug Genie</h2>
                    The Bug Genie is open source software provided <b>free of charge</b> by zegenie studios - however, none of this would be possible without our great community of dedicated users.<br>
                    If you use The Bug Genie on a regular basis, please consider:
                    <ul>
                        <li>contributing patches, fixes and features <a href="http://github.com/thebuggenie/thebuggenie">via github</a></li>
                        <li>writing and improving the <a href="http://issues.thebuggenie.com/wiki/TheBugGenie:MainPage">documentation</a></li>
                        <li>help out other users in our <a href="http://forum.thebuggenie.org/">user forums</a></li>
                        <li>improve or add <a href="https://www.transifex.com/projects/p/tbg/">translations</a></li>
                        <li>author public blog posts or news articles about The Bug Genie</li>
                    </ul>
                    If you are unable to contribute in any of the ways listed above - but would still like to support us - please send us an email and we'll work something out: <a href="opensource@thebuggenie.com">opensource@thebuggenie.com</a>.<br>
                    <br>
                    <h5>How to get involved in The Bug Genie community:</h5>
                    <a target="_blank" href="http://thebuggenie.org/community">http://thebuggenie.org/community</a> <i>(opens in a new window)</i>
                    <div class="progress_buttons">
                        <a href="javascript:void(0);" class="button button-silver button-next" onclick="tbg_upgrade_next($(this).up('.installpage'));">Next</a>
                    </div>
                </div>
                <div class="padded_box installpage backup" id="install_page_2">
                    <?php include_component('main/percentbar', array('percent' => 5, 'height' => 5)); ?>
                    <h2 style="margin-bottom: 15px; padding-bottom: 0;">
                        <span style="font-weight: normal;">You are performing the following upgrade: </span><?php echo $current_version; ?>.x => <?php echo \thebuggenie\core\framework\Settings::getVersion(false, true); ?><br>
                    </h2>
                    Although this upgrade process has been thoroughly tested before the release, errors may still occur. You are strongly encouraged to take a backup of your installation before you continue!<br>
                    <br>
                    Before continuing, please make sure you have backed up the following:
                    <ul class="backuplist">
                        <li style="background-image: url('iconsets/oxygen/backup_database.png');">
                            The Bug Genie database<br>
                            Currently connected to <?php echo b2db\Core::getDBtype(); ?> database <span class="command_box"><?php echo b2db\Core::getDBname(); ?></span> running on <span class="command_box"><?php echo b2db\Core::getHost(); ?></span>
                        </li>
                        <li style="background-image: url('iconsets/oxygen/backup_uploads.png');" class="<?php if (\thebuggenie\core\framework\Settings::getUploadStorage() != 'files') echo 'faded'; ?>">
                            Uploaded files<br>
                            <?php if (\thebuggenie\core\framework\Settings::getUploadStorage() != 'files'): ?>
                                <span class="smaller">When using database file upload storage, this is included in the database backup</span>
                            <?php else: ?>
                                Remember to keep a copy of all files in <span class="command_box"><?php echo \thebuggenie\core\framework\Settings::getUploadsLocalpath(); ?></span>
                            <?php endif; ?>
                        </li>
                        <li style="background-image: url('iconsets/oxygen/backup_specialfiles.png');">
                            The Bug Genie special files<br>
                            There are a number of configuration files used by The Bug Genie for its initialization and configuration. You should keep a copy of these files:
                            <ul>
                                <li class="command_box" style="display: block; margin: 0;"><?php echo THEBUGGENIE_PATH . 'installed'; ?></li>
                                <li class="command_box" style="display: block; margin: 0;"><?php echo THEBUGGENIE_CORE_PATH . 'b2db_bootstrap.inc.php'; ?></li>
                                <li class="command_box" style="display: block; margin: 0;"><?php echo THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.htaccess'; ?></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="progress_buttons">
                        <a href="javascript:void(0);" class="button button-silver button-previous" onclick="tbg_upgrade_previous($(this).up('.installpage'));">Previous</a>
                        <a href="javascript:void(0);" class="button button-silver button-next" onclick="tbg_upgrade_next($(this).up('.installpage'));">Next</a>
                    </div>
                </div>
                <?php if ($current_version == '3.2'): ?>
                    <div class="padded_box installpage" id="install_page_3">
                        <?php include_component('main/percentbar', array('percent' => 25, 'height' => 5)); ?>
                        <h2>Improved workflow handling</h2>
                        In addition to a slew of other improvements, this version introduces improved workflow configuration by letting you configure the initial workflow transition for new issues.<br>
                        To handle this, the upgrade wizard must create an workflow transitions for all existing scopes.<br>
                        <br>
                        This can be changed in the workflow configuration section after the upgrade wizard is complete.<br>
                        <br>
                        <h5>Please select the initial status for issues for each scope in this installation:</h5>
                        <ul class="scope_upgrade">
                        <?php foreach ($statuses as $scope_id => $details): ?>
                            <li title="<?php echo $details['scopename']; ?>">
                                <label for="upgrade_scope_<?php echo $scope_id; ?>" style="font-weight: bold; font-size: 1.1em;"><?php echo $details['scopename']; ?>:</label>
                                <select name="status[<?php echo $scope_id; ?>]" id="upgrade_scope_<?php echo $scope_id; ?>">
                                    <?php foreach ($details['statuses'] as $status_id => $status_name): ?>
                                        <option value="<?php echo $status_id; ?>" <?php if (in_array(trim(strtolower($status_name)), array('new', 'nieuw', 'neu'))) echo 'selected'; ?>><?php echo $status_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                        <div class="progress_buttons">
                            <a href="javascript:void(0);" class="button button-silver button-previous" onclick="tbg_upgrade_previous($(this).up('.installpage'));">Previous</a>
                            <a href="javascript:void(0);" class="button button-silver button-next" onclick="tbg_upgrade_next($(this).up('.installpage'));">Next</a>
                        </div>
                    </div>
                    <div class="padded_box installpage" id="install_page_4">
                        <?php include_component('main/percentbar', array('percent' => 50, 'height' => 5)); ?>
                        <h2>Improved security</h2>
                        This version adds support for application passwords and contains improved security functionality.<br>
                        As a result, all users will require password resets after the upgrade is completed.<br>
                        <br>
                        The upgrade procedure can help you with this by either resetting all users passwords for you, or send password reset emails after the upgrade is completed.<br>
                        <br>
                        <h5>Please select how you would like the upgrade procedure to handle this:</h5>
                        <ul class="passwordlist">
                            <li class="greybox">
                                <input type="radio" name="upgrade_passwords" onchange="$('upgrade_password_manual_input').enable(); if ($(this).checked && $('upgrade_password_manual_input').getValue().length >= 5) { $('upgrade_password_continue').enable(); } else { $('upgrade_password_continue').disable(); } $('upgrade_password_manual_input').focus();" value="manual" id="upgrade_passwords_enterpassword"><label for="upgrade_passwords_enterpassword">Set all user passwords to the password specified below</label><br>
                                <input type="text" name="manual_password" disabled placeholder="Specify a password for all users here" id="upgrade_password_manual_input" onkeyup="if ($(this).getValue().length >= 5) { $('upgrade_password_continue').enable(); } else { $('upgrade_password_continue').disable(); }">&nbsp;Must be at least 5 characters
                            </li>
                            <li class="greybox">
                                <input type="radio" name="upgrade_passwords" onchange="$('upgrade_password_manual_input').disable(); $('upgrade_password_continue').enable();" value="auto" id="upgrade_passwords_auto"><label for="upgrade_passwords_auto">Set all user passwords to the same as their <select name="upgrade_passwords_pick">
                                        <option value="username">username</option>
                                        <option value="email">email address</option>
                                </select></label><br>
                                <div class="explanation">If you choose this option, all users should change their password immediately after logging in for the first time.</div>
                            </li>
                            <li class="greybox">
                                <input type="radio" name="upgrade_passwords" onchange="$('upgrade_password_manual_input').disable(); $('upgrade_password_continue').enable();" value="none" id="upgrade_passwords_none"><label for="upgrade_passwords_none">Don't change any passwords</label><br>
                                <div class="explanation">If you choose this option, all users must use the password reset functionality or manually have their password reset before they are able to log in.</div>
                            </li>
                        </ul>
                        <div class="progress_buttons">
                            <a href="javascript:void(0);" class="button button-silver button-previous" onclick="tbg_upgrade_previous($(this).up('.installpage'));">Previous</a>
                            <a href="javascript:void(0);" class="button button-silver button-next" id="upgrade_password_continue" disabled onclick="tbg_upgrade_next($(this).up('.installpage'));">Next</a>
                        </div>
                    </div>
                    <div class="padded_box installpage" id="install_page_5">
                        <?php include_component('main/percentbar', array('percent' => 90, 'height' => 5)); ?>
                        <h2>Almost done</h2>
                        As mentioned on the previous page, this new version of The Bug Genie will make <strong>all current user passwords stop working</strong> - you did read that, right?<br>
                        Because of this, we need to set a password for the admin account <span class="command_box"><?php echo strtolower($adminusername); ?></span>.<br>
                        <br>
                        <h5><label for="upgrade_password_admin">Please specify a password for the admin account <u>only</u></label></h5>
                        New password for user with username <span class="command_box"><?php echo strtolower($adminusername); ?></span> <input id="upgrade_password_admin" name="admin_password" class="adminpassword" placeholder="Enter a new admin password here" onkeyup="if ($(this).getValue().length >= 5 && $('confirm_backup').checked) { $('start_upgrade').enable(); } else { $('start_upgrade').disable(); }"><br>
                        <br>
                        Please read the upgrade notes before you press "Perform upgrade" to continue.<br>
                        <input type="hidden" name="perform_upgrade" value="1">
                        <input type="checkbox" name="confirm_backup" id="confirm_backup" onclick="($('upgrade_password_admin').getValue().length >= 5 && $('confirm_backup').checked) ? $('start_upgrade').enable() : $('start_upgrade').disable();">
                        <label for="confirm_backup" style="vertical-align: middle; font-weight: bold; font-size: 1.1em;">I have read and understand the <a href="http://thebuggenie.com/release/3_2#upgrade">upgrade notes</a> - and I've taken steps to make sure my data is backed up</label><br>
                        <input type="submit" value="Perform upgrade" id="start_upgrade" disabled="disabled" style="margin-top: 10px;"><br>
                        <br>
                        <a href="javascript:void(0);" onclick="tbg_upgrade_previous($(this).up('.installpage'));">&lt;&lt;&nbsp;or go back to change upgrade settings</a>
                    </div>
                <?php else: ?>
                    <div class="padded_box installpage" id="install_page_5">
                        <?php include_component('main/percentbar', array('percent' => 90, 'height' => 5)); ?>
                        <h2>Almost done</h2>
                        Please read the upgrade notes before you press "Perform upgrade" to continue.<br>
                        <input type="hidden" name="perform_upgrade" value="1">
                        <input type="checkbox" name="confirm_backup" id="confirm_backup" onclick="($('confirm_backup').checked) ? $('start_upgrade').enable() : $('start_upgrade').disable();">
                        <label for="confirm_backup" style="vertical-align: middle; font-weight: bold; font-size: 1.1em;">I have read and understand the <a href="http://thebuggenie.com/release/3_2#upgrade">upgrade notes</a> - and I've taken steps to make sure my data is backed up</label><br>
                        <input type="submit" value="Perform upgrade" id="start_upgrade" disabled="disabled" style="margin-top: 10px;"><br>
                        <br>
                        <a href="javascript:void(0);" onclick="tbg_upgrade_previous($(this).up('.installpage'));">&lt;&lt;&nbsp;or go back to change upgrade settings</a>
                    </div>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <div class="rounded_box shadowed padded_box installation_prerequisites prereq_fail" style="padding: 10px; margin-bottom: 10px;">
                <b>The version information files are not writable</b>
            </div>
            <p style="font-size: 1.2em;">
                The upgrade routine needs the following two files to be writable:<br>
                <div style="font-size: 1.2em; margin-top: 10px; padding-left: 0;">
                    <span class="command_box"><?php echo THEBUGGENIE_PATH . 'installed'; ?></span> and <span class="command_box"><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></span>.
                    <b>Please fix this error and try again.</b>
                </div>
                <div style="font-size: 1.2em; margin-top: 10px; padding-left: 0;">
                    On Linux or Unix systems, you can fix this by running the following command in a console: <br>
                    <div class="command_box" style="font-size: 1em;">chmod a+w <?php echo THEBUGGENIE_PATH . 'installed'; ?> <?php echo THEBUGGENIE_PATH . 'upgrade'; ?></div>
                </div>
            </p>
        <?php endif; ?>
    <?php elseif ($upgrade_complete): ?>
        <?php include_component('main/percentbar', array('percent' => 100, 'height' => 5)); ?>
        <h2>Upgrade successfully completed!</h2>
        <p style="font-size: 1.2em;">
            Remember to remove the file <span class="command_box"><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></span> before you click the "Finish" button below.
        </p>
        <div style="margin-top: 15px;">
            <a href="<?php echo make_url('logout'); ?>" class="button button-silver" style="font-size: 1.2em !important; padding: 3px 10px !important;">Finish</a>
        </div>
    <?php else: ?>
        <h2>No upgrade necessary!</h2>
        <p style="font-size: 1.2em;">
            Make sure that the file <span class="command_box"><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></span> is removed before you click the "Finish" button below.
        </p>
        <div style="margin-top: 15px;">
            <a href="<?php echo make_url('home'); ?>" class="button button-silver" style="font-size: 1.2em !important; padding: 3px 10px !important;">Finish</a>
        </div>
    <?php endif; ?>
</div>
<script>
    function tbg_upgrade_next(container) {
        container.toggle();
        container.next().toggle();
    }
    function tbg_upgrade_previous(container) {
        container.toggle();
        container.previous().toggle();
    }
    document.observe('dom:loaded', function() {
        $$('.installpage').each(Element.hide);
        $('install_page_1').show();
    });

</script>
<?php include_component('installation/footer'); ?>