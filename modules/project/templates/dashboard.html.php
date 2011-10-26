<?php

	$tbg_response->addBreadcrumb(__('Dashboard'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" project dashboard', array('%project_name%' => $selected_project->getName())));
	$tbg_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<?php TBGEvent::createNew('core', 'project_dashboard_top')->trigger(); ?>
			<?php if (!count($views) && TBGContext::getUser()->canEditProjectDetails($selected_project)) :?>
				<p class="content faded_out"><?php echo __("This dashboard doesn't contain any views. To add views in this dashboard, press 'Customize' on the left."); ?></p>
			<?php elseif (!count($views)): ?>
				<p class="content faded_out"><?php echo __("This dashboard doesn't contain any views."); ?></p>
			<?php else: ?>
				<ul id="dashboard">
					<?php foreach($views as $_id => $view): ?>
						<li style="clear: none;" id="dashboard_container_<?php echo $_id; ?>">
							<?php include_component('main/dashboardview', array('view' => $view, 'show' => false)); ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<script type="text/javascript">
					document.observe('dom:loaded', function() {
						TBG.Main.Dashboard.views.each(function(view_id) {
							TBG.Main.Dashboard.View.init('<?php echo make_url('dashboard_view'); ?>', view_id);
						});
					});
				</script>
			<?php endif; ?>
			<?php TBGEvent::createNew('core', 'project_dashboard_bottom')->trigger(); ?>
		</td>
	</tr>
</table>
