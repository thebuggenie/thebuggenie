<div class="backdrop_box medium" id="client_users">
    <div class="backdrop_detail_header"><?php echo __('Archived projects'); ?></div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php
            if (isset($team))
            {
                echo __('Displaying archived projects for %item', array('%item' => '<b>'.$team->getName().'</b>'));
            }
            elseif (isset($client))
            {
                echo __('Displaying archived projects for %item', array('%item' => '<b>'.$client->getName().'</b>'));
            }
            elseif (isset($parent))
            {
                echo __('Displaying archived subprojects for %project', array('%project' => '<b>'.$parent->getName().'</b>'));
            }
        ?>
        <?php if ($project_count > 0): ?>
            <ul class="project_list simple_list">
            <?php foreach ($projects as $project): ?>
                <li><?php include_component('project/overview', array('project' => $project)); ?></li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="content faded_out">
                <?php if (isset($parent)): ?>
                    <?php echo __('This project has no archived subprojects'); ?>
                <?php else: ?>
                    <?php echo __('There are no top-level archived projects'); ?>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>
    <div class="backdrop_detail_footer">
        <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close'); ?></a>
    </div>
</div>
