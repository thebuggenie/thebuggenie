<?php

	$children = $main_article->getChildArticles();
	$is_parent = in_array($main_article->getID(), $parents);
	$is_selected = $main_article->getID() == $article->getID() || ($main_article->isRedirect() && $main_article->getRedirectArticleName() == $article->getTitle());

	$project_key = (TBGContext::isProjectContext()) ? TBGContext::getCurrentProject()->getKey() . ':' : '';
	$article_name = (strpos(mb_strtolower($main_article->getTitle()), 'category:') !== false) ? substr($main_article->getTitle(), 9+mb_strlen($project_key)) : substr($main_article->getTitle(), mb_strlen($project_key));
	
?>
<li class="<?php if (isset($level) && $level >= 2) echo 'child'; ?> <?php if ($is_parent && !$is_selected) echo 'parent'; ?> <?php if ($is_selected) echo 'selected'; ?>">
	<?php if (isset($level) && $level >= 1) echo image_tag('icon_tree_child.png', array('class' => 'branch')); ?>
	<?php echo (!empty($children)) ? image_tag('icon_folder.png', array(), false, 'publish') : image_tag('icon_article', array(), false, 'publish'); ?>
	<?php echo link_tag(make_url('publish_article', array('article_name' => $main_article->getName())), get_spaced_name($article_name)); ?>
	<?php if ($is_parent || $is_selected): ?>
		<ul>
			<?php foreach ($children as $child_article): ?>
				<?php include_template('publish/manualsidebarlink', array('parents' => $parents, 'article' => $article, 'main_article' => $child_article, 'level' => $level + 1)); ?>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</li>
