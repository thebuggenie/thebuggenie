"<?php echo __("Issue number"); ?>","<?php echo __("Issue title"); ?>","<?php echo __("Description"); ?>","<?php echo __("Percentage complete"); ?>"
<?php if ($issues != false): ?>
<?php foreach ($issues as $issue): ?>
<?php 
if ($issue->getPercentComplete() == '')
{
	$percent = '0%';
}
else
{
	$percent = $issue->getPercentComplete();
}
?>
"<?php echo $issue->getFormattedIssueNo(); ?>","<?php echo strip_tags($issue->getTitle()); ?>","<?php echo $issue->getDescription(); ?>","<?php echo $percent; ?>"
<?php endforeach; ?>
<?php endif; ?>