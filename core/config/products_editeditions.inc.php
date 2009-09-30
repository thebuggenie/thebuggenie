<table style="width: 700px;" cellpadding=0 cellspacing=0>
<tr>
<td style="border-bottom: 1px solid #DDD; padding: 4px; width: 20px; text-align: center;"><?php echo image_tag('cfg_icon_projects.png', '', __('Edit settings'), __('Edit settings')); ?></td>
<td style="border-bottom: 1px solid #DDD; padding: 4px; width: 180px;"><a href="config.php?module=core&amp;section=10"><?php echo __('Select a different project'); ?></a></td>
<td style="border-bottom: 1px solid #DDD; width: auto;">&nbsp;</td>
<td style="border-bottom: 1px solid #DDD; padding: 4px; width: 20px; text-align: center;"><?php echo image_tag('cfg_icon_projectsettings.png', '', __('Edit settings'), __('Edit settings')); ?></td>
<td style="border-bottom: 1px solid #DDD; padding: 4px; width: 140px;"><a href="config.php?module=core&amp;section=10&amp;p_id=<?php print $theProject->getID(); ?>&amp;edit_settings=true"><?php echo __('Project information'); ?></a></td>
<td style="border-left: 1px solid #DDD; border-top: 1px solid #DDD; padding: 4px; width: 20px; text-align: center;"><?php echo image_tag('cfg_icon_projecteditionsbuilds.png', '', __('Edit settings'), __('Edit settings')); ?></td>
<td style="border-right: 1px solid #DDD; border-top: 1px solid #DDD; padding: 4px; width: <?php print ($theProject->isBuildsEnabled()) ? 230 : 180; ?>px;"><b><?php echo ($theProject->isBuildsEnabled()) ? __('Editions, builds and components') : __('Editions and components'); ?></b></td>
</tr>
</table>
<table style="width: 700px; margin-top: 10px;" cellpadding=0 cellspacing=0>
<tr>
<td style="width: auto; padding-right: 5px; vertical-align: top;">
<?php

