<li id="milestone_span_<?php echo $milestone->getID(); ?>" style="margin-bottom: 10px;">
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_milestone_action', array('project_id' => $milestone->getProject()->getID(), 'milestone_id' => $milestone->getID(), 'milestone_action' => 'update')); ?>" method="post" id="edit_milestone_<?php echo $milestone->getID(); ?>" style="display: none;" onsubmit="updateMilestone('<?php echo make_url('configure_project_milestone_action', array('project_id' => $milestone->getProject()->getID(), 'milestone_id' => $milestone->getID(), 'milestone_action' => 'update')); ?>', <?php echo $milestone->getID(); ?>);return false;">
		<table style="width: 750px;" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 100px;"><label for="milestone_name_<?php echo $milestone->getID(); ?>"><?php echo __('Name:'); ?></label></td>
				<td style="width: auto;"><input type="text" style="width: 100%;" value="<?php echo $milestone->getName(); ?>" name="name" id="milestone_name_<?php echo $milestone->getID(); ?>"></td>
			</tr>
			<tr>
				<td><label for="milestone_description_<?php echo $milestone->getID(); ?>"><?php echo __('Description:'); ?></label></td>
				<td style="width: auto;"><input type="text" style="width: 100%;" value="<?php echo $milestone->getDescription(); ?>" name="description" id="milestone_description_<?php echo $milestone->getID(); ?>"></td>
			</tr>
			<tr>
				<td><label for="milestone_type_<?php echo $milestone->getID(); ?>"><?php echo __('Milestone type:'); ?></label></td>
				<td style="width: auto;">
					<select name="milestone_type" id="milestone_type_<?php echo $milestone->getID(); ?>">
						<option value="1"<?php if ($milestone->getType() == 1): ?> selected<?php endif; ?>><?php echo __('Regular milestone'); ?></option>
						<option value="2"<?php if ($milestone->getType() == 2): ?> selected<?php endif; ?>><?php echo __('Scrum sprint'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td style="width: 130px;">
					<select name="is_starting" id="starting_date_<?php echo $milestone->getID(); ?>" style="width: 100%;" onchange="if ($('starting_date_<?php echo $milestone->getID(); ?>').getValue() == '1') { $('starting_month_<?php echo $milestone->getID(); ?>').enable(); $('starting_day_<?php echo $milestone->getID(); ?>').enable(); $('starting_year_<?php echo $milestone->getID(); ?>').enable(); } else { $('starting_month_<?php echo $milestone->getID(); ?>').disable(); $('starting_day_<?php echo $milestone->getID(); ?>').disable(); $('starting_year_<?php echo $milestone->getID(); ?>').disable(); } ">
						<option value=0<?php print ($milestone->hasStartingDate()) ? "" : " selected"; ?>><?php echo __('No planned start'); ?></option>
						<option value=1<?php print (!$milestone->hasStartingDate()) ? "" : " selected"; ?>><?php echo __('Planned start'); ?></option>
					</select>
				</td>
				<td style="width: auto;">
					<select style="width: 90px;" name="starting_month" id="starting_month_<?php echo $milestone->getID(); ?>"<?php if (!$milestone->hasStartingDate()): ?> disabled<?php endif; ?>>
					<?php for ($cc = 1;$cc <= 12;$cc++): ?>
						<option value=<?php echo $cc; ?><?php echo (($milestone->getStartingMonth() == $cc) ? " selected" : ""); ?>><?php echo strftime('%B', mktime(0, 0, 0, $cc, 1)); ?></option>
					<?php endfor; ?>
					</select>
					<select style="width: 45px;" name="starting_day" id="starting_day_<?php echo $milestone->getID(); ?>"<?php if (!$milestone->hasStartingDate()): ?> disabled<?php endif; ?>>
					<?php for ($cc = 1;$cc <= 31;$cc++): ?>
						<option value=<?php echo $cc; ?><?php echo (($milestone->getStartingDay() == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
					<?php endfor; ?>
					</select>
					<select style="width: 60px;" name="starting_year" id="starting_year_<?php echo $milestone->getID(); ?>"<?php if (!$milestone->hasStartingDate()): ?> disabled<?php endif; ?>>
					<?php for ($cc = 2000;$cc <= (date("Y") + 5);$cc++): ?>
						<option value=<?php echo $cc; ?><?php echo (($milestone->getStartingYear() == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
					<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td style="width: 130px;">
					<select name="is_scheduled" id="sch_date_<?php echo $milestone->getID(); ?>" style="width: 100%;" onchange="if ($('sch_date_<?php echo $milestone->getID(); ?>').getValue() == '1') { $('sch_month_<?php echo $milestone->getID(); ?>').enable(); $('sch_day_<?php echo $milestone->getID(); ?>').enable(); $('sch_year_<?php echo $milestone->getID(); ?>').enable(); } else { $('sch_month_<?php echo $milestone->getID(); ?>').disable(); $('sch_day_<?php echo $milestone->getID(); ?>').disable(); $('sch_year_<?php echo $milestone->getID(); ?>').disable(); } ">
						<option value=0<?php print ($milestone->hasScheduledDate()) ? "" : " selected"; ?>><?php echo __('No planned release'); ?></option>
						<option value=1<?php print (!$milestone->hasScheduledDate()) ? "" : " selected"; ?>><?php echo __('Planned release'); ?></option>
					</select>
				</td>
				<td style="width: auto;">
					<select style="width: 90px;" name="sch_month" id="sch_month_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->hasScheduledDate()) ? "disabled" : ""; ?>>
					<?php for ($cc = 1;$cc <= 12;$cc++): ?>
						<option value=<?php echo $cc; ?><?php echo (($milestone->getScheduledMonth() == $cc) ? " selected" : ""); ?>><?php echo tbg_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
					<?php endfor; ?>
					</select>
					<select style="width: 45px;" name="sch_day" id="sch_day_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->hasScheduledDate()) ? "disabled" : ""; ?>>
					<?php for ($cc = 1;$cc <= 31;$cc++): ?>
						<option value=<?php echo $cc; ?><?php echo (($milestone->getScheduledDay() == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
					<?php endfor; ?>
					</select>
					<select style="width: 60px;" name="sch_year" id="sch_year_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->hasScheduledDate()) ? "disabled" : ""; ?>>
					<?php for ($cc = 2000;$cc <= (date("Y") + 5);$cc++): ?>
						<option value=<?php echo $cc; ?><?php echo (($milestone->getScheduledYear() == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
					<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom: 15px; text-align: right;" colspan=2>
					<?php echo __('%update% or %cancel%', array('%update%' => '<input type="submit" value="' . __('Update') . '">', '%cancel%' => '<a href="javascript:void(0);" onclick="Element.hide(\'edit_milestone_' . $milestone->getID() . '\');Element.show(\'show_milestone_' . $milestone->getID() . '\');" style="font-size: 10px;">' . __('Cancel') . '</a>')); ?>
				</td>
			</tr>
		</table>
	</form>
	<table id="milestone_<?php echo $milestone->getID(); ?>_indicator" style="display: none; width: 750px;" cellpadding=0 cellspacing=0>
		<tr>
			<td colspan="3" style="text-align: right; height: 30px; padding: 3px; font-size: 12px; color: #AAA;">
				<span style="float: right;"><?php echo __('Please wait'); ?>...</span>
				<?php echo image_tag('spinning_20.gif', array('style' => 'float: right; margin-right: 5px;')); ?>
			</td>
		</tr>
	</table>
	<div id="show_milestone_<?php echo $milestone->getID(); ?>" style="position: relative;">
		<a class="image" href="javascript:void(0);" onclick="Element.show('edit_milestone_<?php echo $milestone->getID(); ?>');Element.hide('show_milestone_<?php echo $milestone->getID(); ?>');"><?php echo image_tag('icon_edit.png', array('style' => 'float: left; margin-right: 5px;')); ?></a>
		<a class="image" href="javascript:void(0);" onclick="Effect.Appear('delete_milestone_<?php echo $milestone->getID(); ?>', { duration: 0.5 });"><?php echo image_tag('icon_delete.png', array('style' => 'float: left; margin-right: 5px;')); ?></a>
		<div style="display: none; width: 400px; position: absolute; left: 0; padding: 5px; text-align: left;" id="delete_milestone_<?php echo $milestone->getID(); ?>" class="rounded_box white shadowed">
			<b><?php echo __('Do you really want to delete this milestone?'); ?></b>
			<div style="text-align: right;">
				<a href="javascript:void(0);" onclick="deleteMilestone('<?php echo make_url('configure_project_milestone_action', array('project_id' => $milestone->getProject()->getID(), 'milestone_id' => $milestone->getID(), 'milestone_action' => 'delete')); ?>', <?php echo $milestone->getID();?>);"><?php echo __('Yes'); ?></a>&nbsp;::&nbsp;<a href="javascript:void(0);" onclick="Effect.Fade('delete_milestone_<?php echo $milestone->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a>
			</div>
		</div>
		<div style="padding: 0; width: auto; font-size: 13px;"><b><?php echo $milestone->getName(); ?></b></div>
		<div style="padding: 0 0 3px 0; color: #AAA;">
			<?php if (!$milestone->hasScheduledDate() && !$milestone->hasStartingDate()): ?>
				<?php echo __('This milestone has no planned schedule'); ?>
			<?php elseif ($milestone->hasScheduledDate() && $milestone->hasStartingDate()): ?>
				<?php echo __('This milestone is starting on %starting_date% and scheduled for %scheduled_date%', array('%starting_date%' => tbg_formatTime($milestone->getStartingDate(), 5), '%scheduled_date%' => tbg_formatTime($milestone->getScheduledDate(), 5))); ?>
			<?php elseif ($milestone->hasScheduledDate()): ?>
				<?php echo __('This milestone is scheduled for %scheduled_date%', array('%scheduled_date%' => tbg_formatTime($milestone->getScheduledDate(), 5))); ?>
			<?php elseif ($milestone->hasStartingDate()): ?>
				<?php echo __('This milestone is starting on %starting_date%', array('%starting_date%' => tbg_formatTime($milestone->getStartingDate(), 5))); ?>
			<?php endif;?>
		</div>
		<?php if ($milestone->getDescription()): ?>
			<div style="padding: 5px 0 10px 0;"><?php echo $milestone->getDescription(); ?></div>
		<?php endif; ?>
	</div>
</li>