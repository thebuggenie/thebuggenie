<?php foreach ($items as $item): ?>
    <?php include_component('search/interactivefilteritem', compact('filter', 'item')); ?>
<?php endforeach; ?>
