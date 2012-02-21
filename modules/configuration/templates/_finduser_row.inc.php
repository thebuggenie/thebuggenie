<td style="padding: 3px;"><?php echo image_tag('icon_user.png', array('title' => "User ID: {$user->getID()}")); ?></td>
<td style="padding: 3px;"><?php echo ($user->getID()); ?></td>
<td style="padding: 3px;">
	<span id="user_<?php echo $user->getID(); ?>_username_span"><?php echo $user->getUsername(); ?></span>
	<?php if (isset($random_password)): ?>
		<br>
		<?php echo __('New password: %random_password%', array('%random_password%' => "<b>{$random_password}</b>")); ?></td>
	<?php endif; ?>
</td>
<td style="padding: 3px;"><span id="user_<?php echo $user->getID(); ?>_realname_span"><?php echo $user->getRealname(); ?></span> <span class="faded_out">(<span id="user_<?php echo $user->getID(); ?>_nickname_span"><?php echo $user->getNickname(); ?></span>)</span></td>
<td style="padding: 3px;"><?php echo ($user->getEmail() != '') ? link_tag("mailto:{$user->getEmail()}", $user->getEmail()) : '<span class="faded_out"> - </span>'; ?></td>
<td style="padding: 3px;"><?php echo ($user->isActivated()) ? __('Yes') : __('No'); ?></td>
<td style="padding: 3px; position: relative; text-align: right;" class="button-group">
	<?php echo javascript_link_tag(image_tag('icon_edit.png', array('title' => __('Edit this user'))), array('onclick' => "TBG.Config.User.getEditForm('".make_url('configure_users_edit_user_form', array('user_id' => $user->getID()))."', ".$user->getID().");$('users_results_user_".$user->getID()."').toggle();", 'class' => 'button button-silver button-icon')); ?>
	<?php echo javascript_link_tag(image_tag('cfg_icon_permissions.png', array('title' => __('Edit permissions for this user'))), array('onclick' => "TBG.Config.User.getPermissionsBlock('".make_url('configure_permissions_get_configurator', array('user_id' => $user->getID(), 'base_id' => $user->getID())). "', ".$user->getID().");", 'class' => 'button button-silver button-icon', 'id' => 'permissions_'.$user->getID().'_link')); ?>
	<?php echo javascript_link_tag(image_tag('spinning_16.gif'), array('id' => 'permissions_'.$user->getID().'_indicator', 'style' => 'display: none;')); ?>
	<?php if (!in_array($user->getUsername(), array('administrator', 'guest'))): ?>
		<?php if (TBGContext::getScope()->isDefault()): ?>
			<?php echo javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete this user'))), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Permanently delete this user?')."', '".__('Are you sure you want to remove this user? This will remove the users login data, as well as memberships in (and data in) any scopes the user is a member of.')."', {yes: {click: function() {TBG.Config.User.remove('".make_url('configure_users_delete_user', array('user_id' => $user->getID()))."', ".$user->getID()."); TBG.Main.Helpers.Dialog.dismiss(); } }, no: {click: TBG.Main.Helpers.Dialog.dismiss}});", 'class' => 'button button-silver button-icon')); ?>
		<?php else: ?>
			<?php echo javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete this user'))), array('onclick' => "TBG.Main.Helpers.Dialog.show('".__('Remove this user?')."', '".__('Are you sure you want to remove this user from the current scope? The users login is kept, and you can re-add the user later.')."', {yes: {click: function() {TBG.Config.User.remove('".make_url('configure_users_delete_user', array('user_id' => $user->getID()))."', ".$user->getID()."); TBG.Main.Helpers.Dialog.dismiss(); } }, no: {click: TBG.Main.Helpers.Dialog.dismiss}});", 'class' => 'button button-silver button-icon')); ?>
		<?php endif; ?>
	<?php else: ?>
	<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('<?php echo __('This user cannot be removed'); ?>', '<?php echo __('This is a system user which cannot be removed'); ?>');" class="button button-silver button-icon disabled"><?php echo image_tag("icon_delete.png"); ?></a>
	<?php endif; ?>
</td>