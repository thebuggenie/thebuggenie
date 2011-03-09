<?php if ($parent instanceof TBGProject): ?>
	<?php $url = make_url('configure_projects_add_build', array('project_id' => $parent->getID())); ?>
<?php else: ?>
	<?php $url = make_url('configure_edition_add_build', array('project_id' => $parent->getProject()->getID(), 'edition_id' => $parent->getID())); ?>
<?php endif; ?>
<div id="<?php echo ($parent instanceof TBGProject) ? 'project' : 'edition'; ?>_builds"<?php if ($parent instanceof TBGProject && !$parent->isBuildsEnabled()): ?> style="display: none;"<?php endif; ?>>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo $url; ?>" method="post" id="add_build_form" onsubmit="addBuild('<?php echo $url; ?>');return false;">
		<div class="rounded_box lightyellow" style="vertical-align: middle; padding: 5px; font-size: 12px;">
			<table cellpadding=0 cellspacing=0 style="width: 100%;">
				<tr>
					<td style="width: 150px; text-align: left;"><label for="build_name"><?php echo __('Release name:'); ?></label></td>
					<td style="width: 300px; text-align: left;"><input type="text" name="build_name" id="build_name" style="width: 300px;" value="<?php echo $parent->getName(). ' '.__('version 0.0.0'); ?>"></td>
					<td style="text-align: right; font-size: 0.9em;"><label for="ver_mj"><?php echo __('Version: %version_number%', array('%version_number%' => '')); ?></label></td>
					<td style="width: 100px; text-align: left;"><input type="text" name="ver_mj" id="ver_mj" style="width: 20px; font-size: 0.9em; text-align: center;" value="0">&nbsp;.&nbsp;<input type="text" name="ver_mn" id="ver_mn" style="width: 20px; font-size: 0.9em; text-align: center;" value="0">&nbsp;.&nbsp;<input type="text" name="ver_rev" id="ver_rev" style="width: 20px; font-size: 0.9em; text-align: center;" value="0"></td>
					<td style="text-align: right;" colspan="2"><input type="submit" style="font-weight: bold;" value="<?php echo __('Add release'); ?>"></td>
				</tr>
			</table>
		</div>
		<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="build_add_indicator">
			<tr>
				<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
				<td style="padding: 0px; text-align: left;"><?php echo __('Adding release, please wait'); ?>...</td>
			</tr>
		</table>
	</form>
	<div id="build_table" style="overflow: auto; height: 300px;">
	<?php foreach ($parent->getBuilds() as $build): ?>
		<span id="build_list_<?php echo $build->getID(); ?>">
			<?php include_template('configuration/buildbox', array('build' => $build, 'access_level' => $access_level)); ?>
		</span>
	<?php endforeach; ?>
	</div>
	<div id="no_builds" style="padding: 5px;<?php if (count($parent->getBuilds()) > 0): ?> display: none;<?php endif; ?>" class="faded_out"><?php echo __('There are no releases'); ?></div>
</div>