<?php if (!$user instanceof \thebuggenie\core\entities\User || $user->getID() == 0 || $user->isDeleted()): ?>
    <span class="faded_out"><?php echo __('No such user'); ?></span>
<?php elseif (!$user->isScopeConfirmed()): ?>
    <span class="faded_out" title="<?php echo __('This user has not been confirmed yet'); ?>"><?php echo $user->getUsername() ?></span>
<?php else: ?>
    <a href="javascript:void(0);" class="dropper userlink<?php if ($tbg_user->isFriend($user)): ?> friend" title="<?php echo __('This is one of your friends'); ?><?php endif; ?>" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $user->getID())); ?>');$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').hide();">
        <?php if (!isset($userstate) || $userstate): ?><span class="userstate"><?php echo tbg_get_userstate_image($user); ?></span><?php endif; ?>
        <?php if ($show_avatar): ?>
            <?php $extraClass = (!isset($size) || $size == 'small') ? "small" : ""; ?>
            <?php echo image_tag($user->getAvatarURL(), array('alt' => ' ', 'class' => 'avatar '.$extraClass), true); ?>
        <?php endif; ?>
        <?php echo (isset($displayname)) ? $displayname : $user->getNameWithUsername(); ?>
    </a>
<?php endif; ?>
