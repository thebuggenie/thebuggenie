<?php

	include_template('publish/wikibreadcrumbs', array('article_name' => $article_name));
	TBGContext::loadLibrary('publish/publish');
	$tbg_response->setTitle(__('%article_name% history', array('%article_name%' => $article_name)));

?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="side_bar">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
		<td class="main_area article">
			<a name="top"></a>
			<div class="article" style="width: auto; padding: 5px; position: relative;">
				<div class="header tab_menu">
					<ul class="right">
						<li><?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), __('Show')); ?></li>
						<?php if (TBGContext::getModule('publish')->canUserEditArticle($article->getName())): ?>
							<li><?php echo link_tag(make_url('publish_article_edit', array('article_name' => $article->getName())), __('Edit')); ?></li>
						<?php endif; ?>
						<li class="selected"><?php echo link_tag(make_url('publish_article_history', array('article_name' => $article->getName())), __('History')); ?></li>
						<li><?php echo link_tag(make_url('publish_article_permissions', array('article_name' => $article->getName())), __('Permissions')); ?></li>
						<li><?php echo link_tag(make_url('publish_article_attachments', array('article_name' => $article->getName())), __('Attachments')); ?></li>
					</ul>
					<?php if (TBGContext::isProjectContext()): ?>
						<?php if ((strpos($article->getName(), ucfirst(TBGContext::getCurrentProject()->getKey())) == 0) || ($article->isCategory() && strpos($article->getName(), ucfirst(TBGContext::getCurrentProject()->getKey())) == 9)): ?>
							<?php $project_article_name = substr($article->getName(), ($article->isCategory() * 9) + strlen(TBGContext::getCurrentProject()->getKey())+1); ?>
							<?php if ($article->isCategory()): ?><span class="faded_out blue">Category:</span><?php endif; ?><span class="faded_out dark"><?php echo ucfirst(TBGContext::getCurrentProject()->getKey()); ?>:</span><?php echo get_spaced_name($project_article_name); ?>
						<?php endif; ?>
					<?php elseif (substr($article->getName(), 0, 9) == 'Category:'): ?>
						<span class="faded_out blue">Category:</span><?php echo get_spaced_name(substr($article->getName(), 9)); ?>
					<?php else: ?>
						<?php echo get_spaced_name($article->getName()); ?>
					<?php endif; ?>
					<span class="faded_out"><?php echo __('%article_name% ~ History', array('%article_name%' => '')); ?></span>
				</div>
			</div>
			<?php if ($history_action == 'list'): ?>
				<form action="<?php echo make_url('publish_article_diff', array('article_name' => $article->getName())); ?>" method="post">
					<table cellpadding="0" cellspacing="0" id="article_history">
						<thead>
							<tr>
								<th style="width: 25px; text-align: center;">#</th>
								<th style="width: 150px;"><?php echo __('Updated'); ?></th>
								<th style="width: 200px;"><?php echo __('Author'); ?></th>
								<th><?php echo __('Comment'); ?></th>
								<?php if ($revision_count > 1): ?>
									<th style="width: 60px;" colspan="2"><?php echo __('Compare'); ?></th>
									<th style="width: 150px;"><?php echo __('Actions'); ?></th>
								<?php endif; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($history as $revision => $history_item): ?>
								<tr>
									<td style="text-align: center;"><b><?php echo ($revision < $revision_count) ? link_tag(make_url('publish_article_revision', array('article_name' => $article->getName(), 'revision' => $revision)), $revision) : $revision; ?></b></td>
									<td style="text-align: center;"><?php echo tbg_formatTime($history_item['updated'], 20); ?></td>
									<td><i><?php echo ($history_item['author'] instanceof TBGUser) ? $history_item['author']->getName() : __('Initial import'); ?></i></td>
									<td><?php echo $history_item['change_reason']; ?></td>
									<?php if ($revision_count > 1): ?>
										<td style="width: 30px; text-align: center;">
											<?php if ($revision > 1): ?>
												<input type="radio" value="<?php echo $revision; ?>" <?php if ($revision == $revision_count): ?>checked <?php endif; ?> name="to_revision" id="from_revision_<?php echo $revision; ?>">
											<?php endif; ?>
										</td>
										<td style="width: 30px; text-align: center;">
											<?php if ($revision < $revision_count): ?>
												<input type="radio" value="<?php echo $revision; ?>" <?php if ($revision == $revision_count - 1): ?>checked <?php endif; ?> name="from_revision" id="to_revision_<?php echo $revision; ?>">
											<?php endif; ?>
										</td>
										<td style="position: relative;">
											<?php if ($revision < $revision_count): ?>
												<?php echo javascript_link_tag(__('Restore this version'), array('onclick' => "$('restore_article_revision_{$revision}').toggle();")); ?>
												<div class="rounded_box white shadowed" style="width: 400px; position: absolute; right: 15px; display: none; z-index: 100;" id="restore_article_revision_<?php echo $revision; ?>">
													<div class="header_div"><?php echo __('Are you sure you want to restore this revision?'); ?></div>
													<div class="content" style="padding: 5px;">
														<?php echo __('If you confirm, all changes after this revision will be lost, and the article reverted back to the state it was in revision %revision_number%', array('%revision_number%' => '<b>'.$revision.'</b>')); ?>
														<div style="text-align: right; padding: 5px;">
															<input type="hidden" name="restore">
															<?php echo __('%yes% or %cancel%', array('%yes%' => link_tag(make_url('publish_article_restore', array('article_name' => $article->getName(), 'revision' => $revision)), __('Yes')), '%cancel%' => javascript_link_tag(__('cancel'), array('onclick' => "$('restore_article_revision_{$revision}').toggle();", 'style' => 'font-weight: bold;')))); ?>
														</div>
													</div>
												</div>
											<?php endif; ?>
										</td>
									<?php endif; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
						<?php if ($revision_count > 1): ?>
							<tfoot>
								<tr>
									<td colspan="4">&nbsp;</td>
									<td colspan="2" style="text-align: center;"><input type="submit" value="<?php echo __('Compare'); ?>"></td>
									<td>&nbsp;</td>
								</tr>
							</tfoot>
						<?php endif; ?>
					</table>
				</form>
			<?php elseif ($history_action == 'diff'): ?>
				<p style="padding: 0 5px 10px 10px; font-size: 13px;">
					<?php echo '<b>'.__('Showing the difference between revisions: %from_revision% &rArr; %to_revision%', array('&rArr;' => '<b>&rArr;</b>', '%from_revision%' => '</b><i>'.__('%revision_number%, by %author% [%date%]', array('%revision_number%' => link_tag(make_url('publish_article_revision', array('article_name' => $article->getName(), 'revision' => $from_revision)), $from_revision, array('style' => 'font-weight: bold;')), '%author%' => $from_revision_author, '%date%' => tbg_formatTime($from_revision_date, 20))).'</i>', '%to_revision%' => '<i>'.__('%revision_number%, by %author% [%date%]', array('%revision_number%' => (($to_revision < $revision_count) ? link_tag(make_url('publish_article_revision', array('article_name' => $article->getName(), 'revision' => $to_revision)), $to_revision, array('style' => 'font-weight: bold;')) : $to_revision)."/{$revision_count}</b>", '%author%' => $to_revision_author, '%date%' => tbg_formatTime($to_revision_date, 20))).'</i>')); ?><br />
					<?php echo link_tag(make_url('publish_article_history', array('article_name' => $article->getName())), '&lt;&lt; '.__('Back to history')); ?>
				</p>
				<?php $cc = 1; ?>
				<table cellpadding="0" cellspacing="0" id="article_diff">
					<?php $odd = true; ?>
					<?php foreach ($diff as $line): ?>
						<tr<?php if ($odd): ?> class="odd"<?php endif; ?>>
							<td style="width: 40px; text-align: right; font-weight: bold; padding-right: 5px;"><?php echo $cc; ?>.</td>
							<td style="padding: 0;"><?php echo $line; ?></td>
						</tr>
						<?php $cc++; ?>
						<?php $odd = !$odd; ?>
					<?php endforeach; ?>
				</table>
			<?php endif; ?>
		</td>
	</tr>
</table>