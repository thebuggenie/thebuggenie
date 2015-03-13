<?php

    $tbg_response->setTitle(__('Manage projects'));
    
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0 class="configuration_page">
    <tr>
        <?php include_component('leftmenu', array('selected_section' => 10)); ?>
        <td valign="top" style="padding-left: 15px;">
            <div style="width: 730px;">
                <h3><?php echo __('Configure projects'); ?></h3>
                <div class="content faded_out">
                    <p>
                        <?php echo __('More information about projects, editions, builds and components is available from the %wiki_help_section.', array('%wiki_help_section' => link_tag(make_url('publish_article', array('article_name' => 'Category:Help')), '<b>'.__('Wiki help section').'</b>'))); ?>
                        <?php if (\thebuggenie\core\framework\Context::getScope()->getMaxProjects()): ?>
                            <div class="faded_out dark" style="margin: 12px 0;">
                                <?php echo __('This instance is using %num of max %max projects', array('%num' => '<b id="current_project_num_count">'.\thebuggenie\core\entities\Project::getProjectsCount().'</b>', '%max' => '<b>'.\thebuggenie\core\framework\Context::getScope()->getMaxProjects().'</b>')); ?>
                            </div>
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
                    <div class="lightyellowbox" style="padding: 5px; margin: 10px 0;<?php if (!\thebuggenie\core\framework\Context::getScope()->hasProjectsAvailable()): ?> display: none;<?php endif; ?>" id="add_project_div">
                        <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" method="post" id="add_project_form" onsubmit="TBG.Project.add('<?php echo make_url('configure_projects_add_project'); ?>');return false;">
                            <div style="height: 25px;">
                                <input type="hidden" name="add_project" value="true">
                                <label for="add_project_input"><?php echo __('Create a new project'); ?></label>
                                <input type="text" style="width: 300px;" id="add_project_input" name="p_name">
                                <div style="text-align: right; float: right;" class="button-group">
                                    <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; margin: 3px 5px -4px;', 'id' => 'project_add_indicator')); ?>
                                    <input type="submit" value="<?php echo __('Create project'); ?>" class="button button-silver first">
                                    <input type="button" class="button button-silver last" onclick="$('add_project_additional').toggle();$(this).toggleClassName('button-pressed');" value="&#x25BC;">
                                </div>
                            </div>
                            <div id="add_project_additional" style="display: none; margin-top: 15px; font-size: 0.9em;">
                                <div class="header"><?php echo __('Additional settings'); ?></div>
                                <label class="optional" for="add_project_workflow_scheme"><?php echo __('Workflow scheme'); ?></label>
                                <select name="workflow_scheme_id" id="add_project_workflow_scheme">
                                    <?php foreach (\thebuggenie\core\entities\WorkflowScheme::getAll() as $workflowscheme): ?>
                                        <option value="<?php echo $workflowscheme->getID(); ?>" <?php if (\thebuggenie\core\framework\Settings::getCoreWorkflowScheme()->getID() == $workflowscheme->getID()) echo ' selected'; ?>><?php echo $workflowscheme->getName(); ?></option>
                                    <?php endforeach; ?>
                                </select><br>
                                <label class="optional" for="add_project_issuetype_scheme"><?php echo __('Issue type scheme'); ?></label>
                                <select name="issuetype_scheme_id" id="add_project_issuetype_scheme">
                                    <?php foreach (\thebuggenie\core\entities\IssuetypeScheme::getAll() as $issuetypescheme): ?>
                                        <option value="<?php echo $issuetypescheme->getID(); ?>" <?php if (\thebuggenie\core\framework\Settings::getCoreIssuetypeScheme()->getID() == $issuetypescheme->getID()) echo ' selected'; ?>><?php echo $issuetypescheme->getName(); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
                <h4><?php echo __('Active projects'); ?></h4>
                <div id="project_table">
                    <?php if (count($active_projects) > 0): ?>
                        <?php foreach ($active_projects as $project): ?>
                            <?php include_component('projectbox', array('project' => $project, 'access_level' => $access_level)); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div id="noprojects_tr" style="padding: 3px; color: #AAA;<?php if (count($active_projects) > 0): ?> display: none;<?php endif;?>">
                    <?php echo __('There are no projects available'); ?>
                </div>
                <h4 style="margin-top: 30px;"><?php echo __('Archived projects'); ?></h4>
                <div id="project_table_archived">
                    <?php foreach ($archived_projects as $project): ?>
                        <?php include_component('projectbox', array('project' => $project, 'access_level' => $access_level)); ?>
                    <?php endforeach; ?>
                </div>
                <div id="noprojects_tr_archived" style="padding: 3px; color: #AAA;<?php if (count($archived_projects) > 0): ?> display: none;<?php endif;?>">
                    <?php echo __('There are no projects available'); ?>
                </div>
            </div>
        </td>
    </tr>
</table>
