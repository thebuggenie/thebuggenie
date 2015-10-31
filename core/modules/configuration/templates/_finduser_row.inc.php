<td style="padding: 6px 0 0 3px;"><?php echo image_tag('icon_user.png'); ?></td>
<td style="padding: 3px;">
    <?php if ($user->isScopeConfirmed()): ?>
        <?php echo ($user->getID()); ?>
    <?php else: ?>
    -
    <?php endif; ?>
</td>
<td style="padding: 3px;">
    <span id="user_<?php echo $user->getID(); ?>_username_span"><?php include_component('main/userdropdown', array('user' => $user, 'displayname' => ($user->isOpenIdLocked()) ? '<span class="faded_out">'.$user->getEmail().'</span>' : $user->getUsername())); ?></span>
    <?php if (isset($random_password)): ?>
        <br>
        <?php echo __('New password: %random_password', array('%random_password' => "<b>{$random_password}</b>")); ?></td>
    <?php endif; ?>
</td>
<td style="padding: 3px;">
    <?php if ($user->isScopeConfirmed()): ?>
        <span id="user_<?php echo $user->getID(); ?>_realname_span"><?php echo $user->getRealname(); ?></span> <span class="faded_out">(<span id="user_<?php echo $user->getID(); ?>_nickname_span"><?php echo $user->getNickname(); ?></span>)</span>
    <?php else: ?>
        -
    <?php endif; ?>
</td>
<td style="padding: 3px;">
    <?php if ($user->isScopeConfirmed()): ?>
        <?php echo ($user->getEmail() != '') ? link_tag("mailto:{$user->getEmail()}", $user->getEmail()) : '<span class="faded_out"> - </span>'; ?>
    <?php else: ?>
    -
    <?php endif; ?>
</td>
<td style="padding: 3px;"><?php echo ($user->isActivated()) ? __e('Yes') : __e('No'); ?></td>
<td style="padding: 3px; position: relative; text-align: right;">
    <?php echo image_tag('spinning_16.gif', array('id' => "toggle_friend_{$user->getID()}_12_indicator", 'style' => 'display: none;')); ?>
    <button class="button button-silver button-icon dropper" id="user_<?php echo $user->getID(); ?>_more_actions"><?php echo image_tag('action_dropdown_small.png'); ?></button>
    <ul id="user_<?php echo $user->getID(); ?>_more_actions_dropdown" style="position: absolute; font-size: 1.1em; margin-top: 0; z-index: 1000; width: 250px;" class="simple_list rounded_box white shadowed popup_box more_actions_dropdown" onclick="$(this).previous().toggleClassName('button-pressed');$(this).toggle();"></li>
        <?php if ($user->isScopeConfirmed()): ?>
            <li><?php echo javascript_link_tag(__('Edit this user'), array('onclick' => "TBG.Config.User.getEditForm('".make_url('configure_users_edit_user_form', array('user_id' => $user->getID()))."', ".$user->getID().");")); ?></li>
        <?php else: ?>
            <li class="disabled"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('<?php echo __e('This user cannot be edited'); ?>', '<?php echo __e('The user must confirm his membership in this scope before you can perform this action'); ?>');" class="disabled"><?php echo __('Edit this user'); ?></a></li>
        <?php endif; ?>
        <li><?php echo javascript_link_tag(__('Edit permissions for this user'), array('onclick' => "TBG.Config.User.getPermissionsBlock('".make_url('configure_permissions_get_configurator', array('user_id' => $user->getID(), 'base_id' => $user->getID())). "', ".$user->getID().");", 'id' => 'permissions_'.$user->getID().'_link')); ?></li>
        <?php if (\thebuggenie\core\framework\Context::getScope()->isDefault()): ?>
            <li><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'userscopes', 'user_id' => $user->getID())); ?>');"><?php echo __('Edit available scopes for this user'); ?></a></li>
        <?php endif; ?>
        <?php if (\thebuggenie\core\entities\User::isThisGuest() == false && $user->getID() != $tbg_user->getID()): ?>
            <li style="<?php if ($tbg_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="add_friend_<?php echo $user->getID(); ?>_12">
                <?php echo javascript_link_tag(__('Become friends'), array('onclick' => "TBG.Main.Profile.addFriend('".make_url('toggle_friend', array('mode' => 'add', 'user_id' => $user->getID()))."', {$user->getID()}, 12);")); ?>
            </li>
            <li style="<?php if (!$tbg_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="remove_friend_<?php echo $user->getID(); ?>_12">
                <?php echo javascript_link_tag(__('Remove this friend'), array('onclick' => "TBG.Main.Profile.removeFriend('".make_url('toggle_friend', array('mode' => 'remove', 'user_id' => $user->getID()))."', {$user->getID()}, 12);")); ?>
            </li>
        <?php endif; ?>
        <li><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $user->getID())); ?>');$('bud_<?php echo $user->getUsername() . "_12"; ?>').hide();"><?php echo __('Show user details'); ?></a></li>
        <?php if (!in_array($user->getID(), array(1, (int) \thebuggenie\core\framework\Settings::get(\thebuggenie\core\framework\Settings::SETTING_DEFAULT_USER_ID)))): ?>
            <?php if (\thebuggenie\core\framework\Context::getScope()->isDefault()): ?>
                <li><?php echo javascript_link_tag(__('Delete this user'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__e('Permanently delete this user?')."', '".__e('Are you sure you want to remove this user? This will remove the users login data, as well as memberships in (and data in) any scopes the user is a member of.')."', {yes: {click: function() {TBG.Config.User.remove('".make_url('configure_users_delete_user', array('user_id' => $user->getID()))."', ".$user->getID()."); TBG.Main.Helpers.Dialog.dismiss(); } }, no: {click: TBG.Main.Helpers.Dialog.dismiss}});")); ?></li>
            <?php elseif ($user->isScopeConfirmed()): ?>
                <li><?php echo javascript_link_tag(__('Remove user from this scope'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__e('Remove this user?')."', '".__e('Are you sure you want to remove this user from the current scope? The users login is kept, and you can re-add the user later.')."', {yes: {click: function() {TBG.Config.User.remove('".make_url('configure_users_delete_user', array('user_id' => $user->getID()))."', ".$user->getID()."); TBG.Main.Helpers.Dialog.dismiss(); } }, no: {click: TBG.Main.Helpers.Dialog.dismiss}});")); ?></li>
            <?php else: ?>
                <li><?php echo javascript_link_tag(__('Cancel invitation'), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__e('Cancel membership in this scope?')."', '".__e('If you cancel the invitation to this scope, then this user will be notified and the unconfirmed membership removed from this scope.')."', {yes: {click: function() {TBG.Config.User.remove('".make_url('configure_users_delete_user', array('user_id' => $user->getID()))."', ".$user->getID()."); TBG.Main.Helpers.Dialog.dismiss(); } }, no: {click: TBG.Main.Helpers.Dialog.dismiss}});")); ?></li>
            <?php endif; ?>
        <?php else: ?>
            <li class="disabled"><a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('<?php echo __e('This user cannot be removed'); ?>', '<?php echo __e('This is a system user which cannot be removed'); ?>');" class="disabled"><?php echo __('Delete this user'); ?></a></li>
        <?php endif; ?>
    </ul>
</td>