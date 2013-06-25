<?php include_template('installation/header', array('mode' => 'upgrade')); ?>
<div class="installation_box">
	<?php if ($upgrade_available): ?>
		<div class="donate padded_box rounded_box shadowed" style="margin-bottom: 15px;">
			<h2>Please keep this project going</h2>
			Even though this software has been provided to you <b>free of charge</b>, developing it is not possible without support from our users.<br>
			You can help in any number of ways:
			<ul>
				<li>Buy a <a href="http://thebuggenie.com/support">support agreement</a><?php /*, <a href="http://thebuggenie.com/partners">partners badge</a> */ ?> or <a href="http://thebuggenie.com/help">donate</a></li>
				<li>Help fund our development and hosting servers</li>
				<li>Contribute patches, fixes and features <a href="http://github.com/thebuggenie/thebuggenie">via github</a></li>
				<li>Write <a href="http://issues.thebuggenie.com/wiki/TheBugGenie:MainPage">documentation</a>, blogs or news articles about The Bug Genie</li>
			</ul>
			<h4>If this software has turned out to be valuable to you and/or your business - please consider supporting it.</h4>
			More information about supporting The Bug Genie development can be found here:
			<a target="_blank" href="http://www.thebuggenie.com/support">http://www.thebuggenie.com/support</a> <i>(opens in a new window)</i>
		</div>
		<?php if (isset($permissions_ok) && $permissions_ok): ?>
			<div class="grey_box padded_box shadowed rounded_box">
				<h2 style="margin-bottom: 15px; padding-bottom: 0;">
					<span style="font-weight: normal;">You are performing the following upgrade: </span><?php echo $current_version; ?>.x => <?php echo TBGSettings::getVersion(false, true); ?><br>
					<span class="smaller">Make a backup of your installation before you continue!</span>
				</h2>
				<?php if (version_compare($current_version, '3.2', '<')): ?>
					<div class="rounded_box shadowed padded_box yellow" style="font-size: 1.1em; margin-bottom: 10px;">
						<u>You are performing an update from an older version of The Bug Genie (<?php echo $current_version; ?>), and not 3.2.x</u>.<br>
						This is a valid upgrade, but if you really are upgrading from <?php echo $current_version; ?>, you need to upgrade to the latest 3.2.x release first, and then upgrade to <?php echo TBGSettings::getVersion(false, true); ?>.
					</div>
				<?php else: ?>
					<form accept-charset="utf-8" action="<?php echo make_url('upgrade'); ?>" method="post">
						Please select the initial status for issues for each scope in this installation:<br>
						<?php foreach ($statuses as $scope_id => $details): ?>
							<label for="status_<?php echo $scope_id; ?>" style="font-weight: bold; font-size: 1.1em;">Initial status for scope <?php echo $details['scopename']; ?>:</label>
							<select name="status[<?php echo $scope_id; ?>]">
								<?php foreach ($details['statuses'] as $status_id => $status_name): ?>
									<option value="<?php echo $status_id; ?>" <?php if (trim(strtolower($status_name)) == 'new') echo 'selected'; ?>><?php echo $status_name; ?></option>
								<?php endforeach; ?>
							</select>
							<br style="clear: both;">
						<?php endforeach; ?>
						<br>
						<input type="hidden" name="perform_upgrade" value="1">
						<input type="checkbox" name="confirm_backup" id="confirm_backup" onclick="($('confirm_backup').checked) ? $('start_upgrade').enable() : $('start_upgrade').disable();">
						<label for="confirm_backup" style="font-weight: bold; font-size: 1.1em;">I have read and understand the <a href="http://thebuggenie.com/release/3_2#upgrade">upgrade notes</a> - and I've taken steps to make sure my data is backed up</label><br>
						<input type="submit" value="Perform upgrade" id="start_upgrade" disabled="disabled" style="margin-top: 10px;">
					</form>
				<?php endif; ?>
			</div>
		<?php else: ?>
			<div class="rounded_box shadowed padded_box installation_prerequisites prereq_fail" style="padding: 10px; margin-bottom: 10px;">
				<b>The version information files are not writable</b>
			</div>
			<p style="font-size: 1.2em;">
				The upgrade routine needs the following two files to be writable:<br>
				<div style="font-size: 1.2em; margin-top: 10px; padding-left: 0;">
					<span class="command_box"><?php echo THEBUGGENIE_PATH . 'installed'; ?></span> and <span class="command_box"><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></span>.
					<b>Please fix this error and try again.</b>
				</div>
			</p>
		<?php endif; ?>
	<?php elseif ($upgrade_complete): ?>
		<h2>Upgrade successfully completed!</h2>
		<p style="font-size: 1.2em;">
			<?php if (isset($upgrade_file_failed)): ?>
				The file <span class="command_box"><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></span> could not be removed.<br>
			<?php endif; ?>
			Make sure that the file <span class="command_box"><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></span> is removed before you click the "Finish" button below.
		</p>
		<div style="margin-top: 15px;">
			<a href="<?php echo make_url('logout'); ?>" class="button button-silver" style="font-size: 1.2em !important; padding: 3px 10px !important;">Finish</a>
		</div>
	<?php else: ?>
		<h2>No upgrade necessary!</h2>
		<p style="font-size: 1.2em;">
			Make sure that the file <span class="command_box"><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></span> is removed before you click the "Finish" button below.
		</p>
		<div style="margin-top: 15px;">
			<a href="<?php echo make_url('logout'); ?>" class="button button-silver" style="font-size: 1.2em !important; padding: 3px 10px !important;">Finish</a>
		</div>
	<?php endif; ?>
</div>
<?php include_template('installation/footer'); ?>