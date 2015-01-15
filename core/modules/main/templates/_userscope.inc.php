<li style="padding: 5px;" class="rounded_box invisible" id="account_scope_<?php echo $scope->getID(); ?>">
    <?php if (!$scope->isDefault()): ?>
        <div class="button-group" style="float: right;">
            <button class="button button-red" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Remove access to / from this scope?'); ?>', '<?php echo __('Do you really want to remove the link to this scope?').'<br>'.__('By doing this, it will not be possible to log into this scope, and users in the scope will no longer have access to your information'); ?>', {yes: {click: function() {TBG.Main.Profile.cancelScopeMembership('<?php echo make_url('account_remove_scope', array('scope_id' => $scope->getID())); ?>', <?php echo $scope->getID(); ?>);}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo __('Cancel membership'); ?></button>
            <button class="button button-green" style="<?php if ($tbg_user->isConfirmedMemberOfScope($scope)): ?>display: none;<?php endif; ?>" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Confirm membership in this scope?'); ?>', '<?php echo __('By confirming this membership you will be able to log into this scope, but users and administrators in this scope will also have access to your information (such as email, username, real name, etc.) just like a regular account in that installation.'); ?>', {yes: {click: function() {TBG.Main.Profile.confirmScopeMembership('<?php echo make_url('account_confirm_scope', array('scope_id' => $scope->getID())); ?>', <?php echo $scope->getID(); ?>);}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo __('Confirm membership'); ?></button>
        </div>
    <?php endif; ?>
    <b><?php echo $scope->getName(); ?></b>
    <?php if ($scope->isDefault()): ?>
        <p class="faded_out">
            <?php echo __("This is the default scope membership that all users have. This only means you have a user account in the system."); ?>
        </p>
    <?php else: ?>
        <span class="faded_out">(<?php echo join(', ', $scope->getHostnames()); ?>)</span>
    <?php endif; ?>
</li>
