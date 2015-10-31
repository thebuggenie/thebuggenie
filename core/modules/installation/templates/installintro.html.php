<?php include_component('installation/header'); ?>
<div class="installation_box">
    <h2>We value your feedback</h2>
    The key to success for any open source project is listening to feedback from users - both positive feedback <b>and</b> constructive criticism.<br>
    <b>If you have anything you would like to tell us,</b> please let us know by emailing us: <a href="mailto:feedback@thebuggenie.com">feedback@thebuggenie.com</a><br>
    <br>
    <h2>License information</h2> 
    This software is Open Source Initiative approved Open Source Software. Open Source Initiative Approved is a trademark of the Open Source Initiative.<br>
    True to the <a target="_blank" href="http://opensource.org/docs/definition.php">the Open Source Definition</a>, The Bug Genie is released under the MPL 2.0.<br>
    <br>
    <a target="_blank" href="http://opensource.org/licenses/MPL-2.0" target="_blank" style="font-weight: bold;">Read the license here</a>. <i>(opens in a new window)</i><br>
    <br>
    Before you can continue the installation, you need to confirm that you agree to be bound by the terms in this license.<br>
    <br>
    <br>
    <form accept-charset="utf-8" action="index.php" method="post">
        <input type="hidden" name="step" value="1">
        <input type="checkbox" name="agree_license" id="agree_license" onclick="($('agree_license').checked) ? $('start_installation').enable() : $('start_installation').disable();">
        <label for="agree_license">I agree to be bound by the terms in the MPL 2.0 license</label>&nbsp;&nbsp;<br>
        <input type="submit" style="margin-top: 15px;" value="Continue" id="start_installation" disabled="disabled">
    </form>
</div>

<?php include_component('installation/footer'); ?>
