<div class="article">
    <div class="header">
        <?php echo __('Special:Contributors for %namespace namespace', array('%namespace' => ($projectnamespace ? $projectnamespace : 'global'))); ?>
    </div>
    <?php if ($contributors): ?>
        <p>
            <?php echo __('Below is a listing of all contributors in %namespace namespace', array('%namespace' => ($projectnamespace ? $projectnamespace: 'global'))); ?>
        </p>
        <ul>
            <?php foreach ($contributors as $contributor): ?>
                <li><?php echo link_tag($contributions_base_url . '?user=' . $contributor->getUsername(), $contributor); ?></li>
            <?php endforeach ?>
        </ul>
    <?php else: ?>
        <p>
            <?php echo __('No contributors have made any changes in the %namespace namespace', array('%namespace' => ($projectnamespace ? $projectnamespace: 'global'))); ?>
        </p>
    <?php endif ?>
</div>
