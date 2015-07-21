<?php

    use thebuggenie\modules\agile\entities\BoardColumn,
        thebuggenie\modules\agile\entities\AgileBoard;

    if (! isset($column_id)) $column_id = $column->getColumnOrRandomID();

?>
<td>
    <div id="editagileboard_column_<?php echo $column_id; ?>" class="edit-column">
        <div class="draggable"><?php echo image_tag('icon_arrows_move.png'); ?></div>
        <a class="remover" href="javascript:void(0);" onclick="$(this).up('td').remove();"><?php echo image_tag('icon-mono-remove.png'); ?></a>
        <input type="hidden" name="columns[<?php echo $column_id; ?>][column_id]" value="<?php echo ($column->getID()) ? $column->getID() : ''; ?>">
        <input type="hidden" class="sortorder" name="columns[<?php echo $column_id; ?>][sort_order]" value="<?php echo $column->getSortOrder(); ?>">
        <label for="boardcolumn_<?php echo $column_id; ?>_name_input"><?php echo __('Column name'); ?></label>
        <input type="text" class="column-name" name="columns[<?php echo $column_id; ?>][name]" id="boardcolumn_<?php echo $column_id; ?>_name_input" value="<?php echo tbg_template_escape($column->getName()); ?>" placeholder="<?php echo __('Column status (ex: New, Done)'); ?>">
        <?php if ($column->getBoard()->getType() == AgileBoard::TYPE_KANBAN): ?>
            <label for="boardcolumn_<?php echo $column_id; ?>_min_workitems_input" class="workload-label"><?php echo __('Min workload'); ?></label>
            <label for="boardcolumn_<?php echo $column_id; ?>_max_workitems_input" class="workload-label"><?php echo __('Max workload'); ?></label>
            <input type="text" class="column-workload" name="columns[<?php echo $column_id; ?>][min_workitems]" id="boardcolumn_<?php echo $column_id; ?>_min_workitems_input" value="<?php echo $column->getMinWorkitems(); ?>" placeholder="0">
            <input type="text" class="column-workload" name="columns[<?php echo $column_id; ?>][max_workitems]" id="boardcolumn_<?php echo $column_id; ?>_max_workitems_input" value="<?php echo $column->getMaxWorkitems(); ?>" placeholder="0">
        <?php endif; ?>
        <p>
            <?php echo __('Select which status values are valid for this column'); ?>
        </p>
        <div class="fancyfilter filter interactive_dropdown" id="boardcolumn_<?php echo $column_id; ?>_status" data-filterkey="editagileboard_column_<?php echo $column_id; ?>_status" data-value="<?php echo join(',', $column->getStatusIds()); ?>" data-no-selection-value="<?php echo __('None selected'); ?>" data-exclusivity-group="board-column-status">
            <input type="hidden" name="columns[<?php echo $column_id; ?>][status_ids]" value="<?php echo join(',', $column->getStatusIds()); ?>" id="filter_editagileboard_column_<?php echo $column_id; ?>_status_value_input">
            <label><?php echo __('Status(es)'); ?></label>
            <span class="value"><?php if (!$column->hasStatusIds()) echo __('None selected'); ?></span>
            <div class="interactive_menu">
                <h1><?php echo __('Select status(es)'); ?></h1>
                <div class="interactive_values_container">
                    <ul class="interactive_menu_values">
                        <?php foreach ($statuses as $status): ?>
                            <li data-value="<?php echo $status->getID(); ?>" class="filtervalue<?php if ($column->hasStatusId($status->getID())) echo ' selected'; ?><?php if ($column->isStatusIdTaken($status->getID())) echo ' disabled'; ?>">
                                <?php echo image_tag('icon-mono-checked.png', array('class' => 'checked')); ?>
                                <input type="checkbox" value="<?php echo $status->getID(); ?>" name="editagileboard_column_<?php echo $column_id; ?>_statuss_<?php echo $status->getID(); ?>" id="editagileboard_column_<?php echo $column_id; ?>_statuss_<?php echo $status->getID(); ?>" data-text="<?php echo __($status->getName()); ?>" id="filters_status_value_<?php echo $status->getID(); ?>" <?php if ($column->hasStatusId($status->getID())) echo 'checked'; ?>>
                                <label name="editagileboard_column_<?php echo $column_id; ?>_status_<?php echo $status->getID(); ?>"><?php echo __($status->getName()); ?></label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</td>