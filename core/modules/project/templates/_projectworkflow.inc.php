<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <?php echo __('Change workflow'); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div id="change_workflow_box">
            <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" onsubmit="TBG.Project.workflowtable('<?php echo make_url('configure_projects_workflow_table', array('project_id' => $project->getID())); ?>', <?php echo $project->getID(); ?>);return false;" action="<?php echo make_url('configure_projects_workflow_table', array('project_id' => $project->getID())); ?>" method="post" id="workflow_form" enctype="multipart/form-data">
                <h3><?php echo __('New workflow scheme:'); ?><select name="new_workflow">
                    <?php foreach (\thebuggenie\core\entities\WorkflowScheme::getAll() as $scheme): ?>
                        <?php if ($scheme == $project->getWorkflowScheme()): continue; endif; ?>
                        <option value="<?php echo $scheme->getID(); ?>"><?php echo $scheme->getName(); ?></option>
                    <?php endforeach; ?>
                    <?php if (count(\thebuggenie\core\entities\WorkflowScheme::getAll()) < 2): ?>
                        <option disabled="disabled" value="0"><?php echo __('No other workflows'); ?></option>
                    <?php endif; ?>
                </select></h3>
                <input class="button button-silver" type="submit" value="<?php echo __('Continue'); ?>">
            </form>
        </div>
        <div id="change_workflow_table" style="display: none;">
        </div>
        <div id="change_workflow_spinner" style="display: none;">
            <?php echo image_tag('spinning_32.gif'); ?>
        </div>
    </div>
    <div class="backdrop_detail_footer">
        <?php echo javascript_link_tag(__('Close popup'), array('onclick' => 'TBG.Main.Helpers.Backdrop.reset();')); ?>
    </div>
</div>
