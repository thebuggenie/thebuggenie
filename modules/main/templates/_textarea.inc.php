<textarea name="<?php echo $area_name; ?>" id="<?php echo $area_name; ?>" style="height: <?php echo $height; ?>; width: <?php echo $width; ?>;"><?php echo $value; ?></textarea>
<script type="text/javascript">
	tinyMCE.execCommand('mceAddControl', false, '<?php echo $area_name; ?>');
</script>
<?php if (!isset($hide_hint) || $hide_hint == false): ?>
	<div class="textarea_hint">
		<div class="header"><?php echo __('Formatting tips'); ?></div>
		<p><?php echo __('URLs will be automatically transformed to links - to specify a custom link title, write it in this format: %url_example%.<br>To link to an existing issue, write "issue" or "ticket" followed by the issue number (ex: "%ticket_example%").<br>If you want to paste a block of code or a quote, enclose it in [code][/code] or [quote][/quote] tags.', array('%url_example%' => '<span class="example">[url=http://www.somewhere.com]Link title[/url]</span>', '%ticket_example%' => '<span class="example">ticket #3</span>')); ?></p>
	</div>
<?php endif; ?>