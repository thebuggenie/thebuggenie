<div class="article">
    <div class="header">
        <?php echo __('Special:Contributors for %namespace namespace', array('%namespace' => ($projectnamespace ? $projectnamespace : 'global'))); ?>
    </div>
    <p>
        <?php echo __('Below is a listing of all contributors in %namespace namespace', array('%namespace' => ($projectnamespace ? $projectnamespace: 'global'))); ?>
    </p>
    <ul>
        <?php foreach ($contributors as $contributor): ?>
            <li><?php echo link_tag($contributions_base_url . '?user=' . $contributor->getUsername(), $contributor); ?></li>
        <?php endforeach ?>
    </ul>
</div>
