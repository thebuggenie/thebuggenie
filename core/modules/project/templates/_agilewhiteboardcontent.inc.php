<?php foreach ($board->getMilestoneSwimlanes($milestone) as $swimlane): ?>
    <?php include_component('project/boardswimlane', compact('swimlane')); ?>
<?php endforeach; ?>
