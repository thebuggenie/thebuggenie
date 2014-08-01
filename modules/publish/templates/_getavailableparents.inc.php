<?php foreach ($parent_articles as $article_name => $manual_name): ?>
<li><a href="javascript:void(0);" onclick="$('parent_article_name').setValue('<?php echo $article_name; ?>');$('parent_article_name_span').update('<?php echo $manual_name; ?>');$('parent_selector_container').hide();"><?php echo str_replace(':', ' &rArr; ', $article_name); ?></li>
<?php endforeach; ?>
