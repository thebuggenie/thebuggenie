<table style="width: 780px;" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width: auto; padding-right: 5px; vertical-align: top;">
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_add_milestone', array('project_id' => $project->getID())); ?>" method="post" id="add_milestone_form" onsubmit="TBG.Project.Milestone.add('<?php echo make_url('configure_projects_add_milestone', array('project_id' => $project->getID())); ?>');return false;">
					<div class="rounded_box yellow" style="padding: 5px; margin-bottom: 15px;">
						<table cellpadding=0 cellspacing=0 style="width: 770px;">
							<tr>
								<td style="width: 200px;"><label for="add_milestone_name"><?php echo __('Add milestone'); ?></label></td>
								<td style="width: 400px; padding: 2px;"><input type="text" id="add_milestone_name" style="width: 445px;" name="name"></td>
								<td style="width: 125px; padding: 2px;">
									<select name="milestone_type">
										<option value="1"><?php echo __('Regular milestone'); ?></option>
										<option value="2"><?php echo __('Scrum sprint'); ?></option>
									</select>
								</td>
								<td style="padding: 0px; text-align: right; width: 100px;"><input type="submit" value="<?php echo __('Add'); ?>"></td>
							</tr>
						</table>
						<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="milestone_add_indicator">
							<tr>
								<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
								<td style="padding: 0px; text-align: left;"><?php echo __('Adding milestone, please wait'); ?>...</td>
							</tr>
						</table>
					</div>
				</form>
			<?php endif; ?>
			<p class="faded_out" id="no_milestones" style="padding: 5px;<?php if (count($milestones) > 0): ?> display: none;<?php endif; ?>"><?php echo __('There are no milestones'); ?></p>
			<ul id="milestone_list" style="width: 780px; margin-left: 5px; height: 300px; overflow: auto;" class="simple_list">
				<?php foreach ($milestones as $milestone): ?>
					<?php include_template('configuration/milestonebox', array('milestone' => $milestone)); ?>
				<?php endforeach; ?>
			</ul>
		</td>
	</tr>
</table>