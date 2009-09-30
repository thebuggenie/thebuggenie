<?php

	if (BUGScontext::getRequest()->getParameter('updatestep2button') && (isset($_SESSION['rni_step2_component']) && isset($_SESSION['rni_step2_category']) && isset($_SESSION['rni_step2_severity']) && isset($_SESSION['rni_step2_issuetype'])))
	{
		?>
		<script type="text/javascript">
			updateSteps(2);
		</script>
		<?php /* ?><button onclick="setStep(2);$('step2_button').hide();" id="step2_button"><?php echo __('Confirm'); ?></button> */ ?>
		<?php
	}
	elseif (!$step2_set)
	{
		?><div style="padding: 3px; color: #AAA;"><?php echo __('You must select one item from each list above to continue'); ?></div><?php
	}
		
?>