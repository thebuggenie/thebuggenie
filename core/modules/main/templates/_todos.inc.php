<div id="todos_box">
    <legend class="viewissue_comments_header">
        <?php echo __('Todos (%count)',
            ['%count' => '<span id="viewissue_todo_count">' . $issue->countTodos() . '</span>']); ?>
        <?php echo image_tag('spinning_16.gif', ['id' => 'todos_sort_indicator', 'style' => 'display: none;']); ?>
        <?php if ($issue->canEditDescription()): ?>
            <ul class="simple_list button_container"
                id="add_todo_button_container">
                <li id="todo_add_button"><input class="button button-silver first last"
                                                type="button"
                                                onclick="TBG.Issues.showTodo();"
                                                value="<?php echo __('Add todo'); ?>"></li>
            </ul>
        <?php endif; ?>
    </legend>
    <ul class="simple_list todos-list" id="todos_list">
    <?php if ($issue->countTodos()): ?>
        <?php foreach ($issue->getTodos()['issue'] as $todo_index => $todo): ?>
            <?php include_component('main/todo', array_merge(
                compact('todo', 'issue', 'todo_index'),
                [
                    'done' => false,
                    'todo_key' => 'todos_' . ($todo_index + 1),
                    'comment_id' => 0,
                ]
            )); ?>
        <?php endforeach; ?>
    <?php endif; ?>
    </ul>
    <?php if ($issue->countTodos()): ?>
        <?php foreach ($issue->getTodos()['comments'] as $comment_id => $comment_todos): ?>
            <div class="todo-comment-author">
                <?php echo include_component('main/userdropdown',
                    ['user' => $issue->getComments()[$comment_id]->getPostedBy(), 'size' => 'small']); ?>
                <span><?php echo __('todos from comment %comment_number', [
                        '%comment_number' => link_tag("#comment_{$issue->getComments()[$comment_id]->getID()}",
                            '#' . $issue->getComments()[$comment_id]->getCommentNumber()),
                    ]); ?></span>
            </div>
            <ul class="simple_list comment-todos-list"
                id="comment_<?php echo $comment_id; ?>_todos_list">
                <?php foreach ($comment_todos as $todo_index => $todo): ?>
                    <?php include_component('main/todo', array_merge(compact('todo', 'issue', 'todo_index', 'comment_id'),
                        ['done' => false, 'todo_key' => 'comment_' . $comment_id . '_todos_' . ($todo_index + 1)])); ?>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<div id="done_todos_box">
    <legend class="viewissue_comments_header">
        <?php echo __('Done todos (%count)', ['%count' => '<span id="viewissue_todo_done_count">' . $issue->countDoneTodos() . '</span>']); ?>
    </legend>
    <?php if ($issue->countDoneTodos()): ?>
        <div class="todos-list">
            <?php foreach ($issue->getDoneTodos()['issue'] as $todo_index => $todo): ?>
                <?php include_component('main/todo', array_merge(compact('todo', 'issue', 'todo_index'),
                    ['done' => true, 'todo_key' => $todo_index . '_done', 'comment_id' => 0])); ?>
            <?php endforeach; ?>
        </div>
        <?php foreach ($issue->getDoneTodos()['comments'] as $comment_id => $comment_todos): ?>
            <div class="todo-comment-author">
                <?php echo include_component('main/userdropdown',
                    ['user' => $issue->getComments()[$comment_id]->getPostedBy(), 'size' => 'small']); ?>
                <span><?php echo __('todos from comment %comment_number', [
                        '%comment_number' => link_tag("#comment_{$issue->getComments()[$comment_id]->getID()}",
                            '#' . $issue->getComments()[$comment_id]->getCommentNumber()),
                    ]); ?></span>
            </div>
            <div class="comment-todos-list">
                <?php foreach ($comment_todos as $todo_index => $todo): ?>
                    <?php include_component('main/todo', array_merge(compact('todo', 'issue', 'todo_index', 'comment_id'),
                        ['done' => true, 'todo_key' => $todo_index . '_comment_' . $comment_id . '_done'])); ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<script type="text/javascript">
    require(['domReady', 'prototype', 'dragdrop'], function (domReady) {
        domReady(function () {
//            Sortable.destroy('todos_list');
            <?php if ($issue->canEditDescription()): ?>
//            Sortable.create('todos_list', {constraint: '', onUpdate: function(container) { TBG.Issues.saveTodosOrder(container, '<?php //echo make_url('todo_saveorder', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'comment_id' => 0)); ?>//'); }});
            <?php endif; ?>
            <?php foreach ($issue->getTodos()['comments'] as $comment_id => $comment_todos): ?>
//                Sortable.destroy('comment_<?php //echo $comment_id; ?>//_todos_list');
            <?php if ($comment_id != 0 && $issue->getComments()[$comment_id]->canUserEditComment()): ?>
//                    Sortable.create('comment_<?php //echo $comment_id; ?>//_todos_list', {constraint: '', onUpdate: function(container) { TBG.Issues.saveTodosOrder(container, '<?php //echo make_url('todo_saveorder', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'comment_id' => $comment_id)); ?>//'); }});
            <?php endif; ?>
            <?php endforeach; ?>
        });
    });
</script>