<?php include_component('installation/header'); ?>
<?php if (isset($error)): ?>
    <div class="installation_box">
        <div class="error"><?php echo nl2br($error); ?></div>
        <h2>An error occured</h2>
        <div style="font-size: 13px;">An error occured and the installation has been stopped. Please try to fix the error based on the information above, then click back, and try again.<br>
        If you think this is a bug, please report it in our <a href="http://issues.thebuggenie.com" target="_new">online bug tracker</a>.</div>
    </div>
<?php else: ?>
    <h2>Administrator account set up</h2>
    The administrator account has been set up successfully. To use this account, log in with the following information:
    <div class="installation_box">
        <dl class="install_list">
            <dt><label>Username</label></dt>
            <dd><span class="command_box"><?php echo $user->getUsername(); ?></span></dd>
            <dt><label>Password</label></dt>
            <dd><span class="command_box"><?php echo $password; ?></span></dd>
        </dl>
    </div>
    <br>
    Don't worry, that password is securely stored in your database with full encryption. The password displayed is the input you typed on the previous page.<br>
    You can change all these details from the "Account" page, after logging in.
    <div class="installation_box">
        <form accept-charset="utf-8" action="index.php" method="post" id="finalize_settings">
            <input type="hidden" name="step" value="6">
            <div style="padding-top: 20px; clear: both; text-align: center;">
                <img src="iconsets/oxygen/spinning_30.gif" id="next_indicator" style="display: none;">
                <input type="submit" id="continue_button" style="clear: both; font-size: 17px; margin-top: 10px; padding: 10px; height: 45px;" onclick="$('continue_button').hide();$('next_indicator').show();" value="Finalize installation">
            </div>
        </form>
    </div>
<?php endif; ?>
<?php include_component('installation/footer'); ?>
