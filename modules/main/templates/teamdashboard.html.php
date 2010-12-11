<?php 

	$tbg_response->addBreadcrumb(__('Teams'));
	if ($team instanceof TBGTeam)
	{
		$tbg_response->setTitle(__('Team dashboard for %team_name%', array('%team_name%' => $team->getName())));
		$tbg_response->setPage('team');
		$tbg_response->addBreadcrumb(link_tag(make_url('team_dashboard', array('team_id' => $team->getID())), $team->getName()));
	}
	else
	{
		$tbg_response->setTitle(__('Team dashboard'));
		$tbg_response->addBreadcrumb(__('Team dashboard'));
	}
	
?>

<div class="team_dashboard">
	<div class="dashboard_team_info">
		<span class="dashboard_team_header"><?php echo $team->getName(); ?></span><br />
	</div>
	
	<table class="team_dashboard_table">
		<tr>
			<td class="team_dashboard_projects">
				<div class="header">
					<?php echo __('Projects for %team%', array('%team%' => $team->getName())); ?>
				</div>
				<?php if (count($projects) > 0): ?>
					<ul class="project_list simple_list">
					<?php foreach ($projects as $aProject): ?>
						<li><?php include_component('project/overview', array('project' => $aProject)); ?></li>
					<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p class="content faded_out"><?php echo __('There are no projects linked to this team'); ?>.</p>
				<?php endif; ?>
			</td>
			<td class="team_dashboard_users">
				<div class="header">
					<?php echo __('Members of %team%', array('%team%' => $team->getName())); ?>
				</div>
				<?php if (count($users) > 0): ?>
					<?php foreach ($users as $user): ?>
						<?php echo include_component('main/userdropdown', array('user' => $user)); ?>
						<br>
					<?php endforeach; ?>
				<?php else: ?>
					<p class="content faded_out"><?php echo __('This team has no members'); ?>.</p>
				<?php endif; ?>
			</td>
		</tr>
	</table>
</div>