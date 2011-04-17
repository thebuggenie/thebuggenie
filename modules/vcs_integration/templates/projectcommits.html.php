<?php

	$tbg_response->addBreadcrumb(__('Commits'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" commits', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div id="project_commits">
				<h1><?php echo __("Last two weeks of commits for %project%", array('%project%' => $selected_project->getName()))?></h1>
				<?php echo __('For previous commits, please refer to the source code viewer, available on the left. Note that this page only shows commits which affect issues for this project, other commits can be viewed in the source code viewer.')?>
			</div>
		</td>
	</tr>
</table>