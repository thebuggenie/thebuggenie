<div class="faded_out todos_none" id="todos_none" <?php if ($issue->countTodos() != 0): ?>style="display: none;"<?php endif; ?>><?php echo __('There are no todos'); ?></div>
<div id="todos_box">
    <?php foreach ($issue->getTodos()['issue'] as $todo_key => $todo): ?>
        <?php include_component('main/todo', array_merge(compact('todo_key', 'todo', 'issue'), array('done' => false))); ?>
    <?php endforeach; ?>
    <?php foreach ($issue->getTodos()['comments'] as $comment_id => $comment_todos): ?>
        <div class="header"><?php echo $issue->getComments()[$comment_id]->getPostedBy()->getDisplayName(); ?></div>
        <?php foreach ($comment_todos as $todo_key => $todo): ?>
            <?php include_component('main/todo', array_merge(compact('todo', 'issue'), array('done' => false, 'todo_key' => $todo_key . '_comment'))); ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
<div id="done_todos_box">
    <div class="header"><?php echo __('Done'); ?></div>
    <?php foreach ($issue->getDoneTodos()['issue'] as $todo_key => $todo): ?>
        <?php include_component('main/todo', array_merge(compact('todo', 'issue'), array('done' => true, 'todo_key' => $todo_key . '_done'))); ?>
    <?php endforeach; ?>
    <?php foreach ($issue->getDoneTodos()['comments'] as $comment_id => $comment_todos): ?>
        <div class="header"><?php echo $issue->getComments()[$comment_id]->getPostedBy()->getDisplayName(); ?></div>
        <?php foreach ($comment_todos as $todo_key => $todo): ?>
            <?php include_component('main/todo', array_merge(compact('todo', 'issue'), array('done' => true, 'todo_key' => $todo_key . '_comment_done'))); ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
