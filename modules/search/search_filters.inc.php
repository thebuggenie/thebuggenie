<?php

	$allFilters = BUGScontext::getModule('search')->getFilters();

	/*
	 *
	 * filter_types
	 * 1: is closed/open
	 * 2: is/is_not with values
	 * 3: is lower than/equals/is higher than with values
	 * 4: before/on/after *date*
	 * 5: containing/not containing/starting with/ending with *text*
	 *
	 */

	#print count($appliedFilters);
	#exit;

	$fcc = 0;
	foreach ($appliedFilters as $aFilter)
	{
		if (!$savedsearch)
		{
			?>
			<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="search.php" enctype="multipart/form-data" method="post" onsubmit="return false;" id="update_filter_<?php echo $fcc; ?>">
			<input type="hidden" name="custom_search" value="true">
			<input type="hidden" name="filter_cc" value="<?php echo $fcc; ?>">
			<?php
		}
		?>
		<table style="margin-top: 5px; table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0 id="filter_row_<?php echo $fcc; ?>">
		<?php
		switch ($aFilter['filter_field'])
		{
			case 'B2tIssues::TITLE':
			case 'B2tIssues::LONG_DESCRIPTION':
			case 'B2tComments::CONTENT':
				break;
			default:
				$row = B2DB::getTable('B2tSearchFilters')->doSelectById($aFilter['id']);
				$shortname = $row->get(B2tSearchFilters::SHORT_NAME);
				print '<tr>';
				print '<td style="width: 150px; padding: 3px;';
				if ($aFilter['value'] == "" || $aFilter['operator'] == "")
				{
					print ' color: #C55; background-color: #FEE; font-weight: bold;';
				}
				print '">' . __($shortname) . '</td>';
				switch ($aFilter['filter_type'])
				{
					case 1:
						print '<td style="width: 100px;';
						if ($aFilter['value'] == "" || $aFilter['operator'] == "")
						{
							print ' color: #C55; background-color: #FEE; font-weight: bold;';
						}
						print '">' . __('is') . '</td>';
						print '<td style="width: 250px;';
						if ($aFilter['value'] == "" || $aFilter['operator'] == "")
						{
							print ' color: #C55; background-color: #FEE; font-weight: bold;';
						}
						print '">';
						if ($savedsearch)
						{
							print ($aFilter['value'] == 0) ? __('Open') : __('Closed');
						}
						else
						{
							print '<input type="hidden" name="operator" value="B2DBCriteria::DB_EQUALS">';
							print '<select style="width: 100%;" name="value">';
							print '<option value=0';
							print ($aFilter['value'] == 0) ? ' selected' : '';
							print '>' . __('Open') . '</option>';
							print '<option value=1';
							print ($aFilter['value'] == 1) ? ' selected' : '';
							print '>' . __('Closed') . '</option>';
							print '</select>';
						}
						print '</td>';
						break;
					case 2:
						print '<td style="width: 100px;';
						if ($aFilter['value'] == "" || $aFilter['operator'] == "")
						{
							print ' color: #C55; background-color: #FEE; font-weight: bold;';
						}
						print '">';
						if ($savedsearch)
						{
							print ($aFilter['operator'] == 'B2DBCriteria::DB_EQUALS') ? __('is') : __('is not');
						}
						else
						{
							print '<select style="width: 100%;" name="operator">';
							print '<option value="B2DBCriteria::DB_EQUALS"';
							print ($aFilter['operator'] == 'B2DBCriteria::DB_EQUALS') ? ' selected' : '';
							print '>' . __('is') . '</option>';
							print '<option value="B2DBCriteria::DB_NOT_EQUALS"';
							print ($aFilter['operator'] == 'B2DBCriteria::DB_NOT_EQUALS') ? ' selected' : '';
							print '>' . __('is not') . '</option>';
							print '</select>';
						}
						print '</td>';
						print '<td style="width: 250px;';
						if ($aFilter['value'] == "" || $aFilter['operator'] == "")
						{
							print ' color: #C55; background-color: #FEE; font-weight: bold;';
						}
						print '">';
						$crit = new B2DBCriteria();
						if ($savedsearch)
						{
							$crit->addWhere(constant($aFilter['value_from_field']), $aFilter['value']);
							$row = B2DB::getTable($aFilter['values_from'])->doSelectById($aFilter['value'], $crit);
							print $row->get(constant($aFilter['name_from_field']));
						}
						else
						{
							$crit = new B2DBCriteria();
							if ($aFilter['from_tbl_crit_field'] != "")
							{
								$crit->addWhere(constant($aFilter['from_tbl_crit_field']), $aFilter['from_tbl_crit_value']);
							}
							$crit->addWhere(constant($aFilter['values_from'] . '::SCOPE'), BUGScontext::getScope()->getID());
							
							$resultset = B2DB::getTable($aFilter['values_from'])->doSelect($crit);
							print '<select style="width: 100%;" name="value">';
							if ($aFilter['includes_notset'] == 1)
							{
								print '<option value=' . $aFilter['notset_value'];
								print ($aFilter['value'] == $aFilter['notset_value']) ? ' selected' : '';
								print '>' . __($aFilter['notset_description']) . '</option>';
							}
							while ($row = $resultset->getNextRow())
							{
								$value_field = $aFilter['value_from_field'];
								$dodisplay = true;
								if ($aFilter['id'] == 2)
								{
									if (!(BUGScontext::getUser()->hasPermission('b2projectaccess', $row->get(constant($value_field)), 'core')))
									{
										$dodisplay = false;
									}
								}
								if ($aFilter['id'] == 14)
								{
									if (!(BUGScontext::getUser()->hasPermission('b2buildaccess', $row->get(constant($value_field)), 'core')))
									{
										$dodisplay = false;
									}
								}
								if ($aFilter['id'] == 12)
								{
									if (!(BUGScontext::getUser()->hasPermission('b2editionaccess', $row->get(constant($value_field)), 'core')))
									{
										$dodisplay = false;
									}
								}
								if ($dodisplay)
								{
									$name_field = $aFilter['name_from_field'];
									print '<option value=' . $row->get(constant($value_field));
									print ($aFilter['value'] == $row->get(constant($value_field))) ? ' selected' : '';
									if ($aFilter['id'] == 15)
									{
										print '>';
										$component_details = BUGSfactory::componentLab($row->get(constant($value_field)));
										print $component_details->getName() . ' (' . $component_details->getProject()->getName() . ')</option>';
									}
									elseif ($aFilter['id'] == 14)
									{
										$build_details = BUGSfactory::buildLab($row->get(constant($value_field)));
										print '>' . $build_details . ' (' . $build_details->getProject()->getName() . ' - ' . $build_details->getEdition()->getName() . ')</option>';
									}
									elseif ($aFilter['id'] == 12)
									{
										$edition_details = BUGSfactory::editionLab($row->get(constant($value_field)));
										print '>' . $edition_details->getName() . ' (' . $edition_details->getProject()->getName() . ')</option>';  
									}
									else
									{
										print '>' . $row->get(constant($name_field)) . '</option>';
									}
								}
							}
							print '</select>';
						}
						print '</td>';
						break;
					case 3:
						print '<td style="width: 100px;';
						if ($aFilter['value'] == "" || $aFilter['operator'] == "")
						{
							print ' color: #C55; background-color: #FEE; font-weight: bold;';
						}
						print '">';
						if ($savedsearch)
						{
							print ($aFilter['operator'] == 'B2DBCriteria::DB_LESS_THAN') ? __('is less than') : '';
							print ($aFilter['operator'] == 'B2DBCriteria::DB_EQUALS') ? __('equals') : '';
							print ($aFilter['operator'] == 'B2DBCriteria::DB_GREATER_THAN') ? __('is more than') : '';
						}
						else
						{
							print '<select style="width: 100%;" name="operator">';
							print '<option value="B2DBCriteria::DB_LESS_THAN"';
							print ($aFilter['operator'] == 'B2DBCriteria::DB_LESS_THAN') ? ' selected' : '';
							print '>' . __('is less than') . '</option>';
							print '<option value="B2DBCriteria::DB_GREATER_THAN"';
							print ($aFilter['operator'] == 'B2DBCriteria::DB_GREATER_THAN') ? ' selected' : '';
							print '>' . __('is more than') . '</option>';
							print '</select>';
						}
						print '</td>';
						print '<td style="width: 250px;';
						if ($aFilter['value'] == "" || $aFilter['operator'] == "")
						{
							print ' color: #C55; background-color: #FEE; font-weight: bold;';
						}
						print '">';
						print ($savedsearch) ? $aFilter['value'] : '<input type="text" name="value" style="width: ' . ($aFilter['value_length'] * 10) . 'px;" value="' . $aFilter['value'] . '">';
						print '</td>';
						break;
					case 4:
						if ($aFilter['value'])
						{
							list ($this_day, $this_month, $this_year) = explode(',', date('d,m,Y', $aFilter['value']));
						}
						else
						{
							list ($this_day, $this_month, $this_year) = explode(',', date('d,m,Y'));
						}
						print '<td style="width: 100px;';
						if ($aFilter['value'] == "" || $aFilter['operator'] == "")
						{
							print ' color: #C55; background-color: #FEE; font-weight: bold;';
						}
						print '">';
						if ($savedsearch)
						{
							print ($aFilter['operator'] == 'B2DBCriteria::DB_LESS_THAN') ? __('before') : '';
							print ($aFilter['operator'] == 'B2DBCriteria::DB_EQUALS') ? __('at') : '';
							print ($aFilter['operator'] == 'B2DBCriteria::DB_GREATER_THAN') ? __('after') : '';
						}
						else
						{
							print '<select style="width: 100%;" name="operator">';
							print '<option value="B2DBCriteria::DB_LESS_THAN"';
							print ($aFilter['operator'] == 'B2DBCriteria::DB_LESS_THAN') ? ' selected' : '';
							print '>' . __('before') . '</option>';
							print '<option value="B2DBCriteria::DB_EQUALS"';
							print ($aFilter['operator'] == 'B2DBCriteria::DB_EQUALS') ? ' selected' : '';
							print '>' . __('at') . '</option>';
							print '<option value="B2DBCriteria::DB_GREATER_THAN"';
							print ($aFilter['operator'] == 'B2DBCriteria::DB_GREATER_THAN') ? ' selected' : '';
							print '>' . __('after') . '</option>';
							print '</select>';
						}
						print '</td>';
						print '<td style="width: 250px;';
						if ($aFilter['value'] == "" || $aFilter['operator'] == "")
						{
							print ' color: #C55; background-color: #FEE; font-weight: bold;';
						}
						print '">';
						if ($savedsearch)
						{
							echo bugs_formatTime($aFilter['value']);
						}
						else
						{
							echo '<select name="month_' . $fcc . '">';
							for ($cc = 1; $cc <= 12; $cc++)
							{
								echo '<option value="' . $cc . '"';
								if ($cc == $this_month) echo ' selected';
								echo '>' . bugs_formatTime(mktime(0, 0, 0, $cc, 1), 15) . '</option>';
							}
							echo '</select>';
							echo '<select name="day_' . $fcc . '">';
							for ($cc = 1; $cc <= 31; $cc++)
							{
								echo '<option value="' . $cc . '"';
								if ($cc == $this_day) echo ' selected';
								echo '>' . $cc . '</option>';
							}
							echo '</select>';
							echo '<select name="year_' . $fcc .'">';
							for ($cc = date('Y') - 2; $cc <= date('Y') + 4; $cc++)
							{
								echo '<option value="' . $cc . '"';
								if ($cc == $this_year) echo ' selected';
								echo '>' . $cc . '</option>';
							}
							echo '</select>';
						}
						print '</td>';
						break;
					case 5:
						print '<td style="width: 100px;';
						if ($aFilter['value'] == "" || $aFilter['operator'] == "")
						{
							print ' color: #C55; background-color: #FEE; font-weight: bold;';
						}
						print '">' . __('is') . '</td>';
						print '<td style="width: 250px;';
						if ($aFilter['value'] == "" || $aFilter['operator'] == "")
						{
							print ' color: #C55; background-color: #FEE; font-weight: bold;';
						}
						print '">';
						if ($savedsearch)
						{
							print ($aFilter['value'] == 0) ? 'False' : 'True';
						}
						else
						{
							print '<input type="hidden" name="operator" value="B2DBCriteria::DB_EQUALS">';
							print '<select style="width: 100%;" name="value">';
							print '<option value=0';
							print ($aFilter['value'] == 0) ? ' selected' : '';
							print '>' . __('False') . '</option>';
							print '<option value=1';
							print ($aFilter['value'] == 1) ? ' selected' : '';
							print '>' . __('True') . '</option>';
							print '</select>';
						}
						print '</td>';
						break;
				}
				if (!$savedsearch)
				{
					print '<td style="padding-left: 5px; width: 100px;';
					if ($aFilter['value'] == "" || $aFilter['operator'] == "")
					{
						print ' color: #C55; background-color: #FEE; font-weight: bold;';
					}
					print '"><button onclick="updateFilter(' . $fcc . ');" style="width: 100%;">' . __('Set/Update') . '</button></td>';
					print '<td style="padding-left: 5px; width: 70px;';
					if ($aFilter['value'] == "" || $aFilter['operator'] == "")
					{
						print ' color: #C55; background-color: #FEE; font-weight: bold;';
					}
					print '"><a href="javascript:void(0);" onclick="removeFilter(' . $fcc . ');" style="width: 100%;">' . __('Remove') . '</a></td>';
				}
				print '<td style="padding: 5px; width: auto;';
				if ($aFilter['value'] == "" || $aFilter['operator'] == "")
				{
					print ' color: #C55; background-color: #FEE; font-weight: bold;';
				}
				print '">' . __($aFilter['description']) . '</td>';
				print '</tr>';
		}
		$fcc++;
		?></table><?php
		
		if (!$savedsearch)
		{
			?></form><?php
		}
	}

	if (!$savedsearch)
	{
		?><form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="search.php" enctype="multipart/form-data" method="post" id="add_filter" onsubmit="return false">
		<input type="hidden" name="custom_search" value="true">
		<table style="margin-top: 5px; table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
			<tr>
			<td style="width: 250px; padding: 3px; padding-top: 10px;"><?php echo __('Add search criterion'); ?></td>
			<td style="width: 350px; padding-top: 10px;" colspan=2>
			<select name="add_filter" style="width: 100%;">
			<?php
		
			foreach ($allFilters as $aFilter)
			{
				$hasbeenapplied = false;
				foreach ($appliedFilters as $anAppliedFilter)
				{
					if ($aFilter instanceof B2DBRow)
					{
						if ($anAppliedFilter['id'] == $aFilter->get(B2tSearchFilters::ID) && $aFilter->get(B2tSearchFilters::FILTER_UNIQUE) == 1)
						{
							$hasbeenapplied = true;
						}
					}
					else
					{
						if ($anAppliedFilter['id'] == $aFilter['id'] && $aFilter['filter_unique'] == 1)
						{
							$hasbeenapplied = true;
						}
					}
				}
				if (!$hasbeenapplied)
				{
					if ($aFilter instanceof B2DBRow)
					{
						print '<option value=' . $aFilter->get(B2tSearchFilters::ID) . '>';
						print __($aFilter->get(B2tSearchFilters::SHORT_NAME)) . ' - ' . __($aFilter->get(B2tSearchFilters::DESCRIPTION));
						print '</option>';
					}
					else
					{
						print var_dump($aFilter);
						print '<option value=' . $aFilter['id'] . '>';
						print __($aFilter['short_name']) . ' - ' . __($aFilter['description']);
						print '</option>';
					}
				}
			}
		
			?>
			</select>
			</td>
			<td style="width: auto; text-align: left; padding-left: 5px; padding-top: 10px;"><button onclick="addFilter();"><?php echo __('Add this'); ?></button></td>
			</tr>
		</table>
		</form><?php
	}

?>