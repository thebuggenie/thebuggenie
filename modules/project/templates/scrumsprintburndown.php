<?php

	$tbg_response->setTitle(__('"%project_name%" project planning', array('%project_name%' => $selected_project->getName())));

?>
<table style="width: 100%;" cellpadding="0" cellspacing="0" id="scrum">
	<tr>
		<td style="width: 210px; padding: 0 5px 0 5px;">
			<div class="rounded_box mediumgrey_borderless" style="margin-top: 5px;" id="scrum_menu">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
					<div class="header"><?php echo __('Actions'); ?></div>
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_burndown.png'); ?></td>
							<td style="padding: 3px 0 0 2px; text-align: left; font-size: 12px; font-weight: bold;"><?php echo link_tag(make_url('project_scrum_sprint_burndown', array('project_key' => $selected_project->getKey())), __('Show sprint burndown')); ?></td>
						</tr>
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_burndown.png'); ?></td>
							<td style="padding: 3px 0 0 2px; text-align: left; font-size: 12px; font-weight: bold;"><?php echo link_tag('#', __('Show release burndown'), array('class' => 'faded_medium')); ?></td>
						</tr>
					</table>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		</td>
		<td style="width: auto; padding-right: 5px;" id="scrum_sprint_burndown">
			<div class="header_div"><?php echo __('Sprint burndown graph'); ?></div>
			<?php echo image_tag(make_url('project_scrum_sprint_burndown_image', array('project_key' => $selected_project->getKey())), array('style' => 'margin: 15px 0 15px 0;'), true); ?>
		</td>
	</tr>
</table>