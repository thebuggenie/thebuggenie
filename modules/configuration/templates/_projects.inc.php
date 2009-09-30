	<script type="text/javascript" src="<?php echo BUGScontext::getTBGPath(); ?>js/config/projects_ajax.js"></script>
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

								if ($access_level == configurationActions::ACCESS_FULL)
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

							if ($access_level == configurationActions::ACCESS_FULL && BUGScontext::getRequest()->getParameter('e_id'))
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
	<?php include_template('configuration/project_messages'); ?>
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
		<td style="border-left: 1px solid #DDD; border-top: 1px solid #DDD; padding: 4px; width: 20px; text-align: center;"><?php echo image_tag('cfg_icon_projects.png'); ?></td>
		<td style="border-right: 1px solid #DDD; border-top: 1px solid #DDD; padding: 4px; width: 160px;"><b><?php echo __('Select a project'); ?></b></td>
		<td style="border-bottom: 1px solid #DDD; width: auto;">&nbsp;</td>
		</tr>
		</table>
		<table cellpadding=0 cellspacing=0 style="width: auto;">
		<tr>
		<td>
			<table cellpadding=0 cellspacing=0 style="margin-top: 10px; width: 700px;">
			<tr>
			<td style="width: 50%;" valign="top">
				<table style="width: 100%;" cellpadding=0 cellspacing=0>
				<tr>
				<td>
				<div style="background-color: #F2F2F2; padding: 3px; border-bottom: 1px solid #DDD; margin-bottom: 5px; width: auto;"><b><?php echo __('DEFAULT PROJECT'); ?></b></div>
				</td>
				</tr>
				<tr>
				<td>
				<?php 
				
				if (count($allProjects) > 0)
				{
					?>
					<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" id="default_project_form" onsubmit="return false">
					<input type="hidden" name="module" value="core">
					<input type="hidden" name="section" value="10">
					<input type="hidden" name="setdefaultproject" value="true">
						<table cellpadding=0 cellspacing=0 style="width: 100%;">
							<tr>
							<td style="width: auto;">
							<select style="width: 240px;" name="defaultproject" id="defaultproject">
							<?php
								
							foreach ($allProjects as $aProject)
							{
								$aProject = BUGSfactory::projectLab($aProject['id']);
								?>
								<option value=<?php print $aProject->getID(); print ($defaultProject->getID() == $aProject->getID()) ? " selected" : ""; ?>><?php print $aProject; ?></option>
								<?php
							}
	
							?>
							</select></td>
							<td style="width: 50px; text-align: right;"><button onclick="setDefaultProject();"><?php echo __('Save'); ?></button></td>
							</tr>
						</table>
					</form>
					<?php 
				}
				else
				{
					?>
					<div style="padding: 3px; color: #AAA;"><?php echo __('Create a project to set it as default'); ?></div>
					<?php
				} 
				
				?>
				</table>
			</td>
			<td style="width: 50%;" valign="top">
				<div style="background-color: #F2F2F2; padding: 3px; border-bottom: 1px solid #DDD; margin-left: 5px; margin-bottom: 5px; width: auto;"><b><?php echo __('ADD A PROJECT'); ?></b></div>
				<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="add_project_form" onsubmit="addProject('<?php echo make_url('configure_projects_add_project'); ?>');return false;">
				<input type="hidden" name="add_project" value="true">
				<table cellpadding=0 cellspacing=0 style="margin-left: 5px; width: auto;">
				<tr>
				<td style="width: auto; padding: 2px;" colspan=2><input type="text" style="width: 350px;" name="p_name"></td>
				<td style="padding: 0px; text-align: right;"><a class="image" href="javascript:void(0);" onclick="addProject('<?php echo make_url('configure_projects_add_project'); ?>');"><?php echo image_tag('icon_plus_small.png'); ?></a></td>
				</tr>
				</table>
				</form>
			</td>
			</tr>
			</table>
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
				include_template('projectbox', array('project' => $aProject));
			}
		}

		?>
		<tr id="noprojects_tr" <?php if (count($allProjects) > 0): ?>style="display: none;"<?php endif;?>><td style="padding: 3px; color: #AAA;"><?php echo __('There are no projects available'); ?></td></tr>
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

?>