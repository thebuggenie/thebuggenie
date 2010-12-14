<?php

	TBGContext::loadLibrary('ui');

?>

<script>
	showFadedBackdrop('<?php echo make_url('get_partial_for_backdrop', array_merge(array('key' => 'login'), $options)); ?>');
</script>