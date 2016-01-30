<?php if (!$user instanceof \thebuggenie\core\entities\User || $user->getID() == 0 || $user->isDeleted()): ?>
    <?php echo __('No such user'); ?>
<?php elseif (!$user->isScopeConfirmed()): ?>
    <?php echo $user->getUsername() ?>
<?php else: ?>
    <?php echo (isset($displayname)) ? $displayname : $user->getNameWithUsername(); ?>
<?php endif; ?>
