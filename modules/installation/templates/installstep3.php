<?php include_template('installation/header'); ?>
<script type="text/javascript">

	function updateURLPreview()
	{
		if ($('url_host').value.empty() || $('url_subdir').value.empty())
		{
			$('continue_button').hide();
			$('continue_error').show();
			$('continue_error').update('You need to fill out both server and directory url.<br />If The Bug Genie is located directly under the server, end the server url <i>without</i> a forward slash, and put a single forward slash in the directory url.');
		}
		else if($F($('bugs_settings')['url_host']).endsWith('/') == true || ($F($('bugs_settings')['url_subdir']).endsWith('/') == false || $F($('bugs_settings')['url_subdir']).startsWith('/') == false))
		{
			$('continue_button').hide();
			$('continue_error').show();
			$('continue_error').update('The server url <i>cannot end with a forward slash</i>, and the directory url <i>must start and end with a forward slash</i>');
		}
		else 
		{ 
			$('continue_button').show();
			$('continue_error').hide();
			$('url_preview').update($('url_host').value + $('url_subdir').value);
		}
		
		var new_url = $('url_host').value + $('url_subdir').value;
		
		if (new_url.endsWith('//'))
		{
			$('continue_button').hide();
			$('continue_error').show();
			$('continue_error').update('The complete url <i><b>cannot end with two forward slashes</b></i>. If BUGS is located directly under the server, end the server url <i><b>without</b></i> a forward slash, and put <i><b>a single forward slash</b></i> as the directory url.');
		}
	}

</script>
<div class="installation_box">
	<?php if (isset($error)): ?>
		<div class="error"><?php echo nl2br($error); ?></div>
		<h2>An error occured</h2>
		<div style="font-size: 13px;">An error occured and the installation has been stopped. Please try to fix the error based on the information above, then click back, and try again.<br>
		If you think this is a bug, please report it in our <a href="http://b2.thebuggenie.com" target="_new">online bug tracker</a>.</div>
	<?php else: ?>
		<div class="ok">
			All tables were created successfully
		</div>
		<div class="ok">
			Database connection details have been saved<br>
			If you need to restart the installation, the database details will be reused
		</div>
		<h2 style="margin-top: 10px;">Basic information</h2>
		We need some basic information before the installation can continue. Please provide the following information, and press "Continue":
		<form accept-charset="utf-8" action="index.php" method="post" id="bugs_settings">
			<input type="hidden" name="step" value="4">
			<fieldset>
				<legend>The Bug Genie URL information</legend>
				<dl class="install_list">
					<dt>
						<label for="url_host">Server url</label><br>
						The url of the server The Bug Genie is hosted on <b>without the trailing slash</b>
					</dt>
					<dd><input onblur="updateURLPreview();" onkeyup="updateURLPreview();" type="text" name="url_host" id="url_host" value="http://<?php echo $_SERVER['SERVER_NAME']; ?>"></dd>
					<dt>
						<label for="url_subdir">Url subdirectory</label><br>
						The Bug Genie subdirectory part of the url
					</dt>
					<dd><input onblur="updateURLPreview();" onkeyup="updateURLPreview();" type="text" name="url_subdir" id="url_subdir" value="<?php echo dirname($_SERVER['PHP_SELF']); ?>/"></dd>
					<dt style="padding-top: 5px;"><b>According to the information above,</b> The Bug Genie will be accessible at</dt>
					<dd id="url_preview">http://<?php echo $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']); ?>/</dd>
				</dl>
			</fieldset>
			<div class="error" id="continue_error" style="display: none;"> </div>
			<div class="ok" id="important_info" style="font-size: 13px;">
				Make sure that the Url subdirectory path is set in the .htaccess file!<br>
				The .htaccess file found in the top level folder makes The Bug Genie function properly.<br><u>You must make sure that the <i>.htaccess</i> file has its <i><b>RewriteBase</b></i> setting set to the same as the <i><b>Url subdirectory</b></i> value</u>.
			</div>
			<fieldset>
				<legend>Default settings</legend>
				<dl class="install_list">
					<dt>
						<label for="language">Language</label><br>
						The language used in The Bug Genie
					</dt>
					<dd>
						<select name="language" id="language">
						<?php foreach (BUGSi18n::getLanguages() as $lang_code => $lang_desc): ?>
							<option value="<?php echo $lang_code; ?>"<?php if ($lang_code == 'en_US'): ?> selected<?php endif; ?>><?php echo $lang_desc; ?></option>
						<?php endforeach; ?>
						</select>
					</dd>
				</dl>
				<p style="margin-bottom: 15px;">The selected language will also be used to load the default settings, and fixtures such as issue types, status values, etc.<br>
				This setting can be changed at any time from the Configuration center, after the installation is completed.</p> 
			</fieldset>
			<div style="padding-top: 20px; clear: both; text-align: center;">
				<label for="continue_button" style="font-size: 13px; margin-right: 10px;">Click this button to continue and load the necessary default settings</label>
				<input type="submit" id="continue_button" value="Continue">
			</div>
		</form>
	<?php endif; ?>
</div>
<?php include_template('installation/footer'); ?>