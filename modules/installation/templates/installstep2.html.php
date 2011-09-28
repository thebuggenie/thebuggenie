<?php include_template('installation/header'); ?>
<div class="installation_box">
	<?php if (isset($error)): ?>
		<div class="error">
			An error occured when trying to validate the connection details:<br>
			<?php echo $error; ?>
		</div>
	<?php endif; ?>
	<?php if ($preloaded): ?>
		<div class="ok">
			It looks like you've already completed this step<br>
			Please review the details and the click "Continue" to go the next step
		</div>
	<?php endif; ?>
	<h2 style="margin-top: 0;">Database information</h2>
	<p>The Bug Genie uses a database to store information. To be able to connect to - and store information in - your database, we need some connection information.<br>
	First of all, we need the database username and password:
	<form accept-charset="utf-8" action="index.php" method="post" id="database_connection">
		<input type="hidden" name="step" value="3">
		<fieldset title="Username, password and table prefix">
			<legend>1: Username, password and table prefix</legend>
			<dl class="install_list">
				<dt>
					<label for="db_username">Username</label><br>
					The username used to connect to the database
				</dt>
				<dd><input type="text" name="db_username" id="db_username"<?php if (isset($username)): ?> value="<?php echo $username; ?>"<?php endif; ?>></dd>
				<dt>
					<label for="db_password">Password</label><br>
					The password used to connect to the database
				</dt>
				<dd><input type="password" name="db_password" id="db_password"<?php if (isset($password)): ?> value="<?php echo $password; ?>"<?php endif; ?>></dd>
				<dt>
					<label for="db_name">Table prefix</label><br>
					The table prefix used for all database tables
				</dt>
				<dd><input type="text" name="db_prefix" id="db_prefix" value="tbg3_"></dd>
			</dl>
		</fieldset>
		<p>Now, please fill in the connection information:</p>
		<label for="connection_type_dsn">DSN</label><input type="radio" name="connection_type" value="dsn" id="connection_type_dsn"<?php if ($selected_connection_detail == 'dsn'): ?> checked<?php endif; ?> onclick="$('dsn_info').show();$('custom_info').hide()">&nbsp;&nbsp;
		<label for="connection_type_custom">Custom</label><input type="radio" name="connection_type" value="custom" id="connection_type_custom"<?php if ($selected_connection_detail == 'custom'): ?> checked<?php endif; ?> onclick="$('dsn_info').hide();$('custom_info').show()">
		<fieldset title="DSN"<?php if ($selected_connection_detail != 'dsn'): ?> style="display: none;"<?php endif; ?> id="dsn_info">
			<legend>2: DSN</legend>
			<dl class="install_list">
				<dt style="padding-bottom: 10px;">
					<label for="db_dsn">DSN</label><br>
					 The Data Source Name required to connect to the database<br>
					 <i><a href="http://www.php.net/manual/en/pdo.construct.php" style="font-size: 11px;" target="_blank">What is a DSN?</a></i>
				</dt>
				<dd><input type="text" name="db_dsn" id="db_dsn"<?php if (isset($dsn)): ?> value="<?php echo $dsn; ?>"<?php endif; ?>></dd>
			</dl>
		</fieldset>
		<fieldset title="Connection information"<?php if ($selected_connection_detail != 'custom'): ?> style="display: none;"<?php endif; ?> id="custom_info">
			<legend>2: Connection information</legend>
			<dl class="install_list">
				<dt>
					<label for="db_type">Type</label><br>
					What kind of database The Bug Genie will connect to
				</dt>
				<dd>
					<select name="db_type" id="db_type">
					<?php foreach (\b2db\Core::getDBtypes() as $db_type => $db_desc): ?>
						<?php if (extension_loaded("pdo_{$db_type}")): ?>
							<option value="<?php echo $db_type; ?>"<?php if (isset($b2db_dbtype) && $b2db_dbtype == $db_type): ?> selected<?php endif; ?>><?php echo $db_desc; ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
					</select>
				</dd>
				<dt>
					<label for="db_hostname">Hostname</label><br>
					The hostname of the database The Bug Genie should connect to
				</dt>
				<dd><input type="text" name="db_hostname" id="db_hostname"<?php if (isset($hostname)): ?> value="<?php echo $hostname; ?>"<?php endif; ?>></dd>
				<dt>
					<label for="db_hostname">Port number</label><br>
					The port number for the database The Bug Genie should connect to
				</dt>
				<dd><input type="text" name="db_port" id="db_port"<?php if (isset($port)): ?> value="<?php echo $port; ?>"<?php endif; ?>></dd>
				<dt>
					<label for="db_name">Database name</label><br>
					The database used to store the bug genie tables <i>(must already exist!)</i>
				</dt>
				<dd><input type="text" name="db_name" id="db_name"<?php if (isset($db_name)): ?> value="<?php echo $db_name; ?>"<?php else: ?>value="thebuggenie"<?php endif; ?>></dd>
			</dl>
		</fieldset>
		<div style="padding-top: 20px; clear: both; text-align: center;">
			<label for="continue_button" style="font-size: 13px; margin-right: 10px;">Click this button to test the database connection details</label>
			<img src="iconsets/oxygen/spinning_30.gif" id="next_indicator" style="display: none;">
			<input type="submit" id="continue_button" onclick="$('continue_button').hide();$('next_indicator').show();" value="Continue">
		</div>
	</form>
	<p id="connection_status"></p>
</div>
<?php include_template('installation/footer'); ?>