<?php if ($show_box): ?>
	<div class="rounded_box iceblue borderless infobox" style="margin: 5px;" id="infobox_<?php echo $key; ?>">
		<div style="padding: 5px;">
			<?php echo image_tag('icon_info_big.png', array('style' => 'float: left; margin: 5px 5px 5px 5px;')); ?>
			<div>
				<div class="header"><?php echo $title; ?></div>
				<div class="content"><?php echo $content; ?></div>
			</div>
			<form id="close_me_<?php echo $key; ?>_form" action="<?php echo make_url('hide_infobox', array('key' => $key)); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="hideInfobox('<?php echo make_url('hide_infobox', array('key' => $key)); ?>', '<?php echo $key; ?>');return false;">
				<div class="close_me">
					<input type="checkbox" value="1" name="dont_show" id="close_me_<?php echo $key; ?>"></input>
					<label for="close_me_<?php echo $key; ?>"><?php echo __("Don't show this again"); ?></label>
					<input type="submit" value="<?php echo __('Hide'); ?>"></input>
				</div>
			</form>
			<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="infobox_<?php echo $key; ?>_indicator">
				<tr>
					<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
					<td style="padding: 0px; text-align: left;"><?php echo __('Updating, please wait'); ?>...</td>
				</tr>
			</table>
		</div>
	</div>
<?php endif; ?>