<?php

    use thebuggenie\modules\mailing\Mailing;

?>
<table class="padded_table" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width: auto; border-bottom: 1px solid #DDD; vertical-align: middle;"><label for="mailing_<?= Mailing::NOTIFY_NOT_WHEN_ACTIVE; ?>_yes"><?= __("Don't send email notification if I'm currently logged in and active") ?></label></td>
        <td style="width: 350px; text-align: right; border-bottom: 1px solid #DDD; vertical-align: middle;">
            <div style="display: inline-block; width: 50px; text-align: center; padding: 0; margin: 0;">
                <input type="checkbox" name="mailing_<?= Mailing::NOTIFY_NOT_WHEN_ACTIVE; ?>" value="1" id="mailing_<?= Mailing::NOTIFY_NOT_WHEN_ACTIVE; ?>_yes"<?php if ($tbg_user->getNotificationSetting(Mailing::NOTIFY_NOT_WHEN_ACTIVE, false, 'mailing')->isOn()): ?> checked<?php endif; ?>>
            </div>
        </td>
    </tr>
</table>
