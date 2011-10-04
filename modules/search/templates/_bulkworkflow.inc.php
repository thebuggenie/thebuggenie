<div class="backdrop_box medium" id="viewissue_add_item_div">
	<div class="backdrop_detail_header"><?php echo __('Perform workflow step'); ?></div>
	<div id="backdrop_detail_content">
		<div class="header"><?php echo __('Selected issues'); ?></div>
		<div class="content">
			<ul>
				<?php foreach ($issues as $issue): ?>
				<li><?php echo $issue->getFormattedTitle(); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php if (!$project instanceof TBGProject): ?>
			<div class="content faded_out">
				<?php echo __('You can only apply workflow transitions on issues in the same project.'); ?>
			</div>
		<?php else: ?>
			<?php if (!count($available_transitions)): ?>
				<div class="content faded_out">
					<?php echo __('There is no workflow transition that can be applied to all these issues. Try selecting fewer issues, or issues that are currently at the same (or similar) workflow step(s).'); ?>
				</div>
			<?php else: ?>
				<div class="header"><?php echo __('Perform the following workflow transition on these issues'); ?></div>
				<div class="content">
					<?php foreach ($available_transitions as $transition): ?>
						<div><?php echo javascript_link_tag($transition->getName(), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'workflow_transition', 'transition_id' => $transition->getID()))."&project_key=".$project->getKey()."&issue_ids[]=".join('&issue_ids[]=', array_keys($issues))."');", 'class' => 'button button-silver')); ?></div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Cancel'); ?></a>
	</div>
</div>