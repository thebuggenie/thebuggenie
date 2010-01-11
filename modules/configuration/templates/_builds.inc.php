<?php if ($parent instanceof TBGProject): ?>
	<?php $url = make_url('configure_projects_add_build', array('project_id' => $parent->getID())); ?>
<?php else: ?>
	<?php $url = make_url('configure_edition_add_build', array('project_id' => $parent->getProject()->getID(), 'edition_id' => $parent->getID())); ?>
<?php endif; ?>
<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo $url; ?>" method="post" id="add_build_form" onsubmit="addBuild('<?php echo $url; ?>');return false;">
	<table cellpadding=0 cellspacing=0 style="width: 100%;">
		<tr>
			<td style="width: 150px; text-align: left;"><label for="build_name"><?php echo __('Build / release name:'); ?></label></td>
			<td style="width: auto;"><input type="text" name="build_name" id="build_name" style="width: 100%;" value="<?php echo $parent->getName(). ' '.__('version 0.0.0'); ?>"></td>
			<td style="width: 30px; text-align: right;"><label for="ver_mj"><?php echo __('Ver: %version_number%', array('%version_number%' => '')); ?></label></td>
			<td style="width: 100px; text-align: right;"><input type="text" name="ver_mj" id="ver_mj" style="width: 25px; text-align: center;" value="0">&nbsp;.&nbsp;<input type="text" name="ver_mn" id="ver_mn" style="width: 25px; text-align: center;" value="0">&nbsp;.&nbsp;<input type="text" name="ver_rev" id="ver_rev" style="width: 25px; text-align: center;" value="0"></td>
			<td style="width: 130px; text-align: right;"><input type="submit" style="width: 125px;" value="<?php echo __('Add build / release'); ?>"></td>
		</tr>
	</table>
	<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="build_add_indicator">
		<tr>
			<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
			<td style="padding: 0px; text-align: left;"><?php echo __('Adding build / release, please wait'); ?>...</td>
		</tr>
	</table>
</form>
<div class="config_header nobg" style="margin-top: 20px;"><b><?php echo __('Existing builds / releases'); ?></b></div>
<span id="build_table">
<?php foreach ($parent->getBuilds() as $build): ?>
	<span id="build_list_<?php echo $build->getID(); ?>">
		<?php include_template('buildbox', array('build' => $build, 'access_level' => $access_level)); ?>
	</span>
<?php endforeach; ?>
</span>