<td style="padding: 3px;"><?php echo image_tag('icon_user.png', array('title' => "User ID: {$user->getID()}")); ?></td>
<td style="padding: 3px;"><?php echo $user->getUsername(); ?></td>
<td style="padding: 3px;"><?php echo $user->getRealname(); ?> <span class="faded_medium">(<?php echo $user->getNickname(); ?>)</span></td>
<td style="padding: 3px;"><?php echo ($user->getEmail() != '') ? link_tag("mailto:{$user->getEmail()}", $user->getEmail()) : '<span class="faded_medium"> - </span>'; ?></td>
<td style="padding: 3px;"><?php echo ($user->isActivated()) ? __('Yes') : __('No'); ?></td>
<td style="padding: 3px;">&nbsp;</td>