<input class="button button-silver dropper" id="search_more_actions_button" type="button" style="float: right;" value="<?php echo __('More actions'); ?>">
<ul id="search_more_actions" style="font-size: 0.9em; right: 0px; margin-top: -1px;" class="simple_list rounded_box white shadowed more_actions_dropdown" onclick="jQuery(this).prev().toggleClass('button-pressed');jQuery(this).toggle();">
    <li class="header" style="margin-top: 10px;"><?php echo __('Download search results'); ?></li>
    <?php if (isset($csv_url)): ?>
        <li><a href="<?php echo $csv_url; ?>"><?php echo image_tag('icon_csv.png') . __('Download as CSV'); ?></a></li>
    <?php endif; ?>
    <?php if (isset($rss_url)): ?>
        <li><a href="<?php echo $rss_url; ?>"><?php echo image_tag('icon_rss.png') . __('Download as RSS'); ?></a></li>
    <?php endif; ?>
</ul>