if ($theEdition instanceof BUGSedition)
{
	require BUGScontext::getIncludePath() . 'include/config/projects_editbuilds.inc.php';
}
else
{
	?>
	<div style="width: auto; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-bottom: 5px;"><?php echo bugs_helpBrowserHelper('setup_editions', image_tag('help.png', array('style' => "float: right;"))); ?><b><?php echo __('EDITIONS'); ?></b></div>
	<div style="padding: 0px 0px 5px 3px; color: #AAA;"><?php echo __('Click an edition name to edit information and manage builds'); ?></div>
	<table cellpadding=0 cellspacing=0 style="width: 100%;" id="edition_table">
	<?php

	foreach ($theProject->getEditions() as $anEdition)
	{
		require BUGScontext::getIncludePath() . 'include/config/projects_editionbox.inc.php';
	}

	?>
	</table>
	<?php

	if ($access_level == "full")
	{
		?>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="add_edition_form" onsubmit="addEdition();return false;">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value="10">
		<input type="hidden" name="p_id" value="<?php print $theProject->getID(); ?>">
		<input type="hidden" name="add_edition" value="true">
		<input type="hidden" name="edit_editions" value="true">
		<table cellpadding=0 cellspacing=0 style="width: 100%;">
		<tr>
		<td style="padding: 3px; border-bottom: 1px solid #DDD;" colspan=3><br><b><?php echo __('Add an edition'); ?></b></td>
		<tr>
		<td style="width: auto; padding: 2px;" colspan=2><input type="text" style="width: 350px;" name="e_name"></td>
		<td style="padding: 0px; text-align: right;"><a class="image" href="javascript:void(0);" onclick="addEdition();"><?php echo image_tag('icon_plus_small.png'); ?></a></td>
		</tr>
		</table>
		</form>
		<?php
	}

	?>
	<div style="width: auto; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-top: 10px; margin-bottom: 5px;"><?php echo bugs_helpBrowserHelper('setup_build', image_tag('help.png', array('style' => "float: right;"))); ?><b><?php echo __('PROJECT DEVELOPERS'); ?></b></div>
	<span id="assignees_list">
	<?php

	$assignees = $theProject->getAssignees();

	if (count($assignees) == 0)
	{
		?><div style="padding-left: 5px; padding-top: 3px; color: #AAA;"><?php echo __('There are no developers assigned to this project'); ?></div><?php
	}
	else
	{
		foreach ($assignees as $aUserID => $assigns)
		{
			require BUGScontext::getIncludePath() . 'include/config/projects_assigneebox.inc.php';
		}
	}
	
	?></span><?php

	if ($access_level == "full")
	{
		?>
		<table style="width: 100%; margin-top: 3px;" cellpadding=0 cellspacing=0>
		<tr>
		<td style="padding: 3px; border-bottom: 1px solid #DDD;"><br><b><?php echo __('Assign developers'); ?></b></td>
		<tr>
		</table>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" onsubmit="findDev();return false;" id="find_dev_form">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value="10">
		<input type="hidden" name="p_id" value="<?php print $theProject->getID(); ?>">
		<input type="hidden" name="add_edition" value="true">
		<input type="hidden" name="edit_editions" value="true">
		<input type="hidden" name="find_dev" value="true">
		<table style="width: 100%; margin-top: 3px; cellpadding=0 cellspacing=0 id="dev_table">
		<tr>
		<td style="width: 70px; padding: 2px; text-align: center;"><?php echo __('Search for'); ?></td>
		<td style="width: auto; padding: 2px;"><input type="text" name="find_dev_uname" value="" style="width: 100%;"></td>
		<td style="width: 50px; padding: 2px; text-align: right;"><button onclick="findDev();"><?php echo __('Find'); ?></button></td>
		</tr>
		</table>
		</form>
		<span id="find_dev_results">
		</span>
		<?php
	}
}

?>
</td>
<td style="width: <?php print ($theEdition instanceof BUGSedition) ? 300 : 350; ?>px; padding-right: 5px; vertical-align: top;">
<?php

