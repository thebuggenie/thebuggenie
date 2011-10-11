<?php

	$tbg_response->addBreadcrumb(__('Commits'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" commits', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div id="project_commits">
				<?php
				if ($commits == false)
				{
				?>
					<p class="faded_out"><?php echo __('No commits have been found for this project'); ?></p>
				<?php
				}
				else
				{
					?>
					<div class="project_commits_box">
						<div id="commits">
							<?php include_template('vcs_integration/projectcommits', array('selected_project' => $selected_project, 'commits' => $commits)); ?>
						</div>
						
						<div class="commits_next">
							<input id="commits_offset" value="40" type="hidden">
							<?php echo image_tag('spinning_16.gif', array('id' => 'commits_indicator', 'style' => 'display: none; float: left; margin-right: 5px;')); ?>
							<?php echo javascript_link_tag(__('Show more').image_tag('action_add_small.png', array('style' => 'float: left; margin-right: 5px;')), array('onclick' => "TBG.Project.Commits.update('".make_url('vcs_commitspage', array('project_key' => $selected_project->getKey()))."');", 'id' => 'commits_more_link')); ?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</td>
	</tr>
</table>