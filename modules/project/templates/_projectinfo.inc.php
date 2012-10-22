<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>" method="post" id="project_info" onsubmit="TBG.Project.submitInfo('<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>'); return false;">
<?php endif; ?>
<h3><?php echo __('Editing project details'); ?></h3>
<?php //include_component('main/hideableInfoBox', array('key' => 'projectinfo_didyouknow', 'title' => __('You can set a project icon too'), 'content' => __('By creating a PNG image in the project_icons directory of your installation, with the same name as the project key, this image will be shown next to your project throughout The Bug Genie. We recommend images are 16x16 in size. For further information please see the documentation.'))); ?>
<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
	<tr>
		<td style="width: 200px;"><label for="project_name"><?php echo __('Project name'); ?></label></td>
		<td style="width: 580px;">
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<input type="text" name="project_name" id="project_name_input" onblur="TBG.Project.updatePrefix('<?php echo make_url('configure_project_get_updated_key', array('project_id' => $project->getID())); ?>', <?php echo $project->getID(); ?>);" value="<?php print $project->getName(); ?>" style="width: 100%;">
			<?php else: ?>
				<?php echo $project->getName(); ?>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td style="width: 200px;"><label for="project_name"><?php echo __('Project key'); ?></label></td>
		<td style="width: 580px; position: relative;">
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<div id="project_key_indicator" class="semi_transparent" style="position: absolute; height: 23px; background-color: #FFF; width: 210px; text-align: center; display: none;"><?php echo image_tag('spinning_16.gif'); ?></div>
				<input type="text" name="project_key" id="project_key_input" value="<?php print $project->getKey(); ?>" style="width: 200px;">
			<?php else: ?>
				<?php echo $project->getKey(); ?>
			<?php endif; ?>
			<div style="float: right; margin-right: 5px;" class="faded_out"><?php echo __('This is a part of all urls referring to this project'); ?></div>
		</td>
	</tr>
	<tr>
		<td><label><?php echo __('Project icons'); ?></label></td>
		<td style="padding: 15px 0;">
			<?php if ($project->hasSmallIcon() || $project->hasLargeIcon()): ?>
				<div class="button button-red" style="float: right; margin-left: 5px;" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Reset project icons?'); ?>', '<?php echo __('Do you really want to reset the project icons? Please confirm.'); ?>', {yes: {click: function() {TBG.Project.resetIcons('<?php echo make_url('configure_projects_icons', array('project_id' => $project->getID())); ?>');}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><span><?php echo __('Reset icons'); ?></span></div>
			<?php endif; ?>
			<div class="button button-blue" style="float: right;" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_icons', 'project_id' => $project->getId())); ?>');"><span><?php echo ($project->hasSmallIcon() || $project->hasLargeIcon()) ? __('Change project icons') : __('Set project icons'); ?></span></div>
			<?php echo image_tag($project->getSmallIconName(), array('style' => 'float: left; margin: 8px 10px 0 0; width: 16px; height: 16px;'), $project->hasSmallIcon()); ?>
			<?php echo image_tag($project->getLargeIconName(), array('style' => 'width: 32px; height: 32px;'), $project->hasLargeIcon()); ?> &nbsp; 
		</td>
	</tr>
	<tr>
		<td><label for="client"><?php echo __('Subproject of'); ?></label></td>
		<td>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<select name="subproject_id" id="subproject_id" style="width: 100%">
					<option value="0"<?php if (!($project->hasParent())): ?> selected<?php endif; ?>><?php echo __('Not a subproject'); ?></option>
					<?php foreach ($valid_subproject_targets as $aproject): ?>
						<option value=<?php echo $aproject->getID(); ?><?php if ($project->hasParent() && $project->getParent()->getID() == $aproject->getID()): ?> selected<?php endif; ?>><?php echo $aproject->getName(); ?></option>
					<?php endforeach; ?>
				</select>
			<?php else: ?>
				<?php if (!($project->hasParent())): echo __('Not a subproject'); else: echo $project->getParent()->getName(); endif; ?>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="client"><?php echo __('Client'); ?></label></td>
		<td>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<select name="client" id="client" style="width: 100%">
					<option value="0"<?php if ($project->getClient() == null): ?> selected<?php endif; ?>><?php echo __('No client'); ?></option>
					<?php foreach (TBGClient::getAll() as $client): ?>
						<option value=<?php echo $client->getID(); ?><?php if (($project->getClient() instanceof TBGClient) && $project->getClient()->getID() == $client->getID()): ?> selected<?php endif; ?>><?php echo $client->getName(); ?></option>
					<?php endforeach; ?>
				</select>
			<?php else: ?>
				<?php if ($project->getClient() == null): echo __('No client'); else: echo $project->getClient()->getName(); endif; ?>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="use_prefix"><?php echo __('Use prefix'); ?></label></td>
		<td>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<select name="use_prefix" id="use_prefix" style="width: 70px;" onchange="if ($('use_prefix').getValue() == 1) { $('prefix').enable(); } else { $('prefix').disable(); }">
					<option value=1<?php if ($project->usePrefix()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
					<option value=0<?php if (!$project->usePrefix()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
				</select>
			<?php else: ?>
				<?php echo ($project->usePrefix()) ? __('Yes') : __('No'); ?>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="prefix"><?php echo __('Issue prefix'); ?></label></td>
		<td>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<input type="text" name="prefix" id="prefix" maxlength="5" value="<?php print $project->getPrefix(); ?>" style="width: 70px;"<?php if (!$project->usePrefix()): ?> disabled<?php endif; ?>>
			<?php elseif ($project->hasPrefix()): ?>
				<?php echo $project->getPrefix(); ?>
			<?php else: ?>
				<span class="faded_out"><?php echo __('No prefix set'); ?></span>
			<?php endif; ?>
			<div style="float: right; margin-right: 5px;" class="faded_out"><?php echo __('See %about_issue_prefix% for an explanation about issue prefixes', array('%about_issue_prefix%' => link_tag(make_url('publish_article', array('article_name' => 'AboutIssuePrefixes')), __('about issue prefixes'), array('target' => '_new')))); ?></div>
		</td>
	</tr>
	<tr>
		<td><label for="description"><?php echo __('Project description'); ?></label></td>
		<td>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<?php include_template('main/textarea', array('area_name' => 'description', 'area_id' => 'project_description_input', 'height' => '75px', 'width' => '100%', 'value' => $project->getDescription(), 'hide_hint' => true)); ?>
			<?php elseif ($project->hasDescription()): ?>
				<?php echo $project->getDescription(); ?>
			<?php else: ?>
				<span class="faded_out"><?php echo __('No description set'); ?></span>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="homepage"><?php echo __('Homepage'); ?></label></td>
		<td>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<input type="text" name="homepage" id="homepage" value="<?php echo $project->getHomepage(); ?>" style="width: 100%;">
			<?php elseif ($project->hasHomepage()): ?>
				<a href="<?php echo $project->getHomepage(); ?>"><?php echo $project->getHomepage(); ?></a>
			<?php else: ?>
				<span class="faded_out"><?php echo __('No homepage set'); ?></span>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="doc_url"><?php echo __('Documentation URL'); ?></label></td>
		<td>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<input type="text" name="doc_url" id="doc_url" value="<?php echo $project->getDocumentationURL(); ?>" style="width: 100%;">
			<?php elseif ($project->hasDocumentationURL()): ?>
				<a href="<?php echo $project->getDocumentationURL(); ?>"><?php echo $project->getDocumentationURL(); ?></a>
			<?php else: ?>
				<span class="faded_out"><?php echo __('No documentation URL provided'); ?></span>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="wiki_url"><?php echo __('Wiki URL'); ?></label></td>
		<td>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<input type="text" name="wiki_url" id="wiki_url" value="<?php echo $project->getWikiURL(); ?>" style="width: 100%;">
			<?php elseif ($project->hasWikiURL()): ?>
				<a href="<?php echo $project->getWikiURL(); ?>"><?php echo $project->getWikiURL(); ?></a>
			<?php else: ?>
				<span class="faded_out"><?php echo __('No wiki URL provided'); ?></span>
			<?php endif; ?>
		</td>
	</tr>
	<?php TBGEvent::createNew('core', 'project/projectinfo', $project)->trigger(); ?>
<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<tr>
		<td colspan="2" style="padding: 10px 0 10px 10px; text-align: right;">
			<div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation"><?php echo __('When you are done, click "%save%" to save your changes', array('%save%' => __('Save'))); ?></div>
			<input class="button button-green" style="float: right;" type="submit" id="project_submit_settings_button" value="<?php echo __('Save'); ?>">
			<span id="project_info_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
		</td>
	</tr>
<?php endif; ?>
</table>
<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
</form>
<?php endif; ?>
