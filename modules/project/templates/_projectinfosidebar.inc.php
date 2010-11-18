<?php

	if ($tbg_user->canEditProjectDetails($selected_project))
	{
		$tbg_response->addJavascript('config/projects_ajax.js');
	}
	
?>
<table style="width: 100%;" cellpadding="0" cellspacing="0">
	<tr>
		<td class="project_information_sidebar" id="project_information_sidebar">
			<?php /*
			<div class="rounded_box lightgrey borderless" style="margin-top: 5px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
					<?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), image_tag('icon_rss.png', array('style' => 'float: right; margin: 5px;')), array('title' => __('Subscribe to updates'))); ?>
					<?php if (count($recent_activities) > 0): ?>
						<table cellpadding=0 cellspacing=0 class="recent_activities">
							<?php $prev_date = null; ?>
							<?php foreach ($recent_activities as $timestamp => $activities): ?>
								<?php $date = tbg_formatTime($timestamp, 5); ?>
									<?php if ($date != $prev_date): ?>
									<tr>
										<td class="latest_action_dates" colspan="2"><?php echo tbg_formatTime($timestamp, 5); ?></td>
									</tr>
								<?php endif; ?>
								<?php foreach ($activities as $activity): ?>
									<?php if ($activity['change_type'] == 'build_release'): ?>
										<tr>
											<td class="imgtd"><?php echo image_tag('icon_build.png'); ?></td>
											<td style="padding-bottom: 10px;"><span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('New version released'); ?></i></td>
										</tr>
									<?php elseif ($activity['change_type'] == 'sprint_start'): ?>
										<tr>
											<td class="imgtd"><?php echo image_tag('icon_sprint.png'); ?></td>
											<td style="padding-bottom: 10px;"><span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('A new sprint has started'); ?></i></td>
										</tr>
									<?php elseif ($activity['change_type'] == 'sprint_end'): ?>
										<tr>
											<td class="imgtd"><?php echo image_tag('icon_sprint.png'); ?></td>
											<td style="padding-bottom: 10px;"><span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('The sprint has ended'); ?></i></td>
										</tr>
									<?php elseif ($activity['change_type'] == 'milestone_release'): ?>
										<tr>
											<td class="imgtd"><?php echo image_tag('icon_milestone.png'); ?></td>
											<td style="padding-bottom: 10px;"><span class="time"><?php echo tbg_formatTime($timestamp, 19); ?></span>&nbsp;<b><?php echo $activity['info']; ?></b><br><i><?php echo __('A new milestone has been reached'); ?></i></td>
										</tr>
									<?php else: ?>
										<?php include_component('main/logitem', array('log_action' => $activity, 'include_time' => true, 'extra_padding' => true, 'include_details' => false)); ?>
									<?php endif; ?>
								<?php endforeach; ?>
								<?php $prev_date = $date; ?>
							<?php endforeach; ?>
						</table>
					<div class="timeline_link">
						<?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey())), image_tag('view_timeline.png', array('style' => 'float: right; margin-left: 5px;')) . __('Show complete timeline')); ?>
					</div>
					<?php else: ?>
						<div class="faded_out dark" style="font-size: 13px; padding-top: 3px;"><b><?php echo __('No recent activity registered for this project.'); ?></b><br><?php echo __('As soon as something important happens it will appear here.'); ?></div>
					<?php endif; ?>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div> */ ?>
			<div id="project_header_container">
				<?php echo image_tag('sidebar_collapse.png', array('id' => 'project_sidebar_collapse', 'onclick' => "\$('project_information_sidebar').addClassName('collapsed');$(this).hide();\$('project_sidebar_expand').show();")); ?>
				<?php echo image_tag('sidebar_expand.png', array('id' => 'project_sidebar_expand', 'style' => 'display: none;', 'onclick' => "\$('project_information_sidebar').removeClassName('collapsed');$(this).hide();\$('project_sidebar_collapse').show();")); ?>
				<div>
					<?php if ($tbg_user->canEditProjectDetails($selected_project)): ?><?php echo javascript_link_tag(image_tag('cfg_icon_projectheader.png'), array('onclick' => "showFadedBackdrop('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $selected_project->getID()))."');")); ?><?php endif; ?>
					<div id="project_name">
						<?php echo image_tag($selected_project->getIcon(), array('class' => 'logo'), $selected_project->hasIcon(), 'core', !$selected_project->hasIcon()); ?>
						<span id="project_name_span"><?php echo $selected_project->getName(); ?></span><br>
						<span id="project_key_span"><?php echo $selected_project->getKey(); ?></span>
					</div>
					<div>
						<span id="project_description_span">
							<?php if ($selected_project->hasDescription()): ?>
								<?php echo tbg_parse_text($selected_project->getDescription()); ?>
							<?php endif; ?>
						</span>
					</div>
					<div id="project_no_description"<?php if ($selected_project->hasDescription()): ?> style="display: none;"<?php endif; ?>>
						<?php echo __('This project has no description'); ?>
					</div>
					<?php if ($selected_project->hasDocumentationUrl()): ?>
					<div id="project_documentation_url">
						<a href="<?php echo $selected_project->getDocumentationUrl(); ?>" target="_blank"><?php echo __('View documentation'); ?></a>
					<?php endif; ?>
					<div class="sidebar_links">
						<?php include_template('project/projectinfolinks'); ?>
					</div>
				</div>
			</div>
		</td>
		<td class="project_information_main">
