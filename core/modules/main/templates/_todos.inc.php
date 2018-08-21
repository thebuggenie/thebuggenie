<?php /** @var \thebuggenie\core\entities\Issue $issue */ ?>
<div id="todos_box">
    <legend class="viewissue_comments_header">
        <?php echo __('Todos (%count_done / %count_total)',
            [
                '%count_done' => '<span id="viewissue_todo_count_done">' . $issue->countDoneTodos() . '</span>',
                '%count_total' => '<span id="viewissue_todo_count_total">' . $issue->countTodos() . '</span>'
            ]); ?>
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
            <?php include_component('main/todo',
                [
                    'todo' => $todo,
                    'issue' => $issue,
                    'todo_index' => $todo_index,
                    'todo_key' => 'todos_' . ($todo_index + 1),
                    'comment_id' => 0,
                ]
            ); ?>
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
                    <?php include_component('main/todo',
                        [
                            'todo' => $todo,
                            'issue' => $issue,
                            'todo_index' => $todo_index,
                            'todo_key' => 'comment_' . $comment_id . '_todos_' . ($todo_index + 1),
                            'comment_id' => $comment_id,
                        ]
                    ); ?>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php endif; ?>
</div>