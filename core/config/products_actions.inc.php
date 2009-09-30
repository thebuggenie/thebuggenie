<?php

	$theProject = null;
	$theEdition = null;

	if (is_numeric(BUGScontext::getRequest()->getParameter('p_id')) && BUGScontext::getUser()->hasPermission("b2projectaccess", BUGScontext::getRequest()->getParameter('p_id'), "core") == true)
	{
		$theProject = new BUGSproject(BUGScontext::getRequest()->getParameter('p_id'));
		$allComponents = $theProject->getComponents();
	}
	if (is_numeric(BUGScontext::getRequest()->getParameter('e_id')) && BUGScontext::getUser()->hasPermission("b2editionaccess", BUGScontext::getRequest()->getParameter('e_id'), "core") == true)
	{
		$theEdition = new BUGSedition(BUGScontext::getRequest()->getParameter('e_id'));
	}
	if (is_numeric(BUGScontext::getRequest()->getParameter('c_id')))
	{
		$theComponent = new BUGScomponent(BUGScontext::getRequest()->getParameter('c_id'));
	}
	
	$hasbeensaved = false;
	$isAddedProject = false;
	$isAddedEdition = false;
	$isAddedBuild = false;
	$isDeletedBuild = false;
	$addedToOpen = 0;

	if ($access_level == "full")
	{
		if (BUGScontext::getRequest()->isAjaxCall())
		{	
			if (BUGScontext::getRequest()->getParameter('setleadby') && BUGScontext::getRequest()->getParameter('p_id') != "")
			{
				if ($theEdition instanceof BUGSedition)
				{
					$theEdition->setLeadBy(BUGScontext::getRequest()->getParameter('id'), BUGScontext::getRequest()->getParameter('lead_type'));
				}
				elseif (is_numeric(BUGScontext::getRequest()->getParameter('id')) && is_numeric(BUGScontext::getRequest()->getParameter('p_id')))
				{
					$theProject->setLeadBy(BUGScontext::getRequest()->getParameter('id'), BUGScontext::getRequest()->getParameter('lead_type'));
				}
			}
	
			if (BUGScontext::getRequest()->getParameter('setqa') && BUGScontext::getRequest()->getParameter('p_id') != "")
			{
				if ($theEdition instanceof BUGSedition)
				{
					$theEdition->setQA(BUGScontext::getRequest()->getParameter('id'), BUGScontext::getRequest()->getParameter('qa_type'));
				}
				elseif (is_numeric(BUGScontext::getRequest()->getParameter('id')) && is_numeric(BUGScontext::getRequest()->getParameter('p_id')))
				{
					$theProject->setQA(BUGScontext::getRequest()->getParameter('id'), BUGScontext::getRequest()->getParameter('qa_type'));
				}
			}
			if (BUGScontext::getRequest()->getParameter('add_project') && BUGScontext::getRequest()->getParameter('p_name') != '')
			{
				$aProject = BUGSproject::createNew(BUGScontext::getRequest()->getParameter('p_name'));
				require BUGScontext::getIncludePath() . 'include/config/projects_projectbox.inc.php';
			}

			if (BUGScontext::getRequest()->getParameter('add_edition') && BUGScontext::getRequest()->getParameter('e_name') != '')
			{
				$anEdition = $theProject->addEdition(BUGScontext::getRequest()->getParameter('e_name'));
				require BUGScontext::getIncludePath() . 'include/config/projects_editionbox.inc.php';
			}
	
			if (BUGScontext::getRequest()->getParameter('add_component') && BUGScontext::getRequest()->getParameter('c_name') != '')
			{
				$aComponent = $theProject->addComponent(BUGScontext::getRequest()->getParameter('c_name'));
				if ($theEdition instanceof BUGSedition)
				{
					$theEdition->addComponent($aComponent);
				}
				require BUGScontext::getIncludePath() . 'include/config/projects_componentbox.inc.php';
			}
			
			if (BUGScontext::getRequest()->getParameter('add_component') && is_numeric(BUGScontext::getRequest()->getParameter('c_id')))
			{
				$aComponent = BUGSfactory::componentLab(BUGScontext::getRequest()->getParameter('c_id'));
				$theEdition->addComponent($aComponent);
				require BUGScontext::getIncludePath() . 'include/config/projects_componentbox.inc.php';
			}
			
			if (BUGScontext::getRequest()->getParameter('getqa'))
			{
				if ($theEdition instanceof BUGSedition)
				{
					echo ($theEdition->getQAType() == BUGSidentifiableclass::TYPE_USER) ? bugs_userDropdown($theEdition->getQA()->getID()) : bugs_teamDropdown($theEdition->getQA()->getID());
				}
				elseif (BUGScontext::getRequest()->getParameter('p_id'))
				{
					echo ($theProject->getQAType() == BUGSidentifiableclass::TYPE_USER) ? bugs_userDropdown($theProject->getQA()->getID()) : bugs_teamDropdown($theProject->getQA()->getID());
				}
			}
			if (BUGScontext::getRequest()->getParameter('getleadby'))
			{
				if ($theEdition instanceof BUGSedition)
				{
					echo ($theEdition->getLeadType() == BUGSidentifiableclass::TYPE_USER) ? bugs_userDropdown($theEdition->getLeadBy()->getID()) : bugs_teamDropdown($theEdition->getLeadBy()->getID());
				}
				elseif (BUGScontext::getRequest()->getParameter('p_id'))
				{
					echo ($theProject->getLeadType() == BUGSidentifiableclass::TYPE_USER) ? bugs_userDropdown($theProject->getLeadBy()->getID()) : bugs_teamDropdown($theProject->getLeadBy()->getID());
				}
			}
			if (BUGScontext::getRequest()->getParameter('update_project') == "true")
			{
				if (is_numeric(BUGScontext::getRequest()->getParameter('p_id')))
				{
					if (BUGScontext::getUser()->hasPermission("b2projectaccess", BUGScontext::getRequest()->getParameter('p_id'), "core") == true)
					{
						$theProject->setDescription(BUGScontext::getRequest()->getParameter('description'));
						$theProject->setHomepage(BUGScontext::getRequest()->getParameter('homepage'));
						$theProject->setName(BUGScontext::getRequest()->getParameter('project_name'));
						if (!$theProject->setPrefix(BUGScontext::getRequest()->getParameter('prefix')))
						{
							$prefix_error = true;
						}
						$theProject->setDocumentationURL(BUGScontext::getRequest()->getParameter('doc_url'));
						$theProject->setUsePrefix((int) BUGScontext::getRequest()->getParameter('use_prefix', null, false));
					}
				}
			}
	
			if (BUGScontext::getRequest()->getParameter('update_project_settings') && !BUGScontext::getRequest()->getParameter('find_leadby') && !BUGScontext::getRequest()->getParameter('find_qa'))
			{
				if (is_numeric(BUGScontext::getRequest()->getParameter('p_id')))
				{
					if (BUGScontext::getUser()->hasPermission("b2projectaccess", BUGScontext::getRequest()->getParameter('p_id'), "core"))
					{
						$release_date = (BUGScontext::getRequest()->getParameter('planned_release') == 1) ? mktime(0, 0, 0, BUGScontext::getRequest()->getParameter('release_month'), BUGScontext::getRequest()->getParameter('release_day'), BUGScontext::getRequest()->getParameter('release_year')) : 0;
						$row = B2DB::getTable('B2tProjects')->doSelectById(BUGScontext::getRequest()->getParameter('p_id'));
						$relsd = $row->get(B2tProjects::RELEASED);
						$release_date = ((BUGScontext::getRequest()->getParameter('released') != $relsd) && $relsd == 0) ? $_SERVER["REQUEST_TIME"] : $release_date;
						$hrsprday = ((int) BUGScontext::getRequest()->getParameter('hrs_pr_day') > 0) ? (int) BUGScontext::getRequest()->getParameter('hrs_pr_day') : 7;
						
						$theProject->setTasksEnabled((int) BUGScontext::getRequest()->getParameter('enable_tasks'));
						$theProject->setReleased((int) BUGScontext::getRequest()->getParameter('released'));
						$theProject->setTasksEnabled((int) BUGScontext::getRequest()->getParameter('enable_tasks'));
						$theProject->setVotesEnabled((int) BUGScontext::getRequest()->getParameter('votes'));
						$theProject->setTimeUnit((int) BUGScontext::getRequest()->getParameter('time_unit'));
						$theProject->setHoursPerDay($hrsprday);
						$theProject->setDefaultStatus((int) BUGScontext::getRequest()->getParameter('defaultstatus'));
						$theProject->setReleaseDate($release_date);
						$theProject->setLocked((int) BUGScontext::getRequest()->getParameter('locked'));
						$theProject->setBuildsEnabled((int) BUGScontext::getRequest()->getParameter('enable_builds'));
					}
				}
			}
			
			if (BUGScontext::getRequest()->getParameter('edit_component') && is_numeric(BUGScontext::getRequest()->getParameter('c_id')))
			{
				$theComponent->setName(BUGScontext::getRequest()->getParameter('c_name'));
				echo $theComponent->getName();
			}
	
			if (BUGScontext::getRequest()->getParameter('remove_component') && is_numeric(BUGScontext::getRequest()->getParameter('c_id')))
			{
				$theEdition->removeComponent((int) BUGScontext::getRequest()->getParameter('c_id'));
			}
			
			if (BUGScontext::getRequest()->getParameter('geteditioncomponents'))
			{
				foreach ($theEdition->getComponents() as $aComponent)
				{
					?>
					<tr id="edition_component_<?php echo $aComponent->getID(); ?>">
					<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?></td>
					<td style="width: auto; padding: 2px;"><?php print $aComponent; ?></td>
					<?php
					
					if ($access_level == 'full') 
					{
						?>
						<td style="width: 40px; text-align: right;"><a href="javascript:void(0);" onclick="removeEditionComponent(<?php echo $theProject->getID(); ?>, <?php echo $theEdition->getID(); ?>, <?php echo $aComponent->getID(); ?>);"><?php echo __('Remove'); ?></a></td>
						<?php
					}
					
					?>
					</tr>
					<?php
				}
				if (count($theEdition->getComponents()) == 0)
				{
					?>
					<tr>
					<td style="padding: 3px; color: #AAA;" colspan=3><?php echo __('This edition has no components'); ?></td>
					</tr>
					<?php
				}
							
				if ($access_level == "full")
				{
					?>
					<tr>
					<td style="padding: 3px; border-bottom: 1px solid #DDD;" colspan=3><br><b><?php echo __('Add existing component'); ?></b></td>
					</tr>
					<?php
			
					foreach ($theProject->getComponents() as $aComponent)
					{
						$hasit = false;
						foreach ($theEdition->getComponents() as $aC)
						{
							if ($aC->getID() == $aComponent->getID())
							{
								$hasit = true;
								break;
							}
						}
						if ($hasit == false)
						{
							?>
							<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?></td>
							<td style="width: auto; padding: 2px;"><?php print $aComponent; ?></td>
							<?php
							
							if ($access_level == 'full') 
							{
								?>
								<td style="width: 40px; text-align: right;"><a href="javascript:void(0);" onclick="addEditionComponent(<?php echo $theProject->getID(); ?>, <?php echo $theEdition->getID(); ?>, <?php echo $aComponent->getID(); ?>);"><?php echo __('Add this'); ?></a></td>
								<?php
							}
							
							?>
							</tr>
							<?php
						}
					}
					if (count($theProject->getComponents()) == 0)
					{
						?>
						<tr>
						<td style="padding: 3px; color: #AAA;" colspan=3><?php echo __('This project has no components'); ?></td>
						</tr>
						<?php
					}
				}
			}

			if (BUGScontext::getRequest()->getParameter('delete_component') && is_numeric(BUGScontext::getRequest()->getParameter('c_id')))
			{
				$theComponent->delete();
			}

			if (BUGScontext::getRequest()->getParameter('delete_edition') && is_numeric(BUGScontext::getRequest()->getParameter('e_id')))
			{
				if (BUGScontext::getUser()->hasPermission('b2editionaccess', BUGScontext::getRequest()->getParameter('e_id'), 'core'))
				{
					$theEdition->delete();
				}
			}
			
			if (BUGScontext::getRequest()->getParameter('find_dev'))
			{
				if (strlen(BUGScontext::getRequest()->getParameter('find_dev_uname')) >= 2)
				{
					$users = BUGSuser::findUsers(BUGScontext::getRequest()->getParameter('find_dev_uname'));
					$retval = '';
					if (count($users) > 0)
					{
						$retval .= '<div style="padding: 2px; border-bottom: 1px solid #DDD;"><b>' . __('Found these users') . '</b>&nbsp;(' . __('click on a user for more options') . ':</div>';
						foreach ($users as $aUser)
						{
							$retval .= '<a href="javascript:void(0);" onclick="Effect.Appear(\'assign_dev_' . $aUser->getID() . '\', { duration: 0.5 });">';
							$retval .= (string) $aUser;
							$retval .= '</a><br>'; 
							$retval .= '<div style="position: absolute; width: 250px; border: 1px solid #DDD; background-color: #FFF; padding: 2px; display: none;" id="assign_dev_' . $aUser->getID() . '">';
							$retval .= __('Select where to assign this user') . '<br>';
							$retval .= '<br><a href="javascript:void(0);" onclick="assignToProject(' . $theProject->getID() . ', ' . $aUser->getID() . ');Effect.Fade(\'assign_dev_' . $aUser->getID() . '\', { duration: 0.5 });">' . __('Assign to project (all editions &amp; components)') . '</a><br>';
							$retval .= '<br><b>' . __('Assign to an edition') . '</b><br>';
							foreach ($theProject->getEditions() as $anEdition)
							{
								$retval .= '<a href="javascript:void(0);" onclick="assignToEdition(' . $theProject->getID() . ', ' . $aUser->getID() . ', ' . $anEdition->getID() . ');Effect.Fade(\'assign_dev_' . $aUser->getID() . '\', { duration: 0.5 });">' . __('Assign to %list_of_items%', array('%list_of_items%' => $anEdition)) . '</a><br>';
							}
							$retval .= '<br><b>Assign to a component</b><br>';
							foreach ($theProject->getComponents() as $aComponent)
							{
								$retval .= '<a href="javascript:void(0);" onclick="assignToComponent(' . $theProject->getID() . ', ' . $aUser->getID() . ', ' . $aComponent->getID() . ');Effect.Fade(\'assign_dev_' . $aUser->getID() . '\', { duration: 0.5 });">' . __('Assign to %list_of_items%', array('%list_of_items%' => $aComponent)) . '</a><br>';
							}
							$retval .= '<div style="text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Effect.Fade(\'assign_dev_' . $aUser->getID() . '\', { duration: 0.5 });">' . __('Close menu') . '</a></div>';
							$retval .= '</div>';
						}
					}
					else
					{
						$retval = '<br>' . __('No users found') . '...';
					}
				}
				else
				{
					$retval = '<br>' . __('Please enter at least two characters to search') . '...';
				}
				echo $retval;
			}
			
			if (BUGScontext::getRequest()->getParameter('add_dev') == true && is_numeric(BUGScontext::getRequest()->getParameter('target')) && is_numeric(BUGScontext::getRequest()->getParameter('target_type')) && is_numeric(BUGScontext::getRequest()->getParameter('u_id')))
			{
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tUserAssigns::MODULE, 'core');
				$crit->addInsert(B2tUserAssigns::TARGET, BUGScontext::getRequest()->getParameter('target'));
				$crit->addInsert(B2tUserAssigns::TARGET_TYPE, BUGScontext::getRequest()->getParameter('target_type'));
				$crit->addInsert(B2tUserAssigns::UID, BUGScontext::getRequest()->getParameter('u_id'));
				$crit->addInsert(B2tUserAssigns::SCOPE, BUGScontext::getScope()->getID());
				B2DB::getTable('B2tUserAssigns')->doInsert($crit);
				BUGScontext::getRequest()->setParameter('getassignees', true);
			}
			
			if (BUGScontext::getRequest()->getParameter('remove_dev') == true && is_numeric(BUGScontext::getRequest()->getParameter('target')) && is_numeric(BUGScontext::getRequest()->getParameter('target_type')) && is_numeric(BUGScontext::getRequest()->getParameter('u_id')))
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tUserAssigns::TARGET, BUGScontext::getRequest()->getParameter('target'));
				$crit->addWhere(B2tUserAssigns::TARGET_TYPE, BUGScontext::getRequest()->getParameter('target_type'));
				$crit->addWhere(B2tUserAssigns::UID, BUGScontext::getRequest()->getParameter('u_id'));
				$crit->addWhere(B2tUserAssigns::SCOPE, BUGScontext::getScope()->getID());
				$crit->addWhere(B2tUserAssigns::MODULE, 'core');
				B2DB::getTable('B2tUserAssigns')->doDelete($crit);
				BUGScontext::getRequest()->setParameter('getassignees', true);
			}
	
			if (BUGScontext::getRequest()->getParameter('getassignees'))
			{
				$assignees = $theProject->getAssignees();
			
				if (count($assignees) == 0)
				{
					?><div style="padding-left: 5px; padding-top: 3px; color: #AAA;"><?php echo __('There are no developers assigned to this project'); ?></div><?php
				}
				else
				{
					foreach ($assignees as $aUserID => $assigns)
					{
						require BUGScontext::getIncludePath() . 'include/config/projects_assigneebox.inc.php';
					}
				}
			}
			
			if (BUGScontext::getRequest()->getParameter('setdefaultproject') != "")
			{
				if (is_numeric(BUGScontext::getRequest()->getParameter('defaultproject')))
				{
					BUGSproject::setDefault(BUGScontext::getRequest()->getParameter('defaultproject'));
				}
			}
			
			if (BUGScontext::getRequest()->getParameter('getprojects'))
			{
				$defaultProject = BUGSproject::getDefaultProject();
				foreach (BUGSproject::getAll() as $aProject)
				{
					$aProject = BUGSfactory::projectLab($aProject['id']);
					?>
					<option value=<?php print $aProject->getID(); print ($defaultProject == $aProject->getID()) ? " selected" : ""; ?>><?php print $aProject; ?></option>
					<?php
				}
			}
	
			if (BUGScontext::getRequest()->getParameter('showmessage'))
			{
				switch (BUGScontext::getRequest()->getParameter('themessage'))
				{
					case 'addedproject':
						echo bugs_successStrip(__('The project has been added'), __('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'));
						break;
					case 'addededition':
						echo bugs_successStrip(__('The edition has been added'), __('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'));
						break;
					case 'addedcomponent':
						echo bugs_successStrip(__('The component has been added'), __('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'));
						break;
					case 'addedbuild':
						echo bugs_successStrip(__('The build has been added'), __('Access has been granted to your group. Remember to give other users/groups permission to access it via the admin section to the left, if necessary.'));
						break;
					case 'updatedcomponent':
						echo bugs_successStrip(__('The component name has been changed'));
						break;
					case 'updatedbuild':
						echo bugs_successStrip(__('The build details has been updated'));
						break;
					case 'deletedcomponent':
						echo bugs_successStrip(__('The selected component has been deleted'));
						break;
					case 'deletededition':
						echo bugs_successStrip(__('The selected edition has been deleted'));
						break;
					case 'deletedbuild':
						echo bugs_successStrip(__('The selected build has been deleted'));
						break;
					case 'buildaddedtoopenissues':
						echo bugs_successStrip(__('The selected build has been added to all open issues'));
						break;
					case 'buildreleased':
						echo bugs_successStrip(__('The selected build has been released'));
						break;
					case 'buildretracted':
						echo bugs_successStrip(__('The selected build has been retracted'));
						break;
					case 'buildlocked':
						echo bugs_successStrip(__('The selected build has been locked for new issue reports'));
						break;
					case 'buildunlocked':
						echo bugs_successStrip(__('The selected build is no longer locked for new issue reports'));
						break;
					case 'buildsetasdefault':
						echo bugs_successStrip(__('The selected build is now marked as the default build for reporting new issues'));
						break;
					case 'projectdetailssaved':
					case 'editiondetailssaved':
					case 'editionsettingssaved':
					case 'projectdefaultsaved':
					case 'projectsettingssaved':
						echo bugs_successStrip(__('Your changes has been saved'));
						break;
				}
				echo '<script type="text/javascript">';
				echo 'Element.show(\'message_span\');';
				echo 'Effect.Fade(\'message_span\', {delay: 10} );';
				echo '</script>';
			}

			if (BUGScontext::getRequest()->getParameter('edit_settings') == true && is_numeric(BUGScontext::getRequest()->getParameter('e_id')))
			{
				$release_date = (BUGScontext::getRequest()->getParameter('planned_release') == 1) ? mktime(0, 0, 0, BUGScontext::getRequest()->getParameter('release_month'), BUGScontext::getRequest()->getParameter('release_day'), BUGScontext::getRequest()->getParameter('release_year')) : 0;
				$relsd = ($theEdition->isReleased()) ? 1 : 0;
				$release_date = ((BUGScontext::getRequest()->getParameter('released') != $relsd) && $relsd == 0) ? $_SERVER["REQUEST_TIME"] : $release_date;
				
				$theEdition->setReleased((int) BUGScontext::getRequest()->getParameter('released'));
				$theEdition->setReleaseDate($release_date);
				$theEdition->setLocked((int) BUGScontext::getRequest()->getParameter('locked'));
			}
	
			if (BUGScontext::getRequest()->getParameter('edit_details') == true && is_numeric(BUGScontext::getRequest()->getParameter('e_id')))
			{
				$theEdition->setDescription(BUGScontext::getRequest()->getParameter('description'));
				$theEdition->setName(BUGScontext::getRequest()->getParameter('e_name'));
				$theEdition->setDocumentationURL(BUGScontext::getRequest()->getParameter('doc_url'));
			}

			if (is_numeric(BUGScontext::getRequest()->getParameter('b_id')) && BUGScontext::getRequest()->getParameter('action') != "")
			{
				$b_id = BUGScontext::getRequest()->getParameter('b_id');
				if ($b_id != 0) $aBuild = BUGSfactory::buildLab($b_id);

				if (BUGScontext::getRequest()->getParameter('action') == 'add')
				{
					$aBuild = BUGSbuild::createNew(BUGScontext::getRequest()->getParameter('build_name'), $theEdition->getID(), (int) BUGScontext::getRequest()->getParameter('ver_mj'), (int) BUGScontext::getRequest()->getParameter('ver_mn'), (int) BUGScontext::getRequest()->getParameter('ver_rev'));
					$include_table = true;
				}
				elseif ($aBuild instanceof BUGSbuild)
				{
					switch (BUGScontext::getRequest()->getParameter('action'))
					{
						case "addtoopen":
							$aBuild->addToOpenIssues();
							break;
						case "edit":
							if ($aBuild->isReleased() == false)
							{
								$planned_rel = "planned_release_" . $b_id;
								$rel_day = "build_release_day_" . $b_id;
								$rel_month = "build_release_month_" . $b_id;
								$rel_year = "build_release_year_" . $b_id;
								$rel_time = (BUGScontext::getRequest()->getParameter($planned_rel) == 1) ? mktime(0, 0, 0, BUGScontext::getRequest()->getParameter($rel_month), BUGScontext::getRequest()->getParameter($rel_day), BUGScontext::getRequest()->getParameter($rel_year)) : 0;
							}
							else
							{
								$rel_time = $aBuild->getReleaseDate();
							}
							$build_name = BUGScontext::getRequest()->getParameter('build_name');
							$ver_mj = (int) BUGScontext::getRequest()->getParameter('ver_mj');
							$ver_mn = (int) BUGScontext::getRequest()->getParameter('ver_mn');
							$ver_rev = (int) BUGScontext::getRequest()->getParameter('ver_rev');
							
							$aBuild->setName($build_name);
							$aBuild->setReleaseDate($rel_time);
							$aBuild->setVersion($ver_mj, $ver_mn, $ver_rev);
							break;
						case "release":
							$aBuild->setReleased(true);
							$aBuild->setReleaseDate();
							break;
						case "retract":
							$aBuild->setReleased(false);
							break;
						case "lock":
							$aBuild->setLocked(true);
							break;
						case "unlock":
							$aBuild->setLocked(false);
							break;
						case "setdefault":
							$aBuild->setDefault();
							foreach ($theEdition->getBuilds() as $aBuild)
							{
								$aBuild = new BUGSbuild($aBuild->getID());
								$include_table = true;
								require BUGScontext::getIncludePath() . 'include/config/projects_buildbox.inc.php';
							}
							break;
						case "delete":
							$aBuild->delete();
							$isDeletedBuild = true;
					}
				}

				if ($aBuild instanceof BUGSbuild && BUGScontext::getRequest()->getParameter('action') != 'delete' && BUGScontext::getRequest()->getParameter('action') != 'setdefault')
				{
					require BUGScontext::getIncludePath() . 'include/config/projects_buildbox.inc.php';
				}
			}
		}
	}

	$allProjects = BUGSproject::getAll();
	$defaultProject = BUGSproject::getDefaultProject();
	if ($defaultProject != 0)
	{
		$defaultProject = BUGSfactory::projectLab($defaultProject);
	}
	elseif (!empty($allProjects))
	{
		$defaultProject = BUGSfactory::projectLab($allProjects[0]['id']);
	}
	
?>