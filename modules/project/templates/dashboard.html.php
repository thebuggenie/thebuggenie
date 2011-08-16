<?php

	$tbg_response->addBreadcrumb(__('Dashboard'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" project dashboard', array('%project_name%' => $selected_project->getName())));
	$tbg_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<?php
				if (TBGContext::hasMessage('account_activate'))
				{
					if (TBGContext::getMessage('activate_success') == true)
					{
						echo '<div class="rounded_box green">'.__('Your account has been activated! You can now log in with the username %user% and the password in your activation email.', array('%user%' => '<b>'.TBGContext::getMessage('activate_uname').'</b>')).'</div>';
					}
					else
					{
						echo '<div class="rounded_box red">'.__('This activation link is not valid').'</div>';
					}
					echo '<br>';
				}
			?>
			<?php TBGEvent::createNew('core', 'project_dashboard_top')->trigger(); ?>
			<?php if (empty($dashboardViews) && TBGContext::getUser()->canEditProjectDetails($selected_project)) :?>
				<p class="content faded_out"><?php echo __("This dashboard doesn't contain any views. To add views in this dashboard, press 'Customize' on the left."); ?></p>
			<?php elseif (empty($dashboardViews)): ?>
				<p class="content faded_out"><?php echo __("This dashboard doesn't contain any views."); ?></p>
			<?php else: ?>
				<ul id="dashboard">
					<?php $clearleft = true; ?>
					<?php foreach($dashboardViews as $view): ?>
					<li style="clear: <?php echo ($clearleft) ? 'left' : 'right'; ?>;">
						<?php include_component('main/dashboardview', array('type' => $view->get(TBGDashboardViewsTable::TYPE), 'id' => $view->get(TBGDashboardViewsTable::ID), 'view' => $view->get(TBGDashboardViewsTable::VIEW), 'rss' => true)); ?>
					</li>
					<?php $clearleft = !$clearleft; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php TBGEvent::createNew('core', 'project_dashboard_bottom')->trigger(); ?>
		</td>
	</tr>
</table>
