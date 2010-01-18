<?php if (!$user instanceof TBGUser || $user->getUID() == 0): ?>
	<tr><td style="padding: 5px; color: #BBB;" colspan=2><?php echo __('No such user'); ?></td></tr>
<?php else: ?>
<?php if (isset($size) && $size == 'small'): ?>
	<tr>
		<td class="imgtd_bud" id="icon_<?php echo $user->getUname() . "_" . $rnd_no; ?>">
			<a href="javascript:void(0);" onclick="showBud('<?php echo $user->getUname() . "_" . $rnd_no; ?>');" class="image"><?php echo image_tag('icon_user.png', array()); ?></a>
		</td>
		<td style="padding: 0px; padding-left: 2px;" valign="middle">
			<a href="javascript:void(0);" onclick="showBud('<?php echo $user->getUname() . "_" . $rnd_no; ?>');" style="font-weight: bold;"><?php echo $user->getBuddyname(); ?></a>&nbsp;&nbsp;<i><span class="faded_medium">(<?php echo $user->getState()->getName(); ?>)</span></i>
		</td>
	</tr>
<?php else: ?>
	<tr>
		<td class="imgtd_bud" id="icon_<?php echo $user->getUname() . "_" . $rnd_no; ?>">
			<a href="javascript:void(0);" onclick="showBud('<?php echo $user->getUname() . "_" . $rnd_no; ?>');" class="image"><?php echo image_tag($user->getAvatarURL(), array(), true); ?></a>
		</td>
		<td style="padding: 0px; padding-left: 2px;" valign="middle">
			<a href="javascript:void(0);" onclick="showBud('<?php echo $user->getUname() . "_" . $rnd_no; ?>');" style="font-weight: bold;"><?php echo $user->getBuddyname(); ?></a><br>
			<span class="faded_medium"><?php echo $user->getState()->getName(); ?></span>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<td colspan="2" class="nopadding">
			<div id="bud_<?php echo $user->getUname() . '_' . $rnd_no; ?>" style="width: 225px; display: none; position: absolute;" class="bud_actions">
				<div style="padding: 3px; margin-bottom: 2px;">
					<table cellpadding=0 cellspacing=0 style="width: 100%;">
						<tr>
							<td style="padding: 2px; width: 30px; text-align: center;">
								<div style="padding: 2px; background-color: #FFF; border: 1px solid #DDD;">
									<?php echo image_tag($user->getAvatarURL(), array(), true); ?>
								</div>
							</td>
							<td style="padding: 3px; font-size: 12px;">
								<b><?php echo $user->getRealname(); ?></b>
							</td>
						</tr>
					</table>
					<div style="padding: 2px;">
						<?php echo '<b>' . __('Currently: %user_state%', array('%user_state%' => '</b> ' . $user->getState()->getName())); ?><br>
						<?php echo '<b>' . __('Last seen: %time%', array('%time%' => '</b>' . tbg_formatTime($user->getLastSeen(), 11))); ?>
					</div>
				</div>
				<?php
	
				TBGContext::trigger('core', 'useractions_top', array("user" => $user, "closemenustring" => $closemenu_string));
				
				?>
				<?php if (TBGUser::isThisGuest() == false): ?>
					<div id="friends_message_<?php echo $user->getUname() . '_' . $rnd_no; ?>" style="padding: 2px; font-size: 9px;"></div>
					<div style="padding: 2px;" id="friends_link_<?php echo $user->getUname() . '_' . $rnd_no; ?>">
					<?php if ($user->getID() != TBGContext::getUser()->getUID() && !(TBGContext::getUser()->isFriend($user->getID())) && !$user->isGuest()): ?>
						<a href="javascript:void(0);" onclick="addFriend('<?php echo $user->getUname(); ?>', <?php echo $rnd_no; ?>, <?php echo $user->getUID(); ?>);"><?php echo __('Become friends'); ?></a>
					<?php elseif ($user->getID() != TBGContext::getUser()->getUID() && TBGContext::getUser()->isFriend($user->getID())): ?>
						<a href="javascript:void(0);" onclick="removeFriend('<?php $user->getUname(); ?>', <?php echo $rnd_no; ?>, <?php echo $user->getUID(); ?>);"><?php echo __('Don\'t be friends any more'); ?></a>
					<?php endif; ?>
					</div>
				<?php endif; ?>
				<div style="padding: 2px;"><a href="javascript:void(0);" onclick="<?php echo $viewuser_string . $closemenu_string; ?>"><?php echo __('View details'); ?></a></div>
				<?php 
					
				TBGContext::trigger('core', 'useractions_bottom', array("user" => $user, "closemenustring" => $closemenu_string));
				
				?>
				<?php if (((TBGContext::getUser()->hasPermission("b2saveconfig", 14, "core") && $user->getScope()->getID() != TBGContext::getScope()->getID()) || TBGContext::getUser()->hasPermission("b2saveconfig", 2, "core")) && $user->getID() != TBGContext::getUser()->getUID()): ?>
					<div style="padding: 2px; padding-top: 10px; padding-bottom: 10px;"><a href="login_validate.inc.php?switch_user=true&amp;new_user=<?php echo $user->getUname(); ?>"><?php echo __('Temporarily switch to this user'); ?></a></div>
					<?php if (TBGContext::getRequest()->hasCookie('b2_username_preswitch')): ?>
						<div style="padding: 2px;"><i><b><?php  echo __('Warning:'); ?></b>&nbsp;<?php __('You have already switched user once. Switching again clears the original user information, and you will have to log out and back in again to return to your original user.'); ?></i></div>
					<?php endif; ?>
				<?php endif; ?>
				<div style="text-align: right; padding: 3px; font-size: 9px;"><a href="javascript:void(0);" onclick="<?php echo $closemenu_string; ?>"><?php echo __('Close this menu'); ?></a></div>
			</div>
		</td>
	</tr>
<?php endif; ?>