<ul>
	<?php foreach ($log_items as $item): ?>
		<?php if (!$item instanceof TBGLogItem) continue; ?>
		<?php include_template('main/issuelogitem', compact('item')); ?>
	<?php endforeach; ?>
</ul>
