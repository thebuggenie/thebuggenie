<div class="td" data-min-workitems="<?php echo $column->getMinWorkitems(); ?>" data-max-workitems="<?php echo $column->getMaxWorkitems(); ?>">
    <h1>
        <?php echo $column->getName(); ?>
        <?php if ($column->getBoard()->getType() != thebuggenie\modules\agile\entities\AgileBoard::TYPE_KANBAN || (!$column->getMinWorkitems() && !$column->getMaxWorkitems())): ?>
            <span class="column_count primary">-</span>
        <?php elseif ($column->getMinWorkitems() && $column->getMaxWorkitems()): ?>
            <span class="column_count workitems"><?php echo __('%count (min %min_workitems - max %max_workitems)', array('%count' => '<span class="count"></span>', '%min_workitems' => $column->getMinWorkitems(), '%max_workitems' => $column->getMaxWorkitems())); ?></span>
        <?php elseif ($column->getMinWorkitems()): ?>
            <span class="column_count workitems"><?php echo __('%count (min %min_workitems)', array('%count' => '<span class="count"></span>', '%min_workitems' => $column->getMinWorkitems())); ?></span>
        <?php elseif ($column->getMaxWorkitems()): ?>
            <span class="column_count workitems"><?php echo __('%count of max %max_workitems', array('%count' => '<span class="count"></span>', '%max_workitems' => $column->getMaxWorkitems())); ?></span>
        <?php endif; ?>
        <span class="column_count under"><?php echo __('%count (under %min_workitems)', array('%count' => '<span class="under_count"></span>', '%min_workitems' => $column->getMinWorkitems())); ?></span>
        <span class="column_count over"><?php echo __('%count (over %max_workitems)', array('%count' => '<span class="over_count"></span>', '%max_workitems' => $column->getMaxWorkitems())); ?></span>
        <?php foreach ($column->getStatusIds() as $status_id): ?>
            <?php if (isset($statuses[$status_id]) && $statuses[$status_id] instanceof \thebuggenie\core\entities\Datatype): ?>
                <div class="status_badge status-<?php echo $status_id; ?>" style="background-color: <?php echo $statuses[$status_id]->getColor(); ?>;color: <?php echo $statuses[$status_id]->getTextColor(); ?>;" title="<?php echo $statuses[$status_id]->getName(); ?>" data-status-id="<?php echo $status_id; ?>">-</div>
            <?php endif; ?>
        <?php endforeach; ?>
    </h1>
</div>