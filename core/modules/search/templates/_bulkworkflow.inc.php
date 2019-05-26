<?php /** @var \thebuggenie\core\entities\WorkflowTransition $transition */ ?>
<div class="backdrop_box medium" id="viewissue_add_item_div">
    <div class="backdrop_detail_header">
        <span><?php echo __('Perform workflow step'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="header"><?php echo __('%number_of issues selected', array('%number_of' => count($issues))); ?></div>
        <?php if (!$project instanceof \thebuggenie\core\entities\Project): ?>
            <div class="content faded_out">
                <?php echo __('You can only apply workflow transitions on issues in the same project.'); ?>
            </div>
        <?php else: ?>
            <?php if (!count($available_transitions)): ?>
                <div class="content faded_out">
                    <?php echo __('There are no workflow transitions that can be applied to all these issues. Try selecting fewer issues, or issues that are currently at the same (or similar) workflow step(s).'); ?>
                </div>
            <?php else: ?>
                <div class="content"><?php echo __('Perform the following workflow transition on these issues'); ?></div>
                <?php foreach ($available_transitions as $transition): ?>
                    <div class="backdrop_details_submit">
                        <span class="explanation"><?= $transition->getDescription(); ?></span>
                        <div class="submit_container">
                            <?php if ($transition->hasTemplate()): ?>
                                <?php echo javascript_link_tag(image_tag('spinning_16.gif', ['style' => 'display: none;', 'id' => 'transition_working_'.$transition->getID().'_indicator']) . $transition->getName(), ['onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', ['key' => 'workflow_transition', 'transition_id' => $transition->getID()])."&project_key=".$project->getKey()."&issue_ids[]=".join('&issue_ids[]=', array_keys($issues))."');", 'class' => 'button button-silver workflow_transition_submit_button']); ?>
                            <?php else: ?>
                                <form action="<?php echo make_url('transition_issues', ['project_key' => $project->getKey(), 'transition_id' => $transition->getID()]); ?>" method="post" onsubmit="TBG.Search.bulkWorkflowTransition('<?php echo make_url('transition_issues', array('project_key' => $project->getKey(), 'transition_id' => $transition->getID())); ?>', <?php echo $transition->getID(); ?>);return false;" accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" id="bulk_workflow_transition_form">
                                    <?php foreach ($issues as $issue_id => $i): ?>
                                        <input type="hidden" name="issue_ids[<?php echo $issue_id; ?>]" value="<?php echo $issue_id; ?>">
                                    <?php endforeach; ?>
                                    <button type="submit" class="workflow_transition_submit_button" id="transition_working_<?php echo $transition->getID(); ?>_submit"><?php echo image_tag('spinning_16.gif', ['style' => 'display: none;', 'id' => 'transition_working_'.$transition->getID().'_indicator']) . $transition->getName(); ?></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
