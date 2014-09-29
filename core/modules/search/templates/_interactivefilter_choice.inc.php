<?php
    $key = $filter->getFilterKey();
    switch ($key)
    {
        case 'priority':
            $title = __('Priority');
            $description = __('Filter on priority');
            break;
        case 'resolution':
            $title = __('Resolution');
            $description = __('Filter on resolution');
            break;
        case 'severity':
            $title = __('Severity');
            $description = __('Filter on severity');
            break;
        case 'reproducability':
            $title = __('Reproducability');
            $description = __('Filter on reproducability');
            break;
        default:
            $title = __($filter->getFilterTitle());
            $description = __("Filter on %customfield", array('%customfield' => $filter->getFilterTitle()));
            break;
    }
?>
<div class="filter interactive_dropdown" id="interactive_filter_<?php echo $key; ?>"
     data-filterkey="<?php echo $key; ?>" data-value="<?php echo $filter->getValue(); ?>"
     data-all-value="<?php echo __('All'); ?>">
    <input type="hidden" name="fs[<?php echo $key; ?>][o]" value="<?php echo $filter->getOperator(); ?>">
    <input type="hidden" name="fs[<?php echo $key; ?>][v]" value=""
           id="filter_<?php echo $key; ?>_value_input">
    <label><?php echo $title; ?></label>
    <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>

    <div class="interactive_menu">
        <h1><?php echo $description; ?></h1>
        <input type="search" class="interactive_menu_filter" placeholder="<?php echo __('Filter values'); ?>">

        <div class="interactive_values_container">
            <ul class="interactive_menu_values">
                <?php foreach ($filter->getAvailableValues() as $value): ?>
                    <li data-value="<?php echo $value->getID(); ?>"
                        class="filtervalue<?php if ($filter->hasValue($value->getID())) echo ' selected'; ?>">
                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                        <input type="checkbox" value="<?php echo $value->getID(); ?>"
                               name="filters_<?php echo $key; ?>_value_<?php echo $value->getID(); ?>"
                               data-text="<?php echo __($value->getName()); ?>"
                               id="filters_<?php echo $key; ?>_value_<?php echo $value->getID(); ?>" <?php if ($filter->hasValue($value->getID())) echo 'checked'; ?>>
                        <label
                            for="filters_<?php echo $key; ?>_value_<?php echo $value->getID(); ?>"><?php echo __($value->getName()); ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="filter_remove_button"
         onclick="TBG.Search.removeFilter($(this).up());"><?php echo image_tag('icon-mono-remove.png'); ?></div>
</div>
