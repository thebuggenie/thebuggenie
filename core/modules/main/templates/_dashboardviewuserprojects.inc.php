<?php if (count($tbg_user->getAssociatedProjects()) > 0): ?>
    <ul id="associated_projects">
        <?php foreach ($tbg_user->getAssociatedProjects() as $project): ?>
            <?php if ($project->isDeleted()): continue; endif; ?>
            <li style="text-align: right;">
                <div style="padding: 0 5px;">
                    <div class="project_name">
                        <?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), $project->getName(), array('style' => 'font-weight: normal; font-size: 1.2em;')); ?>
                    </div>
                    <div style="float: right;" class="button-group">
                        <?php foreach ($links as $link): ?>
                            <?php echo link_tag(str_replace('%25project_key%25', $project->getKey(), $link['url']), $link['text'], array('class' => 'button button-silver')); ?>
                        <?php endforeach; ?>
                    </div>
                    <br style="clear: both;">
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <div class="faded_out" style="font-size: 0.9em; padding: 5px 5px 10px 5px;"><?php echo __('You are not associated with any projects'); ?></div>
<?php endif; ?>
