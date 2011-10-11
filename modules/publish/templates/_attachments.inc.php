<div class="no_items" id="article_<?php echo mb_strtolower($article->getName()); ?>_no_files"<?php if (count($article->getFiles()) > 0): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no file attached to this article'); ?></div>
<table style="table-layout: fixed; width: 100%; background-color: #FFF;" cellpadding=0 cellspacing=0>
	<tbody id="article_<?php echo mb_strtolower($article->getName()); ?>_files" class="hover_highlight article_attachments">
		<?php foreach ($article->getFiles() as $file_id => $file): ?>
			<?php include_component('main/attachedfile', array('base_id' => 'article_'.mb_strtolower($article->getName()).'_files', 'mode' => 'article', 'article' => $article, 'file' => $file)); ?>
		<?php endforeach; ?>
	</tbody>
</table>