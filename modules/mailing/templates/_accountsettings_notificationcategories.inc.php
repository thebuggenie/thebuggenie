<?php

    use thebuggenie\modules\mailing\Mailing;

?>
<table class="padded_table" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width: auto; border-bottom: 1px solid #DDD; vertical-align: middle;"><label for="<?= $category_key; ?>_yes"><?= __('Notify via email when issues are created in selected categories') ?></label></td>
        <td style="width: 350px; text-align: right; border-bottom: 1px solid #DDD; vertical-align: middle;">
            <div class="filter interactive_dropdown rightie" data-filterkey="<?= $category_key; ?>" data-value="" data-all-value="<?= __('None selected'); ?>">
                <input type="hidden" name="mailing_<?= $category_key; ?>" value="<?= join(',', $selected_category_notifications); ?>" id="filter_<?= $category_key; ?>_value_input">
                <label><?= __('Categories'); ?></label>
                <span class="value"><?php if (empty($selected_category_notifications)) echo __('None selected'); ?></span>
                <div class="interactive_menu">
                    <h1><?= __('Select which categories to subscribe to'); ?></h1>
                    <input type="search" class="interactive_menu_filter" placeholder="<?= __('Filter categories'); ?>">
                    <div class="interactive_values_container">
                        <ul class="interactive_menu_values">
                            <?php foreach ($categories as $category_id => $category): ?>
                                <li data-value="<?= $category_id; ?>" class="filtervalue<?php if (in_array($category_id, $selected_category_notifications)) echo ' selected'; ?>">
                                    <?= image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                    <input type="checkbox" value="<?= $category_id; ?>" name="mailing_<?= $category_key; ?>_<?= $category_id; ?>" data-text="<?= __($category->getName()); ?>" id="mailing_<?= $category_key; ?>_value_<?= $category_id; ?>" <?php if (in_array($category_id, $selected_category_notifications)) echo 'checked'; ?>>
                                    <label for="mailing_<?= $category_key; ?>_value_<?= $category_id; ?>"><?= __($category->getName()); ?></label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #DDD; vertical-align: middle;"><label for="mailing_<?= Mailing::NOTIFY_NOT_WHEN_ACTIVE; ?>_yes"><?= __("Don't send email notification if I'm currently logged in and active") ?></label></td>
        <td style="text-align: right; border-bottom: 1px solid #DDD; vertical-align: middle;">
            <div style="display: inline-block; width: 50px; text-align: center; padding: 0; margin: 0;">
                <input type="checkbox" name="mailing_<?= Mailing::NOTIFY_NOT_WHEN_ACTIVE; ?>" value="1" id="mailing_<?= Mailing::NOTIFY_NOT_WHEN_ACTIVE; ?>_yes"<?php if ($tbg_user->getNotificationSetting(Mailing::NOTIFY_NOT_WHEN_ACTIVE, false, 'mailing')->isOn()): ?> checked<?php endif; ?>>
            </div>
        </td>
    </tr>
</table>
