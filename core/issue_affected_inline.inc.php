<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
<tr>
<td style="font-size: 11px; font-weight: bold; width: auto; padding: 3px; border-bottom: 1px solid #DDD;"><b><?php echo __('Affected by this issue'); ?></b></td>
<td style="width: 200px; font-size: 10px; font-weight: bold; padding: 3px; border-bottom: 1px solid #DDD; text-align: left;"><b><?php echo __('Status'); ?></b></td>
<td style="width: 60px; font-size: 10px; font-weight: bold; padding: 3px; border-bottom: 1px solid #DDD; text-align: center;"><b><?php echo __('Confirmed'); ?></b></td>
</tr>
</table>
<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0 id="issue_affected_inline">
<?php

	foreach (array_merge($theIssue->getBuilds(), $theIssue->getComponents()) as $anAffected)
	{
		require TBGContext::getIncludePath() . 'include/issue_affected_itemline.inc.php';
	}

?>
</table>