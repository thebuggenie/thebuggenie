<?php BUGScontext::loadLibrary('ui'); ?>
<span id="build_list_<?php echo $theBuild->getID(); ?>">
	<?php include_template('buildbox', array('aBuild' => $theBuild, 'access_level' => $access_level)); ?>
</span>