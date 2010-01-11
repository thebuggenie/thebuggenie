<?php

	$tbg_response->setTitle(__('Manage projects'));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php 

include_component('configleftmenu', array('selected_section' => 10));

?>
<td valign="top">
	<?php include_template('configuration/project_header', array('mode' => 1)); ?>
	<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
		<div style="width: 700px; padding: 5px 0 5px 0; margin-top: 45px; border-bottom: 1px solid #DDD; font-size: 14px;"><b><?php echo __('Create a new project'); ?></b></div>
		<div style="width: 690px; padding: 5px; background-color: #F9F9F9; margin-bottom: 20px; border-bottom: 1px solid #DDD;">
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="add_project_form" onsubmit="return false;">
				<input type="hidden" name="add_project" value="true">
				<table cellpadding=0 cellspacing=0 style="margin: 0; width: 690px; table-layout: auto;">
					<tr>
						<td style="width: auto; padding-right: 10px;"><b><?php echo __('Project name'); ?></b></td>
						<td style="width: 310px; padding: 2px;"><input type="text" style="width: 300px;" name="p_name"></td>
						<td style="width: 25px; padding: 0px; text-align: left;"><?php echo image_submit_tag('icon_plus_small.png', array('onclick' => "addProject('".make_url('configure_projects_add_project')."');")); ?></td>
						<td style="width: auto; text-align: right;"><a href="#" onclick="Effect.toggle('add_project_details', 'slide', { duration: 0.3 } );"><?php echo __('More details'); ?></a></td>
					</tr>
				</table>
				<div style="display: none; margin-top: 10px; border-top: 1px solid #E9E9E9;" id="add_project_details">
					<table cellpadding=0 cellspacing=0 style="margin: 10px 0 10px 0; width: 690px; table-layout: auto;">
						<tr>
							<td>
								More options for adding projects will go in here.<br />
								Maybe add a list of groups + teams to grant access, as well as being able to select owner instantly.
							</td>
						</tr>
					</table>
				</div>
				<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="project_add_indicator">
					<tr>
						<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
						<td style="padding: 0px; text-align: left;"><?php echo __('Adding project, please wait'); ?>...</td>
					</tr>
				</table>
			</form>
		</div>
	<?php endif; ?>
	<div style="width: 700px; padding: 5px 0 5px 0; border-bottom: 1px solid #DDD;<?php if ($access_level != configurationActions::ACCESS_FULL): ?> margin-top: 45px;<?php endif; ?> font-size: 14px;"><b><?php echo __('Existing projects'); ?></b></div>
	<div id="project_table">
	<?php if (count($allProjects) > 0): ?>
		<?php foreach ($allProjects as $aProject): ?>
			<?php include_template('projectbox', array('project' => $aProject, 'access_level' => $access_level)); ?>
		<?php endforeach; ?>
	<?php endif; ?>
	</div>
	<div id="noprojects_tr" style="padding: 3px; color: #AAA;<?php if (count($allProjects) > 0): ?> display: none;<?php endif;?>"><?php echo __('There are no projects available'); ?></div>
</td>
</tr>
</table>