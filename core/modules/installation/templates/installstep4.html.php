<?php include_component('installation/header'); ?>
<div class="installation_box">
    <?php if (isset($error)): ?>
        <div class="message-box type-error">
            <?= fa_image_tag('times'); ?>
            <span class="message">
                <b>An error occured</b><br>
                <?php echo nl2br($error); ?>
            </span>
        </div>
        <div style="font-size: 13px;">
            An error occured and the installation has been stopped. Please try to fix the error based on the information above, then click start the installation over.<br>
            If you think this is a bug, please report it in our <a href="https://issues.thebuggenie.com" target="_new">online bug tracker</a>.
        </div>
    <?php else: ?>
        <?php if ($htaccess_error !== false): ?>
            <div class="message-box type-warning with-solution">
                <?= fa_image_tag('exclamation-triangle'); ?>
                <span class="message">
                    The installation routine could not setup your .htaccess file automatically.<br>
                    <?php if (!is_bool($htaccess_error)): ?>
                        <?php echo $htaccess_error; ?><br>
                    <?php endif; ?>
                </span>
            </div>
            <div class="message-box type-solution">
                Either fix the problem above (if any details are mentioned), <b>click "Back"</b> and try again - or follow these simple steps:
                <ul>
                    <li>Rename or copy the <span class="command_box"><?php echo THEBUGGENIE_CORE_PATH; ?>templates/htaccess.template</span> file to <span class="command_box">[main folder]/<?php echo THEBUGGENIE_PUBLIC_FOLDER_NAME; ?>/.htaccess</span></li>
                    <li>Open up the <span class="command_box">[main folder]/<?php echo THEBUGGENIE_PUBLIC_FOLDER_NAME; ?>/.htaccess</span> file, and change the <span class="command_box">RewriteBase</span> path to be identical to the <u>URL subdirectory</u></li>
                </ul>
            </div>
        <?php elseif ($htaccess_ok): ?>
            <div class="message-box type-info">
                <?= fa_image_tag('exclamation-circle'); ?>
                <span class="message">
                    Apache .htaccess auto-setup completed successfully
                </span>
            </div>
        <?php endif; ?>
        <div class="message-box type-info">
            <?= fa_image_tag('exclamation-circle'); ?>
            <span class="message">
                All settings were stored. Default data and settings loaded successfully
            </span>
        </div>
        <h2>Default user information</h2>
        To help you get started, please fill in some information about the default administrator user, here.
        <form accept-charset="utf-8" action="index.php" method="post" id="tbg_settings">
            <input type="hidden" name="step" value="5">
            <dl class="install_list">
                <dt><label id="admin_name">Name</label></dt>
                <dd><input type="text" id="admin_name" class="username" value="" name="name" placeholder="Enter your name here"></dd>
                <dt><label for="admin_email">E-mail address</label></dt>
                <dd><input type="email" id="admin_email" class="email" value="" name="email" placeholder="Enter an email address here"></dd>
                <dt><label for="admin_username">Username</label></dt>
                <dd><input type="text" id="admin_username" class="username" value="administrator" name="username"></dd>
                <dt><label id="admin_password">Password</label></dt>
                <dd><input type="password" id="admin_password" class="password small" value="admin" name="password"></dd>
                <dt><label for="admin_password_repeat">Repeat password</label></dt>
                <dd><input type="password" id="admin_password_repeat" class="password small" value="admin" name="password_repeat"></dd>
            </dl>
            <div style="clear: both; padding: 30px 0 15px 0; text-align: right;">
                <img src="images/spinning_30.gif" id="next_indicator" style="display: none; vertical-align: middle; margin-left: 10px;">
                <input type="submit" id="continue_button" onclick="$('continue_button').hide();$('next_indicator').show();" value="Continue">
            </div>
        </form>
    <?php endif; ?>
</div>
<?php include_component('installation/footer'); ?>
