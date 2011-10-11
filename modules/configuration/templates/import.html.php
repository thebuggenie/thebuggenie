<?php

	$tbg_response->setTitle(__('Import data'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php
		
			include_component('leftmenu', array('selected_section' => 16));
		
		?>
		<td valign="top">
			<div class="config_header" style="width: 750px;"><?php echo __('Import data'); ?></div>
			<div style="width: 750px; clear: both; height: 30px;" class="tab_menu">
				<ul id="import_menu">
					<li id="tab_csv" class="selected"><?php echo javascript_link_tag(image_tag('icon_csv.png', array('style' => 'float: left; margin-right: 5px;')) . __('CSV'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_csv', 'import_menu');")); ?></li>
					<li id="tab_tbg"><?php echo javascript_link_tag(image_tag('favicon.png', array('style' => 'float: left; margin-right: 5px;')) . __('BUGS 1.x/The Bug Genie 2'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_tbg', 'import_menu');")); ?></li>
					<li id="tab_sample"><?php echo javascript_link_tag(image_tag('icon_project.png', array('style' => 'float: left; margin-right: 5px;')) . __('Sample data'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_sample', 'import_menu');")); ?></li>
				</ul>
			</div>
			<div id="import_menu_panes">
				<div id="tab_csv_pane" style="padding-top: 0; width: 750px;">
					<div class="tab_content">
						<?php echo __('You can import data from a CSV file copied into a text box in The Bug Genie, exported from other sources. Please see the %CSVImport% wiki article for further details and instructions.', array('%CSVImport%' => link_tag(make_url('publish_article', array('article_name' => 'CSVImport')), __('CSVImport'), array('target' => '_blank')))); ?>
						<div class="tab_header"><?php echo __('What data would you like to import?'); ?></div>
						<ul>
							<li><a href="javascript:void(0);" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'issues')); ?>');"><?php echo __('Issues'); ?></a></li>
							<li><a href="javascript:void(0);" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'projects')); ?>');"><?php echo __('Projects'); ?></a></li>
							<!--<li><a href="javascript:void(0);" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'users')); ?>');"><?php echo __('Users'); ?></a></li>
							<li><a href="javascript:void(0);" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'teams')); ?>');"><?php echo __('Teams'); ?></a></li>-->
							<li><a href="javascript:void(0);" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'clients')); ?>');"><?php echo __('Clients'); ?></a></li>
							<!--<li><a href="javascript:void(0);" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'groups')); ?>');"><?php echo __('Groups'); ?></a></li>-->
						</ul>
						<?php echo __('When you select a type, you will be given the opportunity to copy in your CSV file, and import the data.'); ?>
						<div class="tab_header"><?php echo __('Field IDs'); ?></div>
						<div class="tab_content">
						<?php echo __('To import some field an ID number is required. The table below contains all the ID numbers you require. User, team, group and client IDs can be found in user configuration.'); ?>
						<p style="font-weight: bold; margin-bottom: 5px;"><a href="javascript:void(0);" onclick="$('id_table').toggle();"><?php echo __('Toggle list of ID numbers'); ?></a></p>
						<table class="cleantable" style="display: none" id="id_table">
							<thead>
								<tr>
									<th><?php echo __('Type'); ?></th>
									<th><?php echo __('Name'); ?></th>
									<th><?php echo __('ID'); ?></th>
								</tr>
							</thead>
							<tbody>
						<?php
							foreach (TBGIssuetypeScheme::getAll() as $item)
							{
								echo '<tr><td>'.__('Issue type scheme').'</td><td>'.$item->getName().'</td><td>'.$item->getID().'</td></tr>';
							}
							foreach (TBGWorkflowScheme::getAll() as $item)
							{
								echo '<tr><td>'.__('Workflow scheme').'</td><td>'.$item->getName().'</td><td>'.$item->getID().'</td></tr>';
							}
							foreach (TBGProject::getAll() as $item)
							{
								echo '<tr><td>'.__('Project').'</td><td>'.$item->getName().'</td><td>'.$item->getID().'</td></tr>';
								foreach (TBGMilestone::getAllByProjectID($item->getID()) as $item2)
								{
									echo '<tr><td>'.__('Milestone for project').' '.$item->getID().'</td><td>'.$item2->getName().'</td><td>'.$item2->getID().'</td></tr>';
								}
							}
							foreach (TBGReproducability::getAll() as $item)
							{
								echo '<tr><td>'.__('Reproducability').'</td><td>'.$item->getName().'</td><td>'.$item->getID().'</td></tr>';
							}
							foreach (TBGSeverity::getAll() as $item)
							{
								echo '<tr><td>'.__('Severity').'</td><td>'.$item->getName().'</td><td>'.$item->getID().'</td></tr>';
							}
							foreach (TBGCategory::getAll() as $item)
							{
								echo '<tr><td>'.__('Category').'</td><td>'.$item->getName().'</td><td>'.$item->getID().'</td></tr>';
							}
							foreach (TBGPriority::getAll() as $item)
							{
								echo '<tr><td>'.__('Priority').'</td><td>'.$item->getName().'</td><td>'.$item->getID().'</td></tr>';
							}
							foreach (TBGResolution::getAll() as $item)
							{
								echo '<tr><td>'.__('Resolution').'</td><td>'.$item->getName().'</td><td>'.$item->getID().'</td></tr>';
							}
							foreach (TBGIssuetype::getAll() as $item)
							{
								echo '<tr><td>'.__('Issue type').'</td><td>'.$item->getName().'</td><td>'.$item->getID().'</td></tr>';
							}
						?>
							</tbody>
						</table>
						</div>
					</div>
				</div>
				<div id="tab_tbg_pane" style="padding-top: 0; width: 750px; display: none;">
					<div class="tab_content"><?php echo __('You can import data from previous version of The Bug Genie into your version 3 installation.'); ?></div>
					<div class="tab_header"><?php echo __('BUGS 1.x'); ?></div>
					<div class="tab_content"><?php echo __('Please upgrade to BUGS 1.9, followed by The Bug Genie 2 (an upgrade script is included in the installation package for The Bug Genie 2). After upgrading, then follow the instructions below to upgrade from The Bug Genie 2.'); ?></div>
					<div class="tab_header"><?php echo __('The Bug Genie 2'); ?></div>
					<div class="tab_content"><?php echo __("Please upgrade to The Bug Genie 2.1 if you haven't already done so, then follow the %upgrade_instructions% on The Bug Genie wiki to upgrade your data. There is not a built in upgrade script.", array('%upgrade_instructions%' => link_tag('http://thebuggenie.com/thebuggenie/wiki/TheBugGenie:ImportFromTheBugGenieVersion2x', __('upgrade instructions')))); ?></div>
				</div>
				<div id="tab_sample_pane" style="padding-top: 0; width: 750px; display: none;">
					<div class="tab_header"><?php echo __('Importing sample data'); ?></div>
					<div class="tab_content">
						<?php echo __('The Bug Genie can load sample data for you, so you can play around without having to use your own data. Press the button below to import sample projects and issues.'); ?>
						<form action="<?php echo make_url('configure_import'); ?>" method="post">
							<input type="hidden" name="import_sample_data" value="1">
							<div style="text-align: right;">
								<?php if ($canimport): ?>
									<input type="submit" onClick="$('import_sample_button').hide();$('import_sample_indicator').show();" id="import_sample_button" value="<?php echo __('Import sample data'); ?>" style="font-weight: bold; font-size: 1em; padding: 4px;">
								<?php else: ?>
									<div class="faded_out"><?php echo __('You can only import sample projects once'); ?></div>
								<?php endif; ?>
							</div>
						</form>
						<?php echo image_tag('spinning_20.gif', array('id' => 'import_sample_indicator', 'style' => 'float: right; display: none')); ?>
					</div>
				</div>
			</div>
			<?php if (isset($imported_data)): ?>
				<script type="text/javascript">
					TBG.Main.Helpers.Message.success('<?php echo __('Sample data loaded!'); ?>', '<?php echo __('Sample data was loaded. You can now browse around The Bug Genie and try it out!'); ?>');
				</script>
			<?php endif; ?>
		</td>
	</tr>
</table>