<?php

    use thebuggenie\core\framework\Settings;

?>
<?php if (array_key_exists($key, $notificationsettings)): ?>
    <td style="width: 50px; text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
        <input type="checkbox" class="fancycheckbox" name="mailing_<?php echo $key; ?>" id="mailing_<?= $key; ?>" value="1"<?php if ($tbg_user->getNotificationSetting($key, $key == Settings::SETTINGS_USER_NOTIFY_MENTIONED, 'mailing')->isOn()): ?> checked<?php endif; ?>><label for="mailing_<?= $key; ?>"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?></label>
    </td>
<?php else: ?>
    <td style="text-align: center; border-bottom: 1px solid #DDD;" valign="middle"></td>
<?php endif; ?>
