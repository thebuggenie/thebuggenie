<?php

	$tbg_response->addBreadcrumb(__('Dashboard'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" project dashboard', array('%project_name%' => $selected_project->getName())));
	$tbg_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<?php TBGEvent::createNew('core', 'project_dashboard_top')->trigger(); ?>
			<?php if (!count($views) && $tbg_user->canEditProjectDetails($selected_project)) :?>
				<div style="text-align: center; padding: 40px;">
					<p class="content faded_out"><?php echo __("This dashboard doesn't contain any views."); ?></p>
					<br>
					<form action="<?php echo make_url('project_dashboard', array('project_key' => $selected_project->getKey())); ?>" method="post">
						<input type="hidden" name="setup_default_dashboard" value="1">
						<input type="submit" value="<?php echo __('Setup project dashboard'); ?>" class="button button-green" style="font-size: 1.1em; padding: 5px !important;">
					</form>
				</div>
			<?php elseif (!count($views)): ?>
				<p class="content faded_out"><?php echo __("This dashboard doesn't contain any views."); ?></p>
			<?php else: ?>
				<ul id="dashboard">
					<?php foreach($views as $id => $view): ?>
						<li style="clear: none;" id="dashboard_container_<?php echo $id; ?>">
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
