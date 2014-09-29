<?php

    use thebuggenie\core\entities\BoardColumn,
        thebuggenie\core\entities\AgileBoard;

?>
<div id="editagileboard_column_<?php echo $column->getID(); ?>" class="column">
    <input type="text" name="name" value="<?php echo tbg_template_escape($column->getName()); ?>" placeholder="<?php echo __e('Column status (ex: New, Done)'); ?>">
    <?php if ($column->getBoard()->getType() == AgileBoard::TYPE_KANBAN): ?>
    <?php endif; ?>
    <div class="fancyfilter filter interactive_dropdown" data-filterkey="editagileboard_column_<?php echo $column->getID(); ?>_statuss" data-value="<?php echo join(',', $column->getStatusIds()); ?>" data-no-selection-value="<?php echo __('None selected'); ?>">
        <input type="hidden" name="column[<?php if ($column->getID()) echo $column->getID(); ?>][status_ids]" value="<?php echo join(',', $column->getStatusIds()); ?>" id="filter_editagileboard_column_<?php echo $column->getID(); ?>_statuss_value_input">
        <label><?php echo __('Status(es)'); ?></label>
        <span class="value"><?php if (!$column->hasStatusIds()) echo __('None selected'); ?></span>
        <div class="interactive_menu">
            <h1><?php echo __('Select status(es)'); ?></h1>
            <div class="interactive_values_container">
                <ul class="interactive_menu_values">
                    <?php foreach ($statuses as $status): ?>
                        <li data-value="<?php echo $status->getID(); ?>" class="filtervalue<?php if ($column->hasStatusId($status->getID())) echo ' selected'; ?>">
                            <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                            <input type="checkbox" value="<?php echo $status->getID(); ?>" name="editagileboard_column_<?php echo $column->getID(); ?>_statuss_<?php echo $status->getID(); ?>" id="editagileboard_column_<?php echo $column->getID(); ?>_statuss_<?php echo $status->getID(); ?>" data-text="<?php echo __($status->getName()); ?>" id="filters_status_value_<?php echo $status->getID(); ?>" <?php if ($column->hasStatusId($status->getID())) echo 'checked'; ?>>
                            <label name="editagileboard_column_<?php echo $column->getID(); ?>_statuss_<?php echo $status->getID(); ?>"><?php echo __($status->getName()); ?></label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>