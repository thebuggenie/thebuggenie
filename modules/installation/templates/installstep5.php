<?php include_template('installation/header'); ?>
<?php if (isset($error)): ?>
	<div class="installation_box">
		<div class="error"><?php echo nl2br($error); ?></div>
		<h2>An error occured</h2>
		<div style="font-size: 13px;">An error occured and the installation has been stopped. Please try to fix the error based on the information above, then click back, and try again.<br>
		If you think this is a bug, please report it in our <a href="http://b2.thebuggenie.com" target="_new">online bug tracker</a>.</div>
	</div>
<?php else: ?>
	<div class="installation_box">
		<h2>Please read the following information:</h2>
		<fieldset>
			<legend>Administrator account</legend>
			<div style="font-size: 12px; margin: 10px 0 10px 0;">
			An administrator account has been created. To use this account, log in with the following information:<br>
			<b>Username: </b>Administrator<br>
			<b>Password: </b>admin<br>
			<br>
			You should log in and change this password immediately after finalizing the installation.</div>
		</fieldset>
		<form accept-charset="utf-8" action="index.php" method="post" id="tbg__settings">
			<input type="hidden" name="step" value="5">
			<input type="hidden" name="sample_data" value="1">
			<fieldset>
				<legend>Installing sample data</legend>
				<?php if (!$sample_data): ?>
					<dl class="install_list">
						<dt style="width: 600px;">
							If you want to try out The Bug Genie without real data, install sample data to play with.<br>
							This will populate the database with a few projects, issues, articles, etc.
						</dt>
						<dd style="text-align: right;">
							<input type="submit" value="Install sample data" style="width: 180px; margin-right: 10px;">
						</dd>
					</dl>
				<?php else: ?>
					<div style="font-size: 13px; margin: 10px 0 10px 0;">Installing sample data is not yet implemented</div> 
				<?php endif; ?>
			</fieldset>
		</form>
	</div>
	<div class="installation_box">
		<form accept-charset="utf-8" action="index.php" method="post" id="tbg__settings">
			<input type="hidden" name="step" value="6">
			<div style="padding-top: 20px; clear: both; text-align: center;">
				<label for="continue_button" style="font-size: 13px; margin-right: 10px;">Click this button when you're done, and ready to use The Bug Genie</label>
				<img src="themes/oxygen/spinning_30.gif" id="next_indicator" style="display: none;">
				<input type="submit" id="continue_button" onclick="$('continue_button').hide();$('next_indicator').show();" value="Finalize installation">
			</div>
		</form>
	</div>
<?php endif; ?>
<?php include_template('installation/footer'); ?>