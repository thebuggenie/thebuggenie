<td class="saved_searches side_bar">
	<div class="container_div">
		<div class="header"><?php echo __('Predefined searches'); ?></div>
		<ul class="simple_list content" style="font-size: 1em;">
			<?php if (TBGContext::isProjectContext()): ?>
				<li style="clear: both;">
					<?php echo link_tag(make_url('project_open_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_open_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Open issues for this project')); ?><br>
				</li>
				<li style="clear: both; margin-bottom: 20px;">
					<?php echo link_tag(make_url('project_closed_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_closed_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Closed issues for this project')); ?>
				</li>
				<li>
					<?php echo link_tag(make_url('project_milestone_todo_list', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_milestone_todo_list', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Milestone todo-list for this project')); ?>
				</li>
				<li style="clear: both; margin-bottom: 20px;">
					<?php echo link_tag(make_url('project_most_voted_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_most_voted_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Most voted for issues')); ?>
				</li>
				<li style="clear: both;">
					<?php echo link_tag(make_url('project_my_reported_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_my_reported_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Issues reported by me')); ?><br>
				</li>
				<li style="clear: both;">
					<?php echo link_tag(make_url('project_my_assigned_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_my_assigned_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Open issues assigned to me')); ?><br>
				</li>
				<li style="clear: both;">
					<?php echo link_tag(make_url('project_my_teams_assigned_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('project_my_teams_assigned_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())), __('Open issues assigned to my teams')); ?><br>
				</li>
			<?php else: ?>
				<li style="clear: both;">
					<?php echo link_tag(make_url('my_reported_issues', array('format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('my_reported_issues'), __('Issues reported by me')); ?><br>
				</li>
				<li style="clear: both;">
					<?php echo link_tag(make_url('my_assigned_issues', array('format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('my_assigned_issues'), __('Open issues assigned to me')); ?><br>
				</li>
				<li style="clear: both;">
					<?php echo link_tag(make_url('my_teams_assigned_issues', array('format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
					<?php echo link_tag(make_url('my_teams_assigned_issues'), __('Open issues assigned to my teams')); ?><br>
				</li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="container_div">
		<div class="header"><?php echo (TBGContext::isProjectContext()) ? __('Your saved searches for this project') : __('Your saved searches'); ?></div>
		<div class="content">
			<?php if (count($savedsearches['user']) > 0): ?>
				<?php foreach ($savedsearches['user'] as $a_savedsearch): ?>
					<div id="saved_search_<?php echo $a_savedsearch->get(TBGSavedSearchesTable::ID); ?>_container">
						<?php if (TBGContext::isProjectContext()): ?>
							<div style="clear: both;">
								<?php echo link_tag(make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
								<?php if (!TBGcontext::getCurrentProject()->isArchived()): ?>
									<?php echo javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete saved search'), 'style' => 'float: right; margin-left: 2px;', 'class' => 'image')), array('onclick' => "$('delete_search_".$a_savedsearch->get(TBGSavedSearchesTable::ID)."').toggle();")); ?>
									<?php echo link_tag(make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => 0)), image_tag('icon_edit.png'), array('title' => __('Edit saved search'), 'style' => 'float: right; margin-left: 5px;', 'class' => 'image')); ?>
								<?php endif; ?>
								<?php echo link_tag(make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => true)), __($a_savedsearch->get(TBGSavedSearchesTable::NAME))); ?>
							</div>
							<?php if (!TBGcontext::getCurrentProject()->isArchived()): ?>
							<div class="rounded_box white shadowed" style="position: absolute; width: 300px; display: none;" id="delete_search_<?php echo $a_savedsearch->get(TBGSavedSearchesTable::ID); ?>">
								<div class="header"><?php echo __('Do you really want to delete this saved search?'); ?></div>
								<div class="content">
									<?php echo __('This action cannot be reverted. Note: this will not modify any issues affected by this search'); ?>
									<div style="text-align: right; margin-top: 10px;">
										<?php echo image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'delete_search_'.$a_savedsearch->get(TBGSavedSearchesTable::ID).'_indicator')); ?>
										<input type="submit" onclick="TBG.Search.deleteSavedSearch('<?php echo make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'saved_search_id' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => 0, 'delete_saved_search' => true)); ?>', <?php echo $a_savedsearch->get(TBGSavedSearchesTable::ID); ?>);" value="<?php echo __('Yes, delete'); ?>" style="font-weight: bold;">
										<?php echo __('%yes_delete% or %cancel%', array('%yes_delete%' => '', '%cancel%' => javascript_link_tag(__('cancel'), array('onclick' => "$('delete_search_".$a_savedsearch->get(TBGSavedSearchesTable::ID)."').toggle();")))); ?>
									</div>
								</div>
							</div>
							<?php endif; ?>
							<?php if ($a_savedsearch->get(TBGSavedSearchesTable::DESCRIPTION) != ''): ?>
								<div style="clear: both; padding: 0 0 10px 3px;"><?php echo $a_savedsearch->get(TBGSavedSearchesTable::DESCRIPTION); ?></div>
							<?php endif; ?>
						<?php else: ?>
							<div style="clear: both;">
								<?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
								<?php echo javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete saved search'), 'style' => 'float: right; margin-left: 2px;', 'class' => 'image')), array('onclick' => "$('delete_search_".$a_savedsearch->get(TBGSavedSearchesTable::ID)."').toggle();")); ?>
								<?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => 0)), image_tag('icon_edit.png'), array('title' => __('Edit saved search'), 'style' => 'float: right; margin-left: 5px;', 'class' => 'image')); ?>
								<?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => true)), __($a_savedsearch->get(TBGSavedSearchesTable::NAME))); ?>
							</div>
							<div class="rounded_box white shadowed" style="position: absolute; width: 300px; display: none;" id="delete_search_<?php echo $a_savedsearch->get(TBGSavedSearchesTable::ID); ?>">
								<div class="header"><?php echo __('Do you really want to delete this saved search?'); ?></div>
								<div class="content">
									<?php echo __('This action cannot be reverted. Note: this will not modify any issues affected by this search'); ?>
									<div style="text-align: right; margin-top: 10px;">
										<?php echo image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'delete_search_'.$a_savedsearch->get(TBGSavedSearchesTable::ID).'_indicator')); ?>
										<input type="submit" onclick="TBG.Search.deleteSavedSearch('<?php echo make_url('search', array('saved_search_id' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => 0, 'delete_saved_search' => true)); ?>', <?php echo $a_savedsearch->get(TBGSavedSearchesTable::ID); ?>);" value="<?php echo __('Yes, delete'); ?>" style="font-weight: bold;">
										<?php echo __('%yes_delete% or %cancel%', array('%yes_delete%' => '', '%cancel%' => javascript_link_tag(__('cancel'), array('onclick' => "$('delete_search_".$a_savedsearch->get(TBGSavedSearchesTable::ID)."').toggle();")))); ?>
									</div>
								</div>
							</div>
							<?php if ($a_savedsearch->get(TBGSavedSearchesTable::DESCRIPTION) != ''): ?>
								<div style="clear: both; padding: 0 0 10px 3px;"><?php echo $a_savedsearch->get(TBGSavedSearchesTable::DESCRIPTION); ?></div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="no_items" style="font-size: 1em;" id="no_user_saved_searches"><?php echo (TBGContext::isProjectContext()) ? __("You don't have any saved searches for this project") : __("You don't have any saved searches"); ?></div>
			<?php endif; ?>
		</div>
	</div>
	<div class="container_div">
		<div class="header"><?php echo (TBGContext::isProjectContext()) ? __('Public saved searches for this project') : __('Public saved searches'); ?></div>
		<div class="content">
			<?php if (count($savedsearches['public']) > 0): ?>
				<?php foreach ($savedsearches['public'] as $a_savedsearch): ?>
					<div id="saved_search_<?php echo $a_savedsearch->get(TBGSavedSearchesTable::ID); ?>_container">
						<div style="clear: both;">
							<?php if (TBGContext::isProjectContext()): ?>
								<?php echo link_tag(make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
								<?php if ($tbg_user->canCreatePublicSearches()): ?>
									<?php echo javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete saved search'), 'style' => 'float: right; margin-left: 2px;', 'class' => 'image')), array('onclick' => "$('delete_search_".$a_savedsearch->get(TBGSavedSearchesTable::ID)."').toggle();")); ?>
									<?php echo link_tag(make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => 0)), image_tag('icon_edit.png'), array('title' => __('Edit saved search'), 'style' => 'float: right; margin-left: 5px;', 'class' => 'image')); ?>
								<?php endif; ?>
								<?php echo link_tag(make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => true)), __($a_savedsearch->get(TBGSavedSearchesTable::NAME))); ?>
							<?php else: ?>
								<?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => true, 'format' => 'rss')), image_tag('icon_rss.png'), array('title' => __('Download feed'), 'style' => 'float: left; margin-right: 5px;', 'class' => 'image')); ?>
								<?php if ($tbg_user->canCreatePublicSearches()): ?>
									<?php echo javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete saved search'), 'style' => 'float: right; margin-left: 2px;', 'class' => 'image')), array('onclick' => "$('delete_search_".$a_savedsearch->get(TBGSavedSearchesTable::ID)."').toggle();")); ?>
									<?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => 0)), image_tag('icon_edit.png'), array('title' => __('Edit saved search'), 'style' => 'float: right; margin-left: 5px;', 'class' => 'image')); ?>
								<?php endif; ?>
								<?php echo link_tag(make_url('search', array('saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => true)), __($a_savedsearch->get(TBGSavedSearchesTable::NAME))); ?>
							<?php endif; ?>
						</div>
						<div class="rounded_box white shadowed" style="position: absolute; width: 300px; display: none;" id="delete_search_<?php echo $a_savedsearch->get(TBGSavedSearchesTable::ID); ?>">
							<div class="header"><?php echo __('Do you really want to delete this saved search?'); ?></div>
							<div class="content">
								<?php echo __('This action cannot be reverted. Note: this will not modify any issues affected by this search'); ?>
								<div style="text-align: right; margin-top: 10px;">
									<?php echo image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'delete_search_'.$a_savedsearch->get(TBGSavedSearchesTable::ID).'_indicator')); ?>
									<?php if (TBGContext::isProjectContext()): ?>
										<input type="submit" onclick="TBG.Search.deleteSavedSearch('<?php echo make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'saved_search_id' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => 0, 'delete_saved_search' => true)); ?>', <?php echo $a_savedsearch->get(TBGSavedSearchesTable::ID); ?>);" value="<?php echo __('Yes, delete'); ?>" style="font-weight: bold;">
									<?php else: ?>
										<input type="submit" onclick="TBG.Search.deleteSavedSearch('<?php echo make_url('search', array('saved_search_id' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => 0, 'delete_saved_search' => true)); ?>', <?php echo $a_savedsearch->get(TBGSavedSearchesTable::ID); ?>);" value="<?php echo __('Yes, delete'); ?>" style="font-weight: bold;">
									<?php endif; ?>
									<?php echo __('%yes_delete% or %cancel%', array('%yes_delete%' => '', '%cancel%' => javascript_link_tag(__('cancel'), array('onclick' => "$('delete_search_".$a_savedsearch->get(TBGSavedSearchesTable::ID)."').toggle();")))); ?>
								</div>
							</div>
						</div>
						<?php if ($a_savedsearch->get(TBGSavedSearchesTable::DESCRIPTION) != ''): ?>
							<div style="clear: both; padding: 0 0 10px 3px;"><?php echo $a_savedsearch->get(TBGSavedSearchesTable::DESCRIPTION); ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="no_items" style="font-size: 1em;" id="no_public_saved_searches"><?php echo (TBGContext::isProjectContext()) ? __("There are no saved searches for this project") : __("There are no public saved searches"); ?></div>
			<?php endif; ?>
		</div>
	</div>
</td>
