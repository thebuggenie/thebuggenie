<div class="backdrop_box large" id="login_popup">
	<div id="backdrop_detail_content" class="rounded_top login_content">
		<?php echo $content; ?>
	</div>
	<div class="backdrop_detail_footer">
	<?php if ($mandatory != true): ?>
		<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close'); ?></a>
	<?php endif; ?>
	</div>
</div>
<?php if (isset($options['error'])): ?>
	<script type="text/javascript">
		TBG.Main.Helpers.Message.error('<?php echo $options['error']; ?>');
	</script>
<?php endif; ?>
<script type="text/javascript">
	$('tbg3_username').focus();
</script>