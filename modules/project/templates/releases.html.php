<?php

	$tbg_response->addBreadcrumb(__('Releases'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" releases', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div style="padding: 15px; color: #AAA; font-size: 12px;"><?php echo __('Project release page has not been added yet'); ?></div>
		</td>
	</tr>
</table>