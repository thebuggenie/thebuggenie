<div class="filter interactive_dropdown" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" value="" id="filter_<?php echo $filter->getFilterKey(); ?>_value_input">
    <label><?php echo __('Category'); ?></label>
    <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
    <div class="interactive_menu">
        <h1><?php echo __('Filter on category'); ?></h1>
        <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
        <div class="interactive_values_container">
            <ul class="interactive_menu_values">
                <?php foreach ($filter->getAvailableValues() as $category): ?>
                    <li data-value="<?php echo $category->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($category->getID())) echo ' selected'; ?>">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <input type="checkbox" value="<?php echo $category->getID(); ?>" name="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $category->getID(); ?>" data-text="<?php echo __($category->getName()); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $category->getID(); ?>" <?php if ($filter->hasValue($category->getID())) echo 'checked'; ?>>
                        <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $category->getID(); ?>"><?php echo __($category->getName()); ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>