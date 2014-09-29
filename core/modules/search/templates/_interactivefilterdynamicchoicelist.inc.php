<?php foreach ($items as $item): ?>
    <?php include_template('search/interactivefilteritem', compact('filter', 'item')); ?>
<?php endforeach; ?>
