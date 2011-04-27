<li class="header disabled"><?php echo __('%num% article(s) found', array('%num%' => $resultcount)); ?></li>
<?php $cc = 0; ?>
<?php if ($resultcount > 0): ?>
	<?php foreach ($articles as $article): ?>
		<?php $cc++; ?>
		<?php if ($article instanceof TBGWikiArticle): ?>
			<li class="issue_open<?php if ($cc == count($articles) && $resultcount == count($articles)): ?> last<?php endif; ?>"><?php echo image_tag('tab_publish.png', array('class' => 'informal'), false, 'publish'); ?><div><?php echo (strlen($article->getName()) <= 32) ? $article->getName() : str_pad(substr($article->getName(), 0, 32), 35, '...'); ?></div><span class="informal"><?php echo __('Last updated %updated_at%', array('%updated_at%' => tbg_formatTime($article->getLastUpdatedDate(), 6))); ?></span><span class="informal url"><?php echo make_url('publish_article', array('article_name' => $article->getName())); ?></span></li>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php /* if ($resultcount - $cc > 0): ?>
		<li class="find_more_issues last">
			<span class="informal"><?php echo __('See %num% more articles ...', array('%num%' => $resultcount - $cc)); ?></span>
			<div class="hidden url"><?php echo (TBGContext::isProjectContext()) ? make_url('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey())) : make_url('search'); ?>?filters[text][value]=<?php echo $searchterm; ?>&filters[text][operator]=<?php echo urlencode('='); ?></div>
		</li>
	<?php endif; */ ?>
<?php else: ?>
	<li class="disabled no_issues_found">
		<?php echo __('No articles found matching your query'); ?>
	</li>
<?php endif; ?>
