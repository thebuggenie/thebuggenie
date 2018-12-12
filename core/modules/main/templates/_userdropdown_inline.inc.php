<?php if (!$user instanceof \thebuggenie\core\entities\User || $user->getID() == 0 || $user->isDeleted()): ?>
    <span class="faded_out"><?php echo __('No such user'); ?></span>
<?php elseif (!$user->isScopeConfirmed()): ?>
    <span class="faded_out" title="<?php echo __('This user has not been confirmed yet'); ?>"><?php echo $user->getUsername() ?></span>
<?php else: ?>
    <a href="javascript:void(0);" class="userlink inline<?php if ($tbg_user->isFriend($user)): ?> friend" title="<?php echo __('This is one of your friends'); ?><?php endif; ?>" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $user->getID())); ?>');">
        <?php if ($show_avatar): ?>
            <?php $extraClass = (!isset($size) || $size == 'small') ? "small" : ""; ?>
            <?php echo image_tag($user->getAvatarURL(), array('alt' => ' ', 'class' => 'avatar '.$extraClass, 'style' => (isset($in_email) && $in_email) ? 'vertical-align: text-bottom' : ''), true); ?>
        <?php endif; ?>
        <?php echo (isset($displayname)) ? $displayname : '@' . $user->getUsername(); ?>
    </a>
<?php endif; ?>
