<div id="milestone_span_<?php echo $milestone->getID(); ?>" style="border-bottom: 1px solid #DDD; margin-top: 10px;">
	<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_milestone_action', array('project_id' => $milestone->getProject()->getID(), 'milestone_id' => $milestone->getID(), 'milestone_action' => 'update')); ?>" method="post" id="edit_milestone_<?php echo $milestone->getID(); ?>" style="display: none;" onsubmit="updateMilestone('<?php echo make_url('configure_project_milestone_action', array('project_id' => $milestone->getProject()->getID(), 'milestone_id' => $milestone->getID(), 'milestone_action' => 'update')); ?>', <?php echo $milestone->getID(); ?>);return false;">
		<table style="width: 100%;" cellpadding=0 cellspacing=0>
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
				<td style="width: 120px;">
					<select name="is_starting" id="starting_date_<?php echo $milestone->getID(); ?>" style="width: 100%;" onchange="if ($('starting_date_<?php echo $milestone->getID(); ?>').getValue() == '1') { $('starting_month_<?php echo $milestone->getID(); ?>').enable(); $('starting_day_<?php echo $milestone->getID(); ?>').enable(); $('starting_year_<?php echo $milestone->getID(); ?>').enable(); } else { $('starting_month_<?php echo $milestone->getID(); ?>').disable(); $('starting_day_<?php echo $milestone->getID(); ?>').disable(); $('starting_year_<?php echo $milestone->getID(); ?>').disable(); } ">
						<option value=0<?php print ($milestone->hasStartingDate()) ? "" : " selected"; ?>><?php echo __('No planned start'); ?></option>
						<option value=1<?php print (!$milestone->hasStartingDate()) ? "" : " selected"; ?>><?php echo __('Planned start'); ?></option>
					</select>
				</td>
				<td style="width: auto;">
					<select style="width: 85px;" name="starting_month" id="starting_month_<?php echo $milestone->getID(); ?>"<?php if (!$milestone->isStarting()): ?> disabled<?php endif; ?>>
					<?php for($cc = 1;$cc <= 12;$cc++): ?>
						<option value=<?php echo $cc; ?><?php echo (($milestone->getStartingMonth() == $cc) ? " selected" : ""); ?>><?php echo bugs_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
					<?php endfor; ?>
					</select>
					<select style="width: 40px;" name="starting_day" id="starting_day_<?php echo $milestone->getID(); ?>"<?php if (!$milestone->isStarting()): ?> disabled<?php endif; ?>>
					<?php for($cc = 1;$cc <= 31;$cc++): ?>
						<option value=<?php echo $cc; ?><?php echo (($milestone->getStartingDay() == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
					<?php endfor; ?>
					</select>
					<select style="width: 55px;" name="starting_year" id="starting_year_<?php echo $milestone->getID(); ?>"<?php if (!$milestone->isStarting()): ?> disabled<?php endif; ?>>
					<?php for($cc = 2000;$cc <= (date("Y") + 5);$cc++): ?>
						<option value=<?php echo $cc; ?><?php echo (($milestone->getStartingYear() == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
					<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td style="width: 120px;">
					<select name="is_scheduled" id="sch_date_<?php echo $milestone->getID(); ?>" style="width: 100%;" onchange="if ($('sch_date_<?php echo $milestone->getID(); ?>').getValue() == '1') { $('sch_month_<?php echo $milestone->getID(); ?>').enable(); $('sch_day_<?php echo $milestone->getID(); ?>').enable(); $('sch_year_<?php echo $milestone->getID(); ?>').enable(); } else { $('sch_month_<?php echo $milestone->getID(); ?>').disable(); $('sch_day_<?php echo $milestone->getID(); ?>').disable(); $('sch_year_<?php echo $milestone->getID(); ?>').disable(); } ">
						<option value=0<?php print ($milestone->isScheduled()) ? "" : " selected"; ?>><?php echo __('No planned release'); ?></option>
						<option value=1<?php print (!$milestone->isScheduled()) ? "" : " selected"; ?>><?php echo __('Planned release'); ?></option>
					</select>
				</td>
				<td style="width: auto;">
					<select style="width: 85px;" name="sch_month" id="sch_month_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->isScheduled()) ? "disabled" : ""; ?>>
					<?php for($cc = 1;$cc <= 12;$cc++): ?>
						<option value=<?php echo $cc; ?><?php echo (($milestone->getScheduledMonth() == $cc) ? " selected" : ""); ?>><?php echo bugs_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
					<?php endfor; ?>
					</select>
					<select style="width: 40px;" name="sch_day" id="sch_day_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->isScheduled()) ? "disabled" : ""; ?>>
					<?php for($cc = 1;$cc <= 31;$cc++): ?>
						<option value=<?php echo $cc; ?><?php echo (($milestone->getScheduledDay() == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
					<?php endfor; ?>
					</select>
					<select style="width: 55px;" name="sch_year" id="sch_year_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->isScheduled()) ? "disabled" : ""; ?>>
					<?php for($cc = 2000;$cc <= (date("Y") + 5);$cc++): ?>
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
	<table id="milestone_<?php echo $milestone->getID(); ?>_indicator" style="display: none; width: 100%;" cellpadding=0 cellspacing=0>
		<tr>
			<td colspan="3" style="text-align: right; height: 30px; padding: 3px; font-size: 12px; color: #AAA;">
				<span style="float: right;"><?php echo __('Please wait'); ?>...</span>
				<?php echo image_tag('spinning_20.gif', array('style' => 'float: right; margin-right: 5px;')); ?>
			</td>
		</tr>
	</table>
	<div id="show_milestone_<?php echo $milestone->getID(); ?>">
		<a class="image" href="javascript:void(0);" onclick="Element.show('edit_milestone_<?php echo $milestone->getID(); ?>');Element.hide('show_milestone_<?php echo $milestone->getID(); ?>');"><?php echo image_tag('icon_edit.png', array('style' => 'float: left; margin-top: 10px;')); ?></a>
		<a class="image" href="javascript:void(0);" onclick="Effect.Appear('delete_milestone_<?php echo $milestone->getID(); ?>', { duration: 0.5 });"><?php echo image_tag('icon_delete.png', array('style' => 'float: left; margin: 10px 5px 0 5px;')); ?></a>
		<div style="display: none; width: 300px; position: absolute; left: 600px; padding: 5px; border: 1px solid #DDD; background-color: #FFF; text-align: center;" id="delete_milestone_<?php echo $milestone->getID(); ?>">
			<b><?php echo __('Do you really want to delete this milestone?'); ?></b><br>
			<a href="javascript:void(0);" onclick="deleteMilestone('<?php echo make_url('configure_project_milestone_action', array('project_id' => $milestone->getProject()->getID(), 'milestone_id' => $milestone->getID(), 'milestone_action' => 'delete')); ?>', <?php echo $milestone->getID();?>);"><?php echo __('Yes'); ?></a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="Effect.Fade('delete_milestone_<?php echo $milestone->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a>
		</div>
		<div style="padding: 3px; width: auto; font-size: 13px;"><b><?php echo $milestone->getName(); ?></b></div>
		<div style="padding: 3px; padding-top: 0px; padding-bottom: 0px; color: #AAA;">
			<?php if (!$milestone->isScheduled() && !$milestone->isStarting()): ?>
				<?php echo __('This milestone has no planned schedule'); ?>
			<?php elseif ($milestone->isScheduled() && $milestone->isStarting()): ?>
				<?php echo __('This milestone is starting %starting_date% and scheduled for %scheduled_date%', array('%starting_date%' => bugs_formatTime($milestone->getStartingDate(), 5), '%scheduled_date%' => bugs_formatTime($milestone->getScheduledDate(), 5))); ?>
			<?php elseif ($milestone->isScheduled()): ?>
				<?php echo __('This milestone is scheduled for %scheduled_date%', array('%scheduled_date%' => bugs_formatTime($milestone->getScheduledDate(), 5))); ?>
			<?php elseif ($milestone->isStarting()): ?>
				<?php echo __('This milestone is starting %starting_date%', array('%starting_date%' => bugs_formatTime($milestone->getStartingDate(), 5))); ?>
			<?php endif;?>
		</div>
		<div style="padding: 5px 0 10px 0;"><?php echo bugs_BBDecode($milestone->getDescription()); ?></div>
	</div>
</div>