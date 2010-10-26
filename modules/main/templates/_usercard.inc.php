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
			<?php if(!$user->getLastSeen()): ?>
				<?php echo '<b>' . __('This user was never connected') . '</b>'; ?>
			<?php else: ?>
				<?php echo '<b>' . __('This user was last seen online at %time%', array('%time%' => '</b>' . tbg_formatTime($user->getLastSeen(), 11))); ?> 
			<?php endif; ?>
			<br>
			<?php if(!$user->getLatestActions(1)): ?>
				<?php echo '<b>' . __('This user has no recent activity') . '</b>'; ?>	
			<?php else: ?>
				<?php foreach ($user->getLatestActions(1) as $action): ?>
					<?php echo '<b>' . __('Last user activy was at %time%', array('%time%' => '</b>' . tbg_formatTime($action['timestamp'], 11))); ?>
				<?php endforeach; ?> 
			<?php endif; ?>	
			<br>	
			<?php if (count($user->getIssues(1))): ?>	
				<?php echo '<b>' . __('This user has reported</b> %issues% issue%s%', array('%issues%' => count($user->getIssues()), '%s%' => count($user->getIssues()) > 1 ? 's' : '' )); ?>
				<br>
				<b><?php echo __('Last reported issue: '); ?></b>
					<?php foreach ($user->getIssues(1) as $issue): ?>
						<span class="faded_out smaller"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $issue->get(TBGProjectsTable::KEY))), '['.$issue->get(TBGProjectsTable::KEY).']'); ?></span>
						<?php echo link_tag(make_url('viewissue', array('project_key' =>$issue->get(TBGProjectsTable::KEY), 'issue_no' => $issue->get(TBGIssuesTable::ID))), $issue->get(TBGIssueTypesTable::NAME) . ' #' . $issue->get(TBGIssuesTable::ISSUE_NO) . ' - '. $issue->get(TBGIssuesTable::TITLE)); ?>
					<?php endforeach; ?>
			<?php else: ?>
				<?php echo '<b>' . __('This user has reported no issue') . '</b>'; ?>
			<?php endif; ?>			
			<br>
			<?php if (count($user->getTeams())): ?>
				<b><?php echo __('Member of the following teams:</b> %list_of_teams%', array('%list_of_teams%' => '')); ?></b><br>
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
			<?php if ($user->getID() != TBGContext::getUser()->getUID() && !(TBGContext::getUser()->isFriend($user)) && !$user->isGuest()): ?>
				<a href="javascript:void(0);" onclick="addFriend('<?php echo $user->getUsername(); ?>', <?php echo $rnd_no; ?>, <?php echo $user->getUID(); ?>);"><?php echo __('Become friends'); ?></a>
			<?php elseif ($user->getID() != TBGContext::getUser()->getUID() && TBGContext::getUser()->isFriend($user)): ?>
				<a href="javascript:void(0);" onclick="removeFriend('<?php $user->getUsername(); ?>', <?php echo $rnd_no; ?>, <?php echo $user->getUID(); ?>);"><?php echo __('Don\'t be friends any more'); ?></a>
			<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php TBGEvent::createNew('core', 'usercardactions_bottom', $user)->trigger(); ?>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="resetFadedBackdrop();"><?php echo __('Close'); ?></a>
	</div>
</div>