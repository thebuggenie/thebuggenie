<?php include_component('installation/header'); ?>
<div class="installation_box">
    <h2 style="margin-top: 0px;">Pre-installation checks</h2>
    <p style="margin-bottom: 10px;">
    Before we can start the installation, we need to check a few things.<br>
    Please look through the list of prerequisites below, and take the necessary steps to correct any errors that may have been highlighted.</p>
    <div id="installation_main_box">
        <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>Mozilla Public License 2.0 accepted ...</div>
        <?php if ($php_ok): ?>
            <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>PHP version (<?php echo $php_ver; ?>) meets requirements ...</div>
        <?php else: ?>
            <div class="install_progress prereq_warn">
            <b>PHP interpreter version is too old</b><br>
            The Bug Genie 3 requires PHP 5.3.0 or later. You have version <?php echo $php_ver; ?>.<br/>Grab the latest release from your usual sources or from <a href="http://php.net/downloads.php" target="_blank">php.net</a>
            </div>
        <?php endif; ?>
        <?php if ($pcre_ok): ?>
            <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>PCRE libraries version (<?php echo $pcre_ver; ?>) meets requirements ...</div>
        <?php else: ?>
            <div class="install_progress prereq_warn">
            <b>PCRE libraries version is too old</b><br>
            The Bug Genie 3 requires PCRE libraries 8.0 or later. You have version <?php echo $pcre_ver; ?>.<br/>Update your system to the latest release from your usual sources.
            </div>
        <?php endif; ?>
        <?php if ($docblock_ok): ?>
            <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>PHP docblocks are readable ...</div>
        <?php else: ?>
            <div class="install_progress prereq_fail">
            <b>PHP docblocks are not readable</b><br>
            The Bug Genie 3 requires that PHP docblocks are readable. You may be running a PHP accellerator that removes docblocks from PHP code files as an optimization technique. Please refer to the accelerator documentation for how to disable this feature, or disable the accellerator.</a>
            </div>
        <?php endif; ?>
        <?php if ($base_folder_perm_ok): ?>
            <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>Can write to The Bug Genie directory ...</div>
        <?php else: ?>
            <div class="install_progress prereq_fail">
            <b>Could not write to The Bug Genie directory</b><br>
            The main folder for The Bug Genie should be writable during installation, since we need to store some information in it
            </div>
            <b>If you're installing this on a Linux server,</b> running this command should fix it:<br>
            <div class="command_box">
            chmod a+w <?php echo THEBUGGENIE_PATH; /* str_ireplace('\\', '/', mb_substr(THEBUGGENIE_PATH, 0, strripos(THEBUGGENIE_PATH, DIRECTORY_SEPARATOR) + 1)); */ ?>
            </div>
        <?php endif; ?>
        <?php if ($thebuggenie_folder_perm_ok): ?>
            <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>Can write to The Bug Genie public directory ...</div>
        <?php else: ?>
            <div class="install_progress prereq_fail">
            <b>Could not write to The Bug Genie public directory</b><br>
            The public folder for The Bug Genie should be writable during installation, since we need to store some information in it
            </div>
            <b>If you're installing this on a Linux server,</b> running this command should fix it:<br>
            <div class="command_box">
            chmod a+w <?php echo THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME; ?>/
            </div>
        <?php endif; ?>
        <?php if ($mb_ok): ?>
            <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>PHP mbstring extension is loaded</div>
        <?php else: ?>
            <div class="install_progress prereq_warn">
            <b>PHP extension "mbstring" is not loaded</b><br>
            The Bug Genie 3 requires the PHP extension "mbstring". This extension is used by the internationalization functionality in The Bug Genie and is required for The Bug Genie to operate.<br/>
            More information is available at <a href="http://php.net/manual/en/book.mbstring.php" target="_blank">php.net</a>
            </div>
        <?php endif; ?>
        <?php if ($gd_ok): ?>
            <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>PHP GD library installed and enabled ...</div>
        <?php else: ?>
            <div class="install_progress prereq_warn">
                <b>PHP GD library not enabled</b><br>
                You won't be able to display graphs statistics and some other images.<br/>More information is available at <a href="http://php.net/manual/en/book.image.php" target="_blank">php.net</a>
            </div>
        <?php endif; ?>
        <?php if ($pdo_ok): ?>
            <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>PHP PDO installed and enabled ...</div>
        <?php endif; ?>
        <?php if (!$mysql_ok && !$pgsql_ok): ?>
            <div class="install_progress prereq_fail">
            <b>No PDO driver enabled</b><br>
            To install The Bug Genie, a PDO driver (MySQL or PostgreSQL) must be installed and enabled.<br/>More information is available at <a href="http://fr.php.net/manual/en/pdo.drivers.php">php.net</a>
            </div>
        <?php else: ?>
            <?php if ($mysql_ok): ?>
                <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>PHP PDO MySQL installed and enabled ...</div>
            <?php elseif (!$mysql_ok && $pgsql_ok): ?>
                <div class="install_progress prereq_warn">
                <b>PDO MySQL driver not enabled</b><br>
                You won't be able to install The Bug Genie on a MySQL database.<br/>More information is available at <a href="http://php.net/manual/en/ref.pdo-mysql.php" target="_blank">php.net</a>
                </div>
            <?php endif; ?>
            <?php if ($pgsql_ok): ?>
                <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>PHP PDO PostgreSQL installed and enabled ...</div>
            <?php elseif ($mysql_ok && !$pgsql_ok): ?>
                <div class="install_progress prereq_warn">
                <b>PDO PostgreSQL driver not enabled</b><br>
                You won't be able to install The Bug Genie on a PostgreSQL database.<br/>More information is available at <a href="http://php.net/manual/en/ref.pdo-pgsql.php" target="_blank">php.net</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($b2db_param_file_ok && $b2db_param_folder_ok): ?>
            <div class="install_progress prereq_ok"><?php echo image_tag('iconsets/oxygen/action_ok.png', array(), true); ?>Can save database connection details ...</div>
        <?php elseif (!$b2db_param_file_ok): ?>
            <div class="install_progress prereq_fail">
            <b>Could not write the SQL settings file</b><br>
            The folder that contains the SQL settings is not writable
            </div>
            <b>If you're installing this on a Linux server,</b> running those commands should fix it:<br>
            <div class="command_box">
            touch <?php echo realpath(THEBUGGENIE_CONFIGURATION_PATH) . DS; ?>b2db.yml<br>
            chmod a+w <?php echo realpath(THEBUGGENIE_CONFIGURATION_PATH) . DS; ?>b2db.yml
            </div>
        <?php else: ?>
            <div class="install_progress prereq_fail">
            <b>Could not write the SQL settings file</b><br>
            The file that contains the SQL settings already exists, but is not writable
            </div>
            <b>If you're installing this on a Linux server,</b> running this command should fix it:<br>
            <div class="command_box">
            chmod a+w <?php echo realpath(THEBUGGENIE_CONFIGURATION_PATH) . DS; ?>b2db.yml
            </div>
        <?php endif; ?>
        <?php if (get_magic_quotes_gpc()): ?>
            <div class="install_progress prereq_warn">
            <b>Magic quotes are enabled</b><br>
            You have magic quotes enabled on your PHP setup. You should disable these to avoid double slashes being shown in places where you enter your own text.<br/>More information is available at <a href="http://www.php.net/manual/en/security.magicquotes.php" target="_blank">php.net</a>
            </div>
        <?php endif; ?>
        <?php if ($all_well): ?>
            <br style="clear: both;">
            <div style="clear: both; padding: 15px 0; text-align: center;">
                <form accept-charset="utf-8" action="index.php" method="post">
                    <input type="hidden" name="step" value="2">
                    <label for="start_install">Start the installation by pressing this button</label>
                    <img src="iconsets/oxygen/spinning_30.gif" id="next_indicator" style="display: none; vertical-align: middle; margin-left: 10px;" >
                    <input type="submit" onclick="$('start_install').hide();$('next_indicator').show();" id="start_install" value="Start installation" style="margin-left: 10px;">
                </form>
            </div>
        <?php else: ?>
            <div style="clear: both; padding-top: 20px; text-align: center;">
                <form accept-charset="utf-8" action="index.php" method="post">
                    <input type="hidden" name="step" value="1">
                    <input type="hidden" name="agree_license" value="1">
                    <label for="retry_button" style="font-size: 13px; margin-right: 10px;">You need to correct the above error(s) before the installation can continue.</label>
                    <input type="submit" id="retry_button" value="Retry">
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include_component('installation/footer'); ?>
