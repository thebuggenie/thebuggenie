<div class="filter interactive_dropdown" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" value="" id="filter_<?php echo $filter->getFilterKey(); ?>_value_input">
    <label><?php

            switch ($filter->getFilterKey())
            {
                case 'status':
                    echo __('Status');
                    break;
                default:
                    echo __($filter->getFilterTitle());
                    break;
            }

    ?></label>
    <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
    <div class="interactive_menu">
        <h1><?php

                switch ($filter->getFilterKey())
                {
                    case 'status':
                        echo __('Filter on status');
                        break;
                    default:
                        echo __("Filter on %customfield", array('%customfield' => $filter->getFilterTitle()));
                        break;
                }

        ?></h1>
        <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">
        <div class="interactive_values_container">
            <ul class="interactive_menu_values">
                <?php if ($filter->getFilterKey() == 'status'): ?>
                    <li data-value="open" class="filtervalue <?php if ($filter->hasValue('open')) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <input type="checkbox" value="open" name="filters_<?php echo $filter->getFilterKey(); ?>_value_open" data-text="<?php echo __('Only open issues'); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_open" <?php if ($filter->hasValue('open')) echo 'checked'; ?>>
                        <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_open"><?php echo __('Only open issues'); ?></label>
                    </li>
                    <li data-value="closed" class="filtervalue <?php if ($filter->hasValue('closed')) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <input type="checkbox" value="closed" name="filters_<?php echo $filter->getFilterKey(); ?>_value_closed" data-text="<?php echo __('Only closed issues'); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_closed" <?php if ($filter->hasValue('closed')) echo 'checked'; ?>>
                        <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_closed"><?php echo __('Only closed issues'); ?></label>
                    </li>
                    <li class="separator"></li>
                <?php endif; ?>
                <?php foreach ($filter->getAvailableValues() as $status): ?>
                    <li data-value="<?php echo $status->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($status->getID())) echo ' selected'; ?>">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <input type="checkbox" value="<?php echo $status->getID(); ?>" name="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $status->getID(); ?>" data-text="<?php echo __($status->getName()); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $status->getID(); ?>" <?php if ($filter->hasValue($status->getID())) echo 'checked'; ?>>
                        <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $status->getID(); ?>"><?php echo __($status->getName()); ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
