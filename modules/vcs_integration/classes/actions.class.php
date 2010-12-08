<?php
	/**
	 * Module actions, vcs_integration
	 *
	 * @author Philip Kent <kentphilip@gmail.com>
	 * @version 2.0
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
		public function runAddCommit(TBGRequest $request)
		{
			/* Prepare variables */
			$passkey = TBGContext::getRequest()->getParameter('passkey');
			$project = urldecode(TBGContext::getRequest()->getParameter('project'));
			$author = trim(html_entity_decode(urldecode(TBGContext::getRequest()->getParameter('author')), ENT_QUOTES), '"');
			$new_rev = TBGContext::getRequest()->getParameter('rev');
			$commit_msg = trim(html_entity_decode(urldecode(TBGContext::getRequest()->getParameter('commit_msg')), ENT_QUOTES), '"');
			$changed = trim(html_entity_decode(urldecode(TBGContext::getRequest()->getParameter('changed')), ENT_QUOTES), '"');
			
			if (!TBGContext::getRequest()->hasParameter('oldrev'))
			{
				$old_rev = $new_rev - 1;
			}
			else
			{
				$old_rev = TBGContext::getRequest()->getParameter('oldrev'); // for git, etc. which use hashes
			}
			if (!TBGContext::getRequest()->hasParameter('date'))
			{
				$date = null;
			}
			else
			{
				$date = TBGContext::getRequest()->getParameter('date'); // posix timestamp of commit
			}
			
			if (!(TBGContext::getModule('vcs_integration')->isUsingHTTPMethod()))
			{
				echo 'Error: This access method has been disallowed';
				exit;
			}
			
			/* Validation of fields */
			if ($passkey != TBGContext::getModule('vcs_integration')->getSetting('vcs_passkey'))
			{
				echo 'Error: Invalid passkey';
				exit;
			}

			if (empty($author) || empty($new_rev) || empty($commit_msg) || empty($changed) || empty($project))
			{
				echo 'Error: A field was not specified';
				exit;
			}
			
			if (!is_numeric($project))
			{
				echo 'Error: Project ID not a number';
				exit;
			}
			
			if ((!is_numeric($new_rev) && is_numeric($old_rev)) || (is_numeric($new_rev) && !is_numeric($old_rev)))
			{
				echo 'Error: Old and new revision must both be either numbers or hashes';
				exit;
			}
			
			echo TBGContext::getModule('vcs_integration')->addNewCommit($project, $commit_msg, $old_rev, $new_rev, $date, $changed, $author);
			exit;
		}
		
		public function runAddGithubCommit(TBGRequest $request)
		{
			if (!(TBGContext::getModule('vcs_integration')->isUsingHTTPMethod()))
			{
				echo 'Error: This access method has been disallowed';
				exit;
			}
			
			if ($passkey != TBGContext::getModule('vcs_integration')->getSetting('vcs_passkey'))
			{
				echo 'Error: Invalid passkey';
				exit;
			}
			
			$project = TBGContext::getRequest()->getParameter('project');
			$data = TBGContext::getRequest()->getParameter('payload');
			if (empty($data) || $data == null)
			{
				die('Error: Invalid data');
			}
		
			if (!function_exists('json_decode'))
			{
				die('Error: Github support requires either PHP 5.2.0 or later, or the json PECL module version 1.2.0 or later for prior versions of PHP');
			}
		
			$entries = json_decode($data);
		
			$previous = $entries->before;
		
			// Parse each commit individually
			foreach ($entries->commits as $commit)
			{
				$email = $commit->author->_email;
				$author = $commit->author->name;
				$new_rev = $commit->id;
				$commit_msg = $commit->message;
				$time = strtotime($commit->timestamp);
				
				$f_issues = array_unique($f_issues[3]);

				$file_lines = preg_split('/[\n\r]+/', $changed);
				$files = array();
				
				echo TBGContext::getModule('vcs_integration')->addNewCommit($project, $commit_msg, $old_rev, $previous, $date, array($entries->commits->modfied, $entries->commits->added, $entries->commits->removed), $author);
				exit;
			}
		}
	}