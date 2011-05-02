<?php

	TBGContext::loadLibrary('ui');

	if (TBGContext::getUser()->isGuest() || TBGContext::getRequest()->hasParameter('redirect')):
?>

<script>
	showFadedBackdrop('<?php echo make_url('get_partial_for_backdrop', array_merge(array('key' => 'login'), $options)); ?>');
</script>

<?php
	else:
?>

<div class="rounded_box green borderless loggedindiv" >
	<?php echo __('You are already logged in'); ?>
</div>

<?php 
	endif;
?>