<div class="rounded_box borderless" id="wiki_menu" style="margin: 10px 0 5px 5px;">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="padding: 5px;">
		<div class="header"><?php echo __('Wiki menu'); ?></div>
		<div class="content">
			<ul>
				<li>
					<?php if (BUGScontext::getCurrentProject() instanceof BUGSproject): ?>
						<?php echo link_tag(make_url('publish_article', array('article_name' => ucfirst(BUGScontext::getCurrentProject()->getKey()) .':MainPage')), __('Project Wiki Frontpage')); ?>
					<?php else: ?>
						<?php echo link_tag(make_url('publish_article', array('article_name' => 'MainPage')), __('Wiki Frontpage')); ?>
					<?php endif; ?>
				</li>
				<?php if (count($links) > 0): ?>
					<?php foreach ($links as $link): ?>
						<?php if ($link['url'] == ''): ?>
							<li>&nbsp;</li>
						<?php else: ?>
							<li style="font-size: 12px;"><a href="<?php echo $link['url']; ?>" title="<?php echo $link['url']; ?>"><?php echo $link['description']; ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>