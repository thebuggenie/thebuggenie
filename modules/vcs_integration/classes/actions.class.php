<?php
	/**
	 * Module actions, vcs_integration
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 3.2
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */

	/**
	 * Module actions, vcs_integration
	 *
	 * @package thebuggenie
	 * @subpackage vcs_integration
	 */
	class vcs_integrationActions extends TBGAction
	{
		public function runProjectCommits(TBGRequest $request)
		{
			$this->selected_project = TBGProject::getByKey($request->getParameter('project_key'));
			TBGContext::setCurrentProject($this->selected_project);
			
			if (TBGContext::getModule('vcs_integration')->getSetting('vcs_mode_' . TBGContext::getCurrentProject()->getID()) == TBGVCSIntegration::MODE_DISABLED): return $this->return404(TBGContext::getI18n()->__('VCS Integration has been disabled for this project'));; endif;
			
			$offset = $request->getParameter('offset', 0);
			
			$this->commits = TBGVCSIntegrationCommit::getByProject($this->selected_project->getID(), 40, $offset);
			
			if ($offset)
			{
				return $this->renderJSON(array('content' => $this->getTemplateHTML('vcs_integration/projectcommits', array('commits' => $this->commits, 'selected_project' => $this->selected_project)), 'offset' => $offset + 40));
			}
		}
		
		public function runAddCommit(TBGRequest $request)
		{
			TBGContext::getResponse()->setContentType('text/plain');
			TBGContext::getResponse()->renderHeaders();
			
			/* Prepare variables */
			$passkey = TBGContext::getRequest()->getParameter('passkey');
			$project_key = urldecode(TBGContext::getRequest()->getParameter('project_key'));
			$author = trim(html_entity_decode(urldecode(TBGContext::getRequest()->getParameter('author')), ENT_QUOTES), '"');
			$new_rev = TBGContext::getRequest()->getParameter('rev');
			$commit_msg = trim(html_entity_decode(urldecode(TBGContext::getRequest()->getParameter('commit_msg')), ENT_QUOTES), '"');
			$changed = trim(html_entity_decode(urldecode(TBGContext::getRequest()->getParameter('changed')), ENT_QUOTES), '"');
			
			$project = TBGProject::getByKey($project_key);
			
			if (!$project)
			{
				echo 'Error: The project with the key '.$project_key.' does not exist';
				exit;
			}
			
			if (TBGSettings::get('access_method_'.$project->getID(), 'vcs_integration') == TBGVCSIntegration::ACCESS_DIRECT)
			{
				echo 'Error: This project uses the CLI access method, and so access via HTTP has been disabled';
				exit;
			}
			
			if (TBGSettings::get('access_passkey_'.$project->getID(), 'vcs_integration') != $passkey)
			{
				echo 'Error: The passkey specified does not match the passkey specified for this project';
				exit;
			}
			
			// Obtain previous revision
			if (!TBGContext::getRequest()->hasParameter('oldrev'))
			{
				$old_rev = $new_rev - 1;
			}
			else
			{
				$old_rev = TBGContext::getRequest()->getParameter('oldrev'); // for git, etc. which use hashes
			}
			
			// Obtain date timestamp
			if (!TBGContext::getRequest()->hasParameter('date'))
			{
				$date = null;
			}
			else
			{
				$date = TBGContext::getRequest()->getParameter('date'); // posix timestamp of commit
			}
			
			// Validate fields
			if (empty($author) || empty($new_rev) || empty($commit_msg) || empty($changed))
			{
				echo 'Error: One of the required fields were not specified. The required fields are the author, revision number (or hash), commit log and a list of changed files';
var_dump($_GET);
				exit;
			}
			
			if ((!is_numeric($new_rev) && is_numeric($old_rev)) || (is_numeric($new_rev) && !is_numeric($old_rev)))
			{
				echo 'Error: If the old revision is specified, it must be the same format as the new revision (number or hash)';
				exit;
			}
			
			// Add commit
			echo TBGVCSIntegration::processCommit($project, $commit_msg, $old_rev, $new_rev, $date, $changed, $author);
			exit;
		}
		
		public function runAddCommitGithub(TBGRequest $request)
		{
			TBGContext::getResponse()->setContentType('text/plain');
			TBGContext::getResponse()->renderHeaders();
				
			$passkey = TBGContext::getRequest()->getParameter('passkey');
			$project_key = urldecode(TBGContext::getRequest()->getParameter('project_key'));
			$project = TBGProject::getByKey($project_key);
			
			// Validate access
			if (!$project)
			{
				echo 'Error: The project with the key '.$project_key.' does not exist';
				exit;
			}
			
			if (TBGSettings::get('access_method_'.$project->getID(), 'vcs_integration') == TBGVCSIntegration::ACCESS_DIRECT)
			{
				echo 'Error: This project uses the CLI access method, and so access via HTTP has been disabled';
				exit;
			}
			
			if (TBGSettings::get('access_passkey_'.$project->getID(), 'vcs_integration') != $passkey)
			{
				echo 'Error: The passkey specified does not match the passkey specified for this project';
				exit;
			}
			
			// Validate data
			$data = html_entity_decode(TBGContext::getRequest()->getParameter('payload'));
			if (empty($data) || $data == null)
			{
				die('Error: No payload was provided');
			}

			$entries = json_decode($data);
			if ($entries == null)
			{
				die('Error: The payload could not be decoded');
			}
	
			$previous = $entries->before;
		
			// Parse each commit individually
			foreach ($entries->commits as $commit)
			{
				$email = $commit->author->email;
				$author = $commit->author->name;
				$new_rev = $commit->id;
				$old_rev = $previous;
				$commit_msg = $commit->message;
				$time = strtotime($commit->timestamp);
				
				// Build arrays of affected files
				if (property_exists($commit, 'modified'))
				{
					$modified = $commit->modified;
				}
				else
				{
					$modified = array();
				}
				
				if (property_exists($commit, 'removed'))
				{
					$removed = $commit->removed;
				}
				else
				{
					$removed = array();
				}
				
				if (property_exists($commit, 'added'))
				{
					$added = $commit->added;
				}
				else
				{
					$added = array();
				}
				
				// Build a string from these arrays
				$entries = array($modified, $added, $removed);
				$changed = '';
				
				foreach ($entries[0] as $file)
				{
					$changed .= 'M'.$file."\n";
				}
					
				foreach ($entries[1] as $file)
				{
					$changed .= 'A'.$file."\n";
				}
					
				foreach ($entries[2] as $file)
				{
					$changed .= 'D'.$file."\n";
				}
				
				// Add commit
				echo TBGContext::getModule('vcs_integration')->addNewCommit($project, $commit_msg, $old_rev, $new_rev, $time, $changed, $author);
				$previous = $commit->id;
			}
			exit();
		}
		
		public function runAddCommitGitorious(TBGRequest $request)
		{
			TBGContext::getResponse()->setContentType('text/plain');
			TBGContext::getResponse()->renderHeaders();
			
			$passkey = TBGContext::getRequest()->getParameter('passkey');
			$project_key = urldecode(TBGContext::getRequest()->getParameter('project_key'));
			$project = TBGProject::getByKey($project_key);
			
			// Validate access
			if (!$project)
			{
				echo 'Error: The project with the key '.$project_key.' does not exist';
				exit;
			}
			
			if (TBGSettings::get('access_method_'.$project->getID(), 'vcs_integration') == TBGVCSIntegration::ACCESS_DIRECT)
			{
				echo 'Error: This project uses the CLI access method, and so access via HTTP has been disabled';
				exit;
			}
			
			if (TBGSettings::get('access_passkey_'.$project->getID(), 'vcs_integration') != $passkey)
			{
				echo 'Error: The passkey specified does not match the passkey specified for this project';
				exit;
			}
			
			// Validate data
			$data = html_entity_decode(TBGContext::getRequest()->getParameter('payload', null, false));
			if (empty($data) || $data == null)
			{
				die('Error: No payload was provided');
			}

			$entries = json_decode($data);
			if ($entries == null)
			{
				die('Error: The payload could not be decoded');
			}

			$entries = json_decode($data);

			$previous = $entries->before;			
			// Parse each commit individually
			foreach (array_reverse($entries->commits) as $commit)
			{
				$email = $commit->author->email;
				$author = $commit->author->name;
				$new_rev = $commit->id;
				$old_rev = $previous;
				$commit_msg = $commit->message;
				$time = strtotime($commit->timestamp);
				
				// Add commit
				echo TBGContext::getModule('vcs_integration')->addNewCommit($project, $commit_msg, $old_rev, $previous, $time, "", $author);
				$previous = $new_rev;
				exit;
			}
		}
	}