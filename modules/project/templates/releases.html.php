<?php

	$tbg_response->addBreadcrumb(__('Releases'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" releases', array('%project_name%' => $selected_project->getName())));

	if (!$selected_project instanceof TBGProject) exit();
	
?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<h3><?php echo __('Active project releases'); ?></h3>
			<?php if (count($active_builds[0])): ?>
				<ul class="simple_list">
				<?php foreach ($active_builds[0] as $build): ?>
					<?php include_template('project/release', array('build' => $build)); ?>
				<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<div class="faded_out"><?php echo __('There are no active releases for this project'); ?></div>
			<?php endif; ?>
			<?php if ($selected_project->isEditionsEnabled()): ?>
				<?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
					<h4 style="margin-top: 30px;"><?php echo __('Active %edition_name% releases', array('%edition_name%' => $edition->getName())); ?></h4>
					<?php if (count($active_builds[$edition_id])): ?>
						<ul class="simple_list">
						<?php foreach ($active_builds[$edition_id] as $build): ?>
							<?php include_template('project/release', array('build' => $build)); ?>
						<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<div class="faded_out"><?php echo __('There are no active releases for this edition'); ?></div>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			<h3 style="margin-top: 30px;"><?php echo __('Archived releases'); ?></h3>
			<?php if (count($archived_builds[0])): ?>
				<ul class="simple_list">
				<?php foreach ($archived_builds[0] as $build): ?>
					<?php include_template('project/release', array('build' => $build)); ?>
				<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<div class="faded_out"><?php echo __('There are no archived releases for this project'); ?></div>
			<?php endif; ?>
			<?php if ($selected_project->isEditionsEnabled()): ?>
				<?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
					<h4 style="margin-top: 30px;"><?php echo __('Archived %edition_name% releases', array('%edition_name%' => $edition->getName())); ?></h4>
					<?php if (count($archived_builds[$edition_id])): ?>
						<ul class="simple_list">
						<?php foreach ($archived_builds[$edition_id] as $build): ?>
							<?php include_template('project/release', array('build' => $build)); ?>
						<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<div class="faded_out"><?php echo __('There are no archived releases for this edition'); ?></div>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</td>
	</tr>
</table>