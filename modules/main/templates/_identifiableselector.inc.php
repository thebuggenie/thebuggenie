<div class="rounded_box white" id="<?php echo $html_id; ?>" style="clear: both; display: none; width: 324px; margin: 5px 0 5px 0;<?php if (isset($style)): foreach ($style as $key => $val): echo ' ' . $key . ': ' . $val . ';'; endforeach; endif; ?>">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="padding: 5px;">
		<form id="<?php echo $base_id; ?>_form" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" method="post" action="" onsubmit="findIdentifiable('<?php echo make_url('main_find_identifiable'); ?>', '<?php echo $base_id; ?>');return false;">
			<div class="dropdown_header"><?php echo $header; ?></div>
			<?php if ($allow_clear): ?>
				<div class="dropdown_content">
					<a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_type%'), urlencode('%identifiable_value%')), array(0, 0), $callback); ?>"><?php echo $clear_link_text; ?></a><br>
				</div>
			<?php endif; ?>
			<div class="dropdown_content">
				<label for="<?php echo $base_id; ?>_input"><?php echo __('Find a user'); ?>:</label><br>
				<?php $text_title = __('Enter a name here'); ?>
				<input type="hidden" name="callback" value="<?php echo $callback; ?>">
				<input type="hidden" name="include_teams" value="<?php echo (int) $include_teams; ?>">
				<input type="text" name="find_identifiable_by" id="<?php echo $base_id; ?>_input" value="<?php echo $text_title; ?>" style="width: 240px; padding: 1px 1px 1px;" onblur="if (this.getValue() == '') { this.value = '<?php echo $text_title; ?>'; this.addClassName('faded_medium'); }" onfocus="if (this.getValue() == '<?php echo $text_title; ?>') { this.clear(); } this.removeClassName('faded_medium');" class="faded_medium">
				<input type="submit" style="width: 60px;" value="<?php echo __('Find'); ?>"></input>
			</div>
			<div class="dropdown_content" id="<?php echo $base_id; ?>_results">
			</div>
		</form>
		<div class="dropdown_content">
			<a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_type%'), urlencode('%identifiable_value%')), array(1, $bugs_user->getID()), $callback); ?>"><?php echo __('Select yourself'); ?> (<?php echo $bugs_user->getUsername(); ?>)</a><br>
			<br>
			<?php if (count($bugs_user->getFriends()) == 0): ?>
				<b class="faded_medium"><?php echo __("or - if you had any friends registered - you could've selected one from here"); ?></b>
			<?php else: ?>
				<br>
				<b><?php echo __('or select a friend below'); ?>:</b><br>
				<?php include_component('identifiableselectorresults', array('users' => $bugs_user->getFriends(), 'callback' => $callback)); ?>
			<?php endif; ?>
		</div>
		<div id="<?php echo $base_id; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
		<div id="<?php echo $base_id; ?>_change_error" class="error_message" style="display: none;"></div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>