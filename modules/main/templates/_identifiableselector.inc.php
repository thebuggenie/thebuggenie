<div class="rounded_box white<?php if (isset($absolute) && $absolute): ?> shadowed<?php endif; ?>" id="<?php echo $html_id; ?>" style="<?php if (isset($absolute) && $absolute): ?>position: absolute;<?php else: ?>margin: 5px 0 5px 0; clear: both;<?php endif; ?> display: none; width: 324px; z-index: 10001; <?php if (isset($style)): foreach ($style as $key => $val): echo ' ' . $key . ': ' . $val . ';'; endforeach; endif; ?>">
	<form id="<?php echo $base_id; ?>_form" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" method="post" action="" onsubmit="findIdentifiable('<?php echo make_url('main_find_identifiable'); ?>', '<?php echo $base_id; ?>');return false;">
		<div class="dropdown_header"><?php echo $header; ?></div>
		<?php if ($allow_clear): ?>
			<div class="dropdown_content">
				<a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_type%'), '%identifiable_type%', urlencode('%identifiable_value%'), '%identifiable_value%'), array(0, 0, 0, 0), $callback); ?>"><?php echo $clear_link_text; ?></a><br>
			</div>
		<?php endif; ?>
		<div class="dropdown_content">
			<label for="<?php echo $base_id; ?>_input"><?php echo __('Find a user'); ?>:</label><br>
			<?php $text_title = __('Enter a name here'); ?>
			<?php if (isset($teamup_callback)): ?>
				<input type="hidden" name="teamup_callback" value="<?php echo $teamup_callback; ?>">
			<?php endif; ?>
			<input type="hidden" name="callback" value="<?php echo $callback; ?>">
			<input type="hidden" name="include_teams" value="<?php echo (int) $include_teams; ?>">
			<input type="text" name="find_identifiable_by" id="<?php echo $base_id; ?>_input" value="<?php echo $text_title; ?>" style="width: 240px; padding: 1px 1px 1px;" onblur="if (this.getValue() == '') { this.value = '<?php echo $text_title; ?>'; this.addClassName('faded_out'); }" onfocus="if (this.getValue() == '<?php echo $text_title; ?>') { this.clear(); } this.removeClassName('faded_out');" class="faded_out">
			<input type="submit" style="width: 60px;" value="<?php echo __('Find'); ?>"></input>
		</div>
		<div class="dropdown_content" id="<?php echo $base_id; ?>_results">
		</div>
	</form>
	<div class="dropdown_content">
		<a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_type%'), '%identifiable_type%', urlencode('%identifiable_value%'), '%identifiable_value%'), array(TBGIdentifiableClass::TYPE_USER, TBGIdentifiableClass::TYPE_USER, $tbg_user->getID(), $tbg_user->getID()), $callback); ?>"><?php echo __('Select yourself'); ?> (<?php echo $tbg_user->getUsername(); ?>)</a><br>
		<?php if (count($tbg_user->getFriends()) == 0): ?>
			<span class="faded_out"><?php echo __("Your friends will appear here"); ?></span>
		<?php else: ?>
			<b><?php echo __('%select_yourself% or select a friend below', array('%select_yourself%' => '')); ?>:</b><br>
			<?php include_component('identifiableselectorresults', array('users' => $tbg_user->getFriends(), 'callback' => $callback, 'team_callback' => ((isset($team_callback)) ? $team_callback : null))); ?>
		<?php endif; ?>
		<br>
		<br>
		<?php foreach ($tbg_user->getTeams() as $team): ?>
			<a href="javascript:void(0);" onclick="<?php echo str_replace(array(urlencode('%identifiable_type%'), '%identifiable_type%', urlencode('%identifiable_value%'), '%identifiable_value%'), array(TBGIdentifiableClass::TYPE_TEAM, TBGIdentifiableClass::TYPE_TEAM, $team->getID(), $team->getID()), $callback); ?>"><?php echo __('Select %teamname%', array('%teamname%' => $team->getName())); ?> (<?php echo $team->getName(); ?>)</a><br>
		<?php endforeach; ?>
	</div>
	<div id="<?php echo $base_id; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
	<div id="<?php echo $base_id; ?>_change_error" class="error_message" style="display: none;"></div>
</div>