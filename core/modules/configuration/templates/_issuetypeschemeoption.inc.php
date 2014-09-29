<tr class="hover_highlight" id="item_<?php echo $key; ?>_<?php echo $issuetype->getID(); ?>">
    <td style="padding: 2px; font-size: 12px;" id="<?php echo $key; ?>_<?php echo $issuetype->getID(); ?>_name">
        <?php

            if (is_object($item))
            {
                echo $item->getDescription();
            }
            else
            {
                switch ($item)
                {
                    case 'description':
                        echo __('Issue description');
                        break;
                    case 'reproduction_steps':
                        echo __('Steps to reproduce the issue');
                        break;
                    case 'user_pain':
                        echo __('Triaging: User pain');
                        break;
                    case 'percent_complete':
                        echo __('Percent completed');
                        break;
                    case 'build':
                        echo __('Affected release(s)');
                        break;
                    case 'component':
                        echo __('Affected component(s)');
                        break;
                    case 'edition':
                        echo __('Affected edition(s)');
                        break;
                    case 'estimated_time':
                        echo __('Estimated time to complete');
                        break;
                    case 'spent_time':
                        echo __('Time spent working on the issue');
                        break;
                    case 'milestone':
                        echo __('Targetted for milestone');
                        break;
                    case 'votes':
                        echo __('Votes');
                        break;
                    case 'owned_by':
                        echo __('Owner');
                        break;
                    default:
                        echo __(ucfirst($item));
                        break;
                }

            }

        ?>
    </td>
    <td style="padding: 2px; text-align: center;" class="highlighted_column">
        <?php if (in_array($key, array('status'))): ?>
            <input type="hidden" name="field[<?php echo $key; ?>][visible]" value="1"><?php echo image_tag('action_ok.png'); ?>
        <?php else: ?>
            <?php if (!$scheme->isCore()): ?>
                <input type="checkbox" onclick="if (this.checked) { $('f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_reportable').enable(); } else { $('f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_reportable').disable(); }" name="field[<?php echo $key; ?>][visible]" value="1"<?php if (array_key_exists($key, $visiblefields)): ?> checked<?php endif; ?>>
            <?php else: ?>
                <?php echo (array_key_exists($key, $visiblefields)) ? image_tag('action_ok.png') : ''; ?>
            <?php endif; ?>
        <?php endif; ?>
    </td>
    <td style="padding: 2px; text-align: center;">
        <?php if (in_array($key, array('votes', 'owner', 'assignee'))): ?>
            <input type="hidden" id="f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_reportable"> -
        <?php else: ?>
            <?php if (!$scheme->isCore()): ?>
                <input type="checkbox" onclick="if (this.checked) { $('f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_additional').enable();$('f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_required').enable(); } else { $('f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_additional').disable();$('f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_required').disable(); }" id="f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_reportable" name="field[<?php echo $key; ?>][reportable]" value="1"<?php if (array_key_exists($key, $visiblefields) && $visiblefields[$key]['reportable']): ?> checked<?php endif; ?><?php if (!array_key_exists($key, $visiblefields) && !in_array($key, array('status'))): ?> disabled<?php endif; ?>></td>
            <?php else: ?>
                <?php echo (array_key_exists($key, $visiblefields) && $visiblefields[$key]['reportable']) ? image_tag('action_ok.png') : ''; ?>
            <?php endif; ?>
        <?php endif; ?>
    <td style="padding: 2px; text-align: center;">
        <?php if (in_array($key, array('description', 'reproduction_steps', 'user_pain', 'votes', 'owner', 'assignee'))): ?>
            <input type="hidden" id="f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_additional"> -
        <?php else: ?>
            <?php if (!$scheme->isCore()): ?>
                <input type="checkbox" id="f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_additional" name="field[<?php echo $key; ?>][additional]" value="1"<?php if (array_key_exists($key, $visiblefields) && $visiblefields[$key]['additional']): ?> checked<?php endif; ?><?php if ((!array_key_exists($key, $visiblefields) || !$visiblefields[$key]['reportable']) && !in_array($key, array('status'))): ?> disabled<?php endif; ?>>
            <?php else: ?>
                <?php echo (array_key_exists($key, $visiblefields) && $visiblefields[$key]['additional']) ? image_tag('action_ok.png') : ''; ?>
            <?php endif; ?>
        <?php endif; ?>
    </td>
    <td style="padding: 2px; text-align: center;">
        <?php if (in_array($key, array('votes', 'owner', 'assignee'))): ?>
            <input type="hidden" id="f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_required"> -
        <?php else: ?>
            <?php if (!$scheme->isCore()): ?>
                <input type="checkbox" id="f_<?php echo $issuetype->getID(); ?>_<?php echo $key; ?>_required" name="field[<?php echo $key; ?>][required]" value="1"<?php if (array_key_exists($key, $visiblefields) && $visiblefields[$key]['required']): ?> checked<?php endif; ?><?php if ((!array_key_exists($key, $visiblefields) || !$visiblefields[$key]['reportable']) && !in_array($key, array('status'))): ?> disabled<?php endif; ?>>
            <?php else: ?>
                <?php echo (array_key_exists($key, $visiblefields) && $visiblefields[$key]['required']) ? image_tag('action_ok.png') : ''; ?>
            <?php endif; ?>
        <?php endif; ?>
    </td>
</tr>
