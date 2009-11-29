<?php

	define ('THEBUGGENIE_PATH', '../../');
	$page = "search";

	require THEBUGGENIE_PATH . 'include/checkcookie.inc.php';
	require THEBUGGENIE_PATH . 'include/b2_engine.inc.php';
	
	require BUGScontext::getIncludePath() . "include/ui_functions.inc.php";
	
	if (BUGScontext::getRequest()->getParameter('normal_mode'))
	{
		unset($_SESSION['show_printfriendly']);
	}
	
	if (BUGScontext::getRequest()->getParameter('show_printfriendly') || $_SESSION['show_printfriendly'])
	{
		$print_friendly = true;
		$stripmode = true;
		$_SESSION['show_printfriendly'] = true;
	}
	else
	{
		$print_friendly = false;
	}
	
	if (!BUGScontext::getRequest()->isAjaxCall())
	{
		require BUGScontext::getIncludePath() . "include/header.inc.php";
		require BUGScontext::getIncludePath() . "include/menu.inc.php";
	}
	
	BUGScontext::getModule('search')->activate();

	require BUGScontext::getIncludePath() . 'modules/search/search_logic.inc.php';
	
	if ($print_friendly)
	{
		$nm_link = 'search.php?normal_mode=true';
		if ($savedsearch)
		{
			$nm_link .= '&perform_search=true&saved_search=true&s_id=' . $sid;
		}
		else
		{
			$nm_link .= '&perform_search=true&custom_search=true';
		}
		echo bugs_printmodeStrip($nm_link);
	}
	
	if (!$print_friendly)
	{
		?>
		<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
		<tr>
		<td style="width: 255px;" valign="top">
		<div style="margin-top: 0px;">
		<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
		<tr>
		<td class="b2_section_miniframe_header"><?php echo __('Saved searches'); ?></td>
		</tr>
		<tr>
		<td class="td1">
		<?php echo __('Select one of the saved searches below to display results from that search'); ?>
		<div style="padding-top: 5px; border-bottom: 1px solid #DDD; font-weight: bold;"><?php echo __('General searches'); ?></div>
		<table cellpadding=0 cellspacing=0 style="margin-top: 2px;">
		<?php
		
		$savedsearches = BUGScontext::getModule('search')->getSavedSearches();

		if (count($savedsearches) > 0)
		{
			foreach ($savedsearches as $asavedsearch)
			{
				?><div style="padding: 2px;"><a href="search.php?saved_search=true&amp;s_id=<?php echo $asavedsearch['id']; ?>"><?php echo $asavedsearch['name']; ?></a></div><?php
			}
		}
		else
		{
			?><div style="padding: 2px; color: #AAA;"><?php echo __('There are no saved searches to display'); ?></div><?php
		}
		
		?>
		</table>
		<div style="padding-top: 10px; border-bottom: 1px solid #DDD; font-weight: bold;"><?php echo __('Project-specific searches'); ?></div>
		<?php
		
		$sscc = 0;
		foreach (BUGSproject::getAll() as $aProject)
		{
			$savedsearches = BUGScontext::getModule('search')->getSavedSearches($aProject['id']);
	
			if (count($savedsearches) > 0)
			{
				foreach ($savedsearches as $asavedsearch)
				{
					?><div style="padding: 2px;"><a href="search.php?saved_search=true&amp;s_id=<?php echo $asavedsearch['id']; ?>"><?php echo $asavedsearch['name']; ?></a></div><?php
					$sscc++;
				}
			}
		}
		
		if ($sscc == 0)
		{
			?><div style="padding: 2px; color: #AAA;"><?php echo __('There are no saved searches to display'); ?></div><?php
		}
			
		?>
		</td>
		</tr>
		</table>
		<?php
		
			if ($savedsearch)
			{
				?>
				<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
				<tr>
				<td class="b2_section_miniframe_header"><?php echo __('Saved search actions'); ?></td>
				</tr>
				<tr>
				<td class="td1">
				<?php echo __('To use this saved search as a starting point for your own, customized search, select below'); ?>
				<table cellpadding=0 cellspacing=0 style="margin-top: 10px;">
				<tr>
				<td style="width: 20px; text-align: right;" align="right"><?php echo image_tag('search_useasstartingpoint.png', '', '[S]', '[S]'); ?></td>
				<td style="padding: 2px; width: auto; vertical-align: top;"><a href="search.php?saved_search=true&amp;s_id=<?php print BUGScontext::getRequest()->getParameter('s_id'); ?>&amp;set_startpoint=true"><?php echo __('Use as starting point'); ?></a></td>
				</tr>
				<?php
		
					if ((BUGScontext::getUser()->getUID() != 0) && ((BUGScontext::getUser()->getUname() != BUGSsettings::get('defaultuname')) || (BUGSsettings::get('defaultisguest') == 0)))
					{
						if (BUGScontext::getModule('search')->getSavedSearchUid($sid) != BUGScontext::getUser()->getUID())
						{
							?>
							<tr>
							<td style="width: 20px; text-align: right;" align="right"><?php echo image_tag('search_saveaspersonalsearch.png'); ?></td>
							<td style="padding: 2px; width: auto; vertical-align: top;"><a href="javascript:void(0);"><?php echo __('Save this search in your account'); ?></a></td>
							</tr>
							<?php
						}
						if (BUGScontext::getUser()->hasPermission('b2searchmaster', 1, 'search') || BUGScontext::getUser()->hasPermission('b2cancreatepublicsearches', 1, 'search'))
						{
							?>
							</table>
							<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="search.php" method="post">
							<input type="hidden" name="saved_search" value="true">
							<input type="hidden" name="rename_search" value="true">
							<input type="hidden" name="s_id" value="<?php print $sid; ?>">
							<?php $closeform = true; ?>
							<table cellpadding=0 cellspacing=0 style="margin-top: 10px;">
							<tr>
							<td style="width: 20px; text-align: right;"><?php echo image_tag('search_savedsearchtitle.png', '', '[S]', '[S]'); ?></td>
							<td style="padding: 2px; width: auto; vertical-align: top;"><a href="javascript:void(0);" onclick="showHide('savedsearch_title');"><?php echo __('Give this saved search a new name'); ?></a></td>
							</tr>
							<tr style="display: none;" id="savedsearch_title">
							<td style="width: 20px; padding-top: 5px; vertical-align: top; text-align: right;" align="right"><?php echo image_tag('icon_title.png', '', '[S]', '[S]'); ?></td>
							<td style="padding: 2px; width: auto;"><input type="text" name="saved_search_title" value="<?php echo stripslashes(B2DB::getTable('B2tSavedSearches')->doSelectById($sid)->get(B2tSavedSearches::NAME)); ?>" style="width: 100%;">
							<div style="padding-top: 5px; padding-bottom: 5px;"><?php echo __('Enter a new name for this search and click the "Update"-button.'); ?></div>
							<div style="text-align: right;"><input type="submit" value="<?php echo __('Update'); ?>"></div></td>
							</tr>
							<?php
						}
					}
		
				?>
				</table>
				<?php if (isset($closeform)): ?>
					</form>
				<?php endif; ?>
				<?php
		
					if (BUGScontext::getModule('search')->isPublic($sid) && BUGScontext::getModule('search')->getSavedSearchUid($sid) == BUGScontext::getUser()->getUID())
					{
						?><div style="margin-top: 8px;"><?php echo __('This saved search is public'); ?></div>
						<table cellpadding=0 cellspacing=0 style="margin-top: 10px;">
						<tr>
						<td style="width: 20px; text-align: right;" align="right"><?php echo image_tag('search_makesavedsearchprivate.png', '', '[S]', '[S]'); ?></td>
						<td style="padding: 2px; width: auto; vertical-align: top;"><a href="search.php?saved_search=true&amp;s_id=<?php print $sid; ?>&amp;make_private=true"><?php echo __('Make this saved search private'); ?></a></td>
						</tr>
						</table>
						<?php
					}
					elseif (BUGScontext::getModule('search')->isPublic($sid) == false && (BUGScontext::getUser()->hasPermission("b2cancreatepublicsearches", 1, "search") || BUGScontext::getUser()->hasPermission("b2searchmaster", 1, "search")))
					{
						?><div style="margin-top: 8px;"><?php echo __('This saved search is private'); ?></div>
						<table cellpadding=0 cellspacing=0 style="margin-top: 10px;">
						<tr>
						<td style="width: 20px; text-align: right;" align="right"><?php echo image_tag('search_makesavedsearchprivate.png', '', '[S]', '[S]'); ?></td>
						<td style="padding: 2px; width: auto; vertical-align: top;"><a href="search.php?saved_search=true&amp;s_id=<?php print $sid; ?>&amp;make_public=true"><?php echo __('Make this saved search public'); ?></a></td>
						</tr>
						</table>
						<?php
					}
					if (BUGScontext::getUser()->hasPermission('b2searchmaster', 1, 'search') || BUGScontext::getUser()->hasPermission('b2cancreatepublicsearches', 1, 'search'))
					{
						?>
						<table cellpadding=0 cellspacing=0 style="margin-top: 10px;">
						<tr>
						<td style="width: 20px; text-align: right;" align="right"><?php echo image_tag('search_deletesavedsearch.png', '', '[S]', '[S]'); ?></td>
						<td style="padding: 2px; width: auto; vertical-align: top;"><a href="search.php?saved_search=true&amp;s_id=<?php print $sid; ?>&amp;remove_savedsearch=true"><?php echo __('Remove this saved search'); ?></a></td>
						</tr>
						</table>
						<?php
					}
					
				?>
				<table cellpadding=0 cellspacing=0 style="margin-top: 10px;">
				<tr>
				<td style="width: 20px; text-align: right;" align="right"><?php echo image_tag('icon_printer.png'); ?></td>
				<td style="padding: 2px; width: auto; vertical-align: top;"><a href="search.php?saved_search=true&amp;s_id=<?php print $sid; ?>&amp;show_printfriendly=true"><?php echo __('Switch to print friendly view'); ?></a></td>
				</tr>
				</table>
				</td>
				</tr>
				</table>
				<?php
			}
			elseif ($showsearch)
			{
				?>
				<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
				<tr>
				<td class="b2_section_miniframe_header"><?php echo __('Custom search actions'); ?></td>
				</tr>
				<tr>
				<td class="td1">
				<?php
		
					if ((BUGScontext::getUser()->getUID() != 0) && ((BUGScontext::getUser()->getUname() != BUGSsettings::get('defaultuname')) || (BUGSsettings::get('defaultisguest') == 0)) && !$notcompleteFilters)
					{
						if (isset($search_issaved) && $search_issaved == true)
						{
							echo __('The search was saved to your account');
						}
						elseif (count($appliedFilters) > 0)
						{
							?>
							<?php echo __('If you want to save this custom search so that it is available later, you can do it from here.'); ?>
							<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="search.php" method="post">
							<input type="hidden" name="save_search" value="true">
							<input type="hidden" name="custom_search" value="true">
							<table cellpadding=0 cellspacing=0 style="margin-top: 10px; margin-bottom: 10px;">
							<tr>
							<td style="width: 20px; text-align: right;" align="right"><?php echo image_tag('search_saveaspersonalsearch.png', '', '[S]', '[S]'); ?></td>
							<td style="padding: 2px; width: auto;"><a href="javascript:void(0);" onclick="showHide('savedsearch_title');"><?php echo __('Save this search in your account'); ?></a></td>
							</tr>
							<tr style="display: none;" id="savedsearch_title">
							<td style="width: 20px; padding-top: 5px; vertical-align: top; text-align: right;" align="right"><?php echo image_tag('search_savedsearchtitle.png', '', '[S]', '[S]'); ?></td>
							<td style="padding: 2px; width: auto;"><input type="text" name="saved_search_title" style="width: 100%;">
							<div style="padding-top: 5px; padding-bottom: 5px;"><?php echo __('Enter a title for this search and click the "Save search"-button.'); ?></div>
							<div style="text-align: right;"><input type="submit" value="<?php echo __('Save search'); ?>"></div></td>
							</tr>
							</table>
							</form>
							<?php
						}
						?>
						<table cellpadding=0 cellspacing=0>
						<tr>
						<td style="width: 20px; text-align: right;" align="right"><?php echo image_tag('icon_printer.png'); ?></td>
						<td style="padding: 2px; width: auto; vertical-align: top;"><a href="search.php?custom_search=true&amp;perform_search=true&amp;show_printfriendly=true"><?php echo __('Switch to print friendly view'); ?></a></td>
						</tr>
						</table>
						<?php
					}
					elseif ($notcompleteFilters)
					{
						echo __('Make sure all search parameters are set');
					}
					else
					{
						?><div style="color: #CCC;"><?php echo __('Not available for unregistered users'); ?></div><?php
					}
		
				?>
				</td>
				</tr>
				</table>
				<?php
			}
		
		?>
		</div>
		</td>
		<td valign="top" align="left" style="padding-right: 10px;">
		<table class="welcomestrip" cellpadding=0 cellspacing=0>
		<tr>
		<td class="wleft"><b><?php echo __('Text search'); ?></b></td>
		<?php
		
			if ($showsearch)
			{
				?>
				<td class="wright" style="width: 100px;"><a href="javascript:void(0);" onclick="javascript:showHide('qs_table');"><?php echo __('Expand / Collapse'); ?></a></td>
				<?php
			}
		
		?>
		</tr>
		</table>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="search.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="simplesearch" value="true">
		<div style="<?php print ($showsearch) ? "display: none;" : ""; ?>" id="qs_table">
		<table style="margin-top: 5px; table-layout: auto; width: 100%;" cellpadding=0 cellspacing=0>
		<tr>
		<td style="width: 100px;"><?php echo __('Look through'); ?></td>
		<td style="width: 250px;"><select style="width: 100%;" name="lookthrough">
		<option value="all"><?php echo __('all common text fields'); ?></option>
		<option value="all_notcomments"><?php echo __('all common text fields except comments'); ?></option>
		<option value="title"><?php echo __('issue titles'); ?></option>
		<option value="desc"><?php echo __('issue descriptions'); ?></option>
		<option value="comments"><?php echo __('issue comments'); ?></option>
		</select>
		</td>
		<td style="width: 100px; text-align: right; padding-right: 10px;"><?php echo __('for this text:'); ?></td>
		<td style="width: auto;"><input type="text" style="width: 100%;" name="searchfor" value="<?php print ($matchfor != '') ? $matchfor : ""; ?>"></td>
		<td style="width: 10px;">&nbsp;</td>
		<td style="width: 50px;"><input type="submit" value="<?php echo __('Find'); ?>" class="button" style="width: 100%;"<?php print ($notcompleteFilters) ? " disabled" : ""; ?>></td>
		</tr>
		<?php
		
			if (BUGScontext::getModule('search')->getNumberOfLayouts() != 1)
			{
				$searchlayouts = BUGScontext::getModule('search')->getLayouts();
				?>
				<tr>
				<td colspan=3 style="text-align: right; padding-right: 10px;"><?php echo __('For this search, use this layout:'); ?></td>
				<td colspan=3 style="width: auto;"><select name="searchlayout" style="width: 100%;">
				<?php
					
					foreach ($searchlayouts as $aLayout)
					{ 
						?><option value=<?php echo $aLayout['id']; ?><?php echo ($_SESSION['searchlayout'] == $aLayout['id']) ? ' selected' : ''; ?>><?php echo $aLayout['name']; ?></option><?php
					}
				
				?>
				</select>
				</td>
				</tr>
				<?php
			}
		
		?>
		<tr>
		<td colspan=3 style="text-align: right; padding-right: 10px;"><?php echo __('Use the following grouping for this search:'); ?></td>
		<td colspan=3 style="width: auto;"><select name="groupby" style="width: 100%;">
		<option value="" <?php echo ($_SESSION['groupby'] == "") ? ' selected' : ''; ?>><?php echo __('No grouping'); ?></option>
		<option value="project" <?php echo ($_SESSION['groupby'] == "project") ? ' selected' : ''; ?>><?php echo __('Group by project'); ?></option>
		<option value="edition" <?php echo ($_SESSION['groupby'] == "edition") ? ' selected' : ''; ?>><?php echo __('Group by edition'); ?></option>
		<option value="component" <?php echo ($_SESSION['groupby'] == "component") ? ' selected' : ''; ?>><?php echo __('Group by component'); ?></option>
		<option value="milestone" <?php echo ($_SESSION['groupby'] == "milestone") ? ' selected' : ''; ?>><?php echo __('Group by milestone'); ?></option>
		<option value="assignee" <?php echo ($_SESSION['groupby'] == "assignee") ? ' selected' : ''; ?>><?php echo __('Group by who\'s assigned'); ?></option>
		<option value="issuetype" <?php echo ($_SESSION['groupby'] == "issuetype") ? ' selected' : ''; ?>><?php echo __('Group by issue type'); ?></option>
		<option value="severity" <?php echo ($_SESSION['groupby'] == "severity") ? ' selected' : ''; ?>><?php echo __('Group by severity'); ?></option>
		<option value="priority" <?php echo ($_SESSION['groupby'] == "priority") ? ' selected' : ''; ?>><?php echo __('Group by priority'); ?></option>
		<option value="state" <?php echo ($_SESSION['groupby'] == "state") ? ' selected' : ''; ?>><?php echo __('Group by state (open or closed)'); ?></option>
		</select>
		</td>
		</tr>
		</table>
		</div>
		</form>
		<table class="welcomestrip" cellpadding=0 cellspacing=0>
		<tr>
		<td class="wleft"><?php print ($showsearch) ? '<b>' . __('Applied search filters') : (($editingsavedsearch) ? __('Editing saved search:') . ' <b>' . strtoupper($savedsearchtitle) : '<b>' . __('Customize search with more options')) . '</b>'; ?></td>
		<td class="wright" style="width: 80px;"><a href="javascript:void(0);" onclick="javascript:showHide('filter_table');"><?php echo __('Define filters'); ?></a></td>
		</tr>
		</table>
		<?php
	} 
