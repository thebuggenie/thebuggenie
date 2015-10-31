<?php include_component('installation/header'); ?>
<div class="installation_box">
    <?php if (isset($error)): ?>
        <div class="error"><?php echo nl2br($error); ?></div>
        <h2>An error occured</h2>
        <div style="font-size: 13px;">An error occured and the installation has been stopped. Please try to fix the error based on the information above, then click back, and try again.<br>
        If you think this is a bug, please report it in our <a href="http://issues.thebuggenie.com" target="_new">online bug tracker</a>.</div>
    <?php else: ?>
        <?php if ($htaccess_error !== false): ?>
            <div class="error">
                The installation routine could not setup your .htaccess and .user.ini files automatically.<br>
                <?php if (!is_bool($htaccess_error)): ?>
                    <br>
                    <b><?php echo $htaccess_error; ?></b><br>
                <?php endif; ?>
                <br>
                Either fix the problem above (if any details are mentioned), <b>click "Back"</b> and try again - or follow these simple steps:
                <ul>
                    <li>Rename or copy the <i><?php echo THEBUGGENIE_CORE_PATH; ?>templates/htaccess.template</i> file to <i>[main folder]/<?php echo THEBUGGENIE_PUBLIC_FOLDER_NAME; ?>/.htaccess</i></li>
                    <li>Open up the <i>[main folder]/<?php echo THEBUGGENIE_PUBLIC_FOLDER_NAME; ?>/.htaccess</i> file, and change the <u>RewriteBase</u> path to be identical to the <u>URL subdirectory</u></li>
                    <li>If you're using PHP-FPM, rename or copy the <i><?php echo THEBUGGENIE_CORE_PATH; ?>templates/user.ini.template</i> file to <i>[main folder]/<?php echo THEBUGGENIE_PUBLIC_FOLDER_NAME; ?>/.user.ini</i></li>
                </ul>
            </div>
        <?php elseif ($htaccess_ok): ?>
            <div class="ok">
                Apache .htaccess and PHP-FPM .user.ini auto-setup completed successfully
            </div>
        <?php endif; ?>
        <div class="ok">
            All settings were stored. Default data and settings loaded successfully
        </div>
        <h2 style="margin-top: 10px;">Default user information</h2>
        To help you get started, please fill in some information about the default administrator user, here.
        <form accept-charset="utf-8" action="index.php" method="post" id="tbg_settings">
            <input type="hidden" name="step" value="5">
            <dl class="install_list">
                <dt>
                    <label id="admin_name">Name</label>
                </dt>
                <dd>
                    <input type="text" id="admin_name" class="username" value="" name="name" placeholder="Enter your name here">
                </dd>
                <dt>
                    <label for="admin_email">E-mail address</label>
                </dt>
                <dd>
                    <input type="email" id="admin_email" class="email" value="" name="email" placeholder="Enter an email address here">
                </dd>
                <dt>
                    <label for="admin_username">Username</label>
                </dt>
                <dd>
                    <input type="text" id="admin_username" class="username" value="administrator" name="username">
                </dd>
                <dt>
                    <label id="admin_password">Password</label>
                </dt>
                <dd>
                    <input type="password" id="admin_password" class="password small" value="admin" name="password">
                </dd>
                <dt>
                    <label for="admin_password_repeat">Repeat password</label>
                </dt>
                <dd>
                    <input type="password" id="admin_password_repeat" class="password small" value="admin" name="password_repeat">
                </dd>
            </dl>
        <br>
        <h2 style="margin-top: 10px;">The Bug Genie modules</h2>
        The Bug Genie is written using a flexible, module-based architecture, that lets you easily add extra functionality. Even core functionality such as version control integration, email communication and the agile sections are provided using modules, and can be enabled / disabled from the configuration panel.<br>
        <br>
        <div class="feature">
            Find additional modules online, at <a href="http://thebuggenie.com/addons">www.thebuggenie.com &raquo; Addons</a><br>
        </div>
            <div style="padding-top: 20px; clear: both; text-align: center;">
                <label for="continue_button" style="font-size: 13px; margin-right: 10px;">Click this button to continue and set up the default user and default modules</label>
                <img src="iconsets/oxygen/spinning_30.gif" id="next_indicator" style="display: none; vertical-align: middle; margin-left: 10px;">
                <input type="submit" id="continue_button" onclick="$('continue_button').hide();$('next_indicator').show();" value="Continue">
            </div>
        </form>
    <?php endif; ?>
</div>
<?php include_component('installation/footer'); ?>
