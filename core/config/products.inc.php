<?php

	if (($access_level != "full" && $access_level != "read") || BUGScontext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		require_once BUGScontext::getIncludePath() . 'include/config/projects_actions.inc.php';
		if (!BUGScontext::getRequest()->isAjaxCall())
		{
			if ($access_level == 'full')
			{
				?><script type="text/javascript" src="<?php echo BUGScontext::getTBGPath(); ?>js/config/projects_ajax.js"></script><?php
			}
			
			?>
			<table style="width: 100%" cellpadding=0 cellspacing=0>
				<tr>
				<td style="padding-right: 10px;">
					<table class="configstrip" cellpadding=0 cellspacing=0 style="table-layout: fixed;">
						<tr>
							<td class="cleft" style="width: 700px;"><b><?php echo __('Configure projects'); ?></b></td>
							<td class="cright" style="width: auto;">&nbsp;</td>
						</tr>
						<tr>
							<td class="cdesc">
							<?php echo __('This page gives you full control over your projects - each projects specific settings, as well as its editions and builds.'); ?><br>
							<?php if (BUGScontext::getRequest()->getParameter('edit_settings')): ?>
								<?php $help_topic = 'setup_project'; ?>
							<?php else: ?>
								<?php $help_topic = 'config_projects'; ?>
							<?php endif; ?>
							<?php echo __('If you are unsure how to set up projects, editions, builds and components, have a look at the %bugs_online_help%.', array('%bugs_online_help%' => bugs_helpBrowserHelper($help_topic, __('The Bug Genie online help')))); ?>
							<?php
	
								if (!($theProject instanceof BUGSproject) && BUGScontext::getUser()->hasPermission("b2projectaccess", BUGScontext::getRequest()->getParameter('p_id'), "core") == true)
								{
									?><br><br><b><?php echo __('Please select a project from the list to view and edit its details.'); ?></b><br><br><?php
								}
								elseif (BUGScontext::getRequest()->getParameter('edit_settings'))
								{
									?><br><br><?php echo __('View or edit details about the selected project below.'); ?>
									<?php
	
										if ($access_level == "full")
										{
											echo __('Click the "Save"-button below to update settings and details.');
										}
								}
								else
								{
									?>
									<br><br>
									<?php 
									echo __('Manage editions, components and builds from here.'); 
	
									if ($access_level == "full" && BUGScontext::getRequest()->getParameter('e_id'))
									{
										echo __('Click the "Save"-button below to update settings and details.');
									}
								}
	
							?>
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			<span id="message_span">
			</span>
			<?php
	
			if ($theProject instanceof BUGSproject)
			{
				?><div style="margin-bottom: 10px; padding: 5px;"><b><?php echo __('Selected project: %project_name%', array('%project_name%' => '')); ?></b><?php print $theProject; print ($theEdition instanceof BUGSedition) ? ' -&gt; ' . $theEdition : ''; ?><br>
				<?php
				
				if (BUGScontext::getUser()->hasPermission('b2viewconfig', 9) || BUGScontext::getUser()->hasPermission('b2saveconfig', 9))
				{
					?><a href="<?php echo BUGScontext::getTBGPath() ?>config.php?section=9&amp;module=core&amp;p_id=<?php echo $theProject->getID() ?>"><?php echo __('Click here to configure milestones for this project'); ?></a><?php
				}

				?></div><?php
			}
			
			if (!$theProject instanceof BUGSproject)
			{
				?>
				<table style="width: 700px;" cellpadding=0 cellspacing=0>
				<tr>
				<td style="border-left: 1px solid #DDD; border-top: 1px solid #DDD; padding: 4px; width: 20px; text-align: center;"><?php echo image_tag('cfg_icon_projects.png', '', __('Edit settings'), __('Edit settings')); ?></td>
				<td style="border-right: 1px solid #DDD; border-top: 1px solid #DDD; padding: 4px; width: 160px;"><b><?php echo __('Select a project'); ?></b></td>
				<td style="border-bottom: 1px solid #DDD; width: auto;">&nbsp;</td>
				</tr>
				</table>
				<table cellpadding=0 cellspacing=0 style="width: auto;">
				<tr>
				<td>
					<div style="background-color: #F2F2F2; padding: 3px; border-bottom: 1px solid #DDD; margin-left: 5px; margin-bottom: 5px; width: auto;"><b><?php echo __('ADD A PROJECT'); ?></b></div>
					<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="add_project_form" onsubmit="addProject();return false;">
					<input type="hidden" name="add_project" value="true">
					<table cellpadding=0 cellspacing=0 style="margin-left: 5px; width: auto;">
					<tr>
					<td style="width: auto; padding: 2px;" colspan=2><input type="text" style="width: 350px;" name="p_name"></td>
					<td style="padding: 0px; text-align: right;"><a class="image" href="javascript:void(0);" onclick="addProject();"><?php echo image_tag('icon_plus_small.png'); ?></a></td>
					</tr>
					</table>
					</form>
				</td>
				</tr>
				<tr><td style="height: 10px; font-size: 1px;">&nbsp;</td></tr>
				<tr>
				<td>
				<div style="width: 700px; background-color: #F2F2F2; padding: 3px; border-bottom: 1px solid #DDD; margin-bottom: 5px; margin-top: 5px;"><b><?php echo __('AVAILABLE PROJECTS'); ?></b></div>
				</td>
				</tr>
				</table>
				<table style="width: 700px;" cellpadding=0 cellspacing=0 id="project_table">
				<?php
	
				if (count($allProjects) > 0)
				{
					foreach ($allProjects as $aProject)
					{
						$aProject = BUGSfactory::projectLab($aProject['id']);
						require BUGScontext::getIncludePath() . 'include/config/projects_projectbox.inc.php';
					}
				}
				else
				{
					?><tr id="noprojects_tr"><td style="padding: 3px; color: #AAA;"><?php echo __('There are no projects available'); ?></td></tr><?php
				}
	
				?>
				</table>
				<?php
			}
			elseif ($theProject instanceof BUGSproject && BUGScontext::getRequest()->getParameter('edit_editions'))
			{
				require BUGScontext::getIncludePath() . 'include/config/projects_editeditions.inc.php';
			}
			elseif ($theProject instanceof BUGSproject && BUGScontext::getRequest()->getParameter('edit_settings'))
			{
				require BUGScontext::getIncludePath() . 'include/config/projects_editsettings.inc.php';
			}
		}
	}
?>