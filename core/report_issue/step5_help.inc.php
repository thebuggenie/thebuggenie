<div style="padding: 5px; border-bottom: 1px solid #DDD; background-color: #F8F8F8;"><b><?php echo __('"Report an issue", last step'); ?></b></div>
<div style="padding: 5px;">
<?php echo __('Now, have a look at what you\'ve entered, and file your issue when you are satisfied. Remember to check the list below for duplicate issues, so you don\'t file an issue that has already been filed.'); ?><br>
<br>
<div style="width: auto; border-bottom: 1px dotted #DDD; font-weight: bold;"><i><?php echo __('Issues already filed, similar to yours'); ?></i></div>
<?php

	$thisTitle = $_SESSION['rni_step3_title'];
	$thisDesc = $_SESSION['rni_step3_description'];
	$thisProject = $_SESSION['rni_step1_project'];

	$crit = new B2DBCriteria();
	$ctn = $crit->returnCriterion(B2tIssues::TITLE, '%'.$thisTitle.'%', B2DBCriteria::DB_LIKE);
	$ctn->addOr(B2tIssues::LONG_DESCRIPTION, '%'.$thisDesc.'%', B2DBCriteria::DB_LIKE);
	$crit->addWhere($ctn);
	$crit->addWhere(B2tIssues::PROJECT_ID, $selectedProject->getID());
	$crit->addWhere(B2tIssues::DELETED, 0);
	$crit->addWhere(B2tIssues::SCOPE, BUGScontext::getScope()->getID());
	
	$res = B2DB::getTable('B2tIssues')->doSelect($crit);
	if ($res->count() > 0)
	{
		while ($row = $res->getNextRow())
		{
			$theIssue = new BUGSissue($row->get(B2tIssues::ID));
			?>
			<div style="padding: 2px;"><a href="viewissue.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>" target="_blank"><?php echo $theIssue->getFormattedIssueNo(); ?></a>&nbsp;&nbsp;<?php print substr($theIssue->getTitle(), 0, 35); print (strlen($theIssue->getTitle()) >= 36) ? "..." : ""; ?></div>
			<?php
		}
	}
	else
	{
		?>
		<div style="padding: 2px; color: #AAA;"><?php echo __('No issues in this list'); ?></div>
		<?php
	}

?><br>
<?php echo __('You should also have a look at the issues most commonly reported more than once.'); ?><br>
<br>
<div style="width: auto; border-bottom: 1px dotted #DDD; font-weight: bold;"><i><?php echo __('Most common issues'); ?></i></div>
<?php

	$crit = new B2DBCriteria();
	$crit->addSelectionColumn(B2tIssues::ID, 'issues_count', B2DBCriteria::DB_COUNT);
	$crit->addWhere(B2tIssues::PROJECT_ID, $selectedProject->getID());
	$crit->addWhere(B2tIssues::SCOPE, BUGScontext::getScope()->getID());
	$crit->addWhere(B2tIssues::DUPLICATE, 0, B2DBCriteria::DB_NOT_EQUALS);
	$crit->addGroupBy('issues_count', 'desc');
	$crit->setLimit(10);

	$res = B2DB::getTable('B2tIssues')->doSelect($crit);
	if ($res->count() > 0)
	{
		while ($row = $res->getNextRow())
		{
			$theIssue = new BUGSissue($row->get(B2tIssues::ID));
			?>
			<div style="padding: 2px;"><a href="viewissue.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>" target="_blank"><?php echo $theIssue->getFormattedIssueNo(); ?></a>&nbsp;&nbsp;<?php print substr($theIssue->getTitle(), 0, 35); print (strlen($theIssue->getTitle()) >= 36) ? "..." : ""; ?></div>
			<?php
		}
	}
	else
	{
		?>
		<div style="padding: 2px; color: #AAA;"><?php echo __('No issues in this list'); ?></div>
		<?php
	}
?>
<br>
<?php echo __('Please check the list above. If you are sure that your issue is not already filed, press the "Confirm" button below.'); ?>
<div style="width: auto; text-align: center; padding-top: 20px;">
<button onclick="setStep(5);$('step5_button').hide();" id="step5_button" style="font-size: 14px; font-weight: bold; width: 90px; height: 30px; padding: 4px;"><?php echo __('Confirm'); ?></button>
</div>
</div>