if (!$theEdition instanceof BUGSedition)
{
	?>
	<div style="width: auto; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-bottom: 5px;"><?php echo bugs_helpBrowserHelper('setup_editions', image_tag('help.png', array('style' => "float: right;"))); ?><b><?php echo __('PROJECT COMPONENTS'); ?></b></div>
	<div style="padding: 0px 0px 5px 3px; color: #AAA;"><?php echo __('Remember to assign components to editions from the edition settings, otherwise they cannot be reported issues against'); ?></div>	
	<table cellpadding=0 cellspacing=0 style="width: 100%;" id="component_table">
	<?php

		foreach ($theProject->getComponents() as $aComponent)
		{
			require BUGScontext::getIncludePath() . 'include/config/projects_componentbox.inc.php';
		}
	
	?>
	</table>
	<?php

		if ($access_level == "full")
		{
			?>
			<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="add_component_form" onsubmit="addComponent();return false;">
			<input type="hidden" name="module" value="core">
			<input type="hidden" name="section" value="10">
			<input type="hidden" name="p_id" value="<?php print $theProject->getID(); ?>">
			<input type="hidden" name="add_component" value="true">
			<input type="hidden" name="edit_editions" value="true">
			<table cellpadding=0 cellspacing=0 style="width: 100%;">
			<tr>
			<td style="padding: 3px; border-bottom: 1px solid #DDD;" colspan=3><br><b><?php echo __('Add component'); ?></b></td>
			<tr>
			<td style="width: auto; padding: 2px;" colspan=2><input type="text" style="width: 250px;" name="c_name"></td>
			<td style="padding: 0px; text-align: right;"><a class="image" href="javascript:void(0);" onclick="addComponent();"><?php echo image_tag('icon_plus_small.png'); ?></a></td>
			</tr>
			</table>
			</form>
			<?php
		}

	?>
	</table>
	<?php
}
else
{
	?>
	<div style="width: auto; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-bottom: 5px; margin-top: 10px;"><b><?php echo __('EDITION SETTINGS'); ?></b></div>
	<table style="width: auto;" cellpadding=0 cellspacing=0 id="leadby_table">
	<tr>
	<td style="padding: 2px; width: 120px;"><b><?php echo __('Lead by: %user_or_team%', array('%user_or_team%' => '')); ?></b></td>
	<td style="padding: 2px; width: 200px;" id="edition_leadby">
	<table style="width: 100%; margin-top: 3px;" cellpadding=0 cellspacing=0>
	<?php
	
		if ($theEdition->getLeadType() == BUGSidentifiableclass::TYPE_USER)
		{
			print bugs_userDropdown($theEdition->getLeadby()->getID());
		}
		elseif ($theEdition->getLeadType() == BUGSidentifiableclass::TYPE_TEAM)
		{
			print bugs_teamDropdown($theEdition->getLeadby()->getID());
		}
		else
		{
			echo '<tr><td style="color: #AAA;">' . __('Not assigned') . '</td></tr>';
		}
	
	?>
	</table>
	</td>
	<td style="width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.Appear('edit_leadby', { duration: 0.5 })"><?php echo image_tag('icon_switchassignee.png', '', __('Change'), __('Change'), 0, 12, 12); ?></a></td>
	<td style="padding: 2px;" colspan=2>&nbsp;</td>
	</tr>
	</table>
	<span id="edit_leadby" style="display: none;">
	<?php bugs_AJAXuserteamselector(__('Set lead by a user'), 
									__('Set lead by a team'),
									'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_editions=true&e_id=' . $theEdition->getID() . '&setleadby=true&lead_type=1', 
									'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_editions=true&e_id=' . $theEdition->getID() . '&setleadby=true&lead_type=2',
									'edition_leadby', 
									'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_editions=true&e_id=' . $theEdition->getID() . '&getleadby=true',
									'edition_leadby', 
									'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_editions=true&e_id=' . $theEdition->getID() . '&getleadby=true',
									'edit_leadby'
									); ?>
	</span>
	<table style="width: auto;" cellpadding=0 cellspacing=0 id="qa_table">
	<tr>
	<td style="padding: 2px; width: 120px;"><b><?php echo __('QA by: %user_or_team%', array('%user_or_team%' => '')); ?></b></td>
	<td style="padding: 2px; width: 200px;" id="edition_qa">
	<table style="width: 100%; margin-top: 3px;" cellpadding=0 cellspacing=0>
	<?php
	
		if ($theEdition->getQAType() == BUGSidentifiableclass::TYPE_USER)
		{
			print bugs_userDropdown($theEdition->getQA()->getID());
		}
		elseif ($theEdition->getQAType() == BUGSidentifiableclass::TYPE_TEAM)
		{
			print bugs_teamDropdown($theEdition->getQA()->getID());
		}
		else
		{
			echo '<tr><td style="color: #AAA;">' . __('Not assigned') . '</td></tr>';
		}
	
	?>
	</table>
	</td>
	<td style="width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.Appear('edit_qa', { duration: 0.5 })"><?php echo image_tag('icon_switchassignee.png', '', __('Change'), __('Change'), 0, 12, 12); ?></a></td>
	<td style="padding: 2px;" colspan=2>&nbsp;</td>
	</tr>
	</table>
	<span id="edit_qa" style="display: none;">
	<?php bugs_AJAXuserteamselector(__('Set QA\'ed by a user'), 
									__('Set QA\'ed by a team'),
									'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_editions=true&e_id=' . $theEdition->getID() . '&setqa=true&qa_type=1', 
									'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_editions=true&e_id=' . $theEdition->getID() . '&setqa=true&qa_type=2',
									'edition_qa', 
									'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_editions=true&e_id=' . $theEdition->getID() . '&getqa=true',
									'edition_qa', 
									'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_editions=true&e_id=' . $theEdition->getID() . '&getqa=true',
									'edit_qa'
									); ?>
	</span>
	<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="edition_settings" onsubmit="return false;">
	<input type="hidden" name="module" value="core">
	<input type="hidden" name="section" value="10">
	<input type="hidden" name="p_id" value="<?php print $theProject->getID(); ?>">
	<input type="hidden" name="edit_editions" value="true">
	<input type="hidden" name="edit_settings" value="true">
	<input type="hidden" name="e_id" value="<?php print $theEdition->getID(); ?>">
	&nbsp;
	<table cellpadding=0 cellspacing=0 style="width: 100%; margin-top: 5px;">
	<tr>
	<td style="padding: 2px; width: 100px;"><b><?php echo __('Can report issues:'); ?></b></td>
	<td style="padding: 2px; text-align: right;">
	<select style="width: 50px;" name="locked">
	<option value=1<?php print ($theEdition->isLocked()) ? " selected" : ""; ?>><?php echo __('No'); ?></option>
	<option value=0<?php print (!$theEdition->isLocked()) ? " selected" : ""; ?>><?php echo __('Yes'); ?></option>
	</select>
	</td>
	</tr>
	<tr>
	<td style="padding: 2px;"><b><?php echo __('Released:'); ?></b></td>
	<td style="padding: 2px; text-align: right;">
	<select style="width: 50px;" name="released">
	<option value=0<?php print (!$theEdition->isReleased()) ? " selected" : ""; ?>><?php echo __('No'); ?></option>
	<option value=1<?php print ($theEdition->isReleased()) ? " selected" : ""; ?>><?php echo __('Yes'); ?></option>
	</select>
	</td>
	</tr>
	<tr>
	<td style="padding: 2px;">
	<select name="planned_release" id="planned_release" style="width: 100%;" onchange="bB = document.getElementById('planned_release'); cB = document.getElementById('release_day'); dB = document.getElementById('release_month'); eB = document.getElementById('release_year'); if (bB.value == '0') { cB.disabled = true; dB.disabled = true; eB.disabled = true; } else { cB.disabled = false; dB.disabled = false; eB.disabled = false; }"><option value=0<?php print ($theEdition->isReleased()) ? "" : " selected"; ?>><?php echo __('No planned release'); ?></option><option value=1<?php print (!$theEdition->isReleased()) ? "" : " selected"; ?>><?php echo __('Planned release'); ?></option></select>
	</td>
	<td style="padding: 2px; text-align: right;">
	<select style="width: 85px;" name="release_month" id="release_month" <?php print ($theEdition->getReleaseDate() == 0) ? "disabled" : ""; ?>>
	<?php

		for($cc = 1;$cc <= 12;$cc++)
		{
			?>
			<option value=<?php print $cc; ?><?php print (($theEdition->getReleaseDateMonth() == $cc) ? " selected" : "") ?>><?php echo bugs_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
			<?php
		}

	?>
	</select>
	<select style="width: 40px;" name="release_day" id="release_day" <?php print ($theEdition->getReleaseDate() == 0) ? "disabled" : ""; ?>>
	<?php

		for($cc = 1;$cc <= 31;$cc++)
		{
			?>
			<option value=<?php print $cc; ?><?php echo (($theEdition->getReleaseDateDay() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
			<?php
		}

	?>
	</select>
	<select style="width: 55px;" name="release_year" id="release_year" <?php print ($theEdition->getReleaseDate() == 0) ? "disabled" : ""; ?>>
	<?php

		for($cc = 2000;$cc <= (date("Y") + 5);$cc++)
		{
			?>
			<option value=<?php print $cc; ?><?php echo (($theEdition->getReleaseDateYear() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
			<?php
		}

	?>
	</select>
	</td>
	</tr>
	</table>
	<div style="padding: 10px; padding-right: 2px; text-align: right;">
	<button onclick="submitEditionSettings();"><?php echo __('Save'); ?></button>
	</div>
	</form>
	<div style="width: auto; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-bottom: 5px;"><b><?php echo __('EDITION COMPONENTS'); ?></b></div>
	<table cellpadding=0 cellspacing=0 style="width: 100%;" id="edition_components">
	<?php

	foreach ($theEdition->getComponents() as $aComponent)
	{
		?>
		<tr id="edition_component_<?php echo $aComponent->getID(); ?>">
		<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?></td>
		<td style="width: auto; padding: 2px;"><?php print $aComponent; ?></td>
		<?php
		
		if ($access_level == 'full') 
		{
			?>
			<td style="width: 40px; text-align: right;"><a href="javascript:void(0);" onclick="removeEditionComponent(<?php echo $theProject->getID(); ?>, <?php echo $theEdition->getID(); ?>, <?php echo $aComponent->getID(); ?>);"><?php echo __('Remove'); ?></a></td>
			<?php
		}
		
		?>
		</tr>
		<?php
	}
	if (count($theEdition->getComponents()) == 0)
	{
		?>
		<tr>
		<td style="padding: 3px; color: #AAA;" colspan=3><?php echo __('This edition has no components'); ?></td>
		</tr>
		<?php
	}

	if ($access_level == "full")
	{
		?>
		<tr>
		<td style="padding: 3px; border-bottom: 1px solid #DDD;" colspan=3><br><b><?php echo __('Add existing component'); ?></b></td>
		</tr>
		<?php
		foreach ($theProject->getComponents() as $aComponent)
		{
			$hasit = false;
			foreach ($theEdition->getComponents() as $aC)
			{
				if ($aC->getID() == $aComponent->getID())
				{
					$hasit = true;
					break;
				}
			}
			if ($hasit == false)
			{
				?>
				<tr>
				<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?></td>
				<td style="width: auto; padding: 2px;"><?php print $aComponent; ?></td>
				<?php
				
				if ($access_level == 'full') 
				{
					?>
					<td style="width: 40px; text-align: right;"><a href="javascript:void(0);" onclick="addEditionComponent(<?php echo $theProject->getID(); ?>, <?php echo $theEdition->getID(); ?>, <?php echo $aComponent->getID(); ?>);"><?php echo __('Add this'); ?></a></td>
					<?php
				}
				
				?>
				</tr>
				<?php
			}
		}
		if (count($theProject->getComponents()) == 0)
		{
			?>
			<tr>
			<td style="padding: 3px; color: #AAA;" colspan=3><?php echo __('This project has no components'); ?></td>
			</tr>
			<?php
		}
	}
	
	?>
	</table>
	<?php
	if ($access_level == 'full')
	{
	 	?>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="add_component_form" onsubmit="addComponent();getComponents(<?php echo $theProject->getID(); ?>, <?php echo $theEdition->getID(); ?>);return false;">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value="10">
		<input type="hidden" name="p_id" value="<?php print $theProject->getID(); ?>">
		<input type="hidden" name="e_id" value="<?php print $theEdition->getID(); ?>">
		<input type="hidden" name="add_component" value="true">
		<input type="hidden" name="edit_editions" value="true">
		<table cellpadding=0 cellspacing=0 style="width: 100%;" id="edition_components">
		<tr>
		<td style="padding: 3px; border-bottom: 1px solid #DDD;" colspan=3><br><b><?php echo __('Add a new component'); ?></b></td>
		<tr>
		<td style="width: auto; padding: 2px;" colspan=2><input type="text" style="width: 250px;" name="c_name"></td>
		<td style="padding: 0px; text-align: right;"><a class="image" href="javascript:void(0);" onclick="addComponent();"><?php echo image_tag('icon_plus_small.png'); ?></a></td>
		</tr>
		</table>
		</form>
		<?php
	}

	?>
	</table>
	<?php
}

?>
</td>
</tr>
</table>