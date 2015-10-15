<?php if (array_key_exists($key, $notificationsettings)): ?>
    <td style="width: 50px; text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
        <input type="checkbox" name="mailing_<?php echo $key; ?>" value="1"<?php if ($tbg_user->getNotificationSetting($key, false, 'mailing')->isOn()): ?> checked<?php endif; ?>>
    </td>
<?php endif; ?>
