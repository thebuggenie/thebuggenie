<td style="padding: 3px;"><?php echo image_tag('icon_user.png', array('title' => "User ID: {$user->getID()}")); ?></td>
<td style="padding: 3px;"><span id="user_<?php echo $user->getID(); ?>_username_span"><?php echo $user->getUsername(); ?></span></td>
<td style="padding: 3px;"><span id="user_<?php echo $user->getID(); ?>_realname_span"><?php echo $user->getRealname(); ?></span> <span class="faded_medium">(<span id="user_<?php echo $user->getID(); ?>_nickname_span"><?php echo $user->getNickname(); ?></span>)</span></td>
<td style="padding: 3px;"><?php echo ($user->getEmail() != '') ? link_tag("mailto:{$user->getEmail()}", $user->getEmail()) : '<span class="faded_medium"> - </span>'; ?></td>
<td style="padding: 3px;"><?php echo ($user->isActivated()) ? __('Yes') : __('No'); ?></td>
<td style="padding: 3px;">
	<?php echo javascript_link_tag(image_tag('icon_edit.png', array('title' => __('Edit this user'))), array('onclick' => "$('users_results_user_".$user->getID()."_edit').toggleClassName('selected_green');$('users_results_user_".$user->getID()."').toggle();$('users_results_user_".$user->getID()."_edit').toggle();", 'class' => 'image')); ?>
	<?php echo javascript_link_tag(image_tag('cfg_icon_permissions.png', array('title' => __('Edit permissions for this user'))), array('onclick' => "", 'class' => 'image')); ?>
</td>