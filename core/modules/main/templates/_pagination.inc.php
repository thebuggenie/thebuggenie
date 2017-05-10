<div class="paginator">
    <div class="pages">
        <?php foreach ($pagination->getPageURLs() as $page_url): ?>
            <?php $page_url_classes = ($page_url['url'] === null ? 'button button-silver disabled' : 'button button-silver'); ?>
            <?php if ($page_url['url'] === null): ?>
                <a class="<?= $page_url_classes ?>"><?= $page_url['text']?></a>
            <?php else: ?>
                <?= link_tag($page_url['url'], $page_url['text'], ['class' => $page_url_classes, 'title' => $page_url['hint']]); ?>
            <?php endif ?>
        <?php endforeach ?>
    </div>
    <div class="page_sizes">
        <?php foreach ($pagination->getPageSizeURLs() as $page_size_url): ?>
            <?php $page_size_url_classes = ($page_size_url['url'] === null ? 'button button-silver disabled' : 'button button-silver'); ?>
            <?php if ($page_size_url['url'] === null): ?>
                <a class="<?= $page_size_url_classes ?>"><?= $page_size_url['text']?></a>
            <?php else: ?>
                <?= link_tag($page_size_url['url'], $page_size_url['text'], ['class' => $page_size_url_classes, 'title' => $page_size_url['hint']]); ?>
            <?php endif ?>
        <?php endforeach ?>
    </div>
</div>
