<?php if (!$user instanceof TBGUser || $user->getID() == 0 || $user->isDeleted()): ?>
	<span class="faded_out"><?php echo __('No such user'); ?></span>
<?php elseif (!$user->isScopeConfirmed()): ?>
	<span class="faded_out" title="<?php echo __('This user has not been confirmed yet'); ?>"><?php echo $user->getUsername() ?></span>
<?php else: ?>
<div class="userdropdown">
	<a href="javascript:void(0);" class="dropper userlink<?php if ($tbg_user->isFriend($user)): ?> friend" title="<?php echo __('This is one of your friends'); ?><?php endif; ?>">
		<?php echo tbg_get_userstate_image($user); ?>
		<?php if ($show_avatar): ?>
			<?php $extraClass = (!isset($size) || $size == 'small') ? "small" : ""; ?>
			<?php echo image_tag($user->getAvatarURL(), array('alt' => ' ', 'class' => 'avatar '.$extraClass), true); ?>
		<?php endif; ?>
		<?php echo (isset($displayname)) ? $displayname : $user->getNameWithUsername(); ?>
	</a>
	<div class="rounded_box white shadowed user_popup popup_box dropdown_box <?php if (isset($class)) echo $class; ?> leftie">
		<div style="padding: 3px;">
			<div style="padding: 2px; width: 36px; height: 36px; text-align: center; background-color: #FFF; border: 1px solid #DDD; float: left;">
				<?php echo image_tag($user->getAvatarURL(false), array('alt' => ' ', 'style' => "width: 36px; height: 36px;"), true); ?>
			</div>
			<div class="user_realname">
				<?php echo $user->getRealname(); ?>
			</div>
			<div class="user_details">
				<?php if(!$user->getLastSeen()): ?>
					<b><?php echo __('This user has not logged in yet'); ?></b>
				<?php else: ?>
					<b><?php echo __('This user was last seen online at %time', array('%time' => '')); ?></b><?php echo tbg_formatTime($user->getLastSeen(), 11); ?> 
				<?php endif; ?>
			</div>
			<?php TBGEvent::createNew('core', 'useractions_top', $user)->trigger(); ?>
			<?php if (TBGUser::isThisGuest() == false && $user->getID() != $tbg_user->getID()): ?>
				<div style="padding: 2px;<?php if ($tbg_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="add_friend_<?php echo $user->getID() . '_' . $rnd_no; ?>">
					<?php echo javascript_link_tag(__('Become friends'), array('onclick' => "TBG.Main.Profile.addFriend('".make_url('toggle_friend', array('mode' => 'add', 'user_id' => $user->getID()))."', {$user->getID()}, {$rnd_no});")); ?>
				</div>
				<?php echo image_tag('spinning_16.gif', array('id' => "toggle_friend_{$user->getID()}_{$rnd_no}_indicator", 'style' => 'display: none;')); ?>
				<div style="padding: 2px;<?php if (!$tbg_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="remove_friend_<?php echo $user->getID() . '_' . $rnd_no; ?>">
					<?php echo javascript_link_tag(__('Remove this friend'), array('onclick' => "TBG.Main.Profile.removeFriend('".make_url('toggle_friend', array('mode' => 'remove', 'user_id' => $user->getID()))."', {$user->getID()}, {$rnd_no});")); ?>
				</div>
			<?php endif; ?>
			<?php if ($tbg_user->canAccessConfigurationPage(TBGSettings::CONFIGURATION_SECTION_USERS)): ?>
				<div style="padding: 2px;">
					<a href="<?php echo make_url('configure_users'); ?>?finduser=<?php echo $user->getUsername(); ?>"><?php echo __('Edit this user'); ?></a>
				</div>
				<?php if (!$tbg_request->hasCookie('tbg3_original_username')): ?>
					<div style="padding: 2px;"><?php echo link_tag(make_url('switch_to_user', array('user_id' => $user->getID())), __('Switch to this user')); ?></div>
				<?php else: ?>
					<div style="padding: 2px;"><?php echo link_tag(make_url('switch_back_user'), __('Switch back to original user')); ?></div>
				<?php endif; ?>
			<?php endif; ?>
			<div style="padding: 2px;">
				<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $user->getID())); ?>');$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').hide();"><?php echo __('Show user details'); ?></a>
			</div>
			<?php TBGEvent::createNew('core', 'useractions_bottom', $user)->trigger(); ?>
		</div>
		<div style="text-align: right; padding: 3px; font-size: 9px;"><a href="javascript:void(0);" onclick="$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').toggle();"><?php echo __('Close this menu'); ?></a></div>
	</div>
</div>
<?php endif; ?>
