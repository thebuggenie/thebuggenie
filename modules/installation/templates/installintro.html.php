<?php include_template('installation/header'); ?>
<div class="donate installation_box">
	<h2>How to support future development</h2>
	Even though this software has been provided to you free of charge, developing it would not have been possible without support from our users. By making a donation, or buying a support contract you can help us continue development.<br>
	<h4>If this software proves valuable to you - please consider supporting it.</h4>
	More information about supporting The Bug Genie development can be found here:
	<a target="_blank" href="http://www.thebuggenie.com/giving_back.php">http://www.thebuggenie.com/giving_back.php</a> <i>(opens in a new window)</i>
</div>
<div class="installation_box">
	<h2>We value your feedback</h2>
	The key to success for any open source project is listening to feedback from users - both positive feedback <b>and</b> constructive criticism.<br>
	<b>We listen to our users.</b>
	<br>
	<h4>If you have anything you would like to tell us, please let us know by emailing us: <a href="mailto:feedback@thebuggenie.com">feedback@thebuggenie.com</a></h4>
	<br>
	<h2>License information</h2> 
	<b>This software is Open Source Initiative approved Open Source Software. Open Source Initiative Approved is a trademark of the Open Source Initiative.</b><br>
	True to the <a target="_blank" href="http://opensource.org/docs/definition.php">the Open Source Definition</a>, The Bug Genie is released under the MPL 1.1 only. <a target="_blank" href="http://www.opensource.org/licenses/mozilla1.1.php" target="_blank" style="font-weight: bold;">Read the license here</a>. <i>(opens in a new window)</i><br> 
	<br>
	Before you can continue the installation, you need to confirm that you agree to be bound by the terms in this license.<br>
	<br>
	<br>
	<form accept-charset="utf-8" action="index.php" method="post">
		<input type="hidden" name="step" value="1">
		<input type="checkbox" name="agree_license" id="agree_license" onclick="($('agree_license').checked) ? $('start_installation').enable() : $('start_installation').disable();">
		<label for="agree_license" style="font-weight: bold; font-size: 14px;">I agree to be bound by the terms in the MPL 1.1 license</label>&nbsp;&nbsp;<br>
		<input type="submit" style="margin-top: 15px;" value="Continue" id="start_installation" disabled="disabled">
	</form>
</div>

<?php include_template('installation/footer'); ?>