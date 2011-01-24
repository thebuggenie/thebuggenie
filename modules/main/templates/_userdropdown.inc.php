<?php if (!$user instanceof TBGUser || $user->getID() == 0): ?>
	<span class="faded_out"><?php echo __('No such user'); ?></span>
<?php else: ?>
	<?php $avatar_dimensions = (!isset($size) || $size == 'small') ? 16 : 22; ?>
	<?php if ($show_avatar): ?>
		<a href="javascript:void(0);" onclick="$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').toggle();" class="image"><?php echo image_tag($user->getAvatarURL(), array('alt' => ' ', 'style' => "width: {$avatar_dimensions}px; height: {$avatar_dimensions}px; float: left; margin-right: 5px;"), true); ?></a>
	<?php endif; ?>
	<a href="javascript:void(0);" onclick="$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').toggle();" style="font-weight: normal;"<?php if ($tbg_user->isFriend($user)): ?> class="friend"<?php endif; ?>><?php echo $user->getBuddyname(); ?></a>
	<div id="bud_<?php echo $user->getUsername() . '_' . $rnd_no; ?>" style="z-index: 100; width: 300px; display: none; position: absolute;" class="rounded_box white shadowed user_popup">
		<div style="padding: 3px;">
			<div style="padding: 2px; width: 36px; height: 36px; text-align: center; background-color: #FFF; border: 1px solid #DDD; float: left;">
				<?php echo image_tag($user->getAvatarURL(false), array('alt' => ' ', 'style' => "width: 36px; height: 36px;"), true); ?>
			</div>
			<div class="user_realname">
				<?php echo $user->getRealname(); ?>
				<div class="user_status"><?php echo $user->getState()->getName(); ?></div>
			</div>
			<div class="user_details">
				<?php if(!$user->getLastSeen()): ?>
					<b><?php echo __('This user has not logged in yet'); ?></b>
				<?php else: ?>
					<b><?php echo __('This user was last seen online at %time%', array('%time%' => '')); ?></b><?php echo tbg_formatTime($user->getLastSeen(), 11); ?> 
				<?php endif; ?>
			</div>
			<?php TBGEvent::createNew('core', 'useractions_top', $user)->trigger(); ?>
			<?php if (TBGUser::isThisGuest() == false && $user->getID() != $tbg_user->getID()): ?>
				<div style="padding: 2px;<?php if ($tbg_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="add_friend_<?php echo $user->getID() . '_' . $rnd_no; ?>">
					<?php echo javascript_link_tag(__('Become friends'), array('onclick' => "_updateDivWithJSONFeedback('".make_url('toggle_friend', array('mode' => 'add', 'user_id' => $user->getID()))."', null, 'toggle_friend_{$user->getID()}_{$rnd_no}_indicator', null, null, 'add_friend_{$user->getID()}_{$rnd_no}', ['add_friend_{$user->getID()}_{$rnd_no}'], ['remove_friend_{$user->getID()}_{$rnd_no}']);")); ?>
				</div>
				<?php echo image_tag('spinning_16.gif', array('id' => "toggle_friend_{$user->getID()}_{$rnd_no}_indicator", 'style' => 'display: none;')); ?>
				<div style="padding: 2px;<?php if (!$tbg_user->isFriend($user)): ?> display: none;<?php endif; ?>" id="remove_friend_<?php echo $user->getID() . '_' . $rnd_no; ?>">
					<?php echo javascript_link_tag(__('Remove this friend'), array('onclick' => "_updateDivWithJSONFeedback('".make_url('toggle_friend', array('mode' => 'remove', 'user_id' => $user->getID()))."', null, 'toggle_friend_{$user->getID()}_{$rnd_no}_indicator', null, null, 'remove_friend_{$user->getID()}_{$rnd_no}', ['remove_friend_{$user->getID()}_{$rnd_no}'], ['add_friend_{$user->getID()}_{$rnd_no}']);")); ?>
				</div>
			<?php endif; ?>
			<div style="padding: 2px;">
				<a href="javascript:void(0);" onclick="showFadedBackdrop('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $user->getID())); ?>');$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').hide();"><?php echo __('Show user details'); ?></a>
			</div>
			<?php TBGEvent::createNew('core', 'useractions_bottom', $user)->trigger(); ?>
		</div>
		<div style="text-align: right; padding: 3px; font-size: 9px;"><a href="javascript:void(0);" onclick="$('bud_<?php echo $user->getUsername() . "_" . $rnd_no; ?>').toggle();"><?php echo __('Close this menu'); ?></a></div>
	</div>
<?php endif; ?>