<div class="header" style="margin: 2px 0 5px 0; padding: 3px 3px 7px 5px;"><?php echo __('Friends'); ?></div>
<?php if (count($friends) > 0): ?>
    <?php foreach ($friends as $friend): ?>
        <div style="padding: 5px 0 7px 0; clear: both; font-size: 0.95em;">
            <div>
                <?php echo include_component('main/userdropdown', array('user' => $friend)); ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="faded_out" style="font-size: 0.9em; padding: 5px 5px 10px 5px;"><?php echo __("You haven't marked anyone as a friend"); ?></div>
<?php endif; ?>
