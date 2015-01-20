<?php if (count($recent_activities) > 0): ?>
    <?php include_component('project/timeline', array('activities' => $recent_activities)); ?>
    <?php echo link_tag(make_url('project_timeline', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Show complete timeline'), array('class' => 'button button-silver dash', 'title' => __('Show more'))); ?>
    <?php echo link_tag(make_url('project_timeline_important', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Show timeline for important events'), array('class' => 'button button-silver dash', 'title' => __('Show more'))); ?>
    <br style="clear: both;">
<?php else: ?>
    <div class="faded_out"><b><?php echo __('No recent activity registered for this project.'); ?></b><br><?php echo __('As soon as something important happens it will appear here.'); ?></div>
<?php endif; ?>
