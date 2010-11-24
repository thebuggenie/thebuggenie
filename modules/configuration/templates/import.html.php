<?php

	$tbg_response->setTitle(__('Import data'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php
		
			include_component('leftmenu', array('selected_section' => 2));
		
		?>
		<td valign="top">
			<div class="config_header" style="width: 750px;"><?php echo __('Import data'); ?></div>
			<div style="width: 750px; clear: both; height: 30px;" class="tab_menu">
				<ul id="import_menu">
					<li id="tab_csv" class="selected"><?php echo javascript_link_tag(image_tag('icon_csv.png', array('style' => 'float: left; margin-right: 5px;')) . __('CSV'), array('onclick' => "switchSubmenuTab('tab_csv', 'import_menu');")); ?></li>
				</ul>
			</div>
			<div id="import_menu_panes">
				<div id="tab_csv_pane" style="padding-top: 0; width: 750px;">
					<div class="tab_content">
						<?php echo __('You can import data from a CSV file copied into a text box in The Bug Genie, exported from other sources. Please see the %import instructions% wiki article for further details and instructions.', array('%import instructions%' => __('import instructions'))); ?>
						<div class="tab_header"><?php echo __('What data would you like to import?'); ?></div>
						<ul>
							<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('home'); ?>');"><?php echo __('Issues'); ?></a></li>
							<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('home'); ?>');"><?php echo __('Projects'); ?></a></li>
							<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('home'); ?>');"><?php echo __('Users'); ?></a></li>
							<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('home'); ?>');"><?php echo __('Teams'); ?></a></li>
							<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('home'); ?>');"><?php echo __('Clients'); ?></a></li>
							<li><a href="javascript:void(0);" onClick="showFadedBackdrop('<?php echo make_url('home'); ?>');"><?php echo __('Groups'); ?></a></li>
						</ul>
						<?php echo __('When you select a type, you will be given the opportunity to copy in your CSV file, and import the data.'); ?>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>