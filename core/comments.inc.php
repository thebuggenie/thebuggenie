<?php

	if ($canViewComments || $canEditComments)
	{
		if (isset($fromwhen))
		{
			$theComments = TBGComment::getComments($target_id, $target_type, $module, $fromwhen);
			TBGContext::setLoadedAt();
		}
		else
		{
			$theComments = TBGComment::getComments($target_id, $target_type);
		}
		$filteredComments = 0;

		foreach ($theComments as $aComment)
		{
			echo '<div id="comment_' . $aComment->getID() . '">';
			require TBGContext::getIncludePath() . 'include/comment_box.inc.php';
			if ($notFiltered)
			{
				echo '</div>';
			}
		}
	}
	
?>