<?php include_component('installation/header'); ?>
<script type="text/javascript">

    function updateURLPreview()
    {
        if ($('url_subdir').value.empty())
        {
            $('continue_button').hide();
            $('continue_error').show();
            $('continue_error').update('You need to fill the subdirectory url.<br />If The Bug Genie is located directly under the server, put a single forward slash in the subdirectory url.');
        }
        else if(($F($('tbg_settings')['url_subdir']).endsWith('/') == false || $F($('tbg_settings')['url_subdir']).startsWith('/') == false))
        {
            $('continue_button').hide();
            $('continue_error').show();
            $('continue_error').update('The subdirectory url <i>must start and end with a forward slash</i>');
        }
        else
        {
            $('continue_button').show();
            $('continue_error').hide();
            $('url_preview').update($('url_subdir').value);
        }

        var new_url = $('url_subdir').value;

        if (new_url.endsWith('//'))
        {
            $('continue_button').hide();
            $('continue_error').show();
            $('continue_error').update('The complete url <i><b>cannot end with two forward slashes</b></i>. If The Bug Genie is located directly under the server, put <i><b>a single forward slash</b></i> as the directory url.');
        }
    }

</script>
<div class="installation_box">
    <?php if (isset($error)): ?>
        <div class="error"><?php echo nl2br($error); ?></div>
        <h2>An error occured</h2>
        <div style="font-size: 13px;">An error occured and the installation has been stopped. Please try to fix the error based on the information above, then click back, and try again.<br>
        If you think this is a bug, please report it in our <a href="http://issues.thebuggenie.com" target="_new">online bug tracker</a>.</div>
    <?php else: ?>
        <div class="ok">
            The database have been successfully set up<br>
            If you need to restart the installation, you won't have to enter the database details again
        </div>
        <h2 style="margin-top: 10px;">Server / URL information</h2>
        The Bug Genie uses URL rewriting to make URLs look more readable. URL rewriting is what makes it possible to use pretty URLs such as <u><i>/projectname/issue/123</i></u> instead of longer, unreadable URLs like <u><i>viewissue.php?project_key=projectname&amp;issue_id=123</i></u>.<br>
        <br>
        <div class="feature">
            <b>Your web server must be correctly set up with URL rewriting enabled for The Bug Genie to work.</b><br>
            For information on how to configure URL rewriting for your web server, see <a href="http://thebuggenie.com/support">www.thebuggenie.com &raquo; Support</a>
        </div>
        <br>
        The Bug Genie must be configured so that it knows how to translate URLs correctly.<br>
        If you are installing The Bug Genie on an Apache web server, the installation setup can auto-configure the required rewrite file for you.<br>
        <br>
        <form accept-charset="utf-8" action="index.php" method="post" id="tbg_settings" style="clear: both;">
            <input type="hidden" name="step" value="4">
            <dl class="install_list">
                <dt>
                    <label for="apache_autosetup_yes">Auto-configure apache</label>
                </dt>
                <dd>
                    <input type="radio" style="vertical-align: text-top;" name="apache_autosetup" id="apache_autosetup_yes" value="1" onclick="$('server_autosetup_info').show();" <?php if ($server_type == 'apache') echo "checked"; ?>><label for="apache_autosetup_yes">Yes</label>&nbsp;
                    <input type="radio" style="vertical-align: text-top;" name="apache_autosetup" id="apache_autosetup_no" value="0" onclick="$('server_autosetup_info').hide();" <?php if ($server_type != 'apache') echo "checked"; ?>><label for="apache_autosetup_no">No</label>
                </dd>
            </dl>
            <div style="<?php if ($server_type != 'apache') echo "display: none;"; ?>" id="server_autosetup_info">
                <dl class="install_list">
                    <dt>
                        <label for="url_subdir">Url subdirectory</label>
                    </dt>
                    <dd>
                        <input onblur="updateURLPreview();" onkeyup="updateURLPreview();" type="text" name="url_subdir" id="url_subdir" value="<?php echo $dirname; ?>">
                        <span class="helptext">The part from the server root url to The Bug Genie</span>
                    </dd>
                </dl>
                <div style="margin-top: 25px;">
                    <b>According to the information above,</b> The Bug Genie will be accessible at</b><br>
                </div>
                <br>
                <span class="command_box" id="url_preview"><?php echo (array_key_exists('HTTPS', $_SERVER)) ? 'https' : 'http'; ?>://&lt;hostname&gt;<?php echo $dirname; ?></span>
            </div>
            <div class="error" id="continue_error" style="display: none;"> </div>
            <br style="clear: both;">
            <div style="padding-top: 20px; clear: both; text-align: center;">
                <label for="continue_button" style="font-size: 13px; margin-right: 10px;">Click this button to continue and load the necessary default settings</label>
                <img src="iconsets/oxygen/spinning_30.gif" id="next_indicator" style="display: none; vertical-align: middle; margin-left: 10px;">
                <input type="submit" id="continue_button" onclick="$('continue_button').hide();$('next_indicator').show();" value="Continue">
            </div>
        </form>
    <?php endif; ?>
</div>
<?php include_component('installation/footer'); ?>
