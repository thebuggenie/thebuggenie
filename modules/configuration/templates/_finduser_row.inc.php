<td style="padding: 3px;"><?php echo image_tag('icon_user.png', array('title' => "User ID: {$user->getID()}")); ?></td>
<td style="padding: 3px;"><?php echo ($user->getID()); ?></td>
<td style="padding: 3px;">
	<?php if (!in_array($user->getUsername(), array('administrator', 'guest'))): ?>
		<div id="user_<?php echo $user->getID(); ?>_delete" style="position: absolute; left: 280px; width: 700px; z-index: 100; display: none;" class="rounded_box white shadowed">
			<div class="header"><?php echo __('Please confirm that you want to delete this user'); ?></div>
			<div class="content">
				<?php echo __('The user with username "%username%" will be deleted permanently. Are you sure you want to do this?', array('%username%' => '<i>'.$user->getUsername().'</i>')); ?>
				<div style="text-align: right;">
					<?php echo javascript_link_tag(__('Yes'), array('onclick' => "TBG.Config.User.remove('".make_url('configure_users_delete_user', array('user_id' => $user->getID()))."', ".$user->getID().");")); ?>
					 ::
					<b><?php echo javascript_link_tag(__('No'), array('onclick' => "$('user_".$user->getID()."_delete').toggle();")); ?></b>
					<?php echo javascript_link_tag(image_tag('spinning_16.gif'), array('id' => 'delete_user_'.$user->getID().'_indicator', 'style' => 'display: none;')); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<span id="user_<?php echo $user->getID(); ?>_username_span"><?php echo $user->getUsername(); ?></span>
	<?php if (isset($random_password)): ?>
		<br>
		<?php echo __('New password: %random_password%', array('%random_password%' => "<b>{$random_password}</b>")); ?></td>
	<?php endif; ?>
</td>
<td style="padding: 3px;"><span id="user_<?php echo $user->getID(); ?>_realname_span"><?php echo $user->getRealname(); ?></span> <span class="faded_out">(<span id="user_<?php echo $user->getID(); ?>_nickname_span"><?php echo $user->getNickname(); ?></span>)</span></td>
<td style="padding: 3px;"><?php echo ($user->getEmail() != '') ? link_tag("mailto:{$user->getEmail()}", $user->getEmail()) : '<span class="faded_out"> - </span>'; ?></td>
<td style="padding: 3px;"><?php echo ($user->isActivated()) ? __('Yes') : __('No'); ?></td>
<td style="padding: 3px; position: relative;">
	<?php echo javascript_link_tag(image_tag('icon_edit.png', array('title' => __('Edit this user'))), array('onclick' => "TBG.Config.User.getEditForm('".make_url('configure_users_edit_user_form', array('user_id' => $user->getID()))."', ".$user->getID().");$('users_results_user_".$user->getID()."').toggle();", 'class' => 'image')); ?>
	<?php echo javascript_link_tag(image_tag('cfg_icon_permissions.png', array('title' => __('Edit permissions for this user'))), array('onclick' => "TBG.Config.User.getPermissionsBlock('".make_url('configure_permissions_get_configurator', array('user_id' => $user->getID(), 'base_id' => $user->getID())). "', ".$user->getID().");", 'class' => 'image', 'id' => 'permissions_'.$user->getID().'_link')); ?>
	<?php echo javascript_link_tag(image_tag('spinning_16.gif'), array('id' => 'permissions_'.$user->getID().'_indicator', 'style' => 'display: none;')); ?>
	<?php if (!in_array($user->getUsername(), array('administrator', 'guest'))): ?>
		<?php echo javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete this user'))), array('onclick' => "$('user_".$user->getID()."_delete').toggle();", 'class' => 'image')); ?>
	<?php endif; ?>
</td>