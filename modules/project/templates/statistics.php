<?php

	$tbg_response->setTitle(__('"%project_name%" project team', array('%project_name%' => $selected_project->getName())));

?>
<table style="width: 100%;" cellpadding="0" cellspacing="0" id="statistics">
	<tr>
		<td style="width: 300px; padding: 0 5px 0 5px;">
			<div class="rounded_box lightgrey borderless" style="margin-top: 5px; padding: 7px;" id="statistics_menu">
				<div class="left_menu_header"><?php echo __('Statistics'); ?></div>
				<div class="left_menu_content">
					<ul class="left_menu_list">
						<li><a href="getStatistics(<?php echo url_for(''));">item 1</a></li>
						<li><a href="#">item 2</a></li>
						<li><a href="#">item 3</a></li>
					</ul>
				</div>
			</div>
		</td>
		<td style="width: auto; padding-right: 5px;" id="statistics_main">
		</td>
	</tr>
</table>