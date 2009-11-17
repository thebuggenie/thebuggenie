<?php

	$bugs_response->setTitle(__('Manage projects - %project% - editions, components and releases', array('%project%' => $theProject->getName())));
	
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php include_component('configleftmenu', array('selected_section' => 10)); ?>
<td valign="top">
	<?php include_template('configuration/project_header', array('theProject' => $theProject, 'mode' => 3)); ?>
	<table style="width: 700px; margin-top: 10px;" cellpadding=0 cellspacing=0>
		<tr>
			<td style="width: auto; padding-right: 5px; vertical-align: top;">
				<div class="config_header nobg"><?php echo bugs_helpBrowserHelper('setup_editions', image_tag('help.png', array('style' => "float: right;"))); ?><b><?php echo __('Editions'); ?></b></div>
			<?php if ($theProject->isEditionsEnabled()): ?>
				<div style="padding: 0px 0px 5px 3px;"><?php echo __('Click an edition name to edit its information and settings, as well as manage builds for that edition'); ?>.</div>
				<div class="faded_medium" id="no_editions" style="padding: 5px;<?php if (count($theProject->getEditions()) > 0): ?> display: none;<?php endif; ?>"><?php echo __('There are no editions'); ?></div>
				<table cellpadding=0 cellspacing=0 style="width: 100%;">
					<tbody id="edition_table">
					<?php foreach ($theProject->getEditions() as $edition): ?>
						<?php include_template('editionbox', array('theProject' => $theProject, 'edition' => $edition)); ?>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_add_edition', array('project_id' => $theProject->getID())); ?>" method="post" id="add_edition_form" onsubmit="addEdition('<?php echo make_url('configure_projects_add_edition', array('project_id' => $theProject->getID())); ?>');return false;">
					<table cellpadding=0 cellspacing=0 style="width: 100%;">
					<tr>
					<td style="padding: 3px; border-bottom: 1px solid #DDD;" colspan=3><br><b><?php echo __('Add an edition'); ?></b></td>
					<tr>
					<td style="width: auto; padding: 2px;" colspan=2><input type="text" style="width: 330px;" name="e_name"></td>
					<td style="padding: 0px; text-align: right;"><input type="submit" value="<?php echo __('Add'); ?>"></td>
					</tr>
					</table>
					<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="edition_add_indicator">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
							<td style="padding: 0px; text-align: left;"><?php echo __('Adding edition, please wait'); ?>...</td>
						</tr>
					</table>
					</form>
				<?php endif; ?>
			<?php else: ?>
				<div style="padding: 2px 5px 5px 5px;" class="faded_medium"><?php echo __('This project does not use editions'); ?>.<br><?php echo __('Editions can be enabled in project settings'); ?>.</div>
			<?php endif; ?>
			</td>
			<td style="width: 350px; padding-right: 5px; vertical-align: top;">
				<div class="config_header nobg"><?php echo bugs_helpBrowserHelper('setup_components', image_tag('help.png', array('style' => "float: right;"))); ?><b><?php echo __('Components'); ?></b></div>
			<?php if ($theProject->isComponentsEnabled()): ?>
				<div style="padding: 0px 0px 5px 3px;"><?php echo __('This is a list of all the components available for this project'); ?>.</div>	
				<div class="faded_medium" id="no_components" style="padding: 5px;<?php if (count($theProject->getComponents()) > 0): ?> display: none;<?php endif; ?>"><?php echo __('There are no components'); ?></div>
				<table cellpadding=0 cellspacing=0 style="width: 100%;">
					<tbody id="component_table">
					<?php foreach ($theProject->getComponents() as $component): ?>
						<?php include_template('componentbox', array('component' => $component)); ?>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_add_component', array('project_id' => $theProject->getID())); ?>" method="post" id="add_component_form" onsubmit="addComponent('<?php echo make_url('configure_projects_add_component', array('project_id' => $theProject->getID())); ?>');return false;">
					<table cellpadding=0 cellspacing=0 style="width: 100%;">
					<tr>
					<td style="padding: 3px; border-bottom: 1px solid #DDD;" colspan=3><br><b><?php echo __('Add a component'); ?></b></td>
					<tr>
					<td style="width: auto; padding: 2px;" colspan=2><input type="text" style="width: 290px;" name="c_name"></td>
					<td style="padding: 0px; text-align: right;"><input type="submit" value="<?php echo __('Add'); ?>"></td>
					</tr>
					</table>
					<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="component_add_indicator">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
							<td style="padding: 0px; text-align: left;"><?php echo __('Adding component, please wait'); ?>...</td>
						</tr>
					</table>
					</form>
				<?php endif; ?>
			<?php else: ?>
				<div style="padding: 2px 5px 5px 5px;" class="faded_medium"><?php echo __('This project does not use components'); ?>.<br><?php echo __('Components can be enabled in project settings'); ?>.</div>
			<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding-right: 5px;">
			<div class="config_header nobg" style="margin-top: 10px;"><?php echo bugs_helpBrowserHelper('setup_releases', image_tag('help.png', array('style' => "float: right;"))); ?><b><?php echo __('Builds / releases'); ?></b></div>
			<?php if ($theProject->isBuildsEnabled()): ?>
				<?php include_template('builds', array('parent' => $theProject, 'access_level' => $access_level)); ?>
			<?php else: ?>
				<div style="padding: 2px 5px 5px 5px;" class="faded_medium"><?php echo __('This project does not use builds / releases'); ?>.<br><?php echo __('Builds / releases can be enabled in project settings'); ?>.</div>
			<?php endif; ?>
			</td>
		</tr>
	</table>
</td>
</tr>
</table>