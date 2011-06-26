<?php

	$tbg_response->addBreadcrumb(__('Commits'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" commits', array('%project_name%' => $selected_project->getName())));

?>
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
						<?php
						$web_path = TBGContext::getModule('vcs_integration')->getSetting('web_path_' . $selected_project->getID());
						$web_repo = TBGContext::getModule('vcs_integration')->getSetting('web_repo_' . $selected_project->getID());
				
						foreach ($commits as $revno => $entry)
						{
							$revision = $revno;
							/* Build correct URLs */
							switch (TBGContext::getModule('vcs_integration')->getSetting('web_type_' . $selected_project->getID()))
							{
								case 'viewvc':
									$link_rev = $web_path . '/' . '?root=' . $web_repo . '&amp;view=rev&amp;revision=' . $revision;
									break;
								case 'viewvc_repo':
									$link_rev = $web_path . '/' . '?view=rev&amp;revision=' . $revision;
									break;
								case 'websvn':
									$link_rev = $web_path . '/revision.php?repname=' . $web_repo . '&amp;isdir=1&amp;rev=' . $revision;
									break;
								case 'websvn_mv':
									$link_rev = $web_path . '/' . '?repname=' . $web_repo . '&amp;op=log&isdir=1&amp;rev=' . $revision;
									break;
								case 'loggerhead':
									$link_rev = $web_path . '/' . $web_repo . '/revision/' . $revision;
									break;
								case 'gitweb':
									$link_rev = $web_path . '/' . '?p=' . $web_repo . ';a=commitdiff;h=' . $revision;
									break;
								case 'cgit':
									$link_rev = $web_path . '/' . $web_repo . '/commit/?id=' . $revision;
									break;
								case 'hgweb':
									$link_rev = $web_path . '/' . $web_repo . '/rev/' . $revision;
									break;
								case 'github':
									$link_rev = 'http://github.com/' . $web_repo . '/commit/' . $revision;
									break;
							}
							
							/* Now we have everything, render the template */
							include_template('vcs_integration/commitbox', array("project" => $selected_project, "issue_no" => $entry[0][4], "id" => $entry[0][0], "revision" => $revision, "author" => $entry[0][1], "date" => $entry[0][2], "log" => $entry[0][3], "files" => $entry[1], "projectmode" => true));
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
		</td>
	</tr>
</table>