<ul>
    <?php $previous_time = null; ?>
    <?php foreach ($log_items as $item): ?>
        <?php if (!$item instanceof TBGLogItem) continue; ?>
        <?php include_component('main/issuelogitem', compact('item', 'previous_time')); ?>
        <?php $previous_time = $item->getTime(); ?>
    <?php endforeach; ?>
</ul>
