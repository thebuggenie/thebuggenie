<?php if (isset($milestone)): ?>
  <?php foreach ($board->getMilestoneSwimlanes($milestone) as $swimlane): ?>
    <?php include_component('agile/boardswimlane', compact('swimlane')); ?>
  <?php endforeach; ?>
<?php endif; ?>