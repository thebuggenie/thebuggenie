<?php if (count($users) == 0): ?>
    <div class="faded_out"><?php echo __('There are no users in this group'); ?></div>
<?php else: ?>
    <ul class="simple_list" style="max-height: 350px; overflow-y: auto;">
        <?php foreach ($users as $user_id => $user): ?>
            <li><?php include_component('main/userdropdown', array('user' => $user)); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
