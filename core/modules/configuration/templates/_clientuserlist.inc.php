<ul class="client_users simple_list collection_user_list">
    <?php foreach ($users as $user_id => $user): ?>
        <?php include_component('configuration/clientuserlistitem', compact('client', 'user_id', 'user')); ?>
    <?php endforeach; ?>
</ul>
<div class="faded_out" id="client_members_<?php echo $client->getID(); ?>_no_users" style="<?php if (count($users) > 0) echo 'display: none;'; ?>"><?php echo __('There are no users in this client'); ?></div>
