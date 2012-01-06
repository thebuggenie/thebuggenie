<?php include_template('installation/header'); ?>
<div class="installation_box">
	<?php if (isset($error)): ?>
		<div class="error"><?php echo nl2br($error); ?></div>
		<h2>An error occured</h2>
		<div style="font-size: 13px;">An error occured and the installation has been stopped. Please try to fix the error based on the information above, then click back, and try again.<br>
		If you think this is a bug, please report it in our <a href="http://issues.thebuggenie.com" target="_new">online bug tracker</a>.</div>
	<?php else: ?>
		<?php if ($htaccess_error !== false): ?>
			<div class="error">
				The installation routine could not setup your .htaccess file automatically.<br>
				<?php if (!is_bool($htaccess_error)): ?>
					<br>
					<b><?php echo $htaccess_error; ?></b><br>
				<?php endif; ?>
				<br>
				Either fix the problem above (if any details are mentioned), <b>click "Back"</b> and try again - or follow these simple steps:
				<ul style="font-size: 11px;">
					<li>Rename or copy the <i>[main folder]/<?php echo THEBUGGENIE_CORE_PATH; ?>templates/htaccess.template</i> file to <i>[main folder]/<?php echo THEBUGGENIE_PUBLIC_FOLDER_NAME; ?>/.htaccess</i></li>
					<li>Open up the <i>[main folder]/<?php echo THEBUGGENIE_PUBLIC_FOLDER_NAME; ?>/.htaccess</i> file, and change the <u>RewriteBase</u> path to be identical to the <u>URL subdirectory</u></li>
				</ul>
			</div>
		<?php elseif ($htaccess_ok): ?>
			<div class="ok">
				Apache .htaccess auto-setup completed successfully
			</div>
		<?php endif; ?>
		<div class="ok">
			All settings were stored. Default data and settings loaded successfully
		</div>
		<h2 style="margin-top: 10px;">Enabling functionality</h2>
		The Bug Genie consists of the Caspar framework, and a set of modules. These provide extra functionality such as VCS (version control system) integration and email communication.<br>
		<br>
		Please select which modules to enable here, before pressing "Continue":<br>
		<i>(You can always enable / disable this functionality from the configuration center after the installation is completed)</i>
		<form accept-charset="utf-8" action="index.php" method="post" id="tbg_settings">
			<input type="hidden" name="step" value="5">
			<fieldset>
				<legend>The Bug Genie modules</legend>
				<dl class="install_list">
					<dt>
						<strong>Enable email communication</strong><br>
						Enables functionality that sends and receives emails
					</dt>
					<dd>
						<input type="radio" name="modules[mailing]" value="1" id="modules_mailing_yes" checked="checked"><label for="modules_mailing_yes" style="margin-right: 5px;">Yes</label>
						<input type="radio" name="modules[mailing]" value="0" id="modules_mailing_no"><label for="modules_mailing_no">No</label>
					</dd>
					<dt>
						<strong>Enable VCS Integration</strong><br>
						Allows communication between VCS systems (such as svn) and The Bug Genie
					</dt>
					<dd>
						<input type="radio" name="modules[vcs_integration]" value="1" id="modules_vcs_integration_yes" checked="checked"><label for="modules_vcs_integration_yes" style="margin-right: 5px;">Yes</label>
						<input type="radio" name="modules[vcs_integration]" value="0" id="modules_vcs_integration_no"><label for="modules_vcs_integration_no">No</label>
					</dd>
					<?php /*
					<dt>
						<strong>Enable messaging</strong><br>
						Enables functionality that lets users send messages to eachother
					</dt>
					<dd>
						<input type="radio" name="modules[messages]" value="1" id="modules_messages_yes" checked="checked"><label for="modules_messages_yes" style="margin-right: 5px;">Yes</label>
						<input type="radio" name="modules[messages]" value="0" id="modules_messages_no"><label for="modules_messages_no">No</label>
					</dd>
					<dt>
						<strong>Enable calendar</strong><br>
						Enables calendar functionality
					</dt>
					<dd>
						<input type="radio" name="modules[calendar]" value="1" id="modules_calendar_yes" checked="checked"><label for="modules_calendar_yes" style="margin-right: 5px;">Yes</label>
						<input type="radio" name="modules[calendar]" value="0" id="modules_calendar_no"><label for="modules_calendar_no">No</label>
					</dd> */ ?>
				</dl>
			</fieldset>
			<div style="padding-top: 20px; clear: both; text-align: center;">
				<label for="continue_button" style="font-size: 13px; margin-right: 10px;">Click this button to continue and enable the selected modules</label>
				<img src="iconsets/oxygen/spinning_30.gif" id="next_indicator" style="display: none;">
				<input type="hidden" name="modules[publish]" value="1" id="modules_publish_yes">
				<input type="submit" id="continue_button" onclick="$('continue_button').hide();$('next_indicator').show();" value="Continue">
			</div>
		</form>
	<?php endif; ?>
</div>
<?php include_template('installation/footer'); ?>