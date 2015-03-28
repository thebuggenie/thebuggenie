<table cellpadding=0 cellspacing=0 class="<?php echo $key; ?>_percentage statistics_percentage" style="margin: 5px 0 10px 0; width: 100%;">
    <tr>
        <td colspan="2" style="font-size: 0.8em; text-align: right;"><?php echo __('Open / closed (%)'); ?></td>
        <td>&nbsp;</td>
    </tr>
    <?php foreach ($items as $item_id => $item): ?>
        <tr class="hover_highlight">
            <td><?php echo (is_object($item)) ? $item->getName() : $item; ?></td>
            <td title="<?php echo __('%count issues open of %total_count issues total', array('%count' => $counts[$item_id]['open'], '%total_count' => $counts[$item_id]['open'] + $counts[$item_id]['closed'])); ?>"><?php echo $counts[$item_id]['open']; ?>&nbsp;<span style="font-weight: normal;">/&nbsp;<?php echo $counts[$item_id]['open'] + $counts[$item_id]['closed']; ?> (<?php echo (int) $counts[$item_id]['percentage']; ?>%)</span></td>
            <td><?php include_component('main/percentbar', array('percent' => 100 - $counts[$item_id]['percentage'], 'height' => 14)); ?></td>
        </tr>
    <?php endforeach; ?>
    <tr class="hover_highlight">
        <td class="faded_out"><?php echo __('Not set'); ?></td>
        <td class="faded_out" title="<?php echo __('%count issues open of %total_count issues total', array('%count' => $counts[0]['open'], '%total_count' => $counts[0]['open'] + $counts[0]['closed'])); ?>"><?php echo $counts[0]['open']; ?>&nbsp;<span style="font-weight: normal;">/&nbsp;<?php echo $counts[0]['open'] + $counts[0]['closed']; ?> (<?php echo (int) $counts[0]['percentage']; ?>%)</span></td>
        <td class="faded_out"><?php include_component('main/percentbar', array('percent' => 100 - $counts[0]['percentage'], 'height' => 14)); ?></td>
    </tr>
</table>
<?php echo link_tag(make_url('project_statistics', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey())), __('Show more statistics'), array('class' => 'button button-silver dash', 'title' => __('More statistics'))); ?>
<?php if ($key != 'workflowstep'): ?>
    <?php echo link_tag(make_url('project_issues', array('project_key' => \thebuggenie\core\framework\Context::getCurrentProject()->getKey(), 'search' => true, 'fs[state]' => array('o' => '=', 'v' => \thebuggenie\core\entities\Issue::STATE_OPEN), 'groupby' => $key, 'grouporder' => 'desc'))."?sortfields=issues.{$key}=asc", __('Show details'), array('class' => 'button button-silver dash', 'title' => __('Show more issues'))); ?>
<?php endif; ?>
<br style="clear: both;">
