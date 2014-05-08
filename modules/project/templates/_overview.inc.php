<?php

	if ($tbg_user->hasPageAccess('project_timeline', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID()))
	{
		$tbg_response->addFeed(make_url('project_timeline', array('project_key' => $project->getKey(), 'format' => 'rss')), __('"%project_name%" project timeline', array('%project_name%' => $project->getName())));
	}

?>
<div class="rounded_box <?php if (!($project->isIssuelistVisibleInFrontpageSummary() && count($project->getVisibleIssuetypes()))): ?>invisible <?php else: ?> white borderless <?php endif; ?>project_strip">
	<div style="float: left; font-weight: normal; 
	<?php   // ADD PADDING CODE TO REDUCE SPACING BETWEEN GREEN BARS AND PROJECT NAMES ON MAIN PAGE //   ?>   padding: 8px 0 0 0">
			<?php   // COMMENT OUT THIS CODE TO REMOVE PROJECT ICONS FROM MAIN PAGE //   echo image_tag($project->getSmallIconName(), array('style' => 'float: left; margin: 3px 5px 0 0; width: 16px; height: 16px;'), $project->hasSmallIcon()); 
			?>
		<b class="project_name"><?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), '<span id="project_name_span">'.$project->getName()."</span>"); ?> <?php if ($project->usePrefix()): ?>(<?php echo mb_strtoupper($project->getPrefix()); ?>)<?php endif; ?></b><?php if ($tbg_user->canEditProjectDetails($project)): ?>&nbsp;&nbsp;<span class="faded_out button-group" style="float: none;"><?php echo javascript_link_tag(__('Quick edit'), array('class' => 'button button-silver', 'onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $project->getID()))."');", 'style' => 'font-size: 0.85em !important; padding: 0 3px !important; float: none;')); ?><?php echo link_tag(make_url('project_settings', array('project_key' => $project->getKey())), __('Settings'), array('class' => 'button button-silver', 'style' => 'font-size: 0.85em !important; padding: 0 3px !important; float: none;')); ?></span><?php endif; ?><br>
		<?php   // ADD getDescription CODE TO ADD PROJECT DESCRIPTION TO EACH PROJECT ON MAIN PAGE TO SHOW PROJECT DESCRIPTION 
		?>
		<span class="faded_out" style="font-weight: normal;"> <?php echo ($project->getDescription()); ?> </span>
		
		<?php   /* DEACTIVATE THIS BLOCK OF CODE TO REMOVE HOMEPAGE AND DOCUMENTATION URL MESSAGES FROM MAIN PAGE
		<?php if ($project->hasHomepage()): ?>
			<a href="<?php echo $project->getHomepage(); ?>" target="_blank"><?php echo __('Go to project website'); ?></a>
		<?php else: ?>
			<span class="faded_out" style="font-weight: normal;"><?php echo __('No homepage provided'); ?></span>
		<?php endif; ?>
		|
		<?php if ($project->hasDocumentationURL()): ?>
			<a href="<?php echo $project->getDocumentationURL(); ?>" target="_blank"><?php echo __('Open documentation'); ?></a>
		<?php else: ?>
			<span class="faded_out" style="font-weight: normal;"><?php echo __('No documentation URL provided'); ?></span>
		<?php endif; ?>
		*/?>
	</div>
	<nav class="button-group">
<?php if ($tbg_user->hasPageAccess('project_dashboard', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID())) echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), __('Dashboard'), array('class' => 'button button-silver')); ?>
<?php if ($tbg_user->canSearchForIssues() && ($tbg_user->hasPageAccess('project_issues', $project->getID()) || $tbg_user->hasPageAccess('project_allpages', $project->getID()))) echo link_tag(make_url('project_open_issues', array('project_key' => $project->getKey())), __('Issues'), array('class' => 'button button-silver')); ?>
<?php TBGEvent::createNew('core', 'project_overview_item_links', $project)->trigger(); ?>
<?php if (!$project->isLocked() && $tbg_user->canReportIssues($project)) echo link_tag(make_url('project_reportissue', array('project_key' => $project->getKey())), __('Report an issue'), array('class' => 'button button-green')); ?>
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
				<td style="padding-bottom: 2px; width: 60px; padding-right: 10px;"><b><?php echo $issuetype->getName(); ?>:</b></td>
		<?php   //  ADD <div style> TAG INSTEAD OF THE <td style> TAG BELOW TO ALIGN PROJECT NUMBERS RELATLIVE TO THE GREEN BARS ON THE FRONTPAGE
		?>
				<td><div style="padding-bottom: 2px; width: auto; position: relative;">
					<div style="color: #222; position: absolute; left: 3px; text-align: right;"><?php echo __('%closed% closed of %issues% reported', array('%closed%' => '<b>'.$project->countClosedIssuesByType($issuetype->getID()).'</b>', '%issues%' => '<b>'.$project->countIssuesByType($issuetype->getID()).'</b>')); ?>
					
					</div>
					<?php include_template('main/percentbar', array('percent' => $project->getClosedPercentageByType($issuetype->getID()), 'height' => 20)); ?>
		<?php   //  ADDED </div> TAG BELOW TO COMPLETE ALIGNMENT ON PROJECT NUMBERS ON FRONTPAGE
		?>	

				</div></td>
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
					<?php include_template('main/percentbar', array('percent' => $project->getClosedPercentageByMilestone($milestone->getID()), 'height' => 20)); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
	<?php endif; ?>
	<div style="clear: both;"> </div>
</div>