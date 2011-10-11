<?php

	$tbg_response->addBreadcrumb(__('Timeline'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" project timeline', array('%project_name%' => $selected_project->getName())));
	$tbg_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div class="rounded_box lightgrey feature timeline_actions" style="width: 330px; float: right; margin: 10px;">
				<div class="header_div"><?php echo __('Timeline actions'); ?></div>
				<div class="content">
					<?php if ($important): ?>
						<?php echo link_tag(make_url('project_timeline_important', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), image_tag('icon_rss.png', array('style' => 'float: left; margin-right: 5px;')) . __('Subscribe to updates via RSS')); ?>
						<br><?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey())), image_tag('icon_timeline.png', array('style' => 'float: left; margin-right: 5px;')) . __('Show all items')); ?>
					<?php else: ?>
						<?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), image_tag('icon_rss.png', array('style' => 'float: left; margin-right: 5px;')) . __('Subscribe to updates via RSS')); ?>
						<br><?php echo link_tag(make_url('project_timeline_important', array('project_key' => $selected_project->getKey())), image_tag('icon_important.png', array('style' => 'float: left; margin-right: 5px;')) . __('Only show important items')); ?>
					<?php endif; ?>
				</div>
			</div>
			<div id="timeline">
				<?php if (count($recent_activities) > 0): ?>
					<?php include_component('project/timeline', array('activities' => $recent_activities)); ?>
				<?php else: ?>
					<div class="faded_out dark" style="font-size: 13px; padding-top: 3px;"><b><?php echo __('No recent activity registered for this project.'); ?></b><br><?php echo __('As soon as something important happens it will appear here.'); ?></div>
				<?php endif; ?>
			</div>
			<input id="timeline_offset" value="40" type="hidden">
			<?php if (count($recent_activities) > 0): ?>
				<?php echo image_tag('spinning_16.gif', array('id' => 'timeline_indicator', 'style' => 'display: none; float: left; margin-right: 5px;')); ?>
				<?php echo javascript_link_tag(__('Show more').image_tag('action_add_small.png', array('style' => 'float: left; margin-right: 5px;')), array('onclick' => "TBG.Project.Timeline.update('".make_url('project_timeline', array('project_key' => $selected_project->getKey()))."');", 'id' => 'timeline_more_link')); ?>
			<?php endif; ?>
		</td>
	</tr>
</table>
