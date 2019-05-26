<?php

    use thebuggenie\modules\mailing\Mailing;

?>
<label><?= __('Email'); ?></label><br>
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
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <input type="checkbox" value="<?= $category_id; ?>" name="mailing_<?= $category_key; ?>_<?= $category_id; ?>" data-text="<?= __($category->getName()); ?>" id="mailing_<?= $category_key; ?>_value_<?= $category_id; ?>" <?php if (in_array($category_id, $selected_category_notifications)) echo 'checked'; ?>>
                        <label for="mailing_<?= $category_key; ?>_value_<?= $category_id; ?>"><?= __($category->getName()); ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
