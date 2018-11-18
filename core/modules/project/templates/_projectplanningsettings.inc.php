<?php
$selected_columns = $selected_project->getPlanningColumns($tbg_user);
$all_columns = $selected_project->getIssueFields(false, array('status', 'milestone', 'resolution', 'assignee', 'user_pain'));
?>

<div id="planning_column_settings_container" style="display: none;" class="fullpage_backdrop">
    <form id="pcs_column_settings_form" action="<?= make_url('project_planning_save_column_settings', array('project_key' => $selected_project->getKey())); ?>">
        <div class="backdrop_box medium">
            <div class="backdrop_detail_header">
                <span><?= __('Configure visible columns'); ?></span>
                <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
            </div>
            <div id="backdrop_detail_content" class="backdrop_detail_content">
                <div class="planning_column_settings column_settings">
                    <h4><?= __('Select columns to show'); ?></h4>
                    <p class="faded_out"><?= __('Select which columns you would like to show in this result view. Your selection is saved until the next time you visit.'); ?></p>
                    <ul class="simple_list pcs_list">
                        <?php foreach ($all_columns as $fieldname => $fieldopts): ?>
                            <li class="pcs_<?= $fieldname; ?>">
                                <input type="checkbox" class="fancycheckbox" id="planning_column_<?= $fieldname; ?>" name="planning_column[<?= $fieldname; ?>]" value="<?= $fieldname; ?>" <?php if (isset($selected_columns[$fieldname])): ?> checked<?php endif; ?>/>
                                <label for="planning_column_<?= $fieldname; ?>"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Show %fieldlabel', array('%fieldlabel' => $fieldopts['label'])); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php if (!$tbg_user->isGuest()): ?>
                <div class="backdrop_details_submit">
                    <span class="explanation"></span>
                    <div class="submit_container">
                        <button type="submit" id="planning_column_settings_save_button" onclick="$('planning_column_settings_indicator').toggle()" class="column_settings_save_button button button-silver"><?= image_tag('spinning_16.gif', ['id' => 'planning_column_settings_indicator', 'style' => 'display: none;']) . __('Done') ?></button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>
