<?php if (!$user instanceof TBGUser || $user->getUID() == 0): ?>
	<span class="faded_medium"><?php echo __('No such user'); ?></span>
<?php else: ?>
	<?php $avatar_dimensions = (!isset($size) || $size == 'small') ? 16 : 22; ?>
	<a href="javascript:void(0);" onclick="$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').toggle();" class="image"><?php echo image_tag($user->getAvatarURL(), array('alt' => ' ', 'style' => "width: {$avatar_dimensions}px; height: {$avatar_dimensions}px; float: left; margin-right: 5px;"), true); ?></a>
	<a href="javascript:void(0);" onclick="$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').toggle();" style="font-weight: normal;"><?php echo $user->getBuddyname(); ?></a>
	<div id="bud_<?php echo $user->getUsername() . '_' . $rnd_no; ?>" style="z-index: 100; width: 300px; /*display: none;*/ position: absolute;" class="rounded_box white shadowed user_popup">
		<div style="padding: 3px;">
			<div style="padding: 2px; width: 36px; height: 36px; text-align: center; background-color: #FFF; border: 1px solid #DDD; float: left;">
				<?php echo image_tag($user->getAvatarURL(false), array('alt' => ' ', 'style' => "width: 36px; height: 36px;"), true); ?>
			</div>
			<div class="user_realname">
				<?php echo $user->getRealname(); ?>
				<div class="user_status"><?php echo $user->getState()->getName(); ?></div>
			</div>
			<div class="user_details">
				<?php echo '<b>' . __('Last seen: %time%', array('%time%' => '</b>' . tbg_formatTime($user->getLastSeen(), 11))); ?>
			</div>
			<?php TBGEvent::createNew('core', 'useractions_top', $user)->trigger(); ?>
			<?php if (TBGUser::isThisGuest() == false): ?>
				<div id="friends_message_<?php echo $user->getUsername() . '_' . $rnd_no; ?>" style="padding: 2px 0 2px 0; font-size: 0.9em;"></div>
				<div style="padding: 2px;" id="friends_link_<?php echo $user->getUsername() . '_' . $rnd_no; ?>">
				<?php if ($user->getID() != TBGContext::getUser()->getUID() && !(TBGContext::getUser()->isFriend($user->getID())) && !$user->isGuest()): ?>
					<a href="javascript:void(0);" onclick="addFriend('<?php echo $user->getUsername(); ?>', <?php echo $rnd_no; ?>, <?php echo $user->getUID(); ?>);"><?php echo __('Become friends'); ?></a>
				<?php elseif ($user->getID() != TBGContext::getUser()->getUID() && TBGContext::getUser()->isFriend($user->getID())): ?>
					<a href="javascript:void(0);" onclick="removeFriend('<?php $user->getUsername(); ?>', <?php echo $rnd_no; ?>, <?php echo $user->getUID(); ?>);"><?php echo __('Don\'t be friends any more'); ?></a>
				<?php endif; ?>
				</div>
			<?php endif; ?>
			<div style="padding: 2px;">
				<a href="javascript:void(0);" onclick="showFadedBackdrop('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $user->getID())); ?>');$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').hide();"><?php echo __('Show user details'); ?></a>
			</div>
			<?php TBGEvent::createNew('core', 'useractions_bottom', $user)->trigger(); ?>
			<?php if (TBGContext::getUser()->canSaveConfiguration(TBGSettings::CONFIGURATION_SECTION_USERS) && $user->getID() != TBGContext::getUser()->getUID()): ?>
				<div style="padding: 2px; padding-top: 10px; padding-bottom: 10px;"><a href="login_validate.inc.php?switch_user=true&amp;new_user=<?php echo $user->getUsername(); ?>"><?php echo __('Temporarily switch to this user'); ?></a></div>
				<?php if (TBGContext::getRequest()->hasCookie('b2_username_preswitch')): ?>
					<div style="padding: 2px;"><i><b><?php  echo __('Warning:'); ?></b>&nbsp;<?php __('You have already switched user once. Switching again clears the original user information, and you will have to log out and back in again to return to your original user.'); ?></i></div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div style="text-align: right; padding: 3px; font-size: 9px;"><a href="javascript:void(0);" onclick="$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').toggle();"><?php echo __('Close this menu'); ?></a></div>
	</div>
<?php endif; ?>