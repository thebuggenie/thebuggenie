<?php

	/**
	 * CLI command class, main -> fix_times_spent
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * CLI command class, main -> fix_times_spent
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	class CliMainFixTimesSpent extends TBGCliCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'fix_times_spent';
			$this->_description = "Fixes times spent on upgrade from 3.2 -> 3.3";
		}

		public function do_execute()
		{
			if (TBGContext::isInstallmode())
			{
				$this->cliEcho("The Bug Genie is not installed\n", 'red');
			}
			else
			{
				$this->cliEcho("Finding times to fix\n", 'white', 'bold');
				$issuetimes = TBGIssueSpentTimesTable::getTable()->getAllSpentTimesForFixing();
				$error_issues = array();
				foreach ($issuetimes as $issue_id => $times)
				{
					if (count($times) > 1)
					{
						$this->cliEcho("Fixing times spent for issue ID {$issue_id}, ".count($times)." entries\n");
						$prev_times = array('hours' => 0, 'days' => 0, 'weeks' => 0, 'months' => 0, 'points' => 0);
						foreach ($times as $k => $row)
						{
							if ($row[TBGIssueSpentTimesTable::SPENT_DAYS] < $prev_times['days'] ||
								$row[TBGIssueSpentTimesTable::SPENT_HOURS] < $prev_times['hours'] ||
								$row[TBGIssueSpentTimesTable::SPENT_WEEKS] < $prev_times['weeks'] ||
								$row[TBGIssueSpentTimesTable::SPENT_MONTHS] < $prev_times['months'] ||
								$row[TBGIssueSpentTimesTable::SPENT_POINTS] < $prev_times['points'])
							{
								$error_issues[] = $issue_id;
							}
							else
							{
								TBGIssueSpentTimesTable::getTable()->fixRow($row, $prev_times);
								$prev_times['points'] += $row[TBGIssueSpentTimesTable::SPENT_POINTS];
								$prev_times['hours'] += $row[TBGIssueSpentTimesTable::SPENT_HOURS];
								$prev_times['days'] += $row[TBGIssueSpentTimesTable::SPENT_DAYS];
								$prev_times['weeks'] += $row[TBGIssueSpentTimesTable::SPENT_WEEKS];
								$prev_times['months'] += $row[TBGIssueSpentTimesTable::SPENT_MONTHS];
							}
						}
					}

				}
				foreach (TBGIssueSpentTimesTable::getTable()->getAllSpentTimesForFixing() as $issue_id => $times)
				{
					foreach ($times as $row)
					{
						TBGIssueSpentTimesTable::getTable()->fixHours($row);
					}
					TBGIssuesTable::getTable()->fixHours($issue_id);
				}
				if (count($error_issues) > 0)
				{
					$this->cliEcho("\n");
					$this->cliEcho("All spent times have been attempted fixed, but there were some issues that could not be fixed automatically!\n");
					$this->cliEcho("This happens if there has been adjustments in time spent, lowering the value for spent points, hours, days, weeks or months.\n\n");
					$this->cliEcho("You should fix the issues manually (issue ids corresponding to issue_ids in the timesspent table): ");
					$this->cliEcho(join(', ', $error_issues)."\n\n");
					$this->cliEcho("Spent times fixed!\n\n", 'green');
				}
				else
				{
					$this->cliEcho("All spent times fixed successfully!\n\n", 'green');
				}
				$this->cliEcho("IMPORTANT: Don't run this task again!\n", 'white', 'bold');
			}
		}

	}
