<h3><?php echo __('Email notifications'); ?></h3>
<p><?php echo __('In addition to being notified when logging in, you can choose to also be notified via email for issues or articles you subscribe to. The following settings control when you receive emails.'); ?></p>
<table class="padded_table" cellpadding=0 cellspacing=0>
    <thead>
        <tr>
            <th></th>
            <th><?php echo __('Notification box'); ?></th>
            <th><?php echo __('Email'); ?></th>
        </tr>
    </thead>
    <?php foreach ($notificationsettings as $key => $description): ?>
        <tr>
            <td style="width: auto; border-bottom: 1px solid #DDD;"><label for="<?php echo $key; ?>_yes"><?php echo $description ?></label></td>
            <?php if ($key == \thebuggenie\modules\mailing\Mailing::NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY): ?>
                <td colspan="2" style="width: 50px; text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
                    <select name="<?php echo $key; ?>" id="<?php echo $key; ?>">
                        <option value="0"><?php echo __('All categories'); ?></option>
                        <?php foreach (\thebuggenie\core\entities\Category::getAll() as $category_id => $category): ?>
                            <?php if (!$category->canUserSet($tbg_user)) continue; ?>
                            <option value="<?php echo $category_id; ?>"><?php echo $category->getName(); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            <?php else: ?>
                <td style="width: 50px; text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
                    <input type="checkbox" name="mailing_<?php echo $key; ?>" value="1" id="<?php echo $key; ?>_yes"<?php if ($tbg_user->getNotificationSetting($key, false, 'mailing')->isOn()): ?> checked<?php endif; ?>>
                </td>
                <td style="width: 50px; text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
                    <input type="checkbox" name="mailing_<?php echo $key; ?>" value="1" id="<?php echo $key; ?>_yes"<?php if ($tbg_user->getNotificationSetting($key, false, 'mailing')->isOn()): ?> checked<?php endif; ?>>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>
