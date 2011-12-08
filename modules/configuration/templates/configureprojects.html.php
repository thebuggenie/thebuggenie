<?php

	$tbg_response->setTitle(__('Manage projects'));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => 10)); ?>
		<td valign="top" style="padding-left: 15px;">
			<div style="width: 788px;">
				<h3><?php echo __('Configure projects'); ?></h3>
				<div class="content faded_out">
					<p>
						<?php echo __('More information about projects, editions, builds and components is available from the %wiki_help_section%.', array('%wiki_help_section%' => link_tag(make_url('publish_article', array('article_name' => 'Category:Help')), '<b>'.__('Wiki help section').'</b>'))); ?>
						<?php if (TBGContext::getScope()->getMaxProjects()): ?>
							<div class="faded_out dark" style="margin: 12px 0;">
								<?php echo __('This instance is using %num% of max %max% projects', array('%num%' => '<b id="current_project_num_count">'.TBGProject::getProjectsCount().'</b>', '%max%' => '<b>'.TBGContext::getScope()->getMaxProjects().'</b>')); ?>
							</div>
						<?php endif; ?>
					</p>
				</div>
				<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
					<div class="rounded_box lightgrey" style="width: 788px; padding: 5px; margin: 10px 0;<?php if (!TBGContext::getScope()->hasProjectsAvailable()): ?> display: none;<?php endif; ?>" id="add_project_div">
						<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" method="post" id="add_project_form" onsubmit="TBG.Project.add('<?php echo make_url('configure_projects_add_project'); ?>');return false;">
							<input type="hidden" name="add_project" value="true">
							<table cellpadding=0 cellspacing=0 style="margin: 0; width: 690px; table-layout: auto;">
								<tr>
									<td style="width: auto; padding-right: 10px;"><b><?php echo __('Create a new project'); ?></b></td>
									<td style="width: 400px; padding: 2px; text-align: right;">
										<input type="text" style="width: 320px; text-align: left;" name="p_name">
										<input type="submit" style="width: 60px;" value="<?php echo __('Add'); ?>">
									</td>
								</tr>
							</table>
							<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="project_add_indicator">
								<tr>
									<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
									<td style="padding: 0px; text-align: left;"><?php echo __('Adding project, please wait'); ?>...</td>
								</tr>
							</table>
						</form>
					</div>
				<?php endif; ?>
				<h4><?php echo __('Existing projects'); ?></h4>
				<div id="project_table">
				<?php if (count($allProjects) > 0): ?>
					<?php foreach ($allProjects as $aProject): ?>
						<div id="project_box_<?php echo $aProject->getID();?>">
							<?php include_template('projectbox', array('project' => $aProject, 'access_level' => $access_level)); ?>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
				</div>
				<div id="noprojects_tr" style="padding: 3px; color: #AAA;<?php if (count($allProjects) > 0): ?> display: none;<?php endif;?>">
					<?php echo __('There are no projects available'); ?>
				</div>
			</div>
		</td>
	</tr>
</table>