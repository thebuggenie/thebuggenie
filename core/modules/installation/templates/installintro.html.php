<?php include_component('installation/header'); ?>
<div class="installation_box">
    <div class="message-box type-info">
        <?= fa_image_tag('grin-hearts', [], 'far'); ?>
        <span class="message">
            The key to success for any open source project is listening to feedback from users - both positive feedback <b>and</b> constructive criticism.<br>
            <b>If you have anything you would like to tell us,</b> please let us know by emailing us: <a href="mailto:feedback@thebuggenie.com">feedback@thebuggenie.com</a>
        </span>
    </div>
    <h2>License information</h2>
    This software is Open Source Initiative approved Open Source Software. Open Source Initiative Approved is a trademark of the Open Source Initiative.<br>
    True to the <a target="_blank" href="http://opensource.org/docs/definition.php">the Open Source Definition</a>, The Bug Genie is released under the MPL 2.0.<br>
    <br>
    <a target="_blank" href="http://opensource.org/licenses/MPL-2.0" target="_blank" style="font-weight: bold;">Read the license here</a>. <i>(opens in a new window)</i><br>
    <br>
    Before you can continue the installation, you need to confirm that you agree to be bound by the terms in this license.<br>
    <br>
    <br>
    <form accept-charset="utf-8" action="index.php" method="post" style="display: block; text-align: right;">
        <input type="hidden" name="step" value="1">
        <input type="hidden" name="agree_license" value="1">
        <input type="submit" style="margin: 0 15px 15px 0;" value="Agree and continue" id="start_installation">
    </form>
</div>
<?php include_component('installation/footer'); ?>
