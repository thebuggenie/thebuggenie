"<?php echo __("Project"); ?>","<?php echo __("Issue number"); ?>","<?php echo __("Issue title"); ?>","<?php echo __("Assigned to"); ?>","<?php echo __("Status"); ?>","<?php echo __('Category'); ?>","<?php echo __('Priority'); ?>","<?php echo __('Reproducability'); ?>","<?php echo __('Severity'); ?>","<?php echo __("Resolution"); ?>","<?php echo __('Targetted for'); ?>","<?php echo __("Last updated"); ?>","<?php echo __("Percentage complete"); ?>","<?php echo __("User pain"); ?>","<?php echo __("Votes"); ?>"
<?php if ($issues != false): ?>
<?php foreach ($issues as $issue): ?>
<?php 
/* Deal with issue assignee */
$temp = $issue->getAssignee();
if ($temp instanceof TBGUser)
{
	$assignee = $temp->getBuddyname();
}
elseif ($temp instanceof TBGTeam) {
	$assignee = $temp->getName();
}
else
{
	$assignee = '-';
}

/* Deal with issue status */
$temp = $issue->getStatus();
if ($temp instanceof TBGStatus)
{
	$status = $temp->getName();
}
else
{
	$status = '-';
}

/* Deal with issue priority */
$temp = $issue->getPriority();
if ($temp instanceof TBGPriority)
{
	$priority = $temp->getName();
}
else
{
	$priority = '-';
}

/* Deal with issue resolution */
$temp = $issue->getResolution();
if ($temp instanceof TBGResolution)
{
	$resolution = $temp->getName();
}
else
{
	$resolution = '-';
}

/* Deal with issue category */
$temp = $issue->getCategory();
if ($temp instanceof TBGCategory)
{
	$category = $temp->getName();
}
else
{
	$category = '-';
}

/* Deal with issue reproducability */
$temp = $issue->getReproducability();
if ($temp instanceof TBGReproducability)
{
	$reproducability = $temp->getName();
}
else
{
	$reproducability = '-';
}

/* Deal with issue severity */
$temp = $issue->getSeverity();
if ($temp instanceof TBGSeverity)
{
	$severity = $temp->getName();
}
else
{
	$severity = '-';
}

/* Deal with issue milestone */
$temp = $issue->getMilestone();
if ($temp instanceof TBGMilestone)
{
	$milestone = $temp->getName();
}
else
{
	$milestone = '-';
}

unset($temp);

if ($issue->getPercentComplete() == '')
{
	$percent = '0%';
}
else
{
	$percent = $issue->getPercentComplete();
}

?>
"<?php echo $issue->getProject()->getName(); ?>","<?php echo $issue->getFormattedIssueNo(); ?>","<?php echo strip_tags($issue->getTitle()); ?>","<?php echo $assignee; ?>","<?php echo $status; ?>","<?php echo $category; ?>","<?php echo $priority; ?>","<?php echo $reproducability; ?>","<?php echo $severity; ?>","<?php echo $resolution; ?>","<?php echo $milestone; ?>","<?php echo tbg_formatTime($issue->getLastUpdatedTime(), 21); ?>","<?php echo $percent; ?>","<?php echo $issue->getUserpain(); ?>","<?php echo $issue->getVotes(); ?>"
<?php endforeach; ?>
<?php endif; ?>
