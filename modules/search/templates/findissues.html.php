<?php

	if ($show_results)
	{
		$tbg_response->setTitle($searchtitle);
	}
	else
	{
		$tbg_response->setTitle((TBGContext::isProjectContext()) ? __('Find issues for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName())) : __('Find issues'));
	}
	if (TBGContext::isProjectContext())
	{
		$tbg_response->addBreadcrumb(__('Issues'), make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), tbg_get_breadcrumblinks('project_summary', TBGContext::getCurrentProject()));
		$tbg_response->addFeed(make_url('project_open_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), __('Open issues for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName())));
		$tbg_response->addFeed(make_url('project_closed_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), __('Closed issues for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName())));
		$tbg_response->addFeed(make_url('project_milestone_todo_list', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), __('Milestone todo-list for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName())));
		if (!TBGUser::isThisGuest())
		{
			$tbg_response->addFeed(make_url('project_my_reported_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), __('Issues reported by me') . ' ('.TBGContext::getCurrentProject()->getName().')');
			$tbg_response->addFeed(make_url('project_my_assigned_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), __('Open issues assigned to me') . ' ('.TBGContext::getCurrentProject()->getName().')');
			$tbg_response->addFeed(make_url('project_my_teams_assigned_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), __('Open issues assigned to my teams') . ' ('.TBGContext::getCurrentProject()->getName().')');
		}
	}
	else
	{
		$tbg_response->addBreadcrumb(__('Issues'), make_url('search'), tbg_get_breadcrumblinks('main_links'));
		if (!TBGUser::isThisGuest())
		{
			$tbg_response->addFeed(make_url('my_reported_issues', array('format' => 'rss')), __('Issues reported by me'));
			$tbg_response->addFeed(make_url('my_assigned_issues', array('format' => 'rss')), __('Open issues assigned to you'));
			$tbg_response->addFeed(make_url('my_teams_assigned_issues', array('format' => 'rss')), __('Open issues assigned to your teams'));
		}
	}

?>
<table style="width: 100%;" cellpadding="0" cellspacing="0">
	<tr>
		<?php include_component('search/sidebar'); ?>
		<td style="width: auto; padding: 5px; vertical-align: top;" id="find_issues">
			<?php if ($search_error !== null): ?>
				<div class="rounded_box red borderless" style="margin: 0; vertical-align: middle;" id="search_error">
					<div class="header"><?php echo $search_error; ?></div>
				</div>
			<?php endif; ?>
			<?php if ($search_message !== null): ?>
				<div class="rounded_box green borderless" style="margin: 0; vertical-align: middle;" id="search_message">
					<div class="header"><?php echo $search_message; ?></div>
				</div>
			<?php endif; ?>
			<?php include_component('search/searchbuilder', compact('appliedfilters', 'ipp', 'groupby', 'grouporder', 'issavedsearch', 'savedsearch', 'searchterm', 'show_results', 'templatename', 'template_parameter')); ?>
			<?php if ($show_results): ?>
				<div class="main_header">
					<?php echo $searchtitle; ?>
					&nbsp;&nbsp;<span class="faded_out"><?php echo __('%number_of% issue(s)', array('%number_of%' => (int) $resultcount)); ?></span>
					<div class="search_column_settings" id="search_column_settings_toggler" style="display: none;">
						<div id="search_column_settings_button" onclick="$(this).toggleClassName('button-pressed');$('search_column_settings_container').toggle();" class="button button-silver button-icon" title="<?php echo __('Configure visible columns'); ?>"><span><?php echo image_tag('cfg_icon_general.png'); ?></span></div>
						<div class="rounded_box shadowed white" id="search_column_settings_container" style="width: 300px; position: absolute; right: 5px; display: none;">
							<h4><?php echo __('Select columns to show'); ?></h4>
							<p class="faded_out"><?php echo __('Select which columns you would like to show in this result view. Your selection is saved until the next time you visit.'); ?></p>
							<form id="scs_column_settings_form" action="<?php echo make_url('search_save_column_settings'); ?>" onsubmit="TBG.Search.saveVisibleColumns('<?php echo make_url('search_save_column_settings'); ?>');return false;">
								<input type="hidden" name="template" value="" id="scs_current_template">
								<ul class="simple_list scs_list">
									<?php 
										$columns = array('title' => __('Issue title'), 'issuetype' => __('Issue type'), 'assigned_to' => __('Assigned to'), 'status' => __('Status'), 'resolution' => __('Resolution'), 'category' => __('Category'), 'severity' => __('Severity'), 'percent_complete' => __('% completed'), 'reproducability' => __('Reproducability'), 'priority' => __('Priority'), 'last_updated' => __('Last updated time'), 'comments' => __('Number of comments')); 
									?>
									<?php foreach ($columns as $c_key => $c_name): ?>
										<li class="scs_<?php echo $c_key; ?>" style="display: none;"><label><input type="checkbox" onclick="TBG.Search.toggleColumn('<?php echo $c_key; ?>');" name="columns[<?php echo $c_key; ?>]" value="<?php echo $c_key; ?>"></input><div><?php echo $c_name; ?></div></label></li>
									<?php endforeach; ?>
								</ul>
								<div style="text-align: right; clear: both;">
									<div style="float: left; padding: 8px;"><?php echo javascript_link_tag(__('Reset columns'), array('onclick' => 'TBG.Search.resetColumns()')); ?></div>
									<div id="search_column_settings_button" class="button button-green smaller" onclick="TBG.Search.saveVisibleColumns('<?php echo make_url('search_save_column_settings'); ?>');" style="margin-top: 7px;"><span><?php echo __('Ok'); ?></span></div>
									<div id="search_column_settings_indicator" style="display: none; float: right; margin: 7px 5px 0 10px;"><?php echo image_tag('spinning_20.gif'); ?></div>
								</div>
							</form>
						</div>
					</div>
					<div class="search_export_links">
						<?php
							if (TBGContext::getRequest()->hasParameter('quicksearch'))
							{
								$searchfor = TBGContext::getRequest()->getParameter('searchfor');
								$project_key = (TBGContext::getCurrentProject() instanceof TBGProject) ? TBGContext::getCurrentProject()->getKey() : 0; 
								echo __('Export results as:').'&nbsp;&nbsp;<a href="'.make_url('project_issues', array('project_key' => $project_key, 'quicksearch' => 'true', 'format' => 'csv')).'?searchfor='.$searchfor.'"> '.image_tag('icon_csv.png', array('class' => 'image', 'style' => 'vertical-align: top')).'&nbsp;CSV</a>&nbsp;&nbsp;<a href="'.make_url('project_issues', array('project_key' => $project_key, 'quicksearch' => 'true', 'format' => 'rss')).'?searchfor='.$searchfor.'"> '.image_tag('icon_rss.png', array('class' => 'image', 'style' => 'vertical-align: top')).'&nbsp;RSS</a>';
							}
							elseif (TBGContext::getRequest()->hasParameter('predefined_search'))
							{
								$searchno = TBGContext::getRequest()->getParameter('predefined_search');
								$project_key = (TBGContext::getCurrentProject() instanceof TBGProject) ? TBGContext::getCurrentProject()->getKey() : 0; 
								$url = (TBGContext::getCurrentProject() instanceof TBGProject) ? 'project_issues' : 'search';
								echo __('Export results as:').'&nbsp;&nbsp;<a href="'.make_url($url, array('project_key' => $project_key, 'predefined_search' => $searchno, 'search' => '1', 'format' => 'csv')).'"> '.image_tag('icon_csv.png', array('class' => 'image', 'style' => 'vertical-align: top')).'&nbsp;CSV</a>&nbsp;&nbsp;<a href="'.make_url($url, array('project_key' => $project_key, 'predefined_search' => $searchno, 'search' => '1', 'format' => 'rss')).'"> '.image_tag('icon_rss.png', array('class' => 'image', 'style' => 'vertical-align: top')).'&nbsp;RSS</a>';
							}
							else
							{
								/* Produce get parameters for query */
								preg_match('/((?<=\/)issues).+$/i', $_SERVER['QUERY_STRING'], $get);
								if (!isset($get[0]))
								{
									preg_match('/((?<=url=)issues).+$/i', $_SERVER['QUERY_STRING'], $get);
								}
								if (isset($get[0])) // prevent unhandled error
								{
									if (TBGContext::isProjectContext())
									{
										echo __('Export results as:').'&nbsp;&nbsp;<a href="'.make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'csv')).'/'.$get[0].'"> '.image_tag('icon_csv.png', array('class' => 'image', 'style' => 'vertical-align: top')).'&nbsp;CSV</a>&nbsp;&nbsp;<a href="'.make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')).'?'.$get[0].'"> '.image_tag('icon_rss.png', array('class' => 'image', 'style' => 'vertical-align: top')).'&nbsp;RSS</a>';
									}
									else
									{
										echo __('Export results as:').'&nbsp;&nbsp;<a href="'.make_url('search', array('format' => 'csv')).'/'.$get[0].'"> '.image_tag('icon_csv.png', array('class' => 'image', 'style' => 'vertical-align: top')).'&nbsp;CSV</a>&nbsp;&nbsp;<a href="'.make_url('search', array('format' => 'rss')).'?'.$get[0].'"> '.image_tag('icon_rss.png', array('class' => 'image', 'style' => 'vertical-align: top')).'&nbsp;RSS</a>';
									}
								}
							}
						?>
					</div>
				</div>
				<?php if (count($issues) > 0): ?>
					<div id="search_results" class="search_results">
						<?php include_template('search/issues_paginated', array('issues' => $issues, 'templatename' => $templatename, 'template_parameter' => $template_parameter, 'searchterm' => $searchterm, 'filters' => $appliedfilters, 'groupby' => $groupby, 'grouporder' => $grouporder, 'resultcount' => $resultcount, 'ipp' => $ipp, 'offset' => $offset)); ?>
					</div>
				<?php else: ?>
					<div class="faded_out" id="no_issues"><?php echo __('No issues were found'); ?></div>
				<?php endif; ?>
			<?php endif; ?>
		</td>
	</tr>
</table>
