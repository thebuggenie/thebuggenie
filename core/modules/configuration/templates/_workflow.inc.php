<li id="workflow_<?php echo $workflow->getID(); ?>" class="greybox" style="margin-bottom: 5px;">
    <table>
        <tr>
            <td class="workflow_info scheme">
                <div class="workflow_name"><?php echo $workflow->getName(); ?></div>
                <?php if ($workflow->getDescription()): ?>
                    <div class="workflow_description"><?php echo $workflow->getDescription(); ?></div>
                <?php endif; ?>
            </td>
            <td class="workflow_<?php if (!$workflow->isActive()) echo 'in'; ?>active"><?php echo ($workflow->isActive()) ? __('Active') : __('Inactive'); ?></td>
            <td class="workflow_steps"><?php echo __('Steps: %number_of_workflow_steps', array('%number_of_workflow_steps' => '<span>'.$workflow->getNumberOfSteps().'</span>')); ?></td>
            <td class="workflow_actions">
                <div class="button-group">
                    <?php echo link_tag(make_url('configure_workflow_steps', array('workflow_id' => $workflow->getID())), image_tag('icon_workflow_scheme_edit.png', array('title' => __('Show workflow details'))), array('class' => 'button button-icon button-silver')); ?></a>
                    <?php if (\thebuggenie\core\framework\Context::getScope()->hasCustomWorkflowsAvailable()): ?>
                        <a href="javascript:void(0);" onclick="$('copy_workflow_<?php echo $workflow->getID(); ?>_popup').toggle();" class="button button-icon button-silver copy_workflow_link"><?php echo image_tag('icon_copy.png', array('title' => __('Create a copy of this workflow'))); ?></a>
                    <?php endif; ?>
                    <?php if (!$workflow->isCore()): ?>
                        <?php if ($workflow->isInUse()): ?>
                            <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('<?php echo __('Cannot delete workflow'); ?>', '<?php echo __('This workflow can not be deleted as it is being used by %number_of_schemes workflow scheme(s)', array('%number_of_schemes' => $workflow->getNumberOfSchemes())); ?>');" class="button button-icon button-silver"><?php echo image_tag('icon_delete.png', array('title' => __('Delete this workflow'))); ?></a>
                        <?php else: ?>
                            <a href="javascript:void(0);" onclick="$('delete_workflow_<?php echo $workflow->getID(); ?>_popup').toggle();" class="button button-icon button-silver"><?php echo image_tag('icon_delete.png', array('title' => __('Delete this workflow'))); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>
</li>
<?php if (\thebuggenie\core\framework\Context::getScope()->hasCustomWorkflowsAvailable()): ?>
    <li class="rounded_box white shadowed copy_workflow_popup" id="copy_workflow_<?php echo $workflow->getID(); ?>_popup" style="margin-bottom: 5px; padding: 10px; display: none;">
        <div class="header"><?php echo __('Copy workflow'); ?></div>
        <div class="content">
            <?php echo __('Please enter the name of the new workflow'); ?><br>
            <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_workflow_copy_workflow', array('workflow_id' => $workflow->getID())); ?>" onsubmit="TBG.Config.Workflows.Workflow.copy('<?php echo make_url('configure_workflow_copy_workflow', array('workflow_id' => $workflow->getID())); ?>', <?php echo $workflow->getID(); ?>);return false;" id="copy_workflow_<?php echo $workflow->getID(); ?>_form">
                <label for="copy_workflow_<?php echo $workflow->getID(); ?>_new_name"><?php echo __('New name'); ?></label>
                <input type="text" name="new_name" id="copy_workflow_<?php echo $workflow->getID(); ?>_new_name" value="<?php echo __('Copy of %old_name', array('%old_name' => addslashes($workflow->getName()))); ?>" style="width: 300px;">
                <div style="text-align: right;">
                    <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'copy_workflow_'.$workflow->getID().'_indicator')); ?>
                    <input type="submit" value="<?php echo __('Copy workflow'); ?>">
                </div>
            </form>
        </div>
    </li>
<?php endif; ?>
<?php if (!$workflow->isCore() && !$workflow->isInUse()): ?>
    <li class="rounded_box white shadowed" id="delete_workflow_<?php echo $workflow->getID(); ?>_popup" style="margin-bottom: 5px; padding: 10px; display: none;">
        <div class="header"><?php echo __('Are you sure?'); ?></div>
        <div class="content">
            <?php echo __('Please confirm that you want to delete this workflow.'); ?><br>
            <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_workflow_delete_workflow', array('workflow_id' => $workflow->getID())); ?>" onsubmit="TBG.Config.Workflows.Workflow.remove('<?php echo make_url('configure_workflow_delete_workflow', array('workflow_id' => $workflow->getID())); ?>', <?php echo $workflow->getID(); ?>);return false;" id="delete_workflow_<?php echo $workflow->getID(); ?>_form">
                <div style="text-align: right;">
                    <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'delete_workflow_'.$workflow->getID().'_indicator')); ?>
                    <input type="submit" value="<?php echo __('Yes, delete it'); ?>"><?php echo __('%delete or %cancel', array('%delete' => '', '%cancel' => '<b>'.javascript_link_tag(__('cancel'), array('onclick' => "$('delete_workflow_{$workflow->getID()}_popup').toggle();")).'</b>')); ?>
                </div>
            </form>
        </div>
    </li>
<?php endif; ?>
