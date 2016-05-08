<<?php echo $done ? 'div' : 'li'; ?> class="todo" id="item_option_<?php echo $todo_key; ?>" style="clear: both;">
    <div id="item_option_<?php echo $todo_key; ?>_content">
        <span id="todo_<?php echo $todo_key; ?>_mark_wrapper">
        <?php echo image_tag('spinning_16.gif', array('id' => 'todo_' . $todo_key . '_mark_indicator', 'style' => 'display: none;', 'class' => 'todo-mark-indicator')); ?>
        <?php if ($done): ?>
            <a href="javascript:void(0);"
               onclick="TBG.Issues.markTodo('<?php echo make_url('todo_mark', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'mark' => 'not_done')); ?>', '<?php echo $todo; ?>', '<?php echo $todo_key; ?>');"
               class="image todo-mark-done" title="<?php echo __('Click to mark todo item as not done'); ?>">
                <?php echo image_tag('permission_set_ok.png'); ?>
            </a>
        <?php else: ?>
            <a href="javascript:void(0);"
               onclick="TBG.Issues.markTodo('<?php echo make_url('todo_mark', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'mark' => 'done')); ?>', '<?php echo $todo; ?>', '<?php echo $todo_key; ?>');"
               class="image todo-mark-not-done" title="<?php echo __('Click to mark todo item as done'); ?>">
                <?php echo image_tag('permission_set_unset.png'); ?>
            </a>
        <?php endif; ?>
    </span>
        <span id="<?php echo $todo_key; ?>_name"><?php echo $todo; ?></span>
        <?php echo javascript_link_tag(image_tag('icon_delete.png'), array('onclick' => "TBG.Main.Helpers.Dialog.show('" . __('Do you really want to delete this todo?') . "', '" . __('Please confirm that you want to delete this todo.') . "', {yes: {click: function() {TBG.Issues.removeTodo('" . make_url('todo_delete', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())) . "', '" . $todo . "'); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});", 'class' => 'todo-delete')); ?>
    </div>
</<?php echo $done ? 'div' : 'li'; ?>>
