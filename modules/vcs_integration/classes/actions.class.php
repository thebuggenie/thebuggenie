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
			$project_id = urldecode(TBGContext::getRequest()->getParameter('project_id'));
			$author = trim(html_entity_decode(urldecode(TBGContext::getRequest()->getParameter('author')), ENT_QUOTES), '"');
			$new_rev = TBGContext::getRequest()->getParameter('rev');
			$commit_msg = trim(html_entity_decode(urldecode(TBGContext::getRequest()->getParameter('commit_msg')), ENT_QUOTES), '"');
			$changed = trim(html_entity_decode(urldecode(TBGContext::getRequest()->getParameter('changed')), ENT_QUOTES), '"');
			
			if (TBGContext::getRequest()->hasParameter('branch'))
			{
				$branch = trim(html_entity_decode(urldecode(TBGContext::getRequest()->getParameter('branch')), ENT_QUOTES), '"');
			}
			else
			{
				$branch = null;
			}
			
			$project = TBGContext::factory()->TBGProject($project_id);
			
			if (!$project)
			{
				echo 'Error: The project with the ID '.$project_id.' does not exist';
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
				exit;
			}
			
			if ((!is_numeric($new_rev) && is_numeric($old_rev)) || (is_numeric($new_rev) && !is_numeric($old_rev)))
			{
				echo 'Error: If the old revision is specified, it must be the same format as the new revision (number or hash)';
				exit;
			}
			
			// Add commit
			echo TBGVCSIntegration::processCommit($project, $commit_msg, $old_rev, $new_rev, $date, $changed, $author, $branch);
			exit;
		}
		
		public function runAddCommitGithub(TBGRequest $request)
		{
			TBGContext::getResponse()->setContentType('text/plain');
			TBGContext::getResponse()->renderHeaders();
				
			$passkey = TBGContext::getRequest()->getParameter('passkey');
			$project_id = urldecode(TBGContext::getRequest()->getParameter('project_id'));
			$project = TBGContext::factory()->TBGProject($project_id);
			
			// Validate access
			if (!$project)
			{
				echo 'Error: The project with the ID '.$project_id.' does not exist';
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
				
			// Branch is stored in the ref
			$rev = $entries->ref;
			$parts = explode('/', $ref);
			
			if (count($parts) == 3)
			{
				$branch = $parts[3];
			}
			else
			{
				$branch = null;
			}
		
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
				echo TBGVCSIntegration::processCommit($project, $commit_msg, $old_rev, $new_rev, $time, $changed, $author, $branch);
				$previous = $commit->id;
			}
			exit();
		}
		
		public function runAddCommitGitorious(TBGRequest $request)
		{
			TBGContext::getResponse()->setContentType('text/plain');
			TBGContext::getResponse()->renderHeaders();
			
			$passkey = TBGContext::getRequest()->getParameter('passkey');
			$project_id = urldecode(TBGContext::getRequest()->getParameter('project_id'));
			$project = TBGContext::factory()->TBGProject($project_id);
			
			// Validate access
			if (!$project)
			{
				echo 'Error: The project with the ID '.$project_id.' does not exist';
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
			
			// Branch is stored in the ref
			$rev = $entries->ref;
			$parts = explode('/', $ref);
			
			if (count($parts) == 3)
			{
				$branch = $parts[3];
			}
			else
			{
				$branch = null;
			}
					
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
				echo TBGVCSIntegration::processCommit($project, $commit_msg, $old_rev, $previous, $time, "", $author, $branch);
				$previous = $new_rev;
				exit;
			}
		}
		
		public function runConfigureProjectSettings(TBGRequest $request)
		{
			$this->forward403unless($request->isMethod(TBGRequest::POST));
									
			if ($this->access_level != TBGSettings::ACCESS_FULL)
			{
				$project_id = $request->getParameter('project_id');
				
				$fields = array('vcs_mode', 'match_keywords', 'access_method', 'access_passkey', 'commit_url', 'log_url',
								'blob_url', 'diff_url', 'browser_url');
				
				foreach ($fields as $field)
				{
					TBGContext::getModule('vcs_integration')->saveSetting($field.'_'.$project_id, $request->getParameter($field));
				}
				
				switch ($request->getParameter('browser_type'))
				{
					case 'viewvc':
						$base_url = $request->getParameter('browser_url');
						$link_rev = '&amp;view=rev&amp;revision=%revno%';
						$link_file = '&amp;view=log';
						$link_diff = '&amp;r1=%revno%&amp;r2=%oldrev%';
						$link_view = '&amp;revision=%revno%&amp;view=markup';
						break;
					case 'viewvc_repo':
						$base_url = $request->getParameter('browser_url');
						$link_rev = '/?view=rev&amp;revision=%revno%';
						$link_file = '/%file%?view=log';
						$link_diff = '/%file%?r1=%revno%&amp;r2=%oldrev%';
						$link_view = '/%file%?revision=%revno%&amp;view=markup';
						break;
					case 'websvn':
						$base_url = $request->getParameter('browser_url');
						$link_rev = '/revision.php?repname=' . $request->getParameter('repository') . '&amp;isdir=1&amp;rev=%revno%';
						$link_file = '/log.php?repname=' . $request->getParameter('repository') . '&amp;path=/$%file%';
						$link_diff = '/comp.php?repname=' . $request->getParameter('repository') . '&amp;compare[]=/%file%@%revno%&amp;compare[]=/%file%@%oldrev%';
						$link_view = '/filedetails.php?repname=' . $request->getParameter('repository') . '&path=/%file%&amp;rev=%revno%';
						break;
					case 'websvn_mv':
						$base_url = $request->getParameter('browser_url');
						$link_rev = '/' . '?repname=' . $request->getParameter('repository') . '&amp;op=log&isdir=1&amp;rev=%revno%';
						$link_file = '/%file%?repname=' . $request->getParameter('repository');
						$link_diff = '/%file%?repname=' . $request->getParameter('repository') . '&amp;compare[]=/%file%@%revno%&amp;compare[]=/%file%@%oldrev%';
						$link_view = '/%file%?repname=' . $request->getParameter('repository') . '&amp;rev=%revno%';
						break;
					case 'loggerhead':
						$base_url = $request->getParameter('browser_url');
						$link_rev = '/revision/%revno%';
						$link_file = '/changes';
						$link_diff = '/revision/%revno%?compare_revid=%oldrev%';
						$link_view = '/annotate/head:/%file%';
						break;
					case 'gitweb':
						$base_url = $request->getParameter('browser_url');
						$link_rev = ';a=commitdiff;h=%revno%';
						$link_file = ';a=history;f=%file%;hb=HEAD';
						$link_diff = ';a=blobdiff;f=%file%;hb=%revno%;hpb=%oldrev%';
						$link_view = ';a=blob;f=%file%;hb=%revno%';
						break;
					case 'cgit':
						$base_url = $request->getParameter('browser_url');
						$link_rev = '/commit/?id=%revno%';
						$link_file = '/log';
						$link_diff = '/diff/%file%?id=%revno%?id2=%oldrev%';
						$link_view = '/tree/%file%?id=%revno%';
						break;
					case 'hgweb':
						$base_url = $request->getParameter('browser_url');
						$link_rev = '/rev/%revno%';
						$link_file = '/log/tip/%file%';
						$link_diff = '/diff/%revno%/%file%';
						$link_view = '/file/%revno%/%file%';
						break;
					case 'github':
						$base_url = $request->getParameter('browser_url');
						$link_rev = '/commit/%revno%';
						$link_file = '/commits/%branch%/%file%';
						$link_diff = '/commit/%revno%';
						$link_view = '/blob/%revno%/%file%';
						break;
					case 'gitorious':
						$base_url = $request->getParameter('browser_url');
						$link_rev = '/commit/%revno%';
						$link_file = '/blobs/history/%branch%/%file%';
						$link_diff = '/commit/%revno%';
						$link_view = '/blobs/%revno%/%file%';
						break;
				}

				if ($request->getParameter('browser_type') != 'other')
				{
					TBGContext::getModule('vcs_integration')->saveSetting('browser_url_'.$project_id, $base_url);
					TBGContext::getModule('vcs_integration')->saveSetting('log_url_'.$project_id, $link_file);
					TBGContext::getModule('vcs_integration')->saveSetting('blob_url_'.$project_id, $link_diff);
					TBGContext::getModule('vcs_integration')->saveSetting('diff_url_'.$project_id, $link_view);
					TBGContext::getModule('vcs_integration')->saveSetting('commit_url_'.$project_id, $link_rev);
				}
				
				return $this->renderJSON(array('failed' => false, 'message' => TBGContext::getI18n()->__('Settings saved')));
			}
			else
			{
				$this->forward403();
			}
		}
	}