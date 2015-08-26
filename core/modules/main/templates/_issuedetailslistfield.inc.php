<?php

    if (in_array($field, array('priority'))) $primary = true;

?>
<li id="<?php echo $field; ?>_field" class="issue_detail_field <?php echo (isset($primary)) ? ' primary ' : ' secondary '; ?> <?php if (!$info['merged']): ?> issue_detail_unmerged<?php elseif ($info['changed']): ?> issue_detail_changed<?php endif; ?>"<?php if (!$info['visible']): ?> style="display: none;"<?php endif; ?>>
    <dl class="viewissue_list">
        <dt id="<?php echo $field; ?>_header">
            <?php echo $info['title']; ?>
        </dt>
        <dd id="<?php echo $field; ?>_content">
            <?php $canEditField = "canEdit".ucfirst($field); ?>
            <?php if (array_key_exists('choices', $info) && count($info['choices']) && $issue->$canEditField()): ?>
                <a href="javascript:void(0);" onclick="TBG.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>', '<?php echo $field; ?>');" title="<?php echo __('Undo this change'); ?>"><?php echo image_tag('undo.png', array('class' => 'undo')); ?></a>
                <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => $field . '_undo_spinning')); ?>
                <a href="javascript:void(0);" class="dropper dropdown_link"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                <ul class="popup_box more_actions_dropdown" id="<?php echo $field; ?>_change">
                    <li class="header"><?php echo $info['change_header']; ?></li>
                    <li>
                        <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_id' => 0)); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a>
                    </li>
                    <?php if (count($info['choices'])): ?>
                        <li class="separator"></li>
                        <?php foreach ($info['choices'] as $choice): ?>
                            <?php if ($choice instanceof \thebuggenie\core\entities\DatatypeBase && !$choice->canUserSet($tbg_user)) continue; ?>
                            <li>
                                <a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_id' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo image_tag('icon_' . $field . '.png').__($choice->getName()); ?></a>
                            </li>
                        <?php endforeach; ?>
                        <li id="<?php echo $field; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                    <?php else: ?>
                        <li class="faded_out"><?php echo __('No choices available'); ?></li>
                    <?php endif; ?>
                    <li id="<?php echo $field; ?>_change_error" class="error_message" style="display: none;"></li>
                </ul>
            <?php endif; ?>
            <?php if (array_key_exists('url', $info) && $info['url']): ?>
                <a id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?> target="_new" href="<?php echo $info['current_url']; ?>"><?php echo $info['name']; ?></a>
            <?php else: ?>
                <span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __($info['name']); ?></span>
            <?php endif; ?>
            <span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span>
        </dd>
    </dl>
    <div style="clear: both;"> </div>
</li>
