<?php

	if ($tbg_user->hasPageAccess('project_timeline', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID()))
	{
		$tbg_response->addFeed(make_url('project_timeline', array('project_key' => $project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $project->getName())));
	}
	
?>
<div class="rounded_box <?php if (!($project->isIssuelistVisibleInFrontpageSummary() && count($project->getVisibleIssuetypes()))): ?>invisible <?php else: ?> white borderless <?php endif; ?>project_strip">
	<div style="float: left; font-weight: normal; font-size: 14px;">
		<?php echo image_tag($project->getIcon(), array('style' => 'float: left; margin-right: 5px;'), $project->hasIcon()); ?>
		<b class="project_name"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), $project->getName()); ?> <?php if ($project->usePrefix()): ?>(<?php echo strtoupper($project->getPrefix()); ?>)<?php endif; ?></b><?php if ($tbg_user->canEditProjectDetails($project)): ?>&nbsp;&nbsp;<span class="faded_medium"><?php echo link_tag(make_url('configure_project_settings', array('project_id' => $project->getID())), __('Edit project'), array('style' => 'font-size: 12px;')); ?></span><?php endif; ?><br>
		<?php if ($project->hasHomepage()): ?>
			<a href="<?php echo $project->getHomepage(); ?>" style="font-size: 13px;" target="_blank"><?php echo $project->getHomepage(); ?></a>
		<?php else: ?>
			<span class="faded_medium" style="font-size: 13px; font-weight: normal;"><?php echo __('No homepage provided'); ?></span>
		<?php endif; ?>
		|
		<?php if ($project->hasDocumentationURL()): ?>
			<a href="<?php echo $project->getDocumentationURL(); ?>" style="font-size: 13px;" target="_blank"><?php echo $project->getDocumentationURL(); ?></a>
		<?php else: ?>
			<span class="faded_medium" style="font-size: 13px; font-weight: normal;"><?php echo __('No documentation URL provided'); ?></span>
		<?php endif; ?>
	</div>
	<div style="text-align: right; font-size: 13px; font-weight: normal; padding-top: 3px;">
		<?php if ($tbg_user->hasPageAccess('project_dashboard', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID())): ?>
			<?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), __('Overview')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php endif; ?>
		<?php if ($tbg_user->canSearchForIssues() && ($tbg_user->hasPageAccess('project_issues', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID()))): ?>
			<?php echo link_tag(make_url('project_issues', array('project_key' => $project->getKey())), __('Issues')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php endif; ?>
		<?php if ($tbg_user->hasPageAccess('project_roadmap', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID())): ?>
			<?php echo link_tag(make_url('project_roadmap', array('project_key' => $project->getKey())), __('Show roadmap')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php endif; ?>
		<?php TBGEvent::createNew('core', 'project_overview_item_links', $project)->trigger(); ?>
		<form action="<?php echo make_url('project_reportissue', array('project_key' => $project->getKey())); ?>" method="get" style="clear: none; display: inline; width: 160px;">
			<table border="0" cellpadding="0" cellspacing="0" style="float: right;"><tr><td class="nice_button report_button"><input type="submit" value="<?php echo __('Report an issue'); ?>"></td></tr></table>
		</form>
	</div>
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
		<div class="search_results">
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