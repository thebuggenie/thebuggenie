<?php

	if ($tbg_user->hasPageAccess('project_timeline', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID()))
	{
		$tbg_response->addFeed(make_url('project_timeline', array('project_key' => $project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $project->getName())));
	}

?>
<div class="rounded_box <?php if (!($project->isIssuelistVisibleInFrontpageSummary() && count($project->getVisibleIssuetypes()))): ?>invisible <?php else: ?> white borderless <?php endif; ?>project_strip">
	<div style="float: left; font-weight: normal;">
		<?php echo image_tag($project->getSmallIconName(), array('style' => 'float: left; margin-right: 5px; width: 16px; height: 16px;'), $project->hasSmallIcon()); ?>
		<b class="project_name"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), '<span id="project_name_span">'.$project->getName()."</span>"); ?> <?php if ($project->usePrefix()): ?>(<?php echo mb_strtoupper($project->getPrefix()); ?>)<?php endif; ?></b><?php if ($tbg_user->canEditProjectDetails($project)): ?>&nbsp;&nbsp;<span class="faded_out"><?php echo javascript_link_tag(__('Edit project'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $project->getID()))."');", 'style' => 'font-size: 12px;')); ?></span><?php endif; ?><br>
		<?php if ($project->hasHomepage()): ?>
			<a href="<?php echo $project->getHomepage(); ?>" target="_blank"><?php echo $project->getHomepage(); ?></a>
		<?php else: ?>
			<span class="faded_out" style="font-weight: normal;"><?php echo __('No homepage provided'); ?></span>
		<?php endif; ?>
		|
		<?php if ($project->hasDocumentationURL()): ?>
			<a href="<?php echo $project->getDocumentationURL(); ?>" target="_blank"><?php echo $project->getDocumentationURL(); ?></a>
		<?php else: ?>
			<span class="faded_out" style="font-weight: normal;"><?php echo __('No documentation URL provided'); ?></span>
		<?php endif; ?>
	</div>
	<nav>
		<?php if ($tbg_user->canSearchForIssues() && ($tbg_user->hasPageAccess('project_issues', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID()))): ?>
			<?php echo link_tag(make_url('project_open_issues', array('project_key' => $project->getKey())), __('Issues')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php endif; ?>
		<?php if ($tbg_user->hasPageAccess('project_roadmap', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID())): ?>
			<?php echo link_tag(make_url('project_roadmap', array('project_key' => $project->getKey())), __('Show roadmap')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php endif; ?>
		<?php TBGEvent::createNew('core', 'project_overview_item_links', $project)->trigger(); ?>
		<?php if ($tbg_user->canReportIssues($project)): ?>
			<a href="<?php echo make_url('project_reportissue', array('project_key' => $project->getKey())); ?>" class="report_button button button-green"><?php echo __('Report an issue'); ?></a>
		<?php endif; ?>
	</nav>
	<?php if ($project->hasChildren()): ?>
	<div class="subprojects_list">
		<?php echo __('Subprojects'); ?>
		<?php foreach ($project->getChildren() as $child): ?>
			<span class="subproject_link"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $child->getKey())), $child->getName()); ?></span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<?php if ($project->isIssuetypesVisibleInFrontpageSummary() && count($project->getVisibleIssuetypes())): ?>
		<table style="width: 100%; margin-top: 5px;" cellpadding=0 cellspacing=0>
		<?php foreach ($project->getVisibleIssuetypes() as $issuetype): ?>
			<tr>
				<td style="padding-bottom: 2px; width: 200px; padding-right: 10px;"><b><?php echo $issuetype->getName(); ?>:</b></td>
				<td style="padding-bottom: 2px; width: auto; position: relative;">
					<div style="color: #222; position: absolute; right: 20px; text-align: right;"><?php echo __('%closed% closed of %issues% reported', array('%closed%' => '<b>'.$project->countClosedIssuesByType($issuetype->getID()).'</b>', '%issues%' => '<b>'.$project->countIssuesByType($issuetype->getID()).'</b>')); ?></div>
					<?php include_template('main/percentbar', array('percent' => $project->getClosedPercentageByType($issuetype->getID()), 'height' => 14)); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
	<?php elseif ($project->isIssuelistVisibleInFrontpageSummary() && count($project->getVisibleIssuetypes())): ?>
		<div class="search_results" style="clear: both;">
			<?php include_component('search/results_normal', array('issues' => $project->getOpenIssuesForFrontpageSummary(true), 'cc' => 1, 'groupby' => 'issuetype', 'prevgroup_id' => 0)); ?>
		</div>
	<?php elseif ($project->isMilestonesVisibleInFrontpageSummary() && count($project->getVisibleMilestones())): ?>
		<table style="width: 100%; margin-top: 5px;" cellpadding=0 cellspacing=0>
		<?php foreach ($project->getVisibleMilestones() as $milestone): ?>
			<tr>
				<td style="padding-bottom: 2px; width: 200px; padding-right: 10px;"><b><?php echo $milestone->getName(); ?>:</b></td>
				<td style="padding-bottom: 2px; width: auto; position: relative;">
					<div style="color: #222; position: absolute; right: 20px; text-align: right;"><?php echo __('%closed% closed of %issues% assigned', array('%closed%' => '<b>'.$project->countClosedIssuesByMilestone($milestone->getID()).'</b>', '%issues%' => '<b>'.$project->countIssuesByMilestone($milestone->getID()).'</b>')); ?></div>
					<?php include_template('main/percentbar', array('percent' => $project->getClosedPercentageByMilestone($milestone->getID()), 'height' => 14)); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
	<?php endif; ?>
	<div style="clear: both;"> </div>
</div>