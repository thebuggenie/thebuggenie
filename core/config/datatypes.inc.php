<?php

	if (($access_level != "full" && $access_level != "read") || BUGScontext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{

		if ($access_level == "full")
		{
			require BUGScontext::getIncludePath() . 'include/config/datatypes_actions.inc.php';
		}

		if (!BUGScontext::getRequest()->isAjaxCall())
		{
			?>
			<script type="text/javascript" src="<?php echo BUGScontext::getTBGPath(); ?>js/config/datatypes_ajax.js"></script>
			<table style="width: 100%" cellpadding=0 cellspacing=0>
				<tr>
				<td style="padding-right: 10px;">
					<table class="configstrip" cellpadding=0 cellspacing=0>
						<tr>
							<td class="cleft"><b><?php echo __('Configure datatypes'); ?></b></td>
							<td class="cright">&nbsp;</td>
						</tr>
						<tr>
							<td colspan=2 class="cdesc">
							<?php echo __('From here you can manage all data types available in BUGS, which relates to issues.'); ?>
							<?php echo __('Several of these data types have some kind of setting associated with them, which you can set in the corresponding subsection.'); ?>
							</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			<div style="width: 740px;">
			<?php
	
				switch (BUGScontext::getRequest()->getParameter('subsection'))
				{
					case 1:
						require_once BUGScontext::getIncludePath() . 'include/config/datatypes_issuetypes.inc.php';
						break;
					case 2:
						require_once BUGScontext::getIncludePath() . 'include/config/datatypes_resolutiontypes.inc.php';
						break;
					case 3:
						require_once BUGScontext::getIncludePath() . 'include/config/datatypes_prioritylevels.inc.php';
						break;
					case 4:
						require_once BUGScontext::getIncludePath() . 'include/config/datatypes_categories.inc.php';
						break;
					case 5:
						require_once BUGScontext::getIncludePath() . 'include/config/datatypes_reproductionlevels.inc.php';
						break;
					case 6:
						require_once BUGScontext::getIncludePath() . 'include/config/datatypes_statustypes.inc.php';
						break;
					case 7:
						require_once BUGScontext::getIncludePath() . 'include/config/datatypes_severitylevels.inc.php';
						break;
					case 8:
						require_once BUGScontext::getIncludePath() . 'include/config/datatypes_userstates.inc.php';
						break;
				}
			
			?>
			</div>
			<?php // END PERMISSION DOUBLECHECK
		}
	}
?>