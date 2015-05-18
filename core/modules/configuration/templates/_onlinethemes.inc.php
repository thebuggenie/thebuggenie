<?php if (!count($themes)): ?>
    <div class="faded_out"><?php echo __('No themes found'); ?></div>
<?php else: ?>
    <ul class="featured themes-list plugins-list">
        <?php foreach ($themes as $onlinetheme): ?>
            <?php include_component('configuration/onlinetheme', compact('onlinetheme')); ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
