<?php

	$tbg_response->setTitle(__('"%project_name%" project team', array('%project_name%' => $selected_project->getName())));

?>
<table style="width: 1010px;" cellpadding="0" cellspacing="0" id="statistics">
	<tr>
		<td style="width: 300px; padding: 0 5px 0 5px; vertical-align: top;">
			<div class="rounded_box lightgrey borderless" style="margin-top: 5px; padding: 7px;" id="statistics_menu">
				<div class="left_menu_header"><?php echo __('Statistics'); ?></div>
				<div class="left_menu_content">
					<ul class="left_menu_list">
						<li><a href="javascript:void(0);" onclick="getStatistics('<?php echo make_url('project_statistics_images', array('project_key' => $selected_project->getKey(), 'something' => 'something')); ?>');">item 1</a></li>
						<li><a href="#">item 2</a></li>
						<li><a href="#">item 3</a></li>
					</ul>
				</div>
			</div>
		</td>
		<td style="width: auto; padding: 5px 5px 0 0; vertical-align: top;" id="statistics_main">
			<div style="width: 695px; height: 310px; padding: 0;" class="rounded_box white" id="statistics_main_image_div">
				<img src="#" id="statistics_main_image">
			</div>
			<table style="width: 697px; margin-top: 5px;" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="width: 33%; height: 150px;" class="rounded_box white"></td>
					<td style="width: 33%;" class="rounded_box white"></td>
					<td style="width: 34%;" class="rounded_box white"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>