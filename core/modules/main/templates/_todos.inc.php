<div id="todos_box">
    <div class="faded_out todos_none" id="todos_none" <?php if ($issue->countTodos() != 0): ?>style="display: none;"<?php endif; ?>><?php echo __('There are no todos'); ?></div>
    <ul class="simple_list todos-list" id="todos_list">
        <?php foreach ($issue->getTodos()['issue'] as $todo_key => $todo): ?>
            <?php include_component('main/todo', array_merge(compact('todo', 'issue'), array('done' => false, 'todo_key' => 'todos_' . ($todo_key + 1)))); ?>
        <?php endforeach; ?>
    </ul>
    <?php foreach ($issue->getTodos()['comments'] as $comment_id => $comment_todos): ?>
        <div class="todo-comment-author">
            <?php echo include_component('main/userdropdown', array('user' => $issue->getComments()[$comment_id]->getPostedBy(), 'size' => 'small')); ?>
            <span><?php echo __('todos from comment %comment_number', array('%comment_number' => link_tag("#comment_{$issue->getComments()[$comment_id]->getID()}", '#'.$issue->getComments()[$comment_id]->getCommentNumber()))); ?></span>
        </div>
        <ul class="simple_list comment-todos-list" id="comment_<?php echo $comment_id; ?>_todos_list">
            <?php foreach ($comment_todos as $todo_key => $todo): ?>
                <?php include_component('main/todo', array_merge(compact('todo', 'issue'), array('done' => false, 'todo_key' => 'comment_' . $comment_id . '_todos_' . ($todo_key + 1)))); ?>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
</div>
<div id="done_todos_box">
    <div class="header">
        <?php echo __('Done'); ?>
        (<span id="viewissue_todo_done_count"><?php echo $issue->countDoneTodos(); ?></span>)
    </div>
    <div class="faded_out done-todos-none" id="done_todos_none" <?php if ($issue->countDoneTodos() != 0): ?>style="display: none;"<?php endif; ?>><?php echo __('There are no done todos'); ?></div>
    <div class="todos-list">
        <?php foreach ($issue->getDoneTodos()['issue'] as $todo_key => $todo): ?>
            <?php include_component('main/todo', array_merge(compact('todo', 'issue'), array('done' => true, 'todo_key' => $todo_key . '_done'))); ?>
        <?php endforeach; ?>
    </div>
    <?php foreach ($issue->getDoneTodos()['comments'] as $comment_id => $comment_todos): ?>
        <div class="todo-comment-author">
            <?php echo include_component('main/userdropdown', array('user' => $issue->getComments()[$comment_id]->getPostedBy(), 'size' => 'small')); ?>
            <span><?php echo __('todos from comment %comment_number', array('%comment_number' => link_tag("#comment_{$issue->getComments()[$comment_id]->getID()}", '#'.$issue->getComments()[$comment_id]->getCommentNumber()))); ?></span>
        </div>
        <div class="comment-todos-list">
            <?php foreach ($comment_todos as $todo_key => $todo): ?>
                <?php include_component('main/todo', array_merge(compact('todo', 'issue'), array('done' => true, 'todo_key' => $todo_key . '_comment_' . $comment_id . '_done'))); ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
<script type="text/javascript">
    require(['domReady', 'prototype', 'dragdrop'], function (domReady) {
        domReady(function () {
            <?php if (($issue->countTodos() + $issue->countDoneTodos()) === 0): ?>
                Element.remove('viewissue_todos_container');
            <?php else: ?>
                $('viewissue_todo_count').update(<?php echo $issue->countTodos(); ?>);
                Sortable.destroy('todos_list');
                Sortable.create('todos_list', {constraint: '', onUpdate: function(container) { TBG.Issues.saveTodosOrder(container, '<?php echo make_url('todo_saveorder', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'comment_id' => 0)); ?>'); }});
                <?php foreach ($issue->getTodos()['comments'] as $comment_id => $comment_todos): ?>
                Sortable.destroy('comment_<?php echo $comment_id; ?>_todos_list');
                Sortable.create('comment_<?php echo $comment_id; ?>_todos_list', {constraint: '', onUpdate: function(container) { TBG.Issues.saveTodosOrder(container, '<?php echo make_url('todo_saveorder', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'comment_id' => $comment_id)); ?>'); }});
                <?php endforeach; ?>
            <?php endif; ?>
        });
    });
</script>
<div id="todo_add" class="todo_add todo_editor" style="<?php if (!(isset($todo_error) && $todo_error)): ?>display: none; <?php endif; ?>margin-top: 5px;">
    <div class="todo_add_main">
        <div class="todo_add_title"><?php echo __('Create a todo'); ?></div>
        <form id="todo_form" accept-charset="<?php echo mb_strtoupper(\thebuggenie\core\framework\Context::getI18n()->getCharset()); ?>" action="<?php echo make_url('todo_add', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>" method="post" onSubmit="TBG.Issues.addTodo('<?php echo make_url('todo_add', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>');return false;">
            <label for="todo_bodybox"><?php echo __('Todo'); ?></label><br />
            <?php include_component('main/textarea', array('area_name' => 'todo_body', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'todo_bodybox', 'height' => '250px', 'width' => '100%', 'syntax' => $tbg_user->getPreferredCommentsSyntax(true), 'value' => ((isset($todo_error) && $todo_error) ? $todo_error_body : ''))); ?>
            <div id="todo_add_indicator" style="display: none;">
                <?php echo image_tag('spinning_20.gif'); ?>
            </div>
            <div id="todo_add_controls" class="todo_controls">
                <input type="hidden" name="forward_url" value="<?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false); ?>">
                <?php echo __('%create_todo or %cancel', array('%create_todo' => '<input type="submit" class="button button-green" value="'.__('Create todo').'" />', '%cancel' => javascript_link_tag(__('cancel'), array('onclick'=> "$('todo_add').hide();$('todo_add_button').show();")))); ?>
            </div>
        </form>
    </div>
</div>