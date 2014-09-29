<div class="filter interactive_dropdown" id="interactive_filter_<?php echo $filter->getFilterKey(); ?>" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('Anyone'); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" value="" id="filter_<?php echo $filter->getFilterKey(); ?>_value_input">
    <label><?php

        switch ($filter->getFilterKey())
        {
            case 'owner_team':
                echo __('Owned by team');
                break;
            case 'assignee_team':
                echo __('Assigned team');
                break;
            default:
                echo __($filter->getFilterTitle());
                break;
        }

    ?></label>
    <span class="value"><?php if (!$filter->hasValue()) echo __('Any team'); ?></span>
    <div class="interactive_menu">
        <h1><?php echo __('Select team(s)'); ?></h1>
        <input type="search" class="interactive_menu_filter" data-callback-url="<?php echo make_url('search_filter_findteams', array('filterkey' => $filter->getFilterKey())); ?>" placeholder="<?php echo __('Search for a team'); ?>"><?php echo image_tag('spinning_16.gif', array('class' => 'filter_indicator')); ?>
        <div class="interactive_values_container">
            <ul class="interactive_menu_values filter_callback_results">
            </ul>
            <ul class="interactive_menu_values filter_existing_values">
                <?php foreach ($filter->getAvailableValues() as $team): ?>
                    <li data-value="<?php echo $team->getID(); ?>" class="filtervalue<?php if ($filter->hasValue($team->getID())) echo ' selected'; ?>">
                        <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                        <input type="checkbox" value="<?php echo $team->getID(); ?>" name="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $team->getID(); ?>" data-text="<?php echo $team->getName(); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $team->getID(); ?>" <?php if ($filter->hasValue($team->getID())) echo 'checked'; ?>>
                        <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $team->getID(); ?>"><?php echo $team->getName(); ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="filter_remove_button" onclick="TBG.Search.removeFilter($(this).up());"><?php echo image_tag('icon-mono-remove.png'); ?></div>
</div>