?>
<div style="<?php print ($showsearch) ? "display: none;" : ""; ?>" id="filter_table">
<?php require_once BUGScontext::getIncludePath() . 'modules/search/search_filters.inc.php'; ?>
</div>
<?php

	if (!$savedsearch)
	{
		?>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="search.php" method="post" enctype="multipart/form-data" id="submit_search">
		<input type="hidden" name="custom_search" value="true">
		<table style="margin-top: 5px; table-layout: fixed; width: 500px;" cellpadding=0 cellspacing=0>
		<?php
			
			if (BUGScontext::getModule('search')->getNumberOfLayouts() != 1)
			{
				$searchlayouts = BUGScontext::getModule('search')->getLayouts();
				?>
				<tr>
				<td style="padding: 3px; padding-top: 10px; width: 150px;"><?php echo __('Use layout:'); ?></td>
				<td style="width: auto; padding-top: 10px;">
				<select name="searchlayout" style="width: 100%;">
				<?php
					
				foreach ($searchlayouts as $aLayout)
				{ 
					?><option value=<?php echo $aLayout['id']; ?><?php echo ($_SESSION['searchlayout'] == $aLayout['id']) ? ' selected' : ''; ?>><?php echo __($aLayout['name']); ?></option><?php
				}
				
				?>
				</select>
				</td>
				</tr>
				<?php
			}
			
		?>
		<tr>
		<td style="text-align: left; padding: 3px;"><?php echo __('Use grouping:'); ?></td>
		<td style="width: auto;">
		<select name="groupby" style="width: 100%;">
		<option value="" <?php echo ($_SESSION['groupby'] == "") ? ' selected' : ''; ?>><?php echo __('No grouping'); ?></option>
		<option value="project" <?php echo ($_SESSION['groupby'] == "project") ? ' selected' : ''; ?>><?php echo __('Group by project'); ?></option>
		<option value="edition" <?php echo ($_SESSION['groupby'] == "edition") ? ' selected' : ''; ?>><?php echo __('Group by edition'); ?></option>
		<option value="component" <?php echo ($_SESSION['groupby'] == "component") ? ' selected' : ''; ?>><?php echo __('Group by component'); ?></option>
		<option value="milestone" <?php echo ($_SESSION['groupby'] == "milestone") ? ' selected' : ''; ?>><?php echo __('Group by milestone'); ?></option>
		<option value="assignee" <?php echo ($_SESSION['groupby'] == "assignee") ? ' selected' : ''; ?>><?php echo __('Group by who\'s assigned'); ?></option>
		<option value="issuetype" <?php echo ($_SESSION['groupby'] == "issuetype") ? ' selected' : ''; ?>><?php echo __('Group by issue type'); ?></option>
		<option value="severity" <?php echo ($_SESSION['groupby'] == "severity") ? ' selected' : ''; ?>><?php echo __('Group by severity'); ?></option>
		<option value="priority" <?php echo ($_SESSION['groupby'] == "priority") ? ' selected' : ''; ?>><?php echo __('Group by priority'); ?></option>
		<option value="state" <?php echo ($_SESSION['groupby'] == "state") ? ' selected' : ''; ?>><?php echo __('Group by state (open or closed)'); ?></option>
		</select>
		</td>
		</tr>
		<tr id="search_button"><?php require_once BUGScontext::getIncludePath() . 'modules/search/search_button.inc.php'; ?></tr>
		</table>
		</form>
		<?php
	}

	?><div id="search_results"><?php
	
	if ($print_friendly)
	{
		echo '<table style="width: 100%;" cellpadding=0 cellspacing=0><tr><td style="padding: 5px; text-align: left; padding-top: 0px;">';
	}
	elseif ($savedsearch)
	{
		?><div style="padding: 3px; padding-top: 10px;"><?php echo __('To edit filters for this saved search, select "Edit this saved search" from the left menu'); ?><br>
		<?php echo __('You can use this search as a starting point for your own search by selecting "Use as starting point" from the left menu, or you could '); ?><a href="search.php"><b><?php echo __('start from scratch'); ?></b></a></div><?php
	}

	if ($savedsearch)
	{
		$searchresults = BUGScontext::getModule('search')->doSearch($sid, false, true, true);
		BUGScontext::getModule('search')->presentResultsHTML(BUGScontext::getModule('search')->getLayoutFromSearch($sid), $searchresults['issues'], $sid, true, BUGScontext::getModule('search')->getTitleFromSearch($sid), $searchresults['groupby']);
	}
	elseif ($showsearch)
	{
		$searchresults = BUGScontext::getModule('search')->doSearch(0, false, true, false, (count($_SESSION['simplefilters']) > 0) ? true : false);
		BUGScontext::getModule('search')->presentResultsHTML($_SESSION['searchlayout'], $searchresults['issues'], 0, true, '', $_SESSION['groupby']);
	}
	
	if ($print_friendly)
	{
		echo '</td></tr></table></div>';
	}
	else
	{
		?></div>
		<div style="padding: 3px;"></div>
		</td>
		</tr>
		</table>
		<?php
	}

	require_once BUGScontext::getIncludePath() . "include/footer.inc.php";

?>