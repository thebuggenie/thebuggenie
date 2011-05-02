<table style="width: 780px; margin-bottom: 15px;" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width: auto; padding-right: 5px; vertical-align: top;">
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<div class="rounded_box lightgrey" style="margin: 0 0 10px 0; width: 765px; padding: 5px 10px 5px 10px;">
					<div class="config_header"><b><?php echo __('Assign developers'); ?></b></div>
					<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_find_assignee', array('project_id' => $project->getID())); ?>" method="post" onsubmit="findDevs('<?php echo make_url('configure_project_find_assignee', array('project_id' => $project->getID())); ?>');return false;" id="find_dev_form">
						<table style="width: 100%; margin-top: 3px;" cellpadding=0 cellspacing=0 id="find_user">
							<tr>
								<td style="width: 200px; padding: 2px; text-align: left;"><label for="find_by"><?php echo __('Find team or user'); ?></label></td>
								<td style="width: auto; padding: 2px;"><input type="text" name="find_by" id="find_by" value="" style="width: 100%;"></td>
								<td style="width: 50px; padding: 2px; text-align: right;"><input type="submit" value="<?php echo __('Find'); ?>" style="width: 45px;"></td>
							</tr>
						</table>
					</form>
					<div style="padding: 10px 0 10px 0; display: none;" id="find_dev_indicator"><span style="float: left;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
					<div id="find_dev_results">
						<div class="faded_out" style="padding: 4px;"><?php echo __('Enter the name of a user or team to search for it'); ?></div>
					</div>
				</div>
			<?php endif; ?>
			<div id="assignees_list">
				<?php include_template('configuration/projects_assignees', array('project' => $project)); ?>
			</div>
		</td>
	</tr>
</table>