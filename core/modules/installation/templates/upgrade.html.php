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
                        <img src="images/spinning_32.gif" style="margin: 25px auto;">
                    </div>
                </div>
            </div>
            <form accept-charset="utf-8" action="<?php echo make_url('upgrade'); ?>" method="post" onsubmit="$('upgrading_popup').show();">
                <?php if (isset($error)): ?>
                    <div class="padded_box installpage backup" id="install_page_error">
                        <div class="rounded_box shadowed padded_box installation_prerequisites prereq_fail" style="padding: 10px; margin-bottom: 10px;">
                            <b>An error occurred during the upgrade:</b><br>
                            <?= $error; ?>
                        </div>
                        <div class="progress_buttons">
                            <a href="javascript:void(0);" class="button button-silver button-next" onclick="tbg_upgrade_next($(this).up('.installpage'));tbg_upgrade_next($(this).up('.installpage').next());">Okay</a>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="installpage" id="install_page_1">
                    <div class="installation_progress">
                        <h5>Upgrade progress</h5>
                        <div class="progress_bar"><div class="filler" style="width: <?= ($requires_password_reset) ? 20 : 25; ?>%;"></div></div>
                    </div>
                    The Bug Genie is open source software provided <b>free of charge</b> by zegenie studios - however, none of this would be possible without our great community of dedicated users.<br>
                    If you use The Bug Genie on a regular basis, please consider:
                    <ul>
                        <li>contributing patches, fixes and features <a href="http://github.com/thebuggenie/thebuggenie">via github</a></li>
                        <li>writing and improving the <a href="https://issues.thebuggenie.com/wiki/TheBugGenie:MainPage">documentation</a></li>
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
                <div class="installpage backup" id="install_page_2">
                    <div class="installation_progress">
                        <h5>Upgrade progress</h5>
                        <div class="progress_bar"><div class="filler" style="width: <?= ($requires_password_reset) ? 40 : 50; ?>%;"></div></div>
                    </div>
                    <h4 style="margin-bottom: 15px; padding-bottom: 0;">
                        <span style="font-weight: normal;">You are performing the following upgrade: </span><?php echo $current_version; ?>.x <?= fa_image_tag('long-arrow-alt-right'); ?> <?php echo \thebuggenie\core\framework\Settings::getVersion(false, true); ?><br>
                    </h4>
                    Although this upgrade process has been thoroughly tested before the release, errors may still occur.<br>
                    Before continuing, you are strongly encouraged to <strong>make sure you have backed up</strong> the following:
                    <ul class="backuplist">
                        <li style="background-image: url('images/backup_database.png');">
                            The Bug Genie database<br>
                            Currently connected to <?php echo b2db\Core::getDriver(); ?> database <span class="command_box"><?php echo b2db\Core::getDatabaseName(); ?></span> running on <span class="command_box"><?php echo b2db\Core::getHostname(); ?></span>, table prefix <span class="command_box"><?php echo b2db\Core::getTablePrefix(); ?></span>
                        </li>
                        <li style="background-image: url('images/backup_uploads.png');" class="<?php if (\thebuggenie\core\framework\Settings::getUploadStorage() != 'files') echo 'faded'; ?>">
                            Uploaded files<br>
                            <?php if (\thebuggenie\core\framework\Settings::getUploadStorage() != 'files'): ?>
                                <span class="smaller">When using database file upload storage, this is included in the database backup</span>
                            <?php else: ?>
                                Remember to keep a copy of all files in <span class="command_box"><?php echo \thebuggenie\core\framework\Settings::getUploadsLocalpath(); ?></span>
                            <?php endif; ?>
                        </li>
                        <li style="background-image: url('images/backup_specialfiles.png');">
                            The Bug Genie special files<br>
                            There are a number of configuration files used by The Bug Genie for its initialization and configuration. You should keep a copy of these files:
                            <ul>
                                <li class="command_box" style="display: block; margin: 0;"><?php echo THEBUGGENIE_PATH . 'installed'; ?></li>
                                <li class="command_box" style="display: block; margin: 0;"><?php echo THEBUGGENIE_CORE_PATH . 'config/b2db.yml'; ?></li>
                                <li class="command_box" style="display: block; margin: 0;"><?php echo THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . '/.htaccess'; ?></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="progress_buttons">
                        <a href="javascript:void(0);" class="button button-silver button-previous" onclick="tbg_upgrade_previous($(this).up('.installpage'));">Previous</a>
                        <a href="javascript:void(0);" class="button button-silver button-next" onclick="tbg_upgrade_next($(this).up('.installpage'));">Next</a>
                    </div>
                </div>
                <?php if ($requires_password_reset): ?>
                    <div class="installpage" id="install_page_4">
                        <div class="installation_progress">
                            <h5>Upgrade progress</h5>
                            <div class="progress_bar"><div class="filler" style="width: 60%;"></div></div>
                        </div>
                        <h2>Improved security</h2>
                        We're continuously adjusting and improving user security. As a result, this version <u>changes the way passwords are handled and stored</u>.<br>
                        All users will require password resets after the upgrade is completed, and application-specific passwords must be regenerated.<br>
                        <br>
                        If you want to read about the technical details about the change, click here:<br>
                        <a href="https://www.brandonsavage.net/please-stop-hashing-passwords-yourself/" target="_blank">https://www.brandonsavage.net/please-stop-hashing-passwords-yourself/</a><br>
                        <br>
                        The upgrade procedure will help you with this change by allowing you to choose a password for the admin account on the next page.<br>
                        <br>
                        <div class="progress_buttons">
                            <a href="javascript:void(0);" class="button button-silver button-previous" onclick="tbg_upgrade_previous($(this).up('.installpage'));">Previous</a>
                            <a href="javascript:void(0);" class="button button-silver button-next" id="upgrade_password_continue" disabled onclick="tbg_upgrade_next($(this).up('.installpage'));">Next</a>
                        </div>
                    </div>
                    <div class="installpage" id="install_page_5">
                        <div class="installation_progress">
                            <h5>Upgrade progress</h5>
                            <div class="progress_bar"><div class="filler" style="width: 80%;"></div></div>
                        </div>
                        <h2>Almost done</h2>
                        As mentioned on the previous page, this new version of The Bug Genie will make <strong>all current user passwords stop working.</strong><br>
                        Because of this, we need to set a password for the admin account <span class="command_box"><?php echo strtolower($adminusername); ?></span>.<br>
                        <br>
                        <h5><label for="upgrade_password_admin">Please specify a password for the admin account <u>only</u></label></h5>
                        New password for user with username <span class="command_box"><?php echo strtolower($adminusername); ?></span> <input id="upgrade_password_admin" name="admin_password" class="adminpassword" placeholder="Enter a new admin password here" onkeyup="if ($(this).getValue().length >= 5 && $('confirm_backup').checked) { $('start_upgrade').enable(); } else { $('start_upgrade').disable(); }"><br>
                        <br>
                        Please read the <a href="https://thebuggenie.com/release/3_2#upgrade">upgrade notes</a> before you press "Perform upgrade" to continue.<br>
                        <div style="clear: both; padding: 30px 0 15px 0; text-align: right;">
                            <input type="hidden" name="perform_upgrade" value="1">
                            <input type="hidden" name="confirm_backup" value="1" id="confirm_backup">
                            <input type="submit" value="Perform upgrade" id="start_upgrade">
                            <br>
                            <a href="javascript:void(0);" onclick="tbg_upgrade_previous($(this).up('.installpage'));">&lt;&lt;&nbsp;or go back to change upgrade settings</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="installpage" id="install_page_5">
                        <div class="installation_progress">
                            <h5>Upgrade progress</h5>
                            <div class="progress_bar"><div class="filler" style="width: 75%;"></div></div>
                        </div>
                        <h4>Ready to upgrade</h4>
                        <div class="message-box type-warning">
                            <?= fa_image_tag('exclamation-triangle'); ?>
                            <span class="message">
                                Pressing <b>Perform upgrade</b> on this page will start the upgrade process.
                            </span>
                        </div>
                        Please read the <a href="https://thebuggenie.com/release/3_2#upgrade">upgrade notes</a> before you press "Perform upgrade" to continue.<br>
                        <div class="progress_buttons">
                            <input type="hidden" name="perform_upgrade" value="1">
                            <input type="hidden" name="confirm_backup" value="1" id="confirm_backup">
                            <input type="submit" value="Perform upgrade" id="start_upgrade">
                            <br>
                            <a href="javascript:void(0);" onclick="tbg_upgrade_previous($(this).up('.installpage'));">&lt;&lt;&nbsp;or go back to change upgrade settings</a>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <div class="message-box type-warning with-solution">
                <?= fa_image_tag('exclamation-triangle'); ?>
                <span class="message">
                    The upgrade routine needs the following two files to be writable: <span class="command_box"><?php echo THEBUGGENIE_PATH . 'installed'; ?></span> and <span class="command_box"><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></span>
                </span>
            </div>
            <div class="message-box type-solution">
                <div class="message">
                    On Linux or Unix systems, you can fix this by running the following command in a console: <br>
                    <div class="command_box" style="font-size: 1em;">chmod a+w <?php echo THEBUGGENIE_PATH . 'installed'; ?> <?php echo THEBUGGENIE_PATH . 'upgrade'; ?></div>
                </div>
            </div>
        <?php endif; ?>
    <?php elseif ($upgrade_complete): ?>
        <div class="installation_progress">
            <h5>Upgrade progress</h5>
            <div class="progress_bar"><div class="filler" style="width: 100%;"></div></div>
        </div>
        <h4>Upgrade successfully completed!</h4>
        <p>
            Remember to remove the file <span class="command_box"><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></span> before you click the "Finish" button below.
        </p>
        <div class="progress_buttons">
            <a href="<?php echo make_url('logout'); ?>" class="button button-silver button-next">Finish</a>
        </div>
    <?php else: ?>
        <h4>No upgrade necessary!</h4>
        <p>
            Make sure that the file <span class="command_box"><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></span> is removed before you click the "Finish" button below.
        </p>
        <div class="progress_buttons">
            <a href="<?php echo make_url('home'); ?>" class="button button-silver button-next">Finish</a>
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
        $$('.installpage').first().show();
    });

</script>
<?php include_component('installation/footer'); ?>
