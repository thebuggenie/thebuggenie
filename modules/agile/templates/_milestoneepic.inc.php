<li class="epic" id="epic_<?php echo $epic->getId(); ?>" data-issue-id="<?php echo $epic->getID(); ?>" data-assign-issue-url="<?php echo make_url('agile_assignepic', array('project_key' => $board->getProject()->getKey(), 'epic_id' => $epic->getID())); ?>" title="<?php echo __e($epic->getFormattedTitle(true, false)); ?>" data-shortname="<?php echo __e($epic->getShortname()); ?>">
    <?php include_component('agile/colorpicker', array('issue' => $epic)); ?>
    <div class="planning_indicator" id="issue_<?php echo $epic->getId(); ?>_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
    <a class="epic_name" href="<?php echo make_url('viewissue', array('issue_no' => $epic->getFormattedIssueNo(), 'project_key' => $epic->getProject()->getKey())); ?>"><?php echo $epic->getTitle(); ?></a>
    <div class="epic_percentage">
        <div class="filler" id="epic_<?php echo $epic->getID(); ?>_percentage_filler" style="width: <?php echo $epic->getEstimatedPercentCompleted(); ?>%;"></div>
    </div>
    <dl class="info">
        <dt><?php echo __('Current status'); ?></dt>
        <dd><div class="status_badge" style="background-color: <?php echo ($epic->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $epic->getStatus()->getColor() : '#FFF'; ?>;" title="<?php echo ($epic->getStatus() instanceof \thebuggenie\core\entities\Datatype) ? $epic->getStatus()->getName() : __('Unknown'); ?>">&nbsp;&nbsp;&nbsp;</div><?php echo ($epic->getStatus() instanceof \thebuggenie\core\entities\Status) ? $epic->getStatus()->getName() : __('Not determined'); ?></dd>
        <dt><?php echo __('Estimate'); ?></dt>
        <dd id="epic_<?php echo $epic->getID(); ?>_estimate"><?php echo \thebuggenie\core\entities\Issue::getFormattedTime($epic->getEstimatedTime()); ?></dd>
        <dt><?php echo __('Child issues'); ?></dt>
        <dd><?php echo __('%num_child_issues issue(s)', array('%num_child_issues' => '<span id="epic_'.$epic->getID().'_child_issues_count">'.$epic->countChildIssues().'</span>')); ?></dd>
    </dl>
</li>
