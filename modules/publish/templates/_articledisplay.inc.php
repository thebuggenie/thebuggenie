<?php BUGScontext::loadLibrary('publish/publish'); ?>
<div class="article" style="width: auto; padding: 5px; position: relative;">
	<?php if ($show_title): ?>
		<div class="header">
			<?php if ($show_actions): ?>
				<?php echo link_tag(make_url('publish_article_history', array('article_name' => $article->getName())), __('History'), array('style' => 'float: right;')); ?>
				<?php echo link_tag(make_url('publish_article_edit', array('article_name' => $article->getName())), __('Edit'), array('style' => 'float: right; margin-right: 15px;')); ?>
			<?php endif; ?>
			<?php if (BUGScontext::getCurrentProject() instanceof BUGSproject): ?>
				<?php if ((strpos($article->getName(), ucfirst(BUGScontext::getCurrentProject()->getKey())) == 0) || ($article->isCategory() && strpos($article->getName(), ucfirst(BUGScontext::getCurrentProject()->getKey())) == 9)): ?>
					<?php $project_article_name = substr($article->getName(), ($article->isCategory() * 9) + strlen(BUGScontext::getCurrentProject()->getKey())+1); ?>
					<?php if ($article->isCategory()): ?>Category:<?php endif; ?><span class="faded_dark"><?php echo ucfirst(BUGScontext::getCurrentProject()->getKey()); ?>:</span><?php echo get_spaced_name($project_article_name); ?>
				<?php endif; ?>
			<?php else: ?>
				<?php echo get_spaced_name($article->getName()); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if ($show_details): ?>
		<div class="faded_medium" style="padding-bottom: 5px;"><?php echo __('Last updated at %time%, by %user%', array('%time%' => bugs_formatTime($article->getPostedDate(), 3), '%user%' => '<b>'.(($article->getAuthor() instanceof BUGSidentifiable) ? $article->getAuthor()->getName() : __('System')).'</b>')); ; ?></div>
	<?php endif; ?>
	<div style="font-size: 13px; padding-bottom: 5px;"><?php echo tbg_parse_text($article->getContent(), true, $article->getID(), array('embedded' => $embedded)); ?></div>
</div>
<?php if ($article->isCategory() && !$embedded && $show_category_contains): ?>
	<div style="margin: 15px 5px 5px 5px;">
		<?php if (count($article->getSubCategories()) > 0): ?>
			<div class="header"><?php echo __('Subcategories'); ?></div>
			<ul class="category_list">
				<?php foreach ($article->getSubCategories() as $subcategory): ?>
					<li><?php echo link_tag(make_url('publish_article', array('article_name' => $subcategory->getName())), $subcategory->getCategoryName()); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<div class="faded_medium" style="font-size: 13px;"><?php echo __("This category doesn't have any subcategories"); ?></div>
		<?php endif; ?>
	</div>
	<br style="clear: both;">
	<div style="margin: 15px 5px 5px 5px;">
		<?php if (count($article->getCategoryArticles()) > 0): ?>
			<div class="header"><?php echo __('Pages in this category'); ?></div>
			<ul class="category_list">
				<?php foreach ($article->getCategoryArticles() as $categoryarticle): ?>
					<li><?php echo link_tag(make_url('publish_article', array('article_name' => $categoryarticle->getName())), $categoryarticle->getName()); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<div class="faded_medium" style="font-size: 13px;"><?php echo __('There are no pages in this category'); ?></div>
		<?php endif; ?>
	</div>
	<br style="clear: both;">
<?php endif; ?>
<?php if (!$embedded): ?>
	<div class="rounded_box lightgrey_borderless" style="margin: 30px 5px 20px 5px;">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="padding: 3px 10px 3px 10px; font-size: 14px;">
			<div class="content">
				<b><?php echo __('Categories:'); ?></b>
				<?php if (count($article->getCategories()) > 0): ?>
					<?php $category_links = array(); ?>
					<?php foreach ($article->getCategories() as $category): ?>
						<?php $category_links[] = link_tag(make_url('publish_article', array('article_name' => 'Category:'.$category)), $category); ?>
					<?php endforeach; ?>
					<?php echo join(', ', $category_links); ?>
				<?php else: ?>
					<span class="faded_dark"><?php echo __('This article is not in any categories'); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
<?php endif; ?>