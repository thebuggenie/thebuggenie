<?php if (!$team instanceof TBGTeam || $team->getID() == 0): ?>
	<span class="faded_out"><?php echo __('No such team'); ?></span>
<?php else: ?>
	<a href="javascript:void(0);" onclick="$('team_<?php echo $team->getID() . "_" . $rnd_no; ?>').toggle();" class="image"><?php echo image_tag('icon_team.png', array('style' => "width: 16px; height: 16px; float: left; margin-right: 5px;")); ?></a>
	<a href="javascript:void(0);" onclick="$('team_<?php echo $team->getID() . "_" . $rnd_no; ?>').toggle();" style="font-weight: normal;"<?php if ($tbg_user->isMemberOfTeam($team)): ?> class="friend" title="<?php echo __('You are a member of this team'); ?>"<?php endif; ?>><?php echo $team->getName(); ?></a>
	<div id="team_<?php echo $team->getID() . '_' . $rnd_no; ?>" style="z-index: 100; width: 300px; display: none; position: absolute;" class="rounded_box white shadowed user_popup dropdown_box">
		<div style="padding: 3px;">
			<div style="padding: 2px; width: 36px; height: 36px; text-align: center; background-color: #FFF; border: 1px solid #DDD; float: left;">
				<?php echo image_tag('team_large.png', array('alt' => ' ', 'style' => "width: 36px; height: 36px;")); ?>
			</div>
			<div class="user_realname">
				<?php echo $team->getName(); ?>
				<?php if ($tbg_user->isMemberOfTeam($team)): ?>
					<div class="user_status"><?php echo ($team->isOndemand()) ? __('You are working together') : __('You are on this team'); ?></div>
				<?php endif; ?>
			</div>
			<div style="clear: both;">
				<?php if (!$team->isOndemand()): ?>
					<?php echo link_tag(make_url('team_dashboard', array('team_id' => $team->getID())), __('Show team dashboard')); ?><br>
				<?php endif; ?>
				<?php TBGEvent::createNew('core', 'teamactions_bottom', $team)->trigger(); ?>
			</div>
		</div>
		<div style="text-align: right; padding: 3px; font-size: 9px;"><a href="javascript:void(0);" onclick="$('team_<?php echo $team->getID() . "_" . $rnd_no; ?>').toggle();"><?php echo __('Close this menu'); ?></a></div>
	</div>
<?php endif; ?>