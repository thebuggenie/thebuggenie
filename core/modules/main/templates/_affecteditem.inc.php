<?php $canedititem = (($itemtype == 'build' && $issue->canEditAffectedBuilds()) || ($itemtype == 'component' && $issue->canEditAffectedComponents()) || ($itemtype == 'edition' && $issue->canEditAffectedEditions())); ?>
<li id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>" class="affected_item">
    <?php echo image_tag('icon_'.$itemtype.'_large.png', array('alt' => $itemtypename, 'class' => 'icon_affected_type')); ?>
    <?php if ($canedititem): ?>
        <a href="javascript:void(0);" class="removelink" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Remove %itemname?', array('%itemname' => $item[$itemtype]->getName())); ?>', '<?php echo __('Please confirm that you want to remove this item from the list of items affected by this issue'); ?>', {yes: {click: function() {TBG.Issues.Affected.remove('<?php echo make_url('remove_affected', array('issue_id' => $issue->getID(), 'affected_type' => $itemtype, 'affected_id' => $item['a_id'])).'\', '.'\''.$itemtype.'_'.$item['a_id']; ?>');TBG.Main.Helpers.Dialog.dismiss();}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo image_tag('action_delete.png', array('id' => 'affected_'.$itemtype.'_'.$item['a_id'].'_delete_icon', 'alt' => '[D]')); ?></a>
    <?php endif; ?>
    <span class="affected_name"><?php echo $item[$itemtype]->getName(); ?></span>
    <div class="status_badge dropper affected_status" id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status" style="background-color: <?php echo ($item['status'] instanceof \thebuggenie\core\entities\Status) ? $item['status']->getColor() : '#FFF'; ?>;" title="<?php echo ($item['status'] instanceof \thebuggenie\core\entities\Datatype) ? __($item['status']->getName()) : __('Unknown'); ?>"><?php echo ($item['status'] instanceof \thebuggenie\core\entities\Datatype) ? $item['status']->getName() : __('Unknown'); ?></div>
    <ul class="rounded_box white shadowed dropdown_box leftie popup_box more_actions_dropdown" id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status_change" style="display: none;">
        <li class="header"><?php echo __('Set status'); ?></li>
        <?php foreach ($statuses as $status): ?>
            <?php if (!$status->canUserSet($tbg_user)) continue; ?>
            <li>
                <a href="javascript:void(0);" onclick="TBG.Issues.Affected.setStatus('<?php echo make_url('status_affected', array('issue_id' => $issue->getID(), 'affected_type' => $itemtype, 'affected_id' => $item['a_id'], 'status_id' => $status->getID())); ?>', '<?php echo $itemtype.'_'.$item['a_id']; ?>');">
                    <div class="status_badge" style="background-color: <?php echo ($status instanceof \thebuggenie\core\entities\Status) ? $status->getColor() : '#FFF'; ?>;" id="status_<?php echo $issue->getID(); ?>_color">
                        <span id="status_content">&nbsp;&nbsp;</span>
                    </div>
                    <?php echo __($status->getName()); ?>
                </a>
            </li>
        <?php endforeach; ?>
        <li id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status_spinning" style="display: none;"><?php echo image_tag('spinning_20.gif') . '&nbsp;' . __('Please wait'); ?>...</li>
        <li id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status_error" class="error_message" style="display: none;"></li>
    </ul>
    <span onclick="TBG.Issues.Affected.toggleConfirmed('<?php echo make_url('confirm_affected', array('issue_id' => $issue->getID(), 'affected_type' => $itemtype, 'affected_id' => $item['a_id'])); ?>', '<?php echo $itemtype.'_'.$item['a_id']; ?>');" class="affected_state <?php echo ($item['confirmed']) ? 'confirmed' : 'unconfirmed'; ?>"><span id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_state"><?php echo ($item['confirmed']) ? __('Confirmed') : __('Unconfirmed'); ?></span><?php echo image_tag('spinning_16.gif'); ?></span>
    <?php if ($itemtype == 'build'): ?>
        <span class="faded_out">(<?php echo $item['build']->getVersionMajor().'.'.$item['build']->getVersionMinor().'.'.$item['build']->getVersionRevision(); ?>)</span>
    <?php endif; ?>
</li>
