<?php

	$tbg_response->setTitle(__('Import data'));
	$tbg_response->addJavascript('config/settings.js');

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
					<li id="tab_csv" class="selected"><?php echo javascript_link_tag(image_tag('icon_csv.png', array('style' => 'float: left; margin-right: 5px;')) . __('CSV'), array('onclick' => "switchSubmenuTab('tab_csv', 'import_menu');")); ?></li>
					<li id="tab_tbg"><?php echo javascript_link_tag(image_tag('favicon.png', array('style' => 'float: left; margin-right: 5px;')) . __('BUGS 1.x/The Bug Genie 2'), array('onclick' => "switchSubmenuTab('tab_tbg', 'import_menu');")); ?></li>
					<li id="tab_sample"><?php echo javascript_link_tag(image_tag('icon_project.png', array('style' => 'float: left; margin-right: 5px;')) . __('Sample data'), array('onclick' => "switchSubmenuTab('tab_sample', 'import_menu');")); ?></li>
				</ul>
			</div>
			<div id="import_menu_panes">
				<div id="tab_csv_pane" style="padding-top: 0; width: 750px;">
					<div class="tab_content">
						<?php echo __('You can import data from a CSV file copied into a text box in The Bug Genie, exported from other sources. Please see the %CSVImport% wiki article for further details and instructions.', array('%CSVImport%' => link_tag(make_url('publish_article', array('article_name' => 'CSVImport')), __('CSVImport'), array('target' => '_blank')))); ?>
						<div class="tab_header"><?php echo __('What data would you like to import?'); ?></div>
						<ul>
							<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('configure_import_csv', array('type' => 'issues')); ?>');"><?php echo __('Issues'); ?></a></li>
							<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('configure_import_csv', array('type' => 'projects')); ?>');"><?php echo __('Projects'); ?></a></li>
							<!--<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('configure_import_csv', array('type' => 'users')); ?>');"><?php echo __('Users'); ?></a></li>
							<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('configure_import_csv', array('type' => 'teams')); ?>');"><?php echo __('Teams'); ?></a></li>-->
							<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('configure_import_csv', array('type' => 'clients')); ?>');"><?php echo __('Clients'); ?></a></li>
							<!--<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('configure_import_csv', array('type' => 'groups')); ?>');"><?php echo __('Groups'); ?></a></li>-->
						</ul>
						<?php echo __('When you select a type, you will be given the opportunity to copy in your CSV file, and import the data.'); ?>
					</div>
				</div>
				<div id="tab_tbg_pane" style="padding-top: 0; width: 750px; display: none;">
					<div class="tab_content"><?php echo __('You can import data from previous version of The Bug Genie into your version 3 installation.'); ?></div>
					<div class="tab_header"><?php echo __('BUGS 1.x'); ?></div>
					<div class="tab_content"><?php echo __('Please upgrade to BUGS 1.9, followed by The Bug Genie 2 (an upgrade script is included in the installation package for The Bug Genie 2). After upgrading, then follow the instructions below to upgrade from The Bug Genie 2.'); ?></div>
					<div class="tab_header"><?php echo __('The Bug Genie 2'); ?></div>
					<div class="tab_content"><?php echo __('Please upgrade to The Bug Genie 2.1 if you haven\'t already done so, then follow the instructions on The Bug Genie wiki to upgrade your data. There is not a built in upgrade script.'); ?></div>
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
					successMessage('<?php echo __('Sample data loaded!'); ?>', '<?php echo __('Sample data was loaded. You can now browse around The Bug Genie and try it out!'); ?>');
				</script>
			<?php endif; ?>
		</td>
	</tr>
</table>