<?php

	$tbg_response->setTitle(__('Manage projects - %project% - other settings', array('%project%' => $theProject->getName())));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php include_component('leftmenu', array('selected_section' => 10)); ?>
<td valign="top">
	<?php include_template('configuration/project_header', array('theProject' => $theProject, 'mode' => 4)); ?>
	<table style="width: 700px; margin-top: 10px;" cellpadding=0 cellspacing=0>
		<tr>
			<td style="width: auto; padding-right: 5px; vertical-align: top;">
				<div class="config_header nobg"><b><?php echo __('Milestones'); ?></b></div>
				<p style="padding: 0px 0px 5px 3px;">
					<?php echo __('Set up project milestones from this page'); ?>.
					<?php echo __('Click a milestone name to edit its information and settings'); ?>.
				</p>
				<p class="faded_medium" id="no_milestones" style="padding: 5px;<?php if (count($milestones) > 0): ?> display: none;<?php endif; ?>"><?php echo __('There are no milestones'); ?></p>
				<div id="milestone_list" style="width: 500px; margin-left: 5px;">
					<?php foreach ($milestones as $milestone): ?>
						<?php include_template('milestonebox', array('milestone' => $milestone)); ?>
					<?php endforeach; ?>
				</div>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_add_milestone', array('project_id' => $theProject->getID())); ?>" method="post" id="add_milestone_form" onsubmit="addMilestone('<?php echo make_url('configure_projects_add_milestone', array('project_id' => $theProject->getID())); ?>');return false;">
					<div style="margin-top: 10px; padding: 3px; border-bottom: 1px solid #DDD;"><b><?php echo __('Add a milestone'); ?></b></div>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td style="width: 450px; padding: 2px;"><input type="text" style="width: 445px;" name="name"></td>
							<td style="width: 100px; padding: 2px;">
								<select name="milestone_type">
									<option value="1"><?php echo __('Regular milestone'); ?></option>
									<option value="2"><?php echo __('Scrum sprint'); ?></option>
								</select>
							</td>
							<td style="padding: 0px; text-align: right;"><input type="submit" value="<?php echo __('Add'); ?>"></td>
						</tr>
					</table>
					<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="milestone_add_indicator">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
							<td style="padding: 0px; text-align: left;"><?php echo __('Adding milestone, please wait'); ?>...</td>
						</tr>
					</table>
					</form>
				<?php endif; ?>
			</td>
		</tr>
	</table>
</td>
</tr>
</table>