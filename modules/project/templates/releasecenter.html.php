<?php

	$tbg_response->addBreadcrumb(__('Release center'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" release center', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<?php //$url = make_url('configure_edition_add_build', array('project_id' => $selected_project->getProject()->getID(), 'edition_id' => $selected_project->getID())); ?>
			<h3>
				<?php if ($tbg_user->canManageProjectReleases($selected_project)): ?>
				<div class="button button-green" style="float: right; margin-top: -5px;" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_build', 'project_id' => $selected_project->getId())); ?>');"><?php echo __('Add new project release'); ?></div>
				<?php endif; ?>
				<?php echo __('Active project releases'); ?>
			</h3>
			<ul class="simple_list" id="active_builds_0">
				<?php if (count($active_builds[0])): ?>
					<?php foreach ($active_builds[0] as $build): ?>
						<?php include_component('project/buildbox', array('build' => $build)); ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
			<div class="faded_out" id="no_active_builds_0"<?php if (count($active_builds[0])): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no active releases for this project'); ?></div>
			<?php if ($selected_project->isEditionsEnabled()): ?>
				<?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
					<div style="margin: 20px 0 0 0;">
						<h5>
							<?php if ($tbg_user->canManageProjectReleases($selected_project)): ?>
							<div class="button button-green" style="float: right; margin-top: -5px;" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_build', 'project_id' => $selected_project->getId(), 'edition_id' => $edition_id)); ?>');"><?php echo __('Add new edition release'); ?></div>
							<?php endif; ?>
							<?php echo __('Active %edition_name% releases', array('%edition_name%' => $edition->getName())); ?>
						</h5>
						<ul class="simple_list" id="active_builds_<?php echo $edition_id; ?>">
							<?php if (count($active_builds[$edition_id])): ?>
								<?php foreach ($active_builds[$edition_id] as $build): ?>
									<?php include_component('configuration/buildbox', array('build' => $build)); ?>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
						<div class="faded_out" id="no_active_builds_<?php echo $edition_id; ?>"<?php if (count($active_builds[$edition_id])): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no active releases for this edition'); ?></div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
			<h3 style="margin-top: 30px;"><?php echo __('Archived releases'); ?></h3>
			<ul class="simple_list" id="archived_builds">
				<?php if (count($archived_builds[0])): ?>
					<?php foreach ($archived_builds[0] as $build): ?>
						<?php include_component('buildbox', array('build' => $build)); ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
			<div class="faded_out" id="no_archived_builds"<?php if (count($archived_builds)): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no archived releases for this project'); ?></div>
			<?php if ($selected_project->isEditionsEnabled()): ?>
				<?php foreach ($selected_project->getEditions() as $edition_id => $edition): ?>
					<div style="margin: 20px 0 0 0;">
						<h5><?php echo __('Archived %edition_name% releases', array('%edition_name%' => $edition->getName())); ?></h5>
						<ul class="simple_list" id="archived_builds_<?php echo $edition_id; ?>">
							<?php if (count($archived_builds[$edition_id])): ?>
								<?php foreach ($archived_builds[$edition_id] as $build): ?>
									<?php include_component('configuration/buildbox', array('build' => $build)); ?>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
						<div class="faded_out" id="no_archived_builds_<?php echo $edition_id; ?>"<?php if (count($archived_builds[$edition_id])): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no archived releases for this edition'); ?></div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</td>
	</tr>
</table>
<?php if ($build_error): ?>
	<script type="text/javascript">
		document.observe('dom:loaded', function() {
			TBG.Main.Helpers.Message.error(__('An error occured when adding or updating the release'), $build_error);
		});
	</script>
<?php endif; ?>
