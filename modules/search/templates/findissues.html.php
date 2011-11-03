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
		$tbg_response->addFeed(make_url('project_month_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), __('Issues reported for %project_name% this month', array('%project_name%' => TBGContext::getCurrentProject()->getName())));
		$tbg_response->addFeed(make_url('project_last_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss', 'days' => 30)), __('Issues reported for %project_name% last 30 days', array('%project_name%' => TBGContext::getCurrentProject()->getName())));
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
<table style="width: 100%; height: 100%;" cellpadding="0" cellspacing="0">
	<tr>
		<?php include_component('search/sidebar', array('hide' => ($show_results && $resultcount))); ?>
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
				<div class="results_header">
					<?php echo $searchtitle; ?>
					&nbsp;&nbsp;<span class="faded_out"><?php echo __('%number_of% issue(s)', array('%number_of%' => (int) $resultcount)); ?></span>
					<?php include_component('search/extralinks', compact('show_results', 'issavedsearch')); ?>
				</div>
				<?php if ($resultcount > 0): ?>
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
