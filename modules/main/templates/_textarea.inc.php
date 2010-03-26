<textarea name="<?php echo $area_name; ?>" id="<?php echo (isset($area_id)) ? $area_id : $area_name; ?>" style="height: <?php echo $height; ?>; width: <?php echo $width; ?>;"><?php echo $value; ?></textarea>
<?php if (!isset($hide_hint) || $hide_hint == false): ?>
	<div class="textarea_hint">
		<div class="header"><?php echo __('Formatting tips'); ?></div>
		<p><?php echo __('URLs will be automatically transformed to links. To link to an existing issue, write "issue", "bug" or "ticket" followed by the issue number (ex: "%ticket_example_1%" or "%ticket_example_2%").<br>Enclose code samples and comments in &lt;source&gt;&lt;/source&gt; and &lt;quote&gt;&lt;/quote&gt; tags. See more formatting tips in %wiki_formatting%.', array('%ticket_example_1%' => '<span class="example">ticket #3</span>', '%ticket_example_2%' => '<span class="example">ticket MYPROJ-1</span>', '%wiki_formatting%' => link_tag(make_url('publish_article', array('article_name' => 'WikiFormatting')), 'WikiFormatting'))); ?></p>
	</div>
<?php endif; ?>
