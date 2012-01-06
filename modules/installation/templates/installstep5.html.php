<?php include_template('installation/header'); ?>
<?php if (isset($error)): ?>
	<div class="installation_box">
		<div class="error"><?php echo nl2br($error); ?></div>
		<h2>An error occured</h2>
		<div style="font-size: 13px;">An error occured and the installation has been stopped. Please try to fix the error based on the information above, then click back, and try again.<br>
		If you think this is a bug, please report it in our <a href="http://issues.thebuggenie.com" target="_new">online bug tracker</a>.</div>
	</div>
<?php else: ?>
	<div class="installation_box">
		<h2>Please read the following information:</h2>
		<fieldset>
			<legend>Administrator account</legend>
			<div style="font-size: 12px; margin: 10px 0 10px 0;">
				An administrator account has been created. To use this account, log in with the following information:<br>
				<b>Username: </b>administrator<br>
				<b>Password: </b>admin<br>
				<br>
				You should change this password immediately after finalizing the installation.<br>
				This can be done from the "Account" page, available from the dropdown-menu in the top-right corner.
			</div>
		</fieldset>
	</div>
	<div class="installation_box">
		<form accept-charset="utf-8" action="index.php" method="post" id="finalize_settings">
			<input type="hidden" name="step" value="6">
			<div style="padding-top: 20px; clear: both; text-align: center;">
				<img src="iconsets/oxygen/spinning_30.gif" id="next_indicator" style="display: none;">
				<input type="submit" id="continue_button" style="clear: both; font-size: 17px; margin-top: 10px; padding: 10px; height: 40px;" onclick="$('continue_button').hide();$('next_indicator').show();" value="Finalize installation">
			</div>
		</form>
	</div>
<?php endif; ?>
<?php include_template('installation/footer'); ?>