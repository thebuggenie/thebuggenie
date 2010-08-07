"<?php echo __("Issue number"); ?>","<?php echo __("Issue title"); ?>","<?php echo __("Votes"); ?>","<?php echo __("Status"); ?>"
<?php if ($issues != false): ?>
<?php foreach ($issues as $issue): ?>
<?php 
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

unset($temp);
?>
"<?php echo $issue->getFormattedIssueNo(); ?>","<?php echo strip_tags($issue->getTitle()); ?>","<?php echo $issue->getVotes(); ?>","<?php echo $status; ?>"
<?php endforeach; ?>
<?php endif; ?>