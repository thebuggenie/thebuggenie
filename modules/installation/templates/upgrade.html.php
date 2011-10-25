<?php include_template('installation/header', array('mode' => 'upgrade')); ?>
<?php if ($upgrade_available): ?>
	<div class="donate installation_box">
		<h2>Don't you like us?</h2>
		Even though this software has been provided to you free of charge, developing it is not possible without financial support from our users.
		By making a donation or buying a support contract, you:
		<ul>
			<li>Help ensure continued development of The Bug Genie</li>
			<li>Make sure we can keep our web services available</li>
		</ul>
		<h4>If this software has turned out to be valuable to you and/or your business - please consider supporting it.</h4>
		More information about supporting The Bug Genie development can be found here:
		<a target="_blank" href="http://www.thebuggenie.com/support.php">http://www.thebuggenie.com/support.php</a> <i>(opens in a new window)</i>
	</div>
<?php endif; ?>
<div class="installation_box">
	<?php if ($upgrade_available): ?>
		<h2>You are performing the following upgrade: <?php echo $current_version; ?>.x => 3.2<br>
			Make a backup of your installation before you continue!</h2>
		<br>
		<br>
		<?php if (isset($permissions_ok) && $permissions_ok): ?>
			<form accept-charset="utf-8" action="<?php echo make_url('upgrade'); ?>" method="post">
				<input type="hidden" name="perform_upgrade" value="1">
				<input type="checkbox" name="confirm_backup" id="confirm_backup" onclick="($('confirm_backup').checked) ? $('start_upgrade').enable() : $('start_upgrade').disable();">
				<label for="confirm_backup" style="font-weight: bold; font-size: 14px;">
					I have made a backup, and will be solely responsible if I have chosen not to do so
					<div style="font-weight: normal; font-size: 11px; margin-left: 25px;">And even if I have made a backup I know there is no 100% guarantee this will work</div></label>&nbsp;&nbsp;<br>
				<input type="submit" style="margin-top: 15px;" value="Perform upgrade" id="start_upgrade" disabled="disabled">
			</form>
		<?php else: ?>
			<div class="installation_prerequisites prereq_fail">
				<b>The version information files are not writable</b><br>
				The upgrade routine needs to write to the following files:<br>
				<ul>
					<li><?php echo THEBUGGENIE_PATH . 'installed'; ?>, and</li>
					<li><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></li>
				</ul>
				<b>Please make sure these files are writable before you continue</b>
			</div>
		<?php endif; ?>
	<?php elseif ($upgrade_complete): ?>
		<h2>Upgrade successfully completed!</h2>
		If the file <b><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></b> exists, please remove this file and click the "Finish" button below.<br>
		<a href="<?php echo make_url('logout'); ?>" class="button button-silver">Finish</a>
	<?php else: ?>
		<h2>No upgrade necessary!</h2>
		If the file <b><?php echo THEBUGGENIE_PATH . 'upgrade'; ?></b> exists, please remove this file and click the "Finish" button below.<br>
		<a href="<?php echo make_url('logout'); ?>" class="button button-silver">Finish</a>
	<?php endif; ?>
</div>
<?php include_template('installation/footer'); ?>