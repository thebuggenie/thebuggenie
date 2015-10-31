<?php if (!$user instanceof \thebuggenie\core\entities\User || $user->getID() == 0 || $user->isDeleted()): ?>
    <span class="faded_out"><?php echo __('No such user'); ?></span>
<?php elseif (!$user->isScopeConfirmed()): ?>
    <span class="faded_out" title="<?php echo __('This user has not been confirmed yet'); ?>"><?php echo $user->getUsername() ?></span>
<?php else: ?>
<div class="userdropdown">
    <a href="javascript:void(0);" class="dropper userlink<?php if ($tbg_user->isFriend($user)): ?> friend" title="<?php echo __('This is one of your friends'); ?><?php endif; ?>">
        <?php if (!isset($userstate) || $userstate): ?><span class="userstate"><?php echo tbg_get_userstate_image($user); ?></span><?php endif; ?>
        <?php if ($show_avatar): ?>
            <?php $extraClass = (!isset($size) || $size == 'small') ? "small" : ""; ?>
            <?php echo image_tag($user->getAvatarURL(), array('alt' => ' ', 'class' => 'avatar '.$extraClass), true); ?>
        <?php endif; ?>
        <?php echo (isset($displayname)) ? $displayname : $user->getNameWithUsername(); ?>
    </a>
    <ul class="rounded_box white shadowed user_popup popup_box dropdown_box <?php if (isset($class)) echo $class; ?> leftie more_actions_dropdown">
        <li class="header">
            <div class="user_avatar">
                <?php echo image_tag($user->getAvatarURL(false), array('alt' => ' ', 'style' => "width: 36px; height: 36px;"), true); ?>
            </div>
            <div class="user_details">
                <?php echo $user->getRealname(); ?><br>
                <?php if(!$user->getLastSeen()): ?>
                    <b><?php echo __('This user has not logged in yet'); ?></b>
                <?php else: ?>
                    <b><?php echo __('Last seen online at %time', array('%time' => '')); ?></b><?php echo tbg_formatTime($user->getLastSeen(), 11); ?>
                <?php endif; ?>
            </div>
        </li>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'useractions_top', $user)->trigger(); ?>
        <?php if (\thebuggenie\core\entities\User::isThisGuest() == false && $user->getID() != $tbg_user->getID()): ?>
            <li style="<?php if ($tbg_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="add_friend_<?php echo $user->getID() . '_' . $rnd_no; ?>">
                <?php echo javascript_link_tag(__('Become friends'), array('onclick' => "TBG.Main.Profile.addFriend('".make_url('toggle_friend', array('mode' => 'add', 'user_id' => $user->getID()))."', {$user->getID()}, {$rnd_no});")); ?>
            </li>
            <?php echo image_tag('spinning_16.gif', array('id' => "toggle_friend_{$user->getID()}_{$rnd_no}_indicator", 'style' => 'display: none;')); ?>
            <li style="<?php if (!$tbg_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="remove_friend_<?php echo $user->getID() . '_' . $rnd_no; ?>">
                <?php echo javascript_link_tag(__('Remove this friend'), array('onclick' => "TBG.Main.Profile.removeFriend('".make_url('toggle_friend', array('mode' => 'remove', 'user_id' => $user->getID()))."', {$user->getID()}, {$rnd_no});")); ?>
            </li>
        <?php endif; ?>
        <?php if ($tbg_user->canAccessConfigurationPage(\thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_USERS)): ?>
            <?php if ($tbg_routing->getCurrentRouteName() != 'configure_users_find_user'): ?>
                <li>
                    <a href="<?php echo make_url('configure_users'); ?>?finduser=<?php echo $user->getUsername(); ?>"><?php echo __('Edit this user'); ?></a>
                </li>
            <?php endif; ?>
            <?php if (!$tbg_request->hasCookie('tbg3_original_username')): ?>
                <li><?php echo link_tag(make_url('switch_to_user', array('user_id' => $user->getID())), __('Switch to this user')); ?></li>
            <?php else: ?>
                <li><?php echo link_tag(make_url('switch_back_user'), __('Switch back to original user')); ?></li>
            <?php endif; ?>
        <?php endif; ?>
        <li>
            <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $user->getID())); ?>');$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').hide();"><?php echo __('Show user details'); ?></a>
        </li>
        <?php \thebuggenie\core\framework\Event::createNew('core', 'useractions_bottom', $user)->trigger(); ?>
    </ul>
</div>
<?php endif; ?>
