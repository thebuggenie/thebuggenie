"<?php echo __("Project"); ?>","<?php echo __("Issue number"); ?>","<?php echo __("Issue title"); ?>","<?php echo __("Assigned to"); ?>","<?php echo __("Status"); ?>","<?php echo __("Resolution"); ?>","<?php echo __("Last updated"); ?>","<?php echo __("Percentage complete"); ?>","<?php echo __("User pain"); ?>"
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
"<?php echo $issue->getProject()->getName(); ?>","<?php echo $issue->getFormattedIssueNo(); ?>","<?php echo strip_tags($issue->getTitle()); ?>","<?php echo $assignee; ?>","<?php echo $status; ?>","<?php echo $resolution; ?>","<?php echo tbg_formatTime($issue->getLastUpdatedTime(), 21); ?>","<?php echo $percent; ?>","<?php echo $issue->getUserpain(); ?>", "<?php echo $issue->getVotes(); ?>"
<?php endforeach; ?>
<?php endif; ?>
