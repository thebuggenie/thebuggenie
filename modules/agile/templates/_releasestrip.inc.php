<?php foreach ($board->getReleases() as $release): ?>
    <li class="release" id="release_<?php echo $release->getId(); ?>" data-release-id="<?php echo $release->getID(); ?>" data-assign-issue-url="<?php echo make_url('agile_assignrelease', array('project_key' => $board->getProject()->getKey(), 'release_id' => $release->getID())); ?>">
        <div class="planning_indicator" id="release_<?php echo $release->getId(); ?>_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
        <span class="release_name"><?php echo $release->getName(); ?></span>
        <div class="release_percentage">
            <div class="filler" id="release_<?php echo $release->getID(); ?>_percentage_filler" style="width: <?php echo $release->getPercentComplete(); ?>%;"></div>
        </div>
        <dl class="info">
            <dt><?php echo __('Release date'); ?></dt>
            <dd><?php echo ($release->isReleased()) ? tbg_formatTime($release->getReleaseDate(), 20) : __('Not released yet'); ?></dd>
            <dt><?php echo __('Version'); ?></dt>
            <dd><?php echo $release->getVersion(); ?></dd>
        </dl>
    </li>
<?php endforeach; ?>
