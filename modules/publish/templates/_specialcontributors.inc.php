<div class="article">
    <div class="header">
        <?= __('Special:Contributors for %namespace namespace', ['%namespace' => ($projectnamespace ? $projectnamespace : 'global')]); ?>
    </div>
    <?php if ($contributors): ?>
        <p>
            <?= __('Below is a listing of all contributors in %namespace namespace', ['%namespace' => ($projectnamespace ? $projectnamespace: 'global')]); ?>
        </p>
        <ul>
            <?php foreach ($contributors as $contributor): ?>
                <li><?= link_tag($contributions_base_url . '?user=' . $contributor->getUsername(), $contributor); ?></li>
            <?php endforeach ?>
        </ul>
    <?php else: ?>
        <p>
            <?= __('No contributors have made any changes in the %namespace namespace', ['%namespace' => ($projectnamespace ? $projectnamespace: 'global')]); ?>
        </p>
    <?php endif ?>
</div>
