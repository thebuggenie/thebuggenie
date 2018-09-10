<?php

    use thebuggenie\modules\mailing\Mailing;

?>
<table class="padded_table" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width: auto; border-bottom: 1px solid #DDD; vertical-align: middle;">
            <input type="checkbox" class="fancycheckbox" name="mailing_<?= Mailing::NOTIFY_NOT_WHEN_ACTIVE; ?>" value="1" id="mailing_<?= Mailing::NOTIFY_NOT_WHEN_ACTIVE; ?>_yes"<?php if ($tbg_user->getNotificationSetting(Mailing::NOTIFY_NOT_WHEN_ACTIVE, false, 'mailing')->isOn()): ?> checked<?php endif; ?>>
            <label for="mailing_<?= Mailing::NOTIFY_NOT_WHEN_ACTIVE; ?>_yes"><?= fa_image_tag('check-square-o', ['class' => 'checked']) . fa_image_tag('square-o', ['class' => 'unchecked']) . __("Don't send email notification if I'm currently logged in and active") ?></label></td>
        </td>
    </tr>
</table>
