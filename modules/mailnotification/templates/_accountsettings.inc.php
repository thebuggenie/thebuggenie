<?php

	if (BUGScontext::getRequest()->getParameter('forcenotification'))
	{
		BUGScontext::getModule('mailnotification')->saveSetting('forcenotification', BUGScontext::getRequest()->getParameter('forcenotification'), BUGScontext::getUser()->getUID());
	}
	if (BUGScontext::getRequest()->getParameter('hold_email_on_issue_update'))
	{
		BUGScontext::getModule('mailnotification')->saveSetting('hold_email_on_issue_update', BUGScontext::getRequest()->getParameter('hold_email_on_issue_update'), BUGScontext::getUser()->getUID());
	}

?>
<table style="table-layout: fixed; width: 100%; background-color: #F1F1F1; margin-top: 15px; border: 1px solid #DDD;" cellpadding=0 cellspacing=0>
<tr>
<td style="padding-left: 4px; width: 20px;"><?php echo image_tag('cfg_icon_mailnotification.png'); ?></td>
<td style="border: 0px; width: auto; padding: 3px; padding-left: 7px;"><b><?php echo __('Notification settings'); ?></b></td>
</tr>
</table>
<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="account.php" method="post">
<input type="hidden" name="settings" value="mailnotification">
<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
<tr>
<td style="width: 200px;"><b><?php echo __('Notification on own changes'); ?></b></td>
<td style="width: 300px;"><select name="forcenotification" style="width: 100%;">
<option value=1 <?php if (BUGSsettings::get('forcenotification', 'mailnotification', null, BUGScontext::getUser()->getUID()) == 1) echo ' selected'; ?>><?php echo __('Send notification email on my own changes'); ?></option>
<option value=2 <?php if (BUGSsettings::get('forcenotification', 'mailnotification', null, BUGScontext::getUser()->getUID()) == 2) echo ' selected'; ?>><?php echo __('Only notify me when others are committing changes'); ?></option>
</select>
</td>
</tr>
<tr>
<td style="width: 200px;"><b><?php echo __('Always notify'); ?></b></td>
<td style="width: 300px;"><select name="hold_email_on_issue_update" style="width: 100%;">
<option value=0 <?php if (BUGSsettings::get('hold_email_on_issue_update', 'mailnotification', null, BUGScontext::getUser()->getUID()) == 0) echo ' selected'; ?>><?php echo __('Always send me an email whenever an issue changes'); ?></option>
<option value=1 <?php if (BUGSsettings::get('hold_email_on_issue_update', 'mailnotification', null, BUGScontext::getUser()->getUID()) == 1) echo ' selected'; ?>><?php echo __('Stop sending emails until I open the issue'); ?></option>
</select>
</td>
</tr>
<tr>
<td colspan=2 style="text-align: right;"><input type="submit" value="<?php echo __('Save'); ?>"></td>
</tr>
</table>
</form>