<?php BUGScontext::loadLibrary('publish/publish'); ?>
<table style="margin-top: 0px; table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<td class="left_bar" style="width: 250px;">
			<?php include_component('leftmenu', array('article' => $article)); ?>
		</td>
		<td class="main_area article">
			<a name="top"></a>
			<?php if ($error): ?>
				<div class="rounded_box red_borderless" style="margin: 0 0 5px 0;">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent" style="padding: 3px; font-size: 14px; color: #FFF;">
						<?php echo $error; ?>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
			<?php endif; ?>
			<?php if ($message): ?>
				<div class="rounded_box green_borderless" style="margin: 0 0 5px 5px;">
					<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
					<div class="xboxcontent" style="padding: 3px; font-size: 14px;">
						<b><?php echo $message; ?></b>
					</div>
					<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
				</div>
			<?php endif; ?>
			<?php if ($article instanceof PublishArticle): ?>
				<?php include_component('articledisplay', array('article' => $article)); ?>
				<?php if ($article->isCategory()): ?>
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
			<?php else: ?>
				<div class="header" style="padding: 5px;">
					<?php echo link_tag(make_url('publish_article', array('article_name' => 'FrontpageArticle')), __('Front page article'), array('class' => (($article_name == 'FrontpageArticle') ? 'faded_medium' : ''), 'style' => 'float: right; margin-right: 15px;')); ?>
					<?php if (BUGScontext::getCurrentProject() instanceof BUGSproject): ?>
						<?php if ((strpos($article_name, ucfirst(BUGScontext::getCurrentProject()->getKey())) == 0) || ((substr($article_name, 0, 8) == 'Category') && strpos($article_name, ucfirst(BUGScontext::getCurrentProject()->getKey())) == 9)): ?>
							<?php $project_article_name = substr($article_name, ((substr($article_name, 0, 8) == 'Category') * 9) + strlen(BUGScontext::getCurrentProject()->getKey())+1); ?>
							<?php if (substr($article_name, 0, 8) == 'Category'): ?>Category:<?php endif; ?><span class="faded_dark"><?php echo ucfirst(BUGScontext::getCurrentProject()->getKey()); ?>:</span><?php echo get_spaced_name($project_article_name); ?>
						<?php endif; ?>
					<?php else: ?>
						<?php echo get_spaced_name($article_name); ?>
					<?php endif; ?>
				</div>
				<div style="font-size: 14px;">
					<?php echo __('This is a placeholder for an article that has not been created yet. You can create it by clicking %create_this_article% below.', array('%create_this_article%' => '<b>'.__('Create this article').'</b>')); ?>
				</div>
			<?php endif; ?>
			<div class="publish_article_actions">
				<div class="sub_header"><?php echo __('Actions available'); ?></div>
				<form action="<?php echo make_url('publish_article_edit', array('article_name' => $article_name)); ?>" method="get" style="float: left; margin-right: 10px;">
					<input type="submit" value="<?php echo ($article instanceof PublishArticle) ? __('Edit this article') : __('Create this article'); ?>">
				</form>
				<?php if ($article instanceof PublishArticle): ?>
					<button onclick="$('delete_article_confirm').toggle();"><?php echo __('Delete this article'); ?></button>
					<div class="rounded_box" style="margin: 10px 0 5px; width: 720px; display: none;" id="delete_article_confirm">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="padding: 3px 10px 3px 10px; font-size: 14px;">
							<h4><?php echo __('Really delete this article?'); ?></h4>
							<span class="xboxlarge"><?php echo __('Deleting this article will remove it from the system.'); ?></span><br>
							<div style="text-align: right;">
								<?php echo link_tag(make_url('publish_article_delete', array('article_name' => $article_name)), __('Yes')); ?> :: <a href="javascript:void(0)" class="xboxlink" onclick="$('delete_article_confirm').hide();"><?php echo __('No'); ?></a>
							</div>
						</div>
						<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
					</div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>