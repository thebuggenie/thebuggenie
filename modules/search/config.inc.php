<?php

	if (($access_level != "full" && $access_level != "read") || TBGContext::getRequest()->getParameter('access_level'))
	{
		tbg_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		if ($access_level == 'full')
		{
			$settings_arr = array('enable_opensearch', 'opensearch_title', 'opensearch_longname', 'opensearch_description', 'opensearch_contact',
								  'defaultsearchlayout', 'indexshowsavedsearches', 'indexshowtitle', 'frontpagelayout', 'showindexsummary',
								  'showindexsummarydetails', 'indexshowsavedsearches');
			foreach ($settings_arr as $setting)
			{
				if (TBGContext::getRequest()->getParameter($setting))
				{
					TBGContext::getModule('search')->saveSetting($setting, TBGContext::getRequest()->getParameter($setting));
				}
			}
			
			if (TBGContext::getRequest()->getParameter('addsavedsearch'))
			{
				$tmpsavedsearches = TBGContext::getModule('search')->getSetting('indexsearches');
				$tmpsavedsearches .= ';' . TBGContext::getRequest()->getParameter('addsavedsearch');
				TBGContext::getModule('search')->saveSetting('indexsearches', $tmpsavedsearches); 
			}
			if (is_numeric(TBGContext::getRequest()->getParameter('removesavedsearch')))
			{
				$tmpsavedsearches = explode(';', TBGContext::getModule('search')->getSetting('indexsearches'));
				$newsavedsearches = '';
				$firstsearch = true;
				foreach ($tmpsavedsearches as $aSavedsearch)
				{
					if ($aSavedsearch != TBGContext::getRequest()->getParameter('removesavedsearch'))
					{
						if (!$firstsearch)
						{
							$newsavedsearches .= ';';
						}
						$newsavedsearches .= $aSavedsearch;
						$firstsearch = false;
					}
				}
				TBGContext::getModule('search')->saveSetting('indexsearches', $newsavedsearches); 
			}
		}

		$showsearches = TBGContext::getModule('search')->getSetting('indexshowsavedsearches');
		$savedsearches = explode(";", TBGContext::getModule('search')->getSetting('indexsearches'));
		$searchdetails = array();
		foreach ($savedsearches as $aSavedsearch)
		{
			if (trim($aSavedsearch) != '')
			{
				$searchdetails[] = array('id' => $aSavedsearch, 'name' => TBGContext::getModule('search')->getSavedSearchName($aSavedsearch), 'public' => (TBGContext::getModule('search')->getSavedSearchPublic($aSavedsearch) == 1) ? true : false);
			}
		}
		$searchlayouts = TBGContext::getModule('search')->getLayouts();
		$availablesearches = array();
		$crit = new B2DBCriteria();
		$crit->addWhere(TBGSavedSearchesTable::SCOPE, TBGContext::getScope()->getID());
		$crit->addWhere(TBGSavedSearchesTable::APPLIES_TO, 0);
		$criterion = $crit->returnCriterion(TBGSavedSearchesTable::IS_PUBLIC, 1);
		$criterion->addOr(TBGSavedSearchesTable::UID, TBGContext::getUser()->getUID());
		$crit->addWhere($criterion);
		$resultset = B2DB::getTable('TBGSavedSearchesTable')->doSelect($crit);
		while ($row = $resultset->getNextRow())
		{
			$availablesearches[] = array('id' => $row->get(TBGSavedSearchesTable::ID), 'name' => $row->get(TBGSavedSearchesTable::NAME));
		}
		
		foreach (TBGProject::getAll() as $aProject)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGSavedSearchesTable::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(TBGSavedSearchesTable::APPLIES_TO, $aProject['id']);
			$criterion = $crit->returnCriterion(TBGSavedSearchesTable::IS_PUBLIC, 1);
			$criterion->addOr(TBGSavedSearchesTable::UID, TBGContext::getUser()->getUID());
			$crit->addWhere($criterion);
			$resultset = B2DB::getTable('TBGSavedSearchesTable')->doSelect($crit);
			while ($row = $resultset->getNextRow())
			{
				$availablesearches[] = array('id' => $row->get(TBGSavedSearchesTable::ID), 'name' => $row->get(TBGSavedSearchesTable::NAME));
			}
		}
		
		?>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Configure search module'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('Set up the search module here.'); ?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="defaultscopeform">
		<input type="hidden" name="module" value="search">
		<div style="margin-top: 15px; margin-bottom: 5px; padding: 2px; background-color: #F5F5F5; border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: auto;"><?php echo __('OpenSearch settings'); ?></div>
		<div style="padding: 5px; margin-bottom: 5px;"><?php echo __('OpenSearch is an open standard for integrating search functionality.'); ?> <?php echo __('By enabling OpenSearch functionality, you can search BUGS 2 issues from directly in your browsers search area.'); ?> <?php echo __('OpenSearch is supported by most major browsers, including %firefox% and Internet Explorer.', array('%firefox%' => '<a href="http://www.mozilla.com" target="_blank">Firefox</a>')); ?><br>
		<?php echo __('Read more about OpenSearch at %opensearch.org% and at %wikipedia%', array('%opensearch.org%' => '<a href="http://www.opensearch.org" target="_blank">www.opensearch.org</a>', '%wikipedia%' => '<a href="http://en.wikipedia.org/wiki/OpenSearch">Wikipedia</a>')); ?></div> 
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('Enable OpenSearch'); ?></b></td>
				<td style="width: 250px;">
					<select name="enable_opensearch" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (TBGContext::getModule('search')->getSetting('enable_opensearch') == 1) ? ' selected' : ''; ?>><?php echo __('Yes'); ?></option>
						<option value=0 <?php echo (TBGContext::getModule('search')->getSetting('enable_opensearch') == 0) ? ' selected' : ''; ?>><?php echo __('No'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select whether to enable OpenSearch functionality'); ?></td>
			</tr>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('OpenSearch title'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="opensearch_title" value="<?php echo TBGContext::getModule('search')->getSetting('opensearch_title'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The OpenSearch title'); ?></td>
			</tr>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('OpenSearch title (long)'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="opensearch_longname" value="<?php echo TBGContext::getModule('search')->getSetting('opensearch_longname'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The OpenSearch title (long version)'); ?></td>
			</tr>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('OpenSearch description'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="opensearch_description" value="<?php echo TBGContext::getModule('search')->getSetting('opensearch_description'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The OpenSearch description'); ?></td>
			</tr>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('OpenSearch contact'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="opensearch_contact" value="<?php echo TBGContext::getModule('search')->getSetting('opensearch_contact'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The OpenSearch contact email address'); ?></td>
			</tr>
		</table>
		<div style="margin-top: 15px; margin-bottom: 5px; padding: 2px; background-color: #F5F5F5; border-bottom: 1px solid #DDD; font-weight: bold; font-size: 1.0em; width: auto;"><?php echo __('General search settings'); ?></div>
		<div style="padding: 5px;"><?php echo __('General search settings'); ?></div>
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('Default search layout'); ?></b></td>
				<td style="width: 250px;">
					<select name="defaultsearchlayout" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
					<?php foreach ($searchlayouts as $aSearchlayout): ?>
						<option value=<?php echo $aSearchlayout['id']; ?> <?php echo (TBGContext::getModule('search')->getSetting('defaultsearchlayout') == $aSearchlayout['id']) ? ' selected' : ''; ?>><?php echo __($aSearchlayout['name']); ?></option>
					<?php endforeach; ?>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select which search layout to use as default for generated searches'); ?></td>
			</tr>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('Show searches on the front page'); ?></b></td>
				<td style="width: 250px;">
					<select name="indexshowsavedsearches" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (TBGContext::getModule('search')->getSetting('indexshowsavedsearches') == 1) ? ' selected' : ''; ?>><?php echo __('Yes'); ?></option>
						<option value=0 <?php echo (TBGContext::getModule('search')->getSetting('indexshowsavedsearches') == 0) ? ' selected' : ''; ?>><?php echo __('No'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select whether or not to show saved searches on the front page'); ?></td>
			</tr>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('Show search titles on the front page'); ?></b></td>
				<td style="width: 250px;">
					<select name="indexshowtitle" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (TBGContext::getModule('search')->getSetting('indexshowtitle') == 1) ? ' selected' : ''; ?>><?php echo __('Yes'); ?></option>
						<option value=0 <?php echo (TBGContext::getModule('search')->getSetting('indexshowtitle') == 0) ? ' selected' : ''; ?>><?php echo __('No'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select whether or not to show title for saved searches on the front page'); ?></td>
			</tr>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('Show search hit summary on the front page'); ?></b></td>
				<td style="width: 250px;">
					<select name="showindexsummary" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (TBGContext::getModule('search')->getSetting('showindexsummary') == 1) ? ' selected' : ''; ?>><?php echo __('Yes'); ?></option>
						<option value=0 <?php echo (TBGContext::getModule('search')->getSetting('showindexsummary') == 0) ? ' selected' : ''; ?>><?php echo __('No'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select whether or not to show the number of hits with the saved searches on the front page'); ?></td>
			</tr>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('Show search hit details on the front page'); ?></b></td>
				<td style="width: 250px;">
					<select name="showindexsummarydetails" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (TBGContext::getModule('search')->getSetting('showindexsummarydetails') == 1) ? ' selected' : ''; ?>><?php echo __('Yes'); ?></option>
						<option value=0 <?php echo (TBGContext::getModule('search')->getSetting('showindexsummarydetails') == 0) ? ' selected' : ''; ?>><?php echo __('No'); ?></option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select whether or not to show the detailed count numbers with the saved searches on the front page for grouped searches'); ?></td>
			</tr>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('Front page search layout'); ?></b></td>
				<td style="width: 250px;">
					<select name="frontpagelayout" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
					<option value=0><?php echo __('Use the layout saved with the search'); ?></option>
					<?php foreach ($searchlayouts as $aSearchlayout): ?>
						<option value=<?php echo $aSearchlayout['id']; ?> <?php echo (TBGContext::getModule('search')->getSetting('frontpagelayout') == $aSearchlayout['id']) ? ' selected' : ''; ?>><?php echo __($aSearchlayout['name']); ?></option>
					<?php endforeach; ?>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select which search layout to use as default for generated searches'); ?></td>
			</tr>
			<?php if ($access_level == 'full'): ?>
				<tr>
					<td colspan=3 style="padding: 5px; text-align: right;"><input type="submit" value="<?php echo __('Save'); ?>"></td>
				</tr>
			<?php endif; ?>
		</table>
		</form>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="defaultscopeform">
		<input type="hidden" name="module" value="search">
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('Show searches'); ?></b></td>
				<td colspan=2><?php echo __('Select which searches to show on the front page'); ?></td>
			</tr>
			<?php 
			
			$savedsearches_cc = 0;
			foreach ($searchdetails as $aSavedsearch)
			{ 
				if (trim($aSavedsearch['name']) != '')
				{
					?>
						<tr>
							<td style="width: 160px; padding: 5px;">&nbsp;</td>
							<td style="width: 250px;">
							<?php 
							
							echo $aSavedsearch['name'];
							if (!$aSavedsearch['public'])
							{
								echo '<div style="font-size: 10px;">' . __('This saved search is not public, and while users may be able to see it, errors may occur.') . '<br>';
								echo __('You should either %make_it_public%, or select another one.', array('%make_it_public%' => '<a href="' . TBGContext::getTBGPath() . 'modules/search/search.php?saved_search=true&amp;s_id=' . $aSavedsearch['id'] . '&amp;make_public=true" target="_blank">' . __('make this saved search public') . '</a>')) . '</div>';
							} 
							
							?></td>
							<td style="width: auto; padding: 5px;"><a href="config.php?module=search&amp;removesavedsearch=<?php echo $aSavedsearch['id']; ?>" style="font-size: 9px;"><?php echo __('Remove'); ?></a></td>
						</tr>
					<?php
					$savedsearches_cc++;
				}
			}
			if ($savedsearches_cc == 0)
			{
				?>
				<tr>
				<td style="width: 160px; padding: 5px;">&nbsp;</td>
				<td style="width: auto; color: #AAA;" colspan=2><?php echo __('No searches are being displayed on the front page'); ?></td>
				</tr>
				<?php
			}
			
			?>
			<tr>
				<td style="width: 160px; padding: 5px;"><b><?php echo __('Add saved search'); ?></b></td>
				<td style="width: 250px;">
					<select name="addsavedsearch" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
					<?php foreach ($availablesearches as $aSavedSearch): ?>
						<?php if (!in_array($aSavedSearch['id'], $savedsearches)): ?>
							<option value=<?php echo $aSavedSearch['id']; ?>><?php echo $aSavedSearch['name']; ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><input type="submit" value="<?php echo __('Add'); ?>"></td>
			<tr>
				<td colspan=3 style="padding: 5px; text-align: right;">&nbsp;</td>
			</tr>
		</table>
		</form>
		<?php
	}
	
?>