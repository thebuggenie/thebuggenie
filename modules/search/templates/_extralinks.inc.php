<input class="button button-silver" id="search_more_actions_button" type="button" style="float: right;" value="<?php echo __('More actions'); ?>" onclick="$(this).toggleClassName('button-pressed');$('search_more_actions').toggle();">
<ul id="search_more_actions" style="display: none; width: 300px; top: 2px; z-index: 1000;" class="simple_list rounded_box white shadowed more_actions_dropdown" onclick="$('search_more_actions_button').toggleClassName('button-pressed');$('search_more_actions').toggle();">
	<li class="header"><?php echo __('Additional actions available'); ?></li>
	<li id="search_builder_toggler"><a href="javascript:void(0);" onclick="$('search_builder').toggle();"><?php echo __('Refine search'); ?></a></li>
	<?php if ($show_results): ?>
		<?php if (!$tbg_user->isGuest() && !$issavedsearch): ?>
			<li id="save_search_builder_toggler"><a href="javascript:void(0);" onclick="$(this).toggle();$('search_builder').toggle();$('find_issues_form').method = 'post';$('saved_search_details').show();$('saved_search_name').enable();$('saved_search_name').focus();$('saved_search_description').enable();<?php if ($tbg_user->canCreatePublicSearches()): ?>$('saved_search_public').enable();<?php endif; ?>$('save_search').enable();$('search_button_bottom').disable();$('search_button_bottom').hide();$('search_button_top').disable();$('search_button_save').hide();$('search_button_top').hide();return false;"><?php echo __('Save this search'); ?></a></li>
		<?php endif; ?>
	<?php endif; ?>
	<li id="search_column_settings_button"><a href="javascript:void(0);" onclick="$('search_column_settings_container').toggle();" title="<?php echo __('Configure visible columns'); ?>"><?php echo __('Configure visible columns'); ?></a></li>
	<li class="header" style="margin-top: 10px;"><?php echo __('Download search results'); ?></li>
	<li><a href="<?php echo $csv_url; ?>"><?php echo image_tag('icon_csv.png') . __('Download as CSV'); ?></a></li>
	<li><a href="<?php echo $rss_url; ?>"><?php echo image_tag('icon_rss.png') . __('Download as RSS'); ?></a></li>
</ul>
<?php /*<div class="search_export_links">
	<?php echo __('Export results as: %csv_link% %rss_link%', array('%csv_link%' => link_tag($csv_url, image_tag('icon_csv.png')."CSV", array('class' => 'image')), '%rss_link%' => link_tag($rss_url, image_tag('icon_rss.png')."RSS", array('class' => 'image')))); ?>
</div> */ ?>
<div id="search_column_settings_container" style="display: none;" class="fullpage_backdrop">
	<div class="backdrop_box medium">
		<div class="backdrop_detail_header">
			<?php echo __('Configure visible columns'); ?>
		</div>
		<div id="backdrop_detail_content">
			<div class="search_column_settings">
				<h4><?php echo __('Select columns to show'); ?></h4>
				<p class="faded_out"><?php echo __('Select which columns you would like to show in this result view. Your selection is saved until the next time you visit.'); ?></p>
				<form id="scs_column_settings_form" action="<?php echo make_url('search_save_column_settings'); ?>" onsubmit="TBG.Search.saveVisibleColumns('<?php echo make_url('search_save_column_settings'); ?>');return false;">
					<input type="hidden" name="template" value="" id="scs_current_template">
					<ul class="simple_list scs_list">
						<?php foreach ($columns as $c_key => $c_name): ?>
							<li class="scs_<?php echo $c_key; ?>" style="display: none;"><label><input type="checkbox" onclick="TBG.Search.toggleColumn('<?php echo $c_key; ?>');" name="columns[<?php echo $c_key; ?>]" value="<?php echo $c_key; ?>"></input><div><?php echo $c_name; ?></div></label></li>
						<?php endforeach; ?>
					</ul>
					<?php if (!$tbg_user->isGuest()): ?>
						<div style="text-align: right; clear: both;">
							<div style="float: left; padding: 8px;"><?php echo javascript_link_tag(__('Reset columns'), array('onclick' => 'TBG.Search.resetColumns()')); ?></div>
							<div id="search_column_settings_save_button" class="button button-green" onclick="TBG.Search.saveVisibleColumns('<?php echo make_url('search_save_column_settings'); ?>');" style="margin-top: 7px;"><span><?php echo __('Ok'); ?></span></div>
							<div id="search_column_settings_indicator" style="display: none; float: right; margin: 7px 5px 0 10px;"><?php echo image_tag('spinning_20.gif'); ?></div>
						</div>
					<?php endif; ?>
				</form>
			</div>
		</div>
	</div>
</div>
