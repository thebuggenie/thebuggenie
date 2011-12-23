<?php

	$tbg_response->setTitle(__('Import data'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_IMPORT)); ?>
		<td valign="top" style="padding-left: 15px;">
			<div style="width: 788px;">
				<h3><?php echo __('Import data'); ?></h3>
				<div style="width: 100%; clear: both; height: 30px; margin-top: 15px;" class="tab_menu">
					<ul id="import_menu">
						<li id="tab_csv" class="selected"><?php echo javascript_link_tag(image_tag('icon_csv.png', array('style' => 'float: left; margin-right: 5px;')) . __('CSV'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_csv', 'import_menu');")); ?></li>
						<li id="tab_tbg"><?php echo javascript_link_tag(image_tag('favicon.png', array('style' => 'float: left; margin-right: 5px;')) . __('BUGS 1.x/The Bug Genie 2'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_tbg', 'import_menu');")); ?></li>
						<li id="tab_sample"><?php echo javascript_link_tag(image_tag('icon_project.png', array('style' => 'float: left; margin-right: 5px;')) . __('Sample data'), array('onclick' => "TBG.Main.Helpers.tabSwitcher('tab_sample', 'import_menu');")); ?></li>
					</ul>
				</div>
				<div id="import_menu_panes">
					<div id="tab_csv_pane" style="padding-top: 0; width: 100%;">
						<div class="tab_content">
							<?php echo __('You can import data from a CSV file copied into a text box in The Bug Genie, exported from other sources. Please see the %CSVImport% wiki article for further details and instructions.', array('%CSVImport%' => link_tag(make_url('publish_article', array('article_name' => 'CSVImport')), __('CSVImport'), array('target' => '_blank')))); ?>
							<div id="csv_button_area" class="button-group">
								<button class="button" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'issues')); ?>');"><?php echo __('Issues'); ?></button>
								<button class="button" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'projects')); ?>');"><?php echo __('Projects'); ?></button>
								<!--<button class="button" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'users')); ?>');"><?php echo __('Users'); ?></button>
								<button class="button" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'teams')); ?>');"><?php echo __('Teams'); ?></button>-->
								<button class="button" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'clients')); ?>');"><?php echo __('Clients'); ?></button>
								<!--<button class="button" onClick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('configure_import_csv', array('type' => 'groups')); ?>');"><?php echo __('Groups'); ?></button>-->
							</div>
							<br class="clear" />
							<div class="tab_content">
							<?php echo __('To import some fields, ID numbers are required, which can be seen below. User, team, group and client IDs can be found in user configuration.'); ?>
							</div>
							<div class="tab_content"><button onclick="TBG.Config.Import.getImportCsvIds('<?php echo make_url('configure_import_csv_get_ids'); ?>'); $('id_zone').toggle();"><?php echo __('Toggle list of ID numbers'); ?></button></div>
							<div class="tab_content" id="id_zone" style="display: none">
								<div id="id_zone_indicator"><?php echo image_tag('spinning_20.gif'); ?></div>
								<div id="id_zone_content" style="display: none;"></div>
							</div>
						</div>
					</div>
					<div id="tab_tbg_pane" style="padding-top: 0; width: 100%; display: none;">
						<div class="tab_content"><?php echo __('You can import data from previous version of The Bug Genie into your version 3 installation.'); ?></div>
						<div class="tab_header"><?php echo __('BUGS 1.x'); ?></div>
						<div class="tab_content"><?php echo __('Please upgrade to BUGS 1.9, followed by The Bug Genie 2 (an upgrade script is included in the installation package for The Bug Genie 2). After upgrading, then follow the instructions below to upgrade from The Bug Genie 2.'); ?></div>
						<div class="tab_header"><?php echo __('The Bug Genie 2'); ?></div>
						<div class="tab_content"><?php echo __("Please upgrade to The Bug Genie 2.1 if you haven't already done so, then follow the %upgrade_instructions% on The Bug Genie wiki to upgrade your data. There is not a built in upgrade script.", array('%upgrade_instructions%' => link_tag('http://issues.thebuggenie.com/wiki/TheBugGenie:ImportFromTheBugGenieVersion2x', __('upgrade instructions')))); ?></div>
					</div>
					<div id="tab_sample_pane" style="padding-top: 0; width: 100%; display: none;">
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
			</div>
		</td>
	</tr>
</table>