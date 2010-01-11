<?php

	$bugs_response->setTitle(__('Manage projects - %project% - users', array('%project%' => $theProject->getName())));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php 

include_component('configleftmenu', array('selected_section' => 10));

?>
<td valign="top">
	<?php include_template('configuration/project_header', array('theProject' => $theProject, 'mode' => 5)); ?>
	<table style="width: 700px; margin-top: 10px;" cellpadding=0 cellspacing=0>
		<tr>
			<td style="width: auto; padding-right: 5px; vertical-align: top;">
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<div class="rounded_box round_canhover" style="margin: 10px 0px 10px 0px; width: 700px;">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="vertical-align: middle; padding: 5px 10px 5px 10px;">
							<div class="config_header nobg"><b><?php echo __('Assign developers'); ?></b></div>
							<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_find_assignee', array('project_id' => $theProject->getID())); ?>" method="post" onsubmit="findDevs('<?php echo make_url('configure_project_find_assignee', array('project_id' => $theProject->getID())); ?>');return false;" id="find_dev_form">
								<table style="width: 100%; margin-top: 3px;" cellpadding=0 cellspacing=0 id="find_user">
									<tr>
										<td style="width: 200px; padding: 2px; text-align: left;"><label for="find_by"><?php echo __('Find team, user or customer'); ?></label></td>
										<td style="width: auto; padding: 2px;"><input type="text" name="find_by" id="find_by" value="" style="width: 100%;"></td>
										<td style="width: 50px; padding: 2px; text-align: right;"><input type="submit" value="<?php echo __('Find'); ?>" style="width: 45px;"></td>
									</tr>
								</table>
							</form>
							<div style="padding: 10px 0 10px 0; display: none;" id="find_dev_indicator"><span style="float: left;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
							<div id="find_dev_results">
								<div class="faded_medium" style="padding: 4px;"><?php echo __('Enter the name of a user, customer or team to search for it'); ?></div>
							</div>
						</div>
						<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
					</div>
				<?php endif; ?>
				<div id="assignees_list">
					<?php include_template('projects_assignees', array('project' => $theProject)); ?>
				</div>
			</td>
		</tr>
	</table>
</td>
</tr>
</table>