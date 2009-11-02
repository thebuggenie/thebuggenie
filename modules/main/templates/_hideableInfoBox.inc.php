<?php if ($show_box): ?>
	<?php var_dump($key); ?>
	<div class="rounded_box iceblue_borderless infobox" style="margin: 5px;" id="infobox_<?php echo $key; ?>">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="padding: 5px;">
			<?php echo image_tag('icon_info_big.png', array('style' => 'float: left; margin: 0 5px 0 5px;')); ?>
			<div>
				<div class="header"><?php echo $title; ?></div>
				<div class="content"><?php echo $content; ?></div>
			</div>
			<form id="close_me_<?php echo $key; ?>_form" action="<?php echo make_url('hide_infobox', array('key' => $key)); ?>" method="post" accept-charset="<?php echo BUGSsettings::getCharset(); ?>" onsubmit="hideInfobox('<?php echo make_url('hide_infobox', array('key' => $key)); ?>', '<?php echo $key; ?>');return false;">
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
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
<?php endif; ?>