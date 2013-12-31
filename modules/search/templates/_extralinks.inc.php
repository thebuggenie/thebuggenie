<input class="button button-silver" id="search_more_actions_button" type="button" style="float: right;" value="<?php echo __('More actions'); ?>" onclick="$(this).toggleClassName('button-pressed');$('search_more_actions').toggle();">
<ul id="search_more_actions" style="display: none; font-size: 0.9em; right: 0px; margin-top: -1px;" class="simple_list rounded_box white shadowed more_actions_dropdown" onclick="$('search_more_actions_button').toggleClassName('button-pressed');$('search_more_actions').toggle();">
	<li class="header" style="margin-top: 10px;"><?php echo __('Download search results'); ?></li>
	<?php if (isset($csv_url)): ?>
		<li><a href="<?php echo $csv_url; ?>"><?php echo image_tag('icon_csv.png') . __('Download as CSV'); ?></a></li>
	<?php endif; ?>
	<?php if (isset($rss_url)): ?>
		<li><a href="<?php echo $rss_url; ?>"><?php echo image_tag('icon_rss.png') . __('Download as RSS'); ?></a></li>
	<?php endif; ?>
</ul>
