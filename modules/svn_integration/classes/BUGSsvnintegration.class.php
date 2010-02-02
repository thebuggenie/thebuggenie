<?php

	class BUGSsvnintegration extends TBGModule 
	{
		
		public function __construct($m_id, $res = null)
		{
			parent::__construct($m_id, $res);
			$this->_module_version = '1.0';
			$this->setLongName(TBGContext::getI18n()->__('SVN integration'));
			$this->setMenuTitle(TBGContext::getI18n()->__('SVN integration'));
			$this->setConfigTitle(TBGContext::getI18n()->__('SVN integration'));
			$this->setDescription(TBGContext::getI18n()->__('Enables integration with SVN'));
			$this->setHasConfigSettings();
			$this->setConfigDescription(TBGContext::getI18n()->__('Configure source code integration from this section'));
			$this->addAvailableListener('core', 'viewissue_right_middle', 'section_viewissueRightMiddle', 'List of updated files for an issue');
			$this->addAvailableListener('core', 'viewproject_right_top', 'section_viewProject_viewCode', '"View code" link in project overview');
		}

		public function initialize()
		{

		}

		public static function install($scope = null)
		{
  			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			
			$module = parent::_install('svn_integration', 'BUGSsvnintegration', '1.0', true, false, false, $scope);

			$module->enableListenerSaved('core', 'viewissue_right_middle');
			$module->enableListenerSaved('core', 'viewproject_right_top');

			if ($scope == TBGContext::getScope()->getID())
			{
				B2DB::getTable('TBGSVNintegrationTable')->create();
			}

			return true;
		}
					
		public function uninstall()
		{
			if (TBGContext::getScope()->getID() == 1)
			{
				B2DB::getTable('TBGSVNintegrationTable')->drop();
			}
			parent::_uninstall();
		}
		
		public function loadHelpTitle($topic)
		{
			switch ($topic)
			{
				case 'main':
					return TBGContext::getI18n()->__('Main');
					break;
				case 'howto':
					return TBGContext::getI18n()->__('How to set up SVN integration');
					break;
			}
			return parent::loadHelpTitle($topic);
		}
		
		public function enableSection($module, $identifier, $scope)
		{
			$function_name = '';
			switch ($module . '_' . $identifier)
			{
				case 'core_viewissue_right_middle':
					$function_name = 'section_viewissueRightMiddle';
					break;
				case 'viewproject_right_top':
					$function_name = 'section_viewProject_viewCode';
					break;
			}
			if ($function_name != '') parent::registerPermanentTriggerListener($module, $identifier, $function_name, $scope);
		}
		
		public function section_viewissueRightMiddle($theIssue)
		{
			?>
			<div style="margin-top: 10px; margin-bottom: 0px; border: 0px;">
				<div style="border-bottom: 1px solid #DDD; padding: 3px; font-size: 12px;">
				<b><?php echo TBGContext::getI18n()->__('Subversion checkins'); ?></b>
				</div>
				<div style="padding-top: 5px; padding-bottom: 5px;" id="svn_checkins">
				<?php
				
					$crit = new B2DBCriteria();
					$crit->addWhere(TBGSVNintegrationTable::ISSUE_NO, $theIssue->getID());
					$crit->addOrderBy(TBGSVNintegrationTable::DATE, 'desc');
					if ($results = B2DB::getTable('TBGSVNintegrationTable')->doSelect($crit))
					{
						$vvcpath_setting = 'viewvc_path_' . $theIssue->getProject()->getID();
						$viewvc_path = TBGContext::getModule('svn_integration')->getSetting($vvcpath_setting);
						
						echo '<table cellpadding=0 cellspacing=0 style="width: 100%;">';
						while ($results->next())
						{
							$theUser = TBGFactory::userLab($results->get(TBGSVNintegrationTable::AUTHOR));
							echo '<tr>';
							echo '<td class="issuedetailscontentsleft" style="border-bottom: 0px; padding-right: 20px;">';
							echo '<span style="font-size: 10px;">[rev <b>'.$results->get(TBGSVNintegrationTable::NEW_REV).'</b>] </span>';
							if ($viewvc_path)
							{
								echo '<a href="' . $viewvc_path . $results->get(TBGSVNintegrationTable::FILE_NAME) . '?view=log" target="_blank"><b>' . $results->get(TBGSVNintegrationTable::FILE_NAME) . '</b></a>';
							}
							else
							{
								echo $results->get(TBGSVNintegrationTable::FILE_NAME);
							}
							echo '</td>';
							if ($viewvc_path)
							{
								echo '<td class="issuedetailscontentscenter" style="border-bottom: 0px; padding-right: 10px;"><a href="' . $viewvc_path . $results->get(TBGSVNintegrationTable::FILE_NAME) . '?r1=' . $results->get(TBGSVNintegrationTable::OLD_REV) . '&amp;r2=' . $results->get(TBGSVNintegrationTable::NEW_REV) . '" target="_blank""><b>' . TBGContext::getI18n()->__('Diff') . '</b></a></td>';
								echo '<td class="issuedetailscontentscenter" style="border-bottom: 0px; padding-right: 10px;"><a href="' . $viewvc_path . $results->get(TBGSVNintegrationTable::FILE_NAME) . '?revision=' . $results->get(TBGSVNintegrationTable::NEW_REV) . '&amp;view=markup" target="_blank""><b>' . TBGContext::getI18n()->__('View') . '</b></a></td>';
							}
							echo '</tr>';
						}
					}
					else
					{
						echo '<tr><td style="border-bottom: 0px; padding-left: 5px; color: #AAA;">' . TBGContext::getI18n()->__('There are no SVN checkins for this issue') . '</td></tr>';
					}
					echo '</table>';
				
				?>
				</div>
			</div>
			<?php
		}
		
		public function section_viewProject_viewCode($theProject)
		{
			$vvcpath_setting = 'viewvc_path_' . $theProject->getID();
			$viewvc_path = TBGContext::getModule('svn_integration')->getSetting($vvcpath_setting);
			if ($viewvc_path != '')
			{
				echo '<tr>';
				echo '<td class="imgtd" style="padding: 2px;">' . image_tag('svn_integration/icon_view_code.png') . '</td>';
				echo '<td style="padding: 2px;">';
				echo '<a href="' . $viewvc_path . '" target="_blank"><b>'. TBGContext::getI18n()->__('View code') . '</b></a>';
				echo '</td>';
				echo '</tr>';
			}
		}
		
		public function invokeSection($page, $identifier, $vars = array())
		{
			$section = $page . '_' . $identifier;
			switch ($section)
			{
				case 'viewissue_right_middle':
					$theIssue = $vars;
					break;
				case 'help_svn_integration_howto':
					break;
				case 'help_svn_integration_main':
					break;
			}
		}
		
		public function getAvailableCommandLineCommands()
		{
			$retarr = array();
			$retarr[] = array('command' => 'svnupdate', 'params' => 'issue_no filename old_rev new_rev', 'description' => "Updates an issue with information about a sourcecode change", 'help' => 'This command can be run by a post-commit svn-hook to update an issue with information about a change in a sourcefile.', 'function' => 'svnUpdate');
			
			return $retarr;
		}
		
		public function svnUpdate($argv)
		{
			if (count($argv) == 5)
			{
				$issue_uniqueid = TBGIssue::getIssueIDfromLink($argv[2]); 
				
				if ($issue_uniqueid != 0)
				{
					$crit = new B2DBCriteria();
					$crit->addInsert(TBGSVNintegrationTable::ISSUE_NO, $issue_uniqueid); 
					$crit->addInsert(TBGSVNintegrationTable::FILE_NAME, $argv[3]); 
					$crit->addInsert(TBGSVNintegrationTable::NEW_REV, $argv[5]);
					$crit->addInsert(TBGSVNintegrationTable::DATE, $_SERVER["REQUEST_TIME"]);
					$crit->addInsert(TBGSVNintegrationTable::SCOPE, TBGContext::getScope());
					B2DB::getTable('TBGSVNintegrationTable')->doInsert($crit);
					return true;
				}
				else
				{
					echo 'I can\'t update this issue - you have to provide more information.' . "\n";
					echo 'Type ' . $argv[0] . formatText(' explain', 'green', 'bold') . formatText(' svn_integration:svnupdate', 'magenta') . " for more information.\n";
					return false;
				}
			}
			else
			{
				echo 'I can\'t update this issue - you have to provide more information.' . "\n";
				echo 'Type ' . $argv[0] . formatText(' explain', 'green', 'bold') . formatText(' svn_integration:svnupdate', 'magenta') . " for more information.\n";
				return false;
			}
		}
		
	}

?>
