<div class="rounded_box white borderless shadowed backdrop_box large backdrop_detail_content" id="user_details_popup">
	<div class="backdrop_detail_content rounded_top" style="padding: 10px; text-align: left;">
		<div style="padding: 2px; width: 48px; height: 48px; text-align: center; background-color: #FFF; border: 1px solid #DDD; float: left;">
			<?php echo image_tag($user->getAvatarURL(false), array('alt' => ' ', 'style' => "width: 48px; height: 48px;"), true); ?>
		</div>
		<div class="user_realname">
			<?php echo $user->getRealname(); ?> <span class="user_username"><?php echo $user->getUsername(); ?></span>
			<div class="user_status"><?php echo $user->getState()->getName(); ?></div>
		</div>
		<div class="user_details">
			<?php echo '<b>' . __('This user was last seen online at %time%', array('%time%' => '</b>' . tbg_formatTime($user->getLastSeen(), 11))); ?><br>
			<?php if (count($user->getTeams())): ?>
				<b><?php echo __('Member of the following teams: %list_of_teams%', array('%list_of_teams%' => '')); ?></b><br>
				<ul class="teamlist">
					<?php foreach ($user->getTeams() as $team): ?>
						<li><?php echo image_tag('icon_team.png', array('style' => 'float: left; margin-right: 5px;')) . $team->getName(); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php echo __('User ID: ').$user->getID(); ?>
		</div>
		<?php TBGEvent::createNew('core', 'usercardactions_top', $user)->trigger(); ?>
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
		<?php TBGEvent::createNew('core', 'usercardactions_bottom', $user)->trigger(); ?>
		<?php if (TBGContext::getUser()->canSaveConfiguration(TBGSettings::CONFIGURATION_SECTION_USERS) && $user->getID() != TBGContext::getUser()->getUID()): ?>
			<div style="padding: 2px; padding-top: 10px; padding-bottom: 10px;"><a href="login_validate.inc.php?switch_user=true&amp;new_user=<?php echo $user->getUsername(); ?>"><?php echo __('Temporarily switch to this user'); ?></a></div>
			<?php if (TBGContext::getRequest()->hasCookie('b2_username_preswitch')): ?>
				<div style="padding: 2px;"><i><b><?php  echo __('Warning:'); ?></b>&nbsp;<?php __('You have already switched user once. Switching again clears the original user information, and you will have to log out and back in again to return to your original user.'); ?></i></div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="resetFadedBackdrop();"><?php echo __('Close'); ?></a>
	</div>
</div>