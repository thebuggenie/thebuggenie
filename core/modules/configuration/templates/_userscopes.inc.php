<div class="backdrop_box medium" id="client_users">
    <div class="backdrop_detail_header"><?php echo __('Scopes available to this user'); ?></div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <h5 style="text-align: left;">
            <?php echo __('The user can access the following scopes'); ?>
            <div class="faded_out" style="font-size: 0.9em; font-weight: normal;"><?php echo __('Editing scopes for user %username', array('%username' => '<b>'.$user->getUsername().'</b>')); ?></div>
        </h5>
        <form action="<?php echo make_url('configure_users_update_user_scopes', array('user_id' => $user->getID())); ?>" method="post" onsubmit="TBG.Config.User.updateScopes('<?php echo make_url('configure_users_update_user_scopes', array('user_id' => $user->getID())); ?>', '<?php echo $user->getID(); ?>');return false;" id="edit_user_<?php echo $user->getID(); ?>_scopes_form">
            <?php foreach ($scopes as $scope): ?>
                <input type="checkbox" style="float: left; margin-right: 3px;" name="scopes[<?php echo $scope->getID(); ?>]"<?php if ($user->isMemberOfScope($scope)): ?> checked<?php endif; ?><?php if ($scope->isDefault()): ?> disabled<?php endif; ?> id="user_<?php echo $user->getID(); ?>_scopes_<?php echo $scope->getID(); ?>"><label for="user_<?php echo $user->getID(); ?>_scopes_<?php echo $scope->getID(); ?>"><?php echo $scope->getName(); ?>&nbsp;<span class="faded_out" style="font-weight: normal;"><?php echo join(', ', $scope->getHostnames()); ?></span></label><br>
            <?php endforeach; ?>
            <?php echo image_tag('spinning_16.gif', array('style' => 'float: right; margin-left: 5px; display: none;', 'id' => 'edit_user_'.$user->getID().'_scopes_form_indicator')); ?>
            <input type="submit" value="<?php echo __('Save'); ?>" style="float: right;">
        </form>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close'); ?></a>
    </div>
</div>
