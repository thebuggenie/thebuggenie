<?php include_component('installation/header'); ?>
<div class="installation_box">
    <h2 style="margin-top: 0px;">Pre-installation checks</h2>
    <p style="margin-bottom: 10px;">
    Before we can start the installation, we need to check a few things.<br>
    Please look through the list of prerequisites below, and take the necessary steps to correct any errors that may have been highlighted.</p>
    <div id="installation_main_box">
        <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">Mozilla Public License 2.0 accepted</span></div>
        <?php if ($php_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">PHP version (<?php echo $php_ver; ?>) meets requirements</span></div>
        <?php else: ?>
            <div class="prereq type-warn">
                <?= fa_image_tag('exclamation-triangle'); ?>
                <span class="message">
                    <b>PHP interpreter version is too old</b><br>
                    The Bug Genie requires PHP 7.1.0 or later. You have version <?php echo $php_ver; ?>. Grab the latest release from your usual sources or from <a href="http://php.net/downloads.php" target="_blank">php.net</a>
                </span>
            </div>
        <?php endif; ?>
        <?php if ($pcre_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">PCRE libraries version (<?php echo $pcre_ver; ?>) meets requirements</span></div>
        <?php else: ?>
            <div class="prereq type-warn">
                <?= fa_image_tag('exclamation-triangle'); ?>
                <span class="message">
                    <b>PCRE libraries version is too old</b><br>
                    The Bug Genie requires PCRE libraries 8.0 or later. You have version <?php echo $pcre_ver; ?>. Update your system to the latest release from your usual sources.
                </span>
            </div>
        <?php endif; ?>
        <?php if ($docblock_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">PHP docblocks are readable</span></div>
        <?php else: ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>PHP docblocks are not readable</b><br>
                    The Bug Genie requires that PHP docblocks are readable. You may be running a PHP accellerator that removes docblocks from PHP code files as an optimization technique. Please refer to the accelerator documentation for how to disable this feature, or disable the accellerator.</a>
                </span>
            </div>
        <?php endif; ?>
        <?php if ($base_folder_perm_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">Can write to The Bug Genie directory</span></div>
        <?php else: ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>Could not write to The Bug Genie directory</b><br>
                    The main folder for The Bug Genie should be writable during installation, since we need to store some information in it
                </span>
            </div>
            <div class="message-box type-solution">
                <b>If you're installing this on a Linux server,</b> running this command should fix it:
                <div class="command_box">
                    chmod a+w <?php echo THEBUGGENIE_PATH; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($thebuggenie_folder_perm_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">Can write to The Bug Genie public directory</span></div>
        <?php else: ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>Could not write to The Bug Genie public directory</b><br>
                    The public folder for The Bug Genie should be writable during installation, since we need to store some information in it
                </span>
            </div>
            <div class="message-box type-solution">
                <b>If you're installing this on a Linux server,</b> running this command should fix it:
                <div class="command_box">
                    chmod a+w <?php echo THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME; ?>/
                </div>
            </div>
        <?php endif; ?>

        <?php if ($mb_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">PHP "mbstring" extension is loaded</span></div>
        <?php else: ?>
            <div class="prereq type-warn">
                <?= fa_image_tag('exclamation-triangle'); ?>
                <span class="message">
                    <b>PHP extension "mbstring" is not loaded</b><br>
                    The Bug Genie requires the PHP extension "mbstring". Please install and / or enable this extension to continue.
                </span>
            </div>
        <?php endif; ?>

        <?php if ($dom_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">PHP "xml" extension is loaded</span></div>
        <?php else: ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>PHP extension "xml" is not loaded</b><br>
                    The Bug Genie requires the PHP extension "xml". Please install and / or enable this extension to continue.
                </span>
            </div>
        <?php endif; ?>

        <?php if ($gd_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">PHP "gd" extension is loaded</span></div>
        <?php else: ?>
            <div class="prereq type-warn">
                <?= fa_image_tag('exclamation-triangle'); ?>
                <span class="message">
                    <b>PHP extension "gd" is not loaded</b><br>
                    You won't be able to display graphs statistics and some other images.
                </span>
            </div>
        <?php endif; ?>
        <?php if ($pdo_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">PHP "pdo" extension is loaded</span></div>
        <?php endif; ?>
        <?php if (!$mysql_ok && !$pgsql_ok): ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>No PDO driver enabled</b><br>
                    To install The Bug Genie, a PDO database driver must be installed and enabled. Please install and / or enable a supported pdo extension to continue.
                </span>
            </div>
        <?php else: ?>
            <?php if ($mysql_ok): ?>
                <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">PHP "pdo-mysql" extension is loaded</span></div>
            <?php elseif (!$mysql_ok && $pgsql_ok): ?>
                <div class="prereq type-warn">
                    <?= fa_image_tag('exclamation-triangle'); ?>
                    <span class="message">
                        <b>PDO MySQL driver not enabled</b><br>
                        You won't be able to install The Bug Genie on a MySQL database.
                    </span>
                </div>
            <?php endif; ?>
            <?php if ($pgsql_ok): ?>
                <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">PHP "pdo-pgsql" extension is loaded</span></div>
            <?php elseif ($mysql_ok && !$pgsql_ok): ?>
                <div class="prereq type-warn">
                    <?= fa_image_tag('exclamation-triangle'); ?>
                    <span class="message">
                        <b>PDO PostgreSQL driver not enabled</b><br>
                        You won't be able to install The Bug Genie on a PostgreSQL database.
                    </span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($b2db_param_file_ok && $b2db_param_folder_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check-square', [], 'far'); ?><span class="message">Can save database connection details</span></div>
        <?php elseif (!$b2db_param_file_ok): ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>Could not write the SQL settings file</b><br>
                    The folder that contains the SQL settings is not writable
                </span>
            </div>
            <div class="message-box type-solution">
                <b>If you're installing this on a Linux server,</b> running those commands should fix it:<br>
                <div class="command_box">
                    touch <?php echo realpath(THEBUGGENIE_CONFIGURATION_PATH) . DS; ?>b2db.yml<br>
                    chmod a+w <?php echo realpath(THEBUGGENIE_CONFIGURATION_PATH) . DS; ?>b2db.yml
                </div>
            </div>
        <?php else: ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>Could not write the SQL settings file</b><br>
                    The file that contains the SQL settings already exists, but is not writable
                </span>
            </div>
            <div class="message-box type-solution">
                <b>If you're installing this on a Linux server,</b> running this command should fix it:<br>
                <div class="command_box">
                    chmod a+w <?php echo realpath(THEBUGGENIE_CONFIGURATION_PATH) . DS; ?>b2db.yml
                </div>
            </div>
        <?php endif; ?>
        <?php if ($all_well): ?>
            <div style="clear: both; padding: 30px 0 15px 0; text-align: right;">
                <form accept-charset="utf-8" action="index.php" method="post">
                    <input type="hidden" name="step" value="2">
                    <img src="images/spinning_30.gif" id="next_indicator" style="display: none; vertical-align: middle; margin-left: 10px;" >
                    <input type="submit" onclick="$('start_install').hide();$('next_indicator').show();" id="start_install" value="Continue" style="margin-left: 10px;">
                </form>
            </div>
        <?php else: ?>
            <div style="clear: both; padding-top: 20px; text-align: right;">
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
