<?php if (count($users) == 0): ?>
	<div class="faded_out"><?php echo __('There are no users in this team'); ?></div>
<?php else: ?>
	<table style="width: 500px; border: 0;" cellpadding="0" cellspacing="0">
		<?php foreach ($users as $user_id => $user): ?>
			<tr>
				<td style="width: 20px;"><?php echo image_tag('icon_user.png'); ?></td>
				<td style="padding: 2px;"><?php echo $user->getNameWithUsername(); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>