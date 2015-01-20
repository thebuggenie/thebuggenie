<?php if ($access_level == \thebuggenie\core\framework\Settings::ACCESS_FULL): ?>
    <div class="project_save_container">
        <div class="button button-silver" id="add_edition_button" style="<?php if (!$project->isEditionsEnabled()): ?> display: none;<?php endif; ?>" onclick="$('add_edition_form').toggle();if ($('add_edition_form').visible()) $('edition_name').focus();"><?php echo __('Add an edition'); ?></div>
        <div class="button button-silver" id="add_component_button" style="<?php if (!$project->isComponentsEnabled()): ?> display: none;<?php endif; ?>" onclick="$('add_component_form').toggle();if ($('add_component_form').visible()) $('component_name').focus();"><?php echo __('Add a component'); ?></div>
    </div>
    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_add_edition', array('project_id' => $project->getID())); ?>" method="post" id="add_edition_form" onsubmit="TBG.Project.Edition.add('<?php echo make_url('configure_projects_add_edition', array('project_id' => $project->getID())); ?>');return false;" style="display: none;">
        <div class="lightyellowbox">
            <input class="button button-silver" style="float: right; margin: -2px 0;" type="submit" value="<?php echo __('Create'); ?>">
            <label for="edition_name"><?php echo __('Add edition'); ?></label>
            <input type="text" id="edition_name" name="e_name" style="width: 500px;">
        </div>
        <table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="edition_add_indicator">
            <tr>
                <td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
                <td style="padding: 0px; text-align: left;"><?php echo __('Adding edition, please wait'); ?>...</td>
            </tr>
        </table>
    </form>
    <form accept-charset="<?php echo \thebuggenie\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_add_component', array('project_id' => $project->getID())); ?>" method="post" id="add_component_form" onsubmit="TBG.Project.Component.add('<?php echo make_url('configure_projects_add_component', array('project_id' => $project->getID())); ?>');return false;" style="display: none;">
        <div class="lightyellowbox">
            <input class="button button-silver" style="float: right; margin: -2px 0;" type="submit" value="<?php echo __('Create'); ?>">
            <label for="component_name"><?php echo __('Add component'); ?></label>
            <input type="text" id="component_name" name="c_name" style="width: 500px;">
        </div>
        <table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="component_add_indicator">
            <tr>
                <td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
                <td style="padding: 0px; text-align: left;"><?php echo __('Adding component, please wait'); ?>...</td>
            </tr>
        </table>
    </form>
<?php endif; ?>
<h4><?php echo __('Project editions'); ?></h4>
<div id="project_editions"<?php if (!$project->isEditionsEnabled()): ?> style="display: none;"<?php endif; ?>>
    <div class="faded_out" id="no_editions" style="padding: 5px;<?php if (count($project->getEditions()) > 0): ?> display: none;<?php endif; ?>"><?php echo __('There are no editions'); ?></div>
    <ul id="edition_table">
        <?php foreach ($project->getEditions() as $edition): ?>
            <?php include_component('project/editionbox', array('theProject' => $project, 'edition' => $edition, 'access_level' => $access_level)); ?>
        <?php endforeach; ?>
    </ul>
</div>
<div style="padding: 2px 5px 5px 5px;<?php if ($project->isEditionsEnabled()): ?> display: none;<?php endif; ?>" id="project_editions_disabled" class="faded_out"><?php echo __('This project does not use editions. Editions can be enabled in %advanced_settings', array('%advanced_settings' => javascript_link_tag(__('Advanced settings'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_settings', 'project_config_menu');")))); ?>.</div>
<h4><?php echo __('Project components'); ?></h4>
<div id="project_components"<?php if (!$project->isComponentsEnabled()): ?> style="display: none;"<?php endif; ?>>
    <div class="faded_out" id="no_components" style="padding: 5px;<?php if (count($project->getComponents()) > 0): ?> display: none;<?php endif; ?>"><?php echo __('There are no components'); ?></div>
    <ul id="component_table">
        <?php foreach ($project->getComponents() as $component): ?>
            <?php include_component('project/componentbox', array('component' => $component, 'access_level' => $access_level)); ?>
        <?php endforeach; ?>
    </ul>
</div>
<div style="padding: 2px 5px 5px 5px;<?php if ($project->isComponentsEnabled()): ?> display: none;<?php endif; ?>" id="project_components_disabled" class="faded_out"><?php echo __('This project does not use components. Components can be enabled in %advanced_settings', array('%advanced_settings' => javascript_link_tag(__('Advanced settings'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_settings', 'project_config_menu');")))); ?>.</div>
