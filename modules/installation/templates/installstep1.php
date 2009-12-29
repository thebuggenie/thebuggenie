<?php include_template('installation/header'); ?>
<div class="installation_box">
	<div class="features">
		<div class="feature">
			Even if you haven't used The Bug Genie before, you will soon notice that it comes <b>packed</b> with features, such as:
			<ul>
				<li>Great user management through permissions, groups and teams</li>
				<li>Highly configurable and easy to use search, with grouping capabilities</li>
				<li>Integrated wiki for all your documentation, news and article needs</li>
				<li>SVN integration</li>
				<li>Great messaging functionality</li>
				<li>Issue tasks and issue dependancies</li>
				<li>Project dashboard, with detailed breakdowns and statistics</li>
				<li>Automated roadmap generation</li>
				<li>Voting system</li>
				<li>Extensible module based framework</li>
			</ul>
			For more information, refer to <a href="http://doc.thebuggenie.com" target="_blank">The Bug Genie online manual</a>.<br>
		</div>
		<div class="feature">
			<h4 style="padding-top: 0; margin-top: 0;">UPGRADING FROM A PREVIOUS VERSION?</h4>
			If you are upgrading from version 2.x, use the "Import data from version 2"-wizard, available from the Configuration page after installation. Upgrading from version 1 is not supported.
		</div>
	</div>
	<h2 style="margin-top: 0px;">Welcome to The Bug Genie</h2>
	<p>
	Before we can start the installation, we need to check a few things. Please look through the list of prerequisites below, 
	and take the necessary steps to correct any errors that may have been highlighted.</p>
	<?php if ($base_folder_perm_ok): ?>
		<div class="install_progress prereq_ok" style="width: 500px; margin-top: 10px;"><?php echo image_tag('themes/oxygen/action_ok.png', array(), true); ?>Can write to The Bug Genie directory ...</div>
	<?php endif; ?>
	<?php if ($thebuggenie_folder_perm_ok): ?>
		<div class="install_progress prereq_ok" style="width: 500px; margin-top: 10px;"><?php echo image_tag('themes/oxygen/action_ok.png', array(), true); ?>Can write to The Bug Genie public directory ...</div>
	<?php endif; ?>
	<?php if ($pdo_ok): ?>
		<div class="install_progress prereq_ok" style="width: 500px; margin-top: 10px;"><?php echo image_tag('themes/oxygen/action_ok.png', array(), true); ?>PHP PDO installed and enabled ...</div>
	<?php endif; ?>
	<?php if ($b2db_folder_perm_ok && $b2db_param_file_ok): ?>
		<div class="install_progress prereq_ok" style="width: 500px; margin-top: 10px;"><?php echo image_tag('themes/oxygen/action_ok.png', array(), true); ?>Can save database connection details ...</div>
	<?php endif; ?>
	<?php if ($all_well): ?>
		<div style="margin-top: 20px;">
			Please make sure that you have the following information available:
			<ul class="outlined">
				<li>Database connection details</li>
				<li>Server url</li>
			</ul>
			<br>
		</div>
		<div style="clear: both; padding-top: 20px; text-align: center;">
			<form accept-charset="utf-8" action="index.php" method="post">
				<input type="hidden" name="step" value="2">
				<label for="start_install" style="font-size: 13px;">Start the installation by pressing this button</label><br>
				<img src="themes/oxygen/spinning_30.gif" id="next_indicator" style="display: none;">
				<input type="submit" onclick="$('start_install').hide();$('next_indicator').show();" id="start_install" value="Start installation" style="margin-top: 10px;">
			</form>
		</div>
	<?php else: ?>
		<?php if (!$b2db_folder_perm_ok): ?>
			<div class="installation_prerequisites prereq_fail">
			<b>Can not save database connection details</b><br>
			The <i>core/B2DB</i> folder needs to be writable by the web-server
			</div>
			<b>If you're installing this on a linux server,</b> running this command should fix it:<br>
			<div class="command_box">
			chmod 777 <?php echo str_ireplace('\\', '/', substr(BUGScontext::getIncludePath(), 0, strripos(BUGScontext::getIncludePath(), DIRECTORY_SEPARATOR) + 1)); ?>core/B2DB
			</div>
		<?php endif; ?>
		<?php if (!$b2db_param_file_ok): ?>
			<div class="installation_prerequisites prereq_fail">
			<b>Could not write the SQL settings file</b><br>
			The file that contains the SQL settings already exists, but is not writable
			</div>
			<b>If you're installing this on a linux server,</b> running this command should fix it:<br>
			<div class="command_box">
			chmod 777 <?php echo str_ireplace('\\', '/', substr(BUGScontext::getIncludePath(), 0, strripos(BUGScontext::getIncludePath(), DIRECTORY_SEPARATOR) + 1)); ?>core/B2DB/sql_parameters.inc.php
			</div>
		<?php endif; ?>
		<?php if (!$base_folder_perm_ok): ?>
			<div class="installation_prerequisites prereq_fail">
			<b>Could not write to The Bug Genie directory</b><br>
			The main folder for The Bug Genie should be writable during installation, since we need to store some information in it
			</div>
			<b>If you're installing this on a linux server,</b> running this command should fix it:<br>
			<div class="command_box">
			chmod 777 <?php echo str_ireplace('\\', '/', substr(BUGScontext::getIncludePath(), 0, strripos(BUGScontext::getIncludePath(), DIRECTORY_SEPARATOR) + 1)); ?>
			</div>
		<?php endif; ?>
		<?php if (!$base_folder_perm_ok): ?>
			<div class="installation_prerequisites prereq_fail">
			<b>Could not write to The Bug Genie public directory</b><br>
			The public folder for The Bug Genie should be writable during installation, since we need to store some information in it
			</div>
			<b>If you're installing this on a linux server,</b> running this command should fix it:<br>
			<div class="command_box">
			chmod 777 <?php echo str_ireplace('\\', '/', substr(BUGScontext::getIncludePath() . 'thebuggenie/', 0, strripos(BUGScontext::getIncludePath() . 'thebuggenie/', DIRECTORY_SEPARATOR) + 1)); ?>
			</div>
		<?php endif; ?>
		<?php if (!$pdo_ok): ?>
			<div class="installation_prerequisites prereq_fail">
			<b>PDO not enabled</b><br>
			To install The Bug Genie, PDO must be installed and enabled. More information is available at <a href="http://www.php.net/PDO">php.net</a>
			</div>
		<?php endif; ?>
		<br>
		<div style="clear: both; padding-top: 20px; text-align: center;">
			<form accept-charset="utf-8" action="index.php" method="post">
				<input type="hidden" name="step" value="1">
				<input type="hidden" name="agree_license" value="1">
				<label for="retry_button" style="font-size: 13px; margin-right: 10px;">You need to correct the above error(s) before the installation can continue.</label>
				<input type="submit" id="retry_button" value="Retry">
			</form>
		</div>
	<?php endif; ?>
</div>
<?php include_template('installation/footer'); ?>