<div id="tab_register_pane"<?php if ($selected_tab != 'register'): ?> style="display: none;"<?php endif; ?>>
		<div class="rounded_box lightgrey" style="vertical-align: middle; padding: 10px;" id="register1">
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('register1'); ?>" method="post" id="register1_form" onsubmit="loginRegister1('<?php echo make_url('register1'); ?>'); return false;">
				<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
				<div>
					<label class="login_fieldheader"  for="desired_username"><?php echo __('Desired username'); ?></label>&nbsp;
					<input type="text" id="desired_username" name="desired_username" style="width: 200px;">
					<br><br>
					<input type="submit" id="register1_button" value="<?php echo __('Check availability'); ?>">
					<span id="register1_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
				</div>
			</form>
		</div>
		<div class="rounded_box lightgrey" style="vertical-align: middle; padding: 10px; display: none;" id="register2">
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('register2'); ?>" method="post" id="register2_form" onsubmit="loginRegister2('<?php echo make_url('register2'); ?>'); return false;">
				<input type="hidden" id="username" name="username">
				<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
				<div>
					<?php echo __('To register, please fill out the information below.'); ?>
					<br>
					<i>(<?php echo __('Required information is marked with an asterisk'); ?>: <b>*</b>)</i><br><br>
					<table border="0" class="login_fieldtable">
						<tr>
							<td><label class="login_fieldheader" for="fieldusername">&nbsp;<?php echo __('Username'); ?></label></td>
							<td><input type="text" id="fieldusername" name="fieldusername" style="width: 200px;" disabled></td>
						</tr>					
						<tr>
							<td><label class="login_fieldheader" for="buddyname">*&nbsp;<?php echo __('Buddy name'); ?></label></td>
							<td><input type="text" class="required" id="buddyname" name="buddyname" style="width: 200px;"></td>
						</tr>
						<tr>
							<td><label class="login_fieldheader" for="realname">&nbsp;<?php echo __('Real name'); ?></label></td>
							<td><input type="text" id="realname" name="realname" style="width: 200px;"></td>
						</tr>
						<tr>
							<td><label class="login_fieldheader" for="email_address">*&nbsp;<?php echo __('E-mail address'); ?></label></td>
							<td><input type="text" class="required" id="email_address" name="email_address" style="width: 200px;"></td>
						</tr>
						<tr>
							<td><label class="login_fieldheader" for="email_confirm">*&nbsp;<?php echo __('Confirm e-mail'); ?></label></td>
							<td><input type="text" class="required" id="email_confirm" name="email_confirm" style="width: 200px;"></td>
						</tr>
					</table>
					<br>
					
					<?php
						$_SESSION['activation_number'] = tbg_printRandomNumber();
					?>
					
					<br><b><?php echo __('Enter the above number in this box'); ?></b><br><br>
					<label class="login_fieldheader" for="verification_no">*&nbsp;<?php echo __('Security check'); ?></label>
					<input type="text" class="required" id="verification_no" name="verification_no" maxlength="6" style="width: 100px;"><br><br>
					<input type="submit" id="register2_button" value="<?php echo __('Register'); ?>">
					<span id="register2_indicator" style="display: none;"><?php echo image_tag('spinning_20.gif'); ?></span>
				</div>
			</form>
		</div>
		<div class="rounded_box green borderless" style="display: none;" id="register3">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="vertical-align: middle; padding: 5px;">
				<div class="login_boxheader"><?php echo __('Register a new account'); ?></div>
				<span class="login_fieldheader"><?php echo __('Thank you for registering!'); ?></span>
				<br>
				<span style="font-size: 14px;" id="register_message"></span>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>		
	<br>
</div>