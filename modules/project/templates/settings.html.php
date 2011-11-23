<?php

	$tbg_response->addBreadcrumb(__('Project settings'), null, tbg_get_breadcrumblinks('project_settings', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" settings', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div style="width: 790px;">
				<?php include_component('project/projectconfig', array('project' => $selected_project)); ?>
			</div>
		</td>
	</tr>
</table>
<?php if ($settings_saved): ?>
	<script type="text/javascript">
		document.observe('dom:loaded', function() {
			TBG.Main.Helpers.Message.success('<?php echo __('Settings saved'); ?>', '<?php echo __('Project settings have been saved successfully'); ?>');
		});
	</script>
<?php endif; ?>