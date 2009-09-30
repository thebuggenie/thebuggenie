<td colspan=2 style="padding: 4px; padding-top: 20px;"><?php print ($editingsavedsearch) ? __('When you\'re done editing this saved search, click "Done".') : __('When you\'ve added all the search criterias you want, click "Perform search" to list results.'); ?><?php

	if ($notcompleteFilters)
	{
		?><br><br><b><?php echo __('One or more of your search criterias are incomplete.'); ?></b><br><?php echo __('Use the "Set/Update" button on the marked criterias above before trying to perform a search.');
	}
	elseif (count($_SESSION['searchfields']) == 0)
	{
		?><br><br><b><?php echo __('You need to select at least one criteria before you can search'); ?></b><?php
	}
	
	if (count($_SESSION['searchfields']) > 0 && !$notcompleteFilters)
	{
		?>
		<div style="text-align: right;">
		<input type="submit" style="width: 100px;" value="<?php echo ($editingsavedsearch) ? __('Done') : __('Perform search'); ?>">
		</div>
		<?php
	}

/*	if ($editingsavedsearch)
	{
		?><td style="padding-top: 20px;">&nbsp;<?php echo __('%done% or %cancel%', array('%done%' => '', '%cancel%' => '<a href="search.php"><b>' . __('Cancel') . '</b></a>')) ?></td>
		<input type="hidden" name="edit_search" value="true">
		<input type="hidden" name="saved_search" value="true">
		<input type="hidden" name="s_id" value="<?php print $sid; ?>">
		<?php
	}*/
	 
?></td>