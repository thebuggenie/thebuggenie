<div class="bulk_action_container">
    <form method="post" onsubmit="TBG.Search.bulkUpdate('<?php echo make_url('issues_bulk_update'); ?>', '<?php echo $mode; ?>');return false;" id="bulk_action_form_<?php echo $mode; ?>" class="bulk_action_form">
        <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
            <input type="hidden" name="project_key" value="<?php echo \thebuggenie\core\framework\Context::getCurrentProject()->getKey(); ?>">
        <?php endif; ?>
        <div class="search_bulk_container <?php echo $mode; ?> unavailable" id="search_bulk_container_<?php echo $mode; ?>">
            <label for="bulk_action_selector_<?php echo $mode; ?>"><?php echo __('With selected issue(s): %action', array('%action' => '')); ?></label>
            <select name="bulk_action" id="bulk_action_selector_<?php echo $mode; ?>" onchange="TBG.Search.bulkContainerChanger('<?php echo $mode; ?>');">
                <option value=""><?php echo __('Do nothing'); ?></option>
                <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                    <option value="assign_milestone"><?php echo __('Assign to milestone'); ?></option>
                <?php endif; ?>
                <option value="set_status"><?php echo __('Set status'); ?></option>
                <option value="set_resolution"><?php echo __('Set resolution'); ?></option>
                <option value="set_priority"><?php echo __('Set priority'); ?></option>
                <option value="set_category"><?php echo __('Set category'); ?></option>
                <option value="set_severity"><?php echo __('Set severity'); ?></option>
                <option value="perform_workflow_step"><?php echo __('Choose workflow step to perform'); ?></option>
            </select>
            <?php if (\thebuggenie\core\framework\Context::isProjectContext()): ?>
                <span class="bulk_action_subcontainer" id="bulk_action_subcontainer_assign_milestone_<?php echo $mode; ?>" style="display: none;">
                    <select name="milestone" id="bulk_action_assign_milestone_<?php echo $mode; ?>" class="focusable" onchange="TBG.Search.bulkChanger('<?php echo $mode; ?>'); if ($(this).getValue() == 'new') { ['bulk_action_assign_milestone_top_name', 'bulk_action_assign_milestone_bottom_name'].each(function(element) { $(element).show(); }); } else { ['bulk_action_assign_milestone_top_name', 'bulk_action_assign_milestone_bottom_name'].each(function(element) { $(element).hide(); }); }">
                        <option value="0"><?php echo __('No milestone'); ?></option>
                        <option value="new"><?php echo __('Create new milestone from selected issues'); ?></option>
                        <?php foreach (\thebuggenie\core\framework\Context::getCurrentProject()->getMilestonesForIssues() as $milestone_id => $milestone): ?>
                            <option id="bulk_action_assign_milestone_<?php echo $mode; ?>_<?php echo $milestone_id; ?>" value="<?php echo $milestone_id; ?>"><?php echo $milestone->getName(); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="milestone_name" style="display: none;" id="bulk_action_assign_milestone_<?php echo $mode; ?>_name">
                </span>
            <?php endif; ?>
            <span class="bulk_action_subcontainer" id="bulk_action_subcontainer_set_status_<?php echo $mode; ?>" style="display: none;">
                <select name="status" id="bulk_action_set_status_<?php echo $mode; ?>" class="focusable" onchange="TBG.Search.bulkChanger('<?php echo $mode; ?>');">
                    <?php foreach (\thebuggenie\core\entities\Status::getAll() as $status_id => $status): ?>
                        <?php if (!$status->canUserSet($tbg_user)) continue; ?>
                        <option value="<?php echo $status_id; ?>"><?php echo $status->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
            </span>
            <span class="bulk_action_subcontainer" id="bulk_action_subcontainer_set_resolution_<?php echo $mode; ?>" style="display: none;">
                <select name="resolution" id="bulk_action_set_resolution_<?php echo $mode; ?>" class="focusable" onchange="TBG.Search.bulkChanger('<?php echo $mode; ?>');">
                    <option value="0"><?php echo __('No resolution'); ?></option>
                    <?php foreach (\thebuggenie\core\entities\Resolution::getAll() as $resolution_id => $resolution): ?>
                        <?php if (!$resolution->canUserSet($tbg_user)) continue; ?>
                        <option value="<?php echo $resolution_id; ?>"><?php echo $resolution->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
            </span>
            <span class="bulk_action_subcontainer" id="bulk_action_subcontainer_set_priority_<?php echo $mode; ?>" style="display: none;">
                <select name="priority" id="bulk_action_set_priority_<?php echo $mode; ?>" class="focusable" onchange="TBG.Search.bulkChanger('<?php echo $mode; ?>');">
                    <option value="0"><?php echo __('No priority'); ?></option>
                    <?php foreach (\thebuggenie\core\entities\Priority::getAll() as $priority_id => $priority): ?>
                        <?php if (!$priority->canUserSet($tbg_user)) continue; ?>
                        <option value="<?php echo $priority_id; ?>"><?php echo $priority->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
            </span>
            <span class="bulk_action_subcontainer" id="bulk_action_subcontainer_set_category_<?php echo $mode; ?>" style="display: none;">
                <select name="category" id="bulk_action_set_category_<?php echo $mode; ?>" class="focusable" onchange="TBG.Search.bulkChanger('<?php echo $mode; ?>');">
                    <option value="0"><?php echo __('No category'); ?></option>
                    <?php foreach (\thebuggenie\core\entities\Category::getAll() as $category_id => $category): ?>
                        <?php if (!$category->canUserSet($tbg_user)) continue; ?>
                        <option value="<?php echo $category_id; ?>"><?php echo $category->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
            </span>
            <span class="bulk_action_subcontainer" id="bulk_action_subcontainer_set_severity_<?php echo $mode; ?>" style="display: none;">
                <select name="severity" id="bulk_action_set_severity_<?php echo $mode; ?>" class="focusable" onchange="TBG.Search.bulkChanger('<?php echo $mode; ?>');">
                    <option value="0"><?php echo __('No severity'); ?></option>
                    <?php foreach (\thebuggenie\core\entities\Severity::getAll() as $severity_id => $severity): ?>
                        <?php if (!$severity->canUserSet($tbg_user)) continue; ?>
                        <option value="<?php echo $severity_id; ?>"><?php echo $severity->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
            </span>
            <span class="bulk_action_subcontainer" id="bulk_action_subcontainer_perform_workflow_step_<?php echo $mode; ?>" style="display: none;">
                <input type="hidden" id="bulk_action_subcontainer_perform_workflow_step_<?php echo $mode; ?>_url" value="<?php echo make_url('get_partial_for_backdrop', array('key' => 'bulk_workflow')); ?>">
            </span>
            <input type="submit" class="button button-silver disabled" value="<?php echo __('Apply'); ?>" id="bulk_action_submit_<?php echo $mode; ?>">
        </div>
    </form>
    <?php if ($mode == 'bottom'): ?>
        <script type="text/javascript">
            require(['domReady', 'thebuggenie/tbg'], function (domReady, tbgjs) {
                domReady(function () {
                    tbgjs.Search.checkToggledCheckboxes();
                });
            });
        </script>
    <?php endif; ?>
</div>
