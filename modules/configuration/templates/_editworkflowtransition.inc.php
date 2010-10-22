<ul class="simple_list">
	<li>
		<label for="transition_name_input"><?php echo __('Name'); ?></label><br>
		<input type="text" style="width: 200px;" id="transition_name_input" name="name" value="<?php echo $transition->getName(); ?>">
	</li>
	<li>
		<label for="transition_description_input"><?php echo __('Description'); ?></label><br>
		<input type="text" style="width: 400px;" id="transition_description_input" name="description" value="<?php echo $transition->getDescription(); ?>">
	</li>
	<li>
		<label for="transition_template_input"><?php echo __('Popup template'); ?></label><br>
		<select id="transition_template_input" name="template">
			<option value=""<?php if ($transition->getTemplate() == ''): ?> selected<?php endif; ?>><?php echo __('No template, the transition happens instantly'); ?></option>
			<?php foreach (TBGWorkflowTransition::getTemplates() as $template_key => $template_name): ?>
				<option value="<?php echo $template_key; ?>"<?php if ($transition->getTemplate() == $template_key): ?> selected<?php endif; ?>><?php echo $template_name; ?></option>
			<?php endforeach; ?>
		</select>
	</li>
	<li>
		<label for="transition_outgoing_step_input"><?php echo __('Outgoing step'); ?></label><br>
		<select id="transition_outgoing_step_input" name="outgoing_step_id">
			<option value=""<?php if ($transition->getTemplate() == ''): ?> selected<?php endif; ?>><?php echo __('No template, the transition happens instantly'); ?></option>
			<?php foreach (TBGWorkflowTransition::getTemplates() as $template_key => $template_name): ?>
				<option value="<?php echo $template_key; ?>"<?php if ($transition->getTemplate() == $template_key): ?> selected<?php endif; ?>><?php echo $template_name; ?></option>
			<?php endforeach; ?>
		</select>
	</li>
</ul>