<div id="tab_vcs_checkins_pane" style="padding-top: 0; margin: 0 5px 0 5px; display: none;">
	<div id="viewissue_commits">
		<?php if (count($links) == 0 || !is_array($links)): ?>
			<div class="no_items"><?php echo __('There are no code checkins for this issue'); ?></div>
		<?php else: ?>
			<?php foreach ($links as $link) include_template('vcs_integration/commitbox', array("projectId" => $event->getSubject()->getProject()->getID(), "commit" => $link->getCommit())); ?>
		<?php endif; ?>
	</div>
</div>