<?php

	$tbg_response->setTitle(__('"%project_name%" project team', array('%project_name%' => $selected_project->getName())));

?>
<table style="width: 1010px;" cellpadding="0" cellspacing="0" id="statistics">
	<tr>
		<td style="width: 300px; padding: 0 5px 0 5px; vertical-align: top;">
			<div class="rounded_box lightgrey borderless" style="margin-top: 5px; padding: 7px;" id="statistics_menu">
				<div class="left_menu_header"><?php echo __('Statistics'); ?></div>
				<div class="left_menu_content">
					<b><?php echo __('Number of issues per:'); ?></b>
					<ul class="left_menu_list" style="margin-left: 10px;">
						<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_category')); ?>');"><?php echo __('%number_of_issues_per% Category', array('%number_of_issues_per%' => '')); ?></a></li>
						<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_priority')); ?>');"><?php echo __('%number_of_issues_per% Priority level', array('%number_of_issues_per%' => '')); ?></a></li>
						<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_resolution')); ?>');"><?php echo __('%number_of_issues_per% Resolution', array('%number_of_issues_per%' => '')); ?></a></li>
						<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_reproducability')); ?>');"><?php echo __('%number_of_issues_per% Reproducability', array('%number_of_issues_per%' => '')); ?></a></li>
						<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_status')); ?>');"><?php echo __('%number_of_issues_per% Status type', array('%number_of_issues_per%' => '')); ?></a></li>
					</ul>
				</div>
			</div>
		</td>
		<td style="width: auto; padding: 5px 5px 0 0; vertical-align: top;">
			<div style="display: none;" id="statistics_main">
				<div style="width: 695px; height: 310px; padding: 0;" id="statistics_main_image_div">
					<img src="#" id="statistics_main_image" alt="<?php echo __('Loading, please wait'); ?>">
				</div>
				<div style="padding: 5px; text-align: center;"><b><?php echo __('Click one of the graphs below to show details'); ?></b></div>
				<table style="width: 697px; margin-top: 5px;" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td style="width: 33%; height: 150px; padding: 1px;"><img src="#" onclick="toggleStatisticsMainImage(1);" id="statistics_mini_image_1" alt="<?php echo __('Loading, please wait'); ?>" style="cursor: pointer;" title="<?php echo __('Show details'); ?>"></td>
						<td style="width: 33%; padding: 1px;"><img src="#" onclick="toggleStatisticsMainImage(2);" id="statistics_mini_image_2" alt="<?php echo __('Loading, please wait'); ?>" style="cursor: pointer;" title="<?php echo __('Show details'); ?>"></td>
						<td style="width: 34%; padding: 1px;"><img src="#" onclick="toggleStatisticsMainImage(3);" id="statistics_mini_image_3" alt="<?php echo __('Loading, please wait'); ?>" style="cursor: pointer;" title="<?php echo __('Show details'); ?>"></td>
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