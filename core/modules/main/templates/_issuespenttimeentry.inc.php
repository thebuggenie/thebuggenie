<ul class="simple_list">
    <li style="position: relative;">
        <label for="issue_<?php echo $issue->getID(); ?>_timeentry"><?php echo __('Add time spent'); ?></label>
        <input type="text" id="issue_<?php echo $issue->getID(); ?>_timeentry" name="timespent_manual" placeholder="<?php echo __("'1 hour', '1 day, 3 hours' or similar"); ?>." style="width: 250px;">
        <div class="tooltip from-above" style="width: 400px; font-size: 1em; margin: 10px 0 0 50px;">
            <?php echo __('Entering time spent is easy. TBG will make a best effort at understanding what you type, as long as you follow some basic rules:'); ?>
            <ul style="list-style: circle;">
                <li><?php echo __('Separate time with commas: "1 day, 3 hours" is fine, but "1 day 3 hours" will not be understood'); ?></li>
                <li><?php echo __('TBG (currently) only understands english: "2 hours" makes sense, "En time" not so much'); ?></li>
            </ul>
        </div>
        <?php echo __('%specify_manually or select %list', array('%specify_manually' => '', '%list' => '')); ?>
        <input type="text" name="timespent_specified_value" style="width: 50px;">
        <select name="timespent_specified_type">
            <?php foreach (array('points' => __('%number_of point(s)', array('%number_of' => '')), 'hours' => __('%number_of hour(s)', array('%number_of' => '')), 'days' => __('%number_of day(s)', array('%number_of' => '')), 'weeks' => __('%number_of week(s)', array('%number_of' => '')), 'months' => __('%number_of month(s)', array('%number_of' => ''))) as $time => $description): ?>
                <option value="<?php echo $time; ?>" <?php if ($time == 'hours') echo 'selected'; ?>><?php echo $description; ?></option>
            <?php endforeach; ?>
        </select>
    </li>
    <li>
        <label for="issue_<?php echo $issue->getID(); ?>_timespent_activitytype"><?php echo __('Activity'); ?></label>
        <select id="issue_<?php echo $issue->getID(); ?>_timespent_activitytype" name="timespent_activitytype">
            <?php foreach (\thebuggenie\core\entities\ActivityType::getAll() as $activitytype): ?>
                <option value="<?php echo $activitytype->getID(); ?>"><?php echo $activitytype->getName(); ?></option>
            <?php endforeach; ?>
        </select>
    </li>
    <li>
        <label for="issue_<?php echo $issue->getID(); ?>_timespent_comment" class="optional"><?php echo __('Comment (optional)'); ?></label>
        <input id="issue_<?php echo $issue->getID(); ?>_timespent_comment" name="timespent_comment" type="text" style="width: 500px;">
    </li>
    <?php if (isset($save) && $save == true): ?>
        <li style="text-align: right;">
            <input type="submit" class="button button-silver" value="<?php echo __('Log time entry'); ?>">
        </li>
    <?php endif; ?>
</ul>
