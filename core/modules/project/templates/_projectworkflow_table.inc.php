<form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" onsubmit="TBG.Project.workflow('<?php echo make_url('configure_projects_workflow', array('project_id' => $project->getID())); ?>');return false;" action="<?php echo make_url('configure_projects_workflow', array('project_id' => $project->getID())); ?>" method="post" id="workflow_form2" enctype="multipart/form-data">
    <div class="backdrop_detail_header">
        <a href="javascript:void(0);" class="back_link" onclick="$('change_workflow_table').update('');$('change_workflow_box').show();"><?= fa_image_tag('chevron-left'); ?></a>
        <span><?php echo __('Change workflow'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="TBG.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div class="backdrop_detail_content">
        <?php echo __("Issues in the workflow step on the left will have their workflow step changed to the one on the right. This will change the issue's status to the one assigned to the new step."); ?>
        <input type="hidden" name="workflow_id" value="<?php echo $new_workflow->getID(); ?>" />
        <input type="hidden" name="project_id" value="<?php echo $project->getID(); ?>" />
        <div class="workflow_change_div">
            <table cellpadding="0" cellspacing="0" class="padded_table">
                <tbody class="hover_highlight">
                <tr>
                    <td style="width: 450px; padding-right: 10px;">
                        <h4><?php echo __('Old: %old_workflow_name', array('%old_workflow_name' => $project->getWorkflowScheme()->getName())); ?></h4>
                    </td>
                    <td style="width: 450px;">
                        <h4><?php echo __('New: %new_workflow_name', array('%new_workflow_name' => $new_workflow->getName())); ?></h4>
                    </td>
                </tr>
                <?php foreach ($project->getIssuetypeScheme()->getIssuetypes() as $issuetype): ?>
                    <tr>
                        <td><h5><?php echo $issuetype->getName().' - '.$project->getWorkflowScheme()->getWorkflowForIssuetype($issuetype)->getName(); ?></h5></td>
                        <td><h5><?php echo $issuetype->getName().' - '.$new_workflow->getWorkflowForIssuetype($issuetype)->getName(); ?></h5></td>
                    </tr>
                    <?php foreach ($project->getWorkflowScheme()->getWorkflowForIssuetype($issuetype)->getSteps() as $step): ?>
                        <tr>
                            <td><?php echo $step->getName(); ?></td>
                            <td>
                                <select style="width: 100%" name="new_step_<?php echo $issuetype->getID(); ?>_<?php echo $step->getID(); ?>">
                                    <?php foreach ($new_workflow->getWorkflowForIssuetype($issuetype)->getSteps() as $new_step): ?>
                                        <option value="<?php echo $new_step->getID(); ?>"<?php if (mb_strtolower(trim($new_step->getName())) == mb_strtolower(trim($step->getName()))): ?> selected="selected"<?php endif; ?>><?php echo $new_step->getName(); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="backdrop_details_submit">
        <span class="explanation"><?php echo __('When you are done, click "%update_workflow" to switch to the new workflow', array('%update_workflow' => __('Update workflow'))); ?></span>
        <div class="submit_container">
            <button class="button button-silver" type="submit"><?php echo image_tag('spinning_16.gif', ['id' => 'update_workflow_indicator', 'style' => 'display: none;']) . __('Update workflow'); ?></button>
        </div>
    </div>
</form>
