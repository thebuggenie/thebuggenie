<?php if ($show_results): ?>
	<button id="search_builder_toggler" class="button button-silver" style="float: right;" onclick="$('search_builder').toggle();"><?php echo __('Refine search'); ?></button>
<?php endif; ?>
<div class="search_column_settings" id="search_column_settings_toggler" style="display: none;">
	<div id="search_column_settings_button" onclick="$(this).toggleClassName('button-pressed');$('search_column_settings_container').toggle();" class="button button-silver button-icon" title="<?php echo __('Configure visible columns'); ?>"><span><?php echo image_tag('cfg_icon_general.png'); ?></span></div>
	<div class="rounded_box shadowed white" id="search_column_settings_container" style="display: none;">
		<h4><?php echo __('Select columns to show'); ?></h4>
		<p class="faded_out"><?php echo __('Select which columns you would like to show in this result view. Your selection is saved until the next time you visit.'); ?></p>
		<form id="scs_column_settings_form" action="<?php echo make_url('search_save_column_settings'); ?>" onsubmit="TBG.Search.saveVisibleColumns('<?php echo make_url('search_save_column_settings'); ?>');return false;">
			<input type="hidden" name="template" value="" id="scs_current_template">
			<ul class="simple_list scs_list">
				<?php foreach ($columns as $c_key => $c_name): ?>
					<li class="scs_<?php echo $c_key; ?>" style="display: none;"><label><input type="checkbox" onclick="TBG.Search.toggleColumn('<?php echo $c_key; ?>');" name="columns[<?php echo $c_key; ?>]" value="<?php echo $c_key; ?>"></input><div><?php echo $c_name; ?></div></label></li>
				<?php endforeach; ?>
			</ul>
			<div style="text-align: right; clear: both;">
				<div style="float: left; padding: 8px;"><?php echo javascript_link_tag(__('Reset columns'), array('onclick' => 'TBG.Search.resetColumns()')); ?></div>
				<div id="search_column_settings_save_button" class="button button-green" onclick="TBG.Search.saveVisibleColumns('<?php echo make_url('search_save_column_settings'); ?>');" style="margin-top: 7px;"><span><?php echo __('Ok'); ?></span></div>
				<div id="search_column_settings_indicator" style="display: none; float: right; margin: 7px 5px 0 10px;"><?php echo image_tag('spinning_20.gif'); ?></div>
			</div>
		</form>
	</div>
</div>
<div class="search_export_links">
	<?php echo __('Export results as: %csv_link% %rss_link%', array('%csv_link%' => link_tag($csv_url, image_tag('icon_csv.png')."CSV", array('class' => 'image')), '%rss_link%' => link_tag($rss_url, image_tag('icon_rss.png')."RSS", array('class' => 'image')))); ?>
</div>
