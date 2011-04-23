<div id="tab_vcs_checkins_pane" style="padding-top: 0; margin: 0 5px 0 5px; display: none;">
	<div id="viewissue_commits">
		<?php
		if ($items === false)
		{
			echo '<div class="no_items">' . __('There are no code checkins for this issue') . '</div>';
		}

		?>