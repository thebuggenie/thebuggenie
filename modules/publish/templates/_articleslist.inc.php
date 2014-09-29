<?php if (!empty($articles)): ?>
    <ul>
        <?php foreach ($articles as $article): ?>
            <li>
                <?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), $article->getName()); ?>
                <?php if (!isset($include_redirects) || $include_redirects == true): ?>
                    <?php if ($article->isRedirect()) echo ' &rArr; ' . link_tag(make_url('publish_article', array('article_name' => $article->getRedirectArticleName())), $article->getRedirectArticleName()); ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <div class="no_articles"><?php echo __('This list is empty.'); ?></div>
<?php endif; ?>
