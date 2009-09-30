<div style="background-color: #F5F5F5; padding: 5px; width: auto; border-bottom: 1px solid #DDD;"><b><?php echo __('DOUBLE-CHECK AND POST IT!'); ?></b></div>
<div id="step5_main_content">
<?php

	if ($step5_set == false || (($step5_set == true) && ($step1_set == false || $step2_set == false || $step3_set == false || $step4_set == false)))
	{
		if ($step1_set == true && $step2_set == true && $step3_set == true && $step4_set == true)
		{
			?>
			<div style="padding: 10px; padding-right: 5px;" id="step5_main_content_message"><?php echo __('Please read the instructions to the left'); ?></div>
			<script type="text/javascript">
				Effect.Pulsate('step5_main_content_message', { pulses: 8, duration: 5 });
			</script>
			<?php
		}
		else
		{
			?><div style="padding: 5px; background-color: #F9F9F9; text-align: left; color: #AAA;"><?php echo __('Please complete all the above steps first'); ?></div>
			<?php
		}
	}
	else
	{
		?>
		<div style="padding: 10px; text-align: center;">
		<div style="width: 330px; margin-left: auto; margin-right: auto;">
		<div style="display: inline;">
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="reportissue.php" enctype="multipart/form-data" method="post" name="rni_report_issue">
		<p>
		<input type="hidden" name="rni_report_issue" value="true">
		<input type="submit" value="<?php echo __('Report this issue'); ?>" style="font-weight: bold; font-size: 18px; height: 40px;">
		</p>
		</form>
		</div>
		<p style="padding-top: 5px; padding-bottom: 5px;"><?php echo __('(or if you also want to report another issue similar to this one)'); ?></p>
		<div style="display: inline;">
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="reportissue.php" enctype="multipart/form-data" method="post" name="rni_report_issue">
		<p>
		<input type="hidden" name="rni_report_issue" value="true">
		<input type="hidden" name="rni_preserve_info" value="true">
		<input type="submit" value="<?php echo __('Report this issue and start reporting a similar one'); ?>" style="font-weight: bold; font-size: 12px; height: 25px;">
		</p>
		</form>
		</div>		
		</div>
		<p style="padding-top: 5px; padding-bottom: 5px;"><?php echo __('(or if you changed your mind)'); ?></p>
		<p><a href="reportissue.php?restart=true"><?php echo __('Cancel, do not report'); ?></a></p>
		</div>
		<?php
	}

?>
</div>