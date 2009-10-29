<div class="rounded_box invisible">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="vertical-align: middle; padding: 0 5px 0 5px;">
			<div style="float: left; font-weight: normal; font-size: 14px;">
				<?php echo image_tag($project->getIcon(), array('style' => 'float: left; margin-right: 5px;'), $project->hasIcon()); ?>
				<b><?php echo $project->getName(); ?> <?php if ($project->usePrefix()): ?>(<?php echo strtoupper($project->getPrefix()); ?>)<?php endif; ?></b><br>
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
				<?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), __('Overview')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo link_tag(make_url('project_planning', array('project_key' => $project->getKey())), __('Planning')); ?>
				<form action="<?php echo make_url('project_reportissue', array('project_key' => $project->getKey())); ?>" method="get" style="clear: none; display: inline; width: 160px;">
					<div class="report_button" style="width: 150px;"><input type="submit" value="<?php echo __('Report an issue'); ?>"></div>
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
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>								