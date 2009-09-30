<?php BUGScontext::loadLibrary('ui'); ?>
<?php if ($show_mode == 'all'): ?>
	<?php foreach ($theBuild->getParent()->getBuilds() as $aBuild): ?>
		<span id="build_list_<?php echo $aBuild->getID(); ?>">
		<?php include_template('buildbox', array('aBuild' => $aBuild, 'access_level' => $access_level)); ?>
		</span>
	<?php endforeach; ?>
<?php elseif ($show_mode == 'one'): ?>
	<?php include_template('buildbox', array('aBuild' => $theBuild, 'access_level' => $access_level)); ?>
<?php endif; ?>