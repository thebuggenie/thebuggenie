<?php include_template('installation/header'); ?>
<?php 

$dirname = dirname($_SERVER['PHP_SELF']);

if (mb_stristr(PHP_OS, 'WIN'))
{
	$dirname = str_replace("\\", "/", $dirname); /* Windows adds a \ to the URL which we don't want */
}

if ($dirname != '/')
{
	$dirname = $dirname . '/';
}

?>
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
		If you think this is a bug, please report it in our <a href="http://thebuggenie.com/thebuggenie" target="_new">online bug tracker</a>.</div>
	<?php else: ?>
		<div class="ok">
			All tables were created successfully, and the database connection details have been saved<br>
			If you need to restart the installation, you won't have to enter these details again
		</div>
		<div class="features">
			<div class="tab_menu" style="margin-top: 20px;">
				<ul>
					<li id="example_1_link" class="selected"><a href="javascript:void(0);" onclick="$('example_1_link').addClassName('selected');$('example_2_link').removeClassName('selected');$('example_2').hide();$('example_1').show();">Apache vhost example</a></li>
					<li id="example_2_link"><a href="javascript:void(0);" onclick="$('example_2_link').addClassName('selected');$('example_1_link').removeClassName('selected');$('example_1').hide();$('example_2').show();">Apache subdirectory example</a></li>
				</ul>
			</div>
			<div class="feature" style="border-top: 0; padding: 0; border-color: #BBB; margin-bottom: 5px;" id="example_1">
				<div class="description">
					<b>EXAMPLE #1:</b> <i>The Bug Genie is installed in <i>/var/www/thebuggenie</i>, and I want to set up a virtual host for The Bug Genie.</i>
				</div>
				<div class="content">
					<b>Apache setup:</b> Set up the virtual host as usual, but point the <u>DocumentRoot</u> for The Bug Genie to the <i>thebuggenie/</i> subfolder <i>inside</i> the main folder. Make sure the apache virtual host setup has <u>AllowOverride All</u> for the folder where The Bug Genie is located, and make sure the .htaccess file inside the <i>thebuggenie/</i>-folder is accessible to Apache.<br>
					<br>
					<b>The Bug Genie setup:</b> With this setup, The Bug Genie will be located at the top level, so set the <u>URL subdirectory</u> below to "/", which means "top level".
				</div>
			</div>
			<div class="feature" style="border-top: 0; padding: 0; margin-bottom: 5px; display: none;" id="example_2">
				<div class="description">
					<b>EXAMPLE #2:</b> <i>The Bug Genie is installed in <i>/var/www/thebuggenie</i>, and I want to access it as a subfolder of the DocumentRoot, which is /var/www</i>
				</div>
				<div class="content">
					<b>Apache setup:</b> Make sure the apache host setup has <u>AllowOverride All</u> for the folder thebuggenie is located, and make sure the .htaccess file inside the <i>thebuggenie/</i>-folder is accessible to Apache. You may want to copy the main folder content to a folder one level up (extract the main content of the top <i>thebuggenie/</i>-folder directly to /var/www), so that the <i>thebuggenie/</i>-folder inside the main folder is accessible as /var/www/thebuggenie.<br>
					<br>
					<b>The Bug Genie setup:</b> With this setup, The Bug Genie will be located at either <i>http://hostname/thebuggenie/thebuggenie/</i> or <i>http://hostname/thebuggenie/</i> (see above), so set the <u>URL subdirectory</u> below to match the subdirectory part (either "/thebuggenie/" or "/thebuggenie/thebuggenie/").
				</div>
			</div>
			<div class="feature">
				<b>See more information and examples at <a href="http://www.thebuggenie.com">www.thebuggenie.com</a></b>
			</div>
		</div>
		<h2 style="margin-top: 10px;">Server / URL information</h2>
		The Bug Genie uses URL rewriting to make URLs look more readable. URL rewriting is what makes it possible to, instead of <u><i>viewissue.php?project_key=projectname&amp;issue_id=123</i></u>, use URLs such as <u><i>/projectname/issue/123</i></u>.<br>
		<br><b>It is important that The Bug Genie and your web server is correctly set up with url rewriting enabled for this to work.</b><br>
		<br>
		<b>You can read more about setting up URL rewriting, here:</b><br>
		<a href="http://httpd.apache.org/docs/2.2/mod/mod_rewrite.html">http://httpd.apache.org/docs/2.2/mod/mod_rewrite.html</a> (Apache)<br>
		<a href="http://support.microsoft.com/kb/324000/">http://support.microsoft.com/kb/324000/</a> (IIS)<br>
		<br>
		For Apache, it is enough that the rewrite module (mod_rewrite) is installed and enabled, and that the virtual host setup has set <b>AllowOverride All</b> for the folder The Bug Genie is located.<br>
		With this setup, Apache should use the <i>.htaccess</i> file located inside the <i>thebuggenie/</i> folder.<br>
		<br>
		If you for any reason cannot turn on <b>AllowOverride All</b> for that folder, look at the .htaccess file The Bug Genie bundles (located inside the <i>thebuggenie/</i> folder, and copy the necessary lines to your virtual host definition.<br>
		<br>
		<form accept-charset="utf-8" action="index.php" method="post" id="tbg_settings" style="clear: both;">
			<input type="hidden" name="step" value="4">
			<fieldset>
				<legend>The Bug Genie URL information</legend>
				<dl class="install_list">
					<dt style="width: 600px;">
						<label for="apache_autosetup_yes">Set up my .htaccess file automatically</label> <span class="faded_out">Select "yes" if you are using an Apache web server</span>
					</dt>
					<dd>
						<input type="radio" name="apache_autosetup" id="apache_autosetup_yes" value="1" checked><label for="apache_autosetup_yes">Yes</label>&nbsp;
						<input type="radio" name="apache_autosetup" id="apache_autosetup_no" value="0"><label for="apache_autosetup_no">No</label>
					</dd>
					<dt style="width: 600px;">
						<label for="url_subdir">Url subdirectory</label> <span class="faded_out">The part from the server root url to The Bug Genie</span>
					</dt><br>
					<dd><input onblur="updateURLPreview();" onkeyup="updateURLPreview();" type="text" name="url_subdir" id="url_subdir" value="<?php echo $dirname; ?>" style="width: 300px;"></dd>
					<dt style="width: 600px;">
						<b>According to the information above,</b> The Bug Genie will be accessible at</b><br>
						<span class="faded_out"><i>The Bug Genie will be available from other hostnames as well, but the subfolder path needs to be the same</i></span>
					</dt><br>
					<dd id="url_preview"><?php echo (array_key_exists('HTTPS', $_SERVER)) ? 'https' : 'http'; ?>://<?php echo $_SERVER['SERVER_NAME'] . $dirname; ?></dd>
				</dl>
			</fieldset>
			<div class="error" id="continue_error" style="display: none;"> </div>
			<br style="clear: both;">
			<div style="padding-top: 20px; clear: both; text-align: center;">
				<label for="continue_button" style="font-size: 13px; margin-right: 10px;">Click this button to continue and load the necessary default settings</label>
				<img src="iconsets/oxygen/spinning_30.gif" id="next_indicator" style="display: none;">
				<input type="submit" id="continue_button" onclick="$('continue_button').hide();$('next_indicator').show();" value="Continue">
			</div>
		</form>
	<?php endif; ?>
</div>
<?php include_template('installation/footer'); ?>