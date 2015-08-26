<?php include_component('installation/header'); ?>
<?php if (isset($error)): ?>
    <div class="installation_box">
        <div class="error"><?php echo nl2br($error); ?></div>
        <h2>An error occured</h2>
        An error occured and the installation has been stopped. Please try to fix the error based on the information above, then click back, and try again.<br>
        If you think this is a bug, please report it in our <a href="http://issues.thebuggenie.com" target="_new">online bug tracker</a>.
    </div>
<?php else: ?>
    <div class="installation_box">
        <h1>Thank you for installing The Bug Genie!</h1>
        The Bug Genie is open source software. If you find any bugs or issues, please use our <a href="http://thebuggenie.com" target="_new">issue tracker</a> or send an email to <a href="mailto:support@thebuggenie.com">support@thebuggenie.com</a>.<br>
        <br>
        Online documentation is available from <a href="http://www.thebuggenie.com/support" target="_new">www.thebuggenie.com &raquo; support</a>, and our <a href="http://forum.thebuggenie.org" target="_new">community forums</a> are full of helpful people.<br>
        We also provide <a target="_new" href="http://thebuggenie.com/register/support">commercial support</a> and <a target="_new" href="http://thebuggenie.com/training">online training</a> for individuals and groups. For other inquiries, send an email to <a href="mailto:support@thebuggenie.com">support@thebuggenie.com</a>.<br>
        <br>
        <h2>Getting involved</h2>
        If you want to get involved with The Bug Genie, don't hesitate to visit our community website <a target="_new" href="http://thebuggenie.org/community">www.thebuggenie.org</a> to see how you can join our growing community.
    </div>
    <form action="<?php echo make_url('login'); ?>" method="post">
        <input type="hidden" name="tbg3_username" value="administrator">
        <input type="hidden" name="tbg3_password" value="admin">
        <input type="hidden" name="tbg3_referer" value="<?php echo make_url('about'); ?>">
        <div style="font-size: 15px; text-align: center; padding: 25px;">
            <input type="submit" value="Got it!" style="font-size: 15px; margin-top: 10px; padding: 8px; height: 40px; font-weight: normal;">
        </div>
    </form>
<?php endif; ?>
<?php include_component('installation/footer'); ?>
