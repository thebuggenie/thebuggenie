<?php
$selected_columns = $selected_project->getPlanningColumns($tbg_user);
$all_columns = $selected_project->getIssueFields(false, array('status', 'milestone', 'resolution', 'assignee', 'user_pain'));
?>

<div id="planning_column_settings_container" style="display: none;" class="fullpage_backdrop">
    <div class="backdrop_box medium">
        <div class="backdrop_detail_header">
            <?php echo __('Configure visible columns'); ?>
        </div>
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <div class="planning_column_settings column_settings">
                <h4><?php echo __('Select columns to show'); ?></h4>
                <p class="faded_out"><?php echo __('Select which columns you would like to show in this result view. Your selection is saved until the next time you visit.'); ?></p>
                <form id="pcs_column_settings_form" action="<?php echo make_url('project_planning_save_column_settings', array('project_key' => $selected_project->getKey())); ?>">
                    <ul class="simple_list pcs_list">
                        <?php foreach ($all_columns as $fieldname => $fieldopts): ?>
                            <li class="pcs_<?php echo $fieldname; ?>">
                                <label>
                                    <input type="checkbox" name="planning_column[<?php echo $fieldname; ?>]" value="<?php echo $fieldname; ?>" <?php if (isset($selected_columns[$fieldname])): ?> checked<?php endif; ?>/>
                                    <div><?php echo __('Show %fieldlabel', array('%fieldlabel' => $fieldopts['label'])); ?></div>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (!$tbg_user->isGuest()): ?>
                        <div style="text-align: right; clear: both;">
                            <input type="submit" id="planning_column_settings_save_button" onclick="$('planning_column_settings_indicator').toggle()" class="column_settings_save_button button button-green" style="float:right; margin: 7px 0;" value="<?php echo __('Ok') ?>" />
                            <div id="planning_column_settings_indicator" style="display: none; float: right; margin: 7px 5px 0 10px;"><?php echo image_tag('spinning_20.gif'); ?></div>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
