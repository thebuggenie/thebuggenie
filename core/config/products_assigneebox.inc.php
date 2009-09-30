<table cellpadding=0 cellspacing=0 width="100%"><?php
print bugs_userDropdown($aUserID);
?><tr><td colspan=2><?php

	$first_hit = true;
	if ($assigns[0]['target_type'] == 1)
	{
		?>(<a href="javascript:void(0);" onclick="removeFromProject(<?php print $theProject->getID(); ?>, <?php print $aUserID; ?>);"><?php echo __('Remove'); ?></a>)&nbsp;<?php echo __('Assigned to this project (all editions &amp; components)');
	}
	else
	{
		if (count($assigns) > 1)
		{
			?>
			(<a href="javascript:void(0);" onclick="Effect.Appear('remove_user_<?php print $aUserID; ?>', { duration: 0.5 });"><?php echo __('Remove'); ?></a>)&nbsp;
			<div style="display: none; position: absolute; width: 250px; background-color: #FFF; border: 1px solid #DDD; padding: 10px;" id="remove_user_<?php print $aUserID; ?>">
			<a href="javascript:void(0);" onclick="removeFromProject(<?php print $theProject->getID(); ?>, <?php print $aUserID; ?>);"><?php echo __('Remove from project (all editions)'); ?></a><br>
			<div style="margin-top: 5px; border-bottom: 1px solid #DDD;"><b><?php echo __('Remove from an edition'); ?></b></div>
			<?php

				foreach($assigns as $theAssign)
				{
					if ($theAssign['target_type'] == 2)
					{
						$theAssign = BUGSfactory::editionLab($theAssign['target']);
						?><a href="javascript:void(0);" onclick="removeFromEdition(<?php print $theProject->getID(); ?>, <?php print $aUserID; ?>, <?php print $theAssign->getID(); ?>);"><?php echo __('Remove from %item_name%', array('%edition_name%' => $theAssign->getName())); ?></a><br><?php
					}
				}

			?>
			<div style="margin-top: 5px; border-bottom: 1px solid #DDD;"><b><?php echo __('Remove from a specific component'); ?></b></div>
			<?php

				foreach($assigns as $theAssign)
				{
					if ($theAssign['target_type'] == 3)
					{
						$theAssign = BUGSfactory::componentLab($theAssign['target']);
						?><a href="javascript:void(0);" onclick="removeFromComponent(<?php print $theProject->getID(); ?>, <?php print $aUserID; ?>, <?php print $theAssign->getID(); ?>);"><?php echo __('Remove from %item_name%', array('%item_name%' => $theAssign->getName())); ?></a><br><?php
					}
				}

			?>
			<div style="text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Effect.Fade('remove_user_<?php print $aUserID; ?>', { duration: 0.5 });"><?php echo __('Close menu'); ?></a></div>
			</div>
			<?php
		}
		else
		{
			$theAssign = $assigns[0];
			?>(<a href="javascript:void(0);" onclick="removeFrom<?php
			switch ($theAssign['target_type'])
			{
				case 1:
					echo 'Project(' . $theProject->getID() . ', ' . $aUserID;
					break;
				case 2:
					echo 'Edition(' . $theProject->getID() . ', ' . $aUserID . ', ' . $theAssign['target'];
					break;
				case 3:
					echo 'Component(' . $theProject->getID() . ', ' . $aUserID . ', ' . $theAssign['target'];
					break;
			}
			?>);">Remove</a>)&nbsp;<?php
		}
		echo __('Assigned to %list_of_items%', array('%list_of_items%' => ''));
		foreach ($assigns as $anAssign)
		{
			if ($anAssign['target_type'] == 1)
			{
				echo __('%assigned_to% all editions &amp; components', array('%assigned_to%' => ''));
				break;
			}
			elseif ($anAssign['target_type'] == 2)
			{
				$assignedEdition = BUGSfactory::editionLab($anAssign['target']);
				print ($first_hit == false) ? ", " : "";
				print $assignedEdition;
				$first_hit = false;
			}
			elseif ($anAssign['target_type'] == 3)
			{
				$assignedComponent = BUGSfactory::componentLab($anAssign['target']);
				print ($first_hit == false) ? ", " : "";
				print $assignedComponent;
				$first_hit = false;
			}
		}
	}

?></td></tr></table>