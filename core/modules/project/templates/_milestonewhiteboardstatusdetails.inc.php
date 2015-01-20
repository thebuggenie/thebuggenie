<?php if ($milestone instanceof \thebuggenie\core\entities\Milestone): ?>
    <span class="milestonename"><?php echo $milestone->getName(); ?></span>
    <div class="statusblocks">
    <?php foreach ($status_details['details'] as $status): ?>
        <div class="statusblock" style="background-color: <?php echo (isset($statuses[$status['id']])) ? $statuses[$status['id']]->getColor() : '#FFF'; ?>; width: <?php echo $status['percent']; ?>%;" title="<?php echo __('%status_name - %percentage (%count of %total)', array('%status_name' => (isset($statuses[$status['id']])) ? $statuses[$status['id']]->getName() : __('Unknown status'), '%percentage' => $status['percent'].'%', '%count' => $status['count'], '%total' => $status_details['total'])); ?>"></div>
    <?php endforeach; ?>
    </div>
    <div class="milestone_percentage" title="<?php echo __('%percentage % completed', array('%percentage' => $milestone->getPercentComplete())); ?>">
        <div class="filler" id="milestone_<?php echo $milestone->getID(); ?>_percentage_filler" style="width: <?php echo $milestone->getPercentComplete(); ?>%;" title="<?php echo __('%percentage completed', array('%percentage' => $milestone->getPercentComplete().'%')); ?>"></div>
    </div>
<?php else: ?>
    <span class="milestonename faded_out"><?php echo __('No milestones exists'); ?></span>
<?php endif; ?>
