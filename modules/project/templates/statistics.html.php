<?php

	$tbg_response->addBreadcrumb(__('Statistics'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" project team', array('%project_name%' => $selected_project->getName())));

?>
			<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
			<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project)); ?>
			<div style="display: none;" id="statistics_main">
				<div style="width: 695px; height: 310px; padding: 0;" id="statistics_main_image_div">
					<img src="#" id="statistics_main_image" alt="<?php echo __('Loading, please wait'); ?>">
				</div>
				<div style="padding: 5px; text-align: center;"><b><?php echo __('Click one of the graphs below to show details'); ?></b></div>
				<table style="width: 697px; margin-top: 5px;" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td style="width: 33%; height: 150px; padding: 1px;"><img src="#" onclick="TBG.Project.Statistics.toggleImage(1);" id="statistics_mini_image_1" alt="<?php echo __('Loading, please wait'); ?>" style="cursor: pointer;" title="<?php echo __('Show details'); ?>"></td>
						<td style="width: 33%; padding: 1px;"><img src="#" onclick="TBG.Project.Statistics.toggleImage(2);" id="statistics_mini_image_2" alt="<?php echo __('Loading, please wait'); ?>" style="cursor: pointer;" title="<?php echo __('Show details'); ?>"></td>
						<td style="width: 34%; padding: 1px;"><img src="#" onclick="TBG.Project.Statistics.toggleImage(3);" id="statistics_mini_image_3" alt="<?php echo __('Loading, please wait'); ?>" style="cursor: pointer;" title="<?php echo __('Show details'); ?>"></td>
					</tr>
				</table>
				<input type="hidden" id="statistics_mini_1_main" value="">
				<input type="hidden" id="statistics_mini_2_main" value="">
				<input type="hidden" id="statistics_mini_3_main" value="">
			</div>
			<div class="rounded_box verylightgrey borderless" style="width: 690px; text-align: center; padding: 150px 5px 150px 5px; color: #AAA; font-size: 19px;" id="statistics_help">
				<?php echo __('Select an item in the left menu to show more details'); ?>
			</div>
		</td>
	</tr>
</table>