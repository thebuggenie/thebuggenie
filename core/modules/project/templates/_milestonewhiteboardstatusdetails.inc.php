<?php if ($milestone instanceof TBGMilestone): ?>
    <span class="milestonename"><?php echo $milestone->getName(); ?></span>
    <div class="statusblocks">
    <?php foreach ($status_details['details'] as $status): ?>
        <div class="statusblock" style="background-color: <?php echo (isset($statuses[$status['id']])) ? $statuses[$status['id']]->getColor() : '#FFF'; ?>; width: <?php echo $status['percent']; ?>%;" title="<?php echo (isset($statuses[$status['id']])) ? $statuses[$status['id']]->getName() : __('Unknown status'); ?> - <?php echo $status['percent']; ?>%"></div>
    <?php endforeach; ?>
    </div>
    <div class="milestone_percentage">
        <div class="filler" id="milestone_<?php echo $milestone->getID(); ?>_percentage_filler" style="width: <?php echo $milestone->getPercentComplete(); ?>%;"></div>
    </div>
<?php else: ?>
    <span class="milestonename faded_out"><?php echo __('No milestones exists'); ?></span>
<?php endif; ?>
