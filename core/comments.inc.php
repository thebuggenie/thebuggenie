<?php

	if ($canViewComments || $canEditComments)
	{
		if (isset($fromwhen))
		{
			$theComments = BUGSComment::getComments($target_id, $target_type, $module, $fromwhen);
			BUGScontext::setLoadedAt();
		}
		else
		{
			$theComments = BUGSComment::getComments($target_id, $target_type);
		}
		$filteredComments = 0;

		foreach ($theComments as $aComment)
		{
			echo '<div id="comment_' . $aComment->getID() . '">';
			require BUGScontext::getIncludePath() . 'include/comment_box.inc.php';
			if ($notFiltered)
			{
				echo '</div>';
			}
		}
	}
	
?>