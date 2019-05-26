<div class="backdrop_box medium" id="client_users">
    <div class="backdrop_detail_header">
        <span><?= __('Editing scopes for user %username', ['%username' => $user->getUsername()]); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <form action="<?= make_url('configure_users_update_user_scopes', ['user_id' => $user->getID()]); ?>" method="post" onsubmit="TBG.Config.User.updateScopes('<?= make_url('configure_users_update_user_scopes', ['user_id' => $user->getID()]); ?>', '<?= $user->getID(); ?>');return false;" id="edit_user_<?= $user->getID(); ?>_scopes_form">
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <?= __('The user can access the following scopes'); ?>
            <ul class="simple_list user_scope_list">
                <?php foreach ($scopes as $scope): ?>
                    <li><input type="checkbox" style="float: left; margin-right: 3px;" name="scopes[<?= $scope->getID(); ?>]"<?php if ($user->isMemberOfScope($scope)): ?> checked<?php endif; ?><?php if ($scope->isDefault()): ?> disabled<?php endif; ?> id="user_<?= $user->getID(); ?>_scopes_<?= $scope->getID(); ?>"><label for="user_<?= $user->getID(); ?>_scopes_<?= $scope->getID(); ?>"><?= $scope->getName(); ?>&nbsp;<span class="faded_out" style="font-weight: normal;"><?= join(', ', $scope->getHostnames()); ?></span></label></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="backdrop_details_submit">
            <span class="explanation"></span>
            <div class="submit_container">
                <button type="submit" class="button button-silver"><?= image_tag('spinning_16.gif', ['style' => 'display: none;', 'id' => 'edit_user_'.$user->getID().'_scopes_form_indicator']) . __('Save'); ?>
            </div>
        </div>
    </form>
</div>
