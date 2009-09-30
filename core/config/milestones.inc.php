<?php

	if (($access_level != "full" && $access_level != "read") || BUGScontext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{

		$theProject = 0;
		if (is_numeric(BUGScontext::getRequest()->getParameter('p_id')))
		{
			$theProject = new BUGSproject(BUGScontext::getRequest()->getParameter('p_id'));
		}
		
		require_once BUGScontext::getIncludePath() . 'include/config/milestones_actions.inc.php';
		
		if (!BUGScontext::getRequest()->isAjaxCall())
		{

			if ($theProject instanceof BUGSproject)
			{
				?><script type="text/javascript" src="<?php echo BUGScontext::getTBGPath(); ?>js/config/milestones_ajax.js"></script><?php
			}
			
			?>
			<table style="width: 100%" cellpadding=0 cellspacing=0>
				<tr>
				<td style="padding-right: 10px;">
					<table class="configstrip" cellpadding=0 cellspacing=0>
						<tr>
							<td class="cleft"><b><?php echo __('Configure milestones'); ?></b></td>
							<td class="cright">&nbsp;</td>
						</tr>
						<tr>
							<td colspan=2 class="cdesc">
							<?php echo __('From here you can manage milestones for all projects you have access to.'); ?><br>
							<?php echo __('If you want to change access permissions for any milestone, you can do that either from the %manage_users% or the %manage_teams_and_groups% page.', array('%manage_users%' => '<span style="display: float;">' . image_tag('cfg_icon_users.png') . '&nbsp;<a href="config.php?module=core&amp;section=2"><b>' . __('Manage users') . '</b></a></span>', '%manage_teams_and_groups%' => '<span style="display: float;">' . image_tag('cfg_icon_teamgroups.png') . '&nbsp;<a href="config.php?module=core&amp;section=1"><b>' . __('Manage teams &amp; groups') . '</b></a></span>')); ?>
							</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			<div style="padding: 5px;">
			<?php 
				
				if ($isAdded == true)
				{ 
					?>
					<table style="margin-top: 5px; margin-bottom: 10px; width: 100%;" cellpadding=0 cellspacing=0>
					<tr>
					<td style="padding-right: 10px;">
					<table style="width: 700px; background-color: #CFE8CF; border: 1px solid #AAC6AA;" cellpadding=0 cellspacing=0>
					<tr>
					<td style="padding: 3px; width: 20px;"><?php echo image_tag('action_ok.png', '', __('Saved'), __('Saved')); ?></td>
					<td style="padding: 3px;">
						<div style="color: #333;"><b><?php echo __('The milestone has been added.'); ?></b></div>
						<?php echo __('Remember to give other users/groups permission to access this milestone via the admin section to the left'); ?>
					</td>
					</tr>
					</table>
					</td>
					</tr>
					</table>
					<?php
				}
	
			?>
			<?php
			
				if (!$theProject instanceof BUGSproject)
				{
					//$projects = bugs_getProjects();	
					?>
					<?php echo __('Please select which project you want to manage milestones for.'); ?>
					<div style="border-bottom: 1px solid #DDD; padding: 3px; width: 400px; font-size: 12px; margin-bottom: 3px; margin-top: 5px;"><b><?php echo __('Available projects'); ?></b></div>
					<table style="width: 720px;" cellpadding=0 cellspacing=0>
					<?php

						foreach (BUGSproject::getAll() as $aProject)
						{
							$aProject = new BUGSproject($aProject['id']);
							?><tr>
							<td style="padding: 2px; width: 20px;"><?php echo image_tag('icon_project.png'); ?></td>
							<td style="padding: 2px; width: auto;"><a href="config.php?module=core&amp;section=9&amp;p_id=<?php echo $aProject->getID(); ?>"><?php echo $aProject->getName(); ?></a></td>
							</tr>
							<?php
						}
					
					?>
					</table>
					<?php
				}
				else
				{
					if (BUGScontext::getUser()->hasPermission('b2projectaccess', $theProject->getID(), 'core'))
					{
						
						?><b><?php echo __('Selected project: %project_name%', array('%project_name%' => '')); ?> </b><?php echo $theProject->getName(); ?><br>
						<a href="config.php?module=core&amp;section=9">&lt;&lt;&nbsp;<?php echo __('Select a different project'); ?></a>
						<div style="border-bottom: 1px solid #DDD; padding: 3px; width: 500px; font-size: 12px; margin-bottom: 3px; margin-top: 5px;"><b><?php echo __('Existing milestones'); ?></b></div>
						<div style="width: 500px;">
						<span id="milestones_span">
						<?php
						
							if (count($theProject->getMilestones()) > 0)
							{
								$include_table = true;
								foreach ($theProject->getMilestones() as $aMilestone)
								{
									$aMilestone = new BUGSmilestone($aMilestone['id'], $theProject);
									require BUGScontext::getIncludePath() . 'include/config/milestones_milestonebox.inc.php';
								}
							}

						?>
						<div style="padding: 3px; color: #AAA; <?php if (count($theProject->getMilestones()) > 0) { echo 'display: none;'; } ?>" id="nomilestones"><?php echo __('There are no milestones available for this project'); ?></div>
						</span>
						<div style="border-bottom: 1px solid #DDD; padding: 3px; width: 500px; font-size: 12px; margin-bottom: 3px; margin-top: 5px;"><b><?php echo __('Add a milestone'); ?></b></div>
						<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="add_milestone_form" onsubmit="return false;">
						<input type="hidden" name="module" value="core">
						<input type="hidden" name="section" value=9>
						<input type="hidden" name="p_id" value=<?php echo $theProject->getID(); ?>>
						<input type="hidden" name="add_milestone" value="true">
						<table style="width: 100%;" cellpadding=0 cellspacing=0>
						<tr>
						<td style="width: 60px; padding: 3px;"><b><?php echo __('Name'); ?></b></td>
						<td style="width: auto;"><input type="text" style="width: 100%;" value="" name="m_name"></td>
						</tr>
						<tr>
						<td style="width: 60px; padding: 3px;"><b><?php echo __('Description'); ?></b></td>
						<td style="width: auto;"><input type="text" style="width: 100%;" value="" name="description"></td>
						</tr>
						<tr>
						<td style="width: 120px;">
							<select name="sch_date" id="sch_date" style="width: 100%;" onchange="if ($('sch_date').getValue() == '1') { $('sch_month').enable(); $('sch_day').enable(); $('sch_year').enable(); } else { $('sch_month').disable(); $('sch_day').disable(); $('sch_year').disable(); } ">
								<option value=0 selected><?php echo __('No planned release'); ?></option>
								<option value=1><?php echo __('Planned release'); ?></option>
							</select>
						</td>
						<td style="width: auto;">
						<select style="width: 85px;" name="sch_month" id="sch_month" disabled="disabled">
						<?php
	
							for($cc = 1;$cc <= 12;$cc++)
							{
								?><option value=<?php echo $cc; ?>><?php echo bugs_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
								<?php
							}
	
						?>
						</select>
						<select style="width: 40px;" name="sch_day" id="sch_day" disabled>
						<?php
	
							for($cc = 1;$cc <= 31;$cc++)
							{
								?>
								<option value=<?php echo $cc; ?>><?php echo $cc; ?></option>
								<?php
							}
	
						?>
						</select>
						<select style="width: 55px;" name="sch_year" id="sch_year" disabled>
						<?php
	
							for($cc = 2000;$cc <= (date("Y") + 5);$cc++)
							{
								?>
								<option value=<?php echo $cc; ?>><?php echo $cc; ?></option>
								<?php
							}
	
						?>
						</select>
						</td>
						</tr>
						<tr>
						<td style="padding-bottom: 15px; text-align: right;" colspan=2><button onclick="addMilestone(<?php echo $theProject->getID(); ?>);"><?php echo __('Add'); ?></button></td>
						</tr>
						</table>
						</form>
						</div>
						<?php
					}
					else
					{
						bugs_msgbox(false, '' , __('You do not have access to this project'));
					}
				} 
			?>
			</div>
			<?php
			
		} 
	}

?>