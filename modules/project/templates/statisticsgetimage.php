<?php if ($graphmode == 'piechart'): ?>
<?php include_component('pchart/pieChart', array('width' => $width, 'height' => $height, 'style' => '3d', 'labels' => $labels, 'values' => $values, 'title' => $title)); ?>
<?php endif; ?>