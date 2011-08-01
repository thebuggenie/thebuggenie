<?php

	$tbg_response->addBreadcrumb(__('Interactive planning'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" project planning', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div style="padding: 15px; color: #AAA; font-size: 12px;"><?php echo __('Interactive planning has not been added yet'); ?></div>
			<div style="width: 330px; padding: 5px; margin: 5px 3px 5px 3px;">
				<?php /*foreach ($recent_ideas as $idea): ?>
				<?php endforeach; */ ?>
			</div>
		</td>
	</tr>
</table>