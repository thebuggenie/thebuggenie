<?php if ($done): ?>
    <div class="todo"
         id="item_option_<?php echo $todo_key; ?>"
         style="clear: both;">
      <?php include_component('main/todo_contents', compact('todo', 'issue', 'done', 'todo_key', 'comment_id', 'todo_index')); ?>
    </div>
<?php else: ?>
    <li class="todo"
        id="item_option_<?php echo $todo_key; ?>"
        style="clear: both;">
      <?php include_component('main/todo_contents', compact('todo', 'issue', 'done', 'todo_key', 'comment_id', 'todo_index')); ?>
    </li>
<?php endif; ?>