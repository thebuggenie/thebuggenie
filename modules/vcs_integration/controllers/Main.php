<?php

    namespace thebuggenie\modules\vcs_integration\controllers;

    use thebuggenie\core\framework,
        thebuggenie\core\entities\Project,
        thebuggenie\modules\vcs_integration\Vcs_integration,
        thebuggenie\modules\vcs_integration\entities,
        thebuggenie\modules\vcs_integration\entities\Commit;

    /**
     * Module actions, vcs_integration
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage vcs_integration
     */

    /**
     * Module actions, vcs_integration
     *
     * @package thebuggenie
     * @subpackage vcs_integration
     */
    class Main extends framework\Action
    {

        public function getAuthenticationMethodForAction($action)
        {
            switch ($action) {
                case 'addCommit':
                case 'addCommitGithub':
                case 'addCommitBitbucket':
                case 'addCommitGitorious':
                    return framework\Action::AUTHENTICATION_METHOD_DUMMY;
                default:
                    return framework\Action::AUTHENTICATION_METHOD_CORE;
            }
        }

        public function runProjectCommits(framework\Request $request)
        {
            $this->selected_project = Project::getByKey($request['project_key']);
            framework\Context::setCurrentProject($this->selected_project);

            if (framework\Context::getModule('vcs_integration')->getSetting('vcs_mode_' . framework\Context::getCurrentProject()->getID()) == Vcs_integration::MODE_DISABLED) {
                return $this->return404(framework\Context::getI18n()->__('VCS Integration has been disabled for this project'));
            }

            $offset = $request->getParameter('offset', 0);

            $this->commits = Commit::getByProject($this->selected_project->getID(), 40, $offset);

            if ($offset)
            {
                return $this->renderJSON(array('content' => $this->getComponentHTML('vcs_integration/projectcommits', array('commits' => $this->commits, 'selected_project' => $this->selected_project)), 'offset' => $offset + 40));
            }
        }

        public function runAddCommit(framework\Request $request)
        {
            framework\Context::getResponse()->setContentType('text/plain');
            framework\Context::getResponse()->renderHeaders();

            /* Prepare variables */
            $passkey = framework\Context::getRequest()->getParameter('passkey');
            $project_id = framework\Context::getRequest()->getParameter('project_id');
            $author = trim(html_entity_decode(framework\Context::getRequest()->getParameter('author'), ENT_QUOTES), '"');
            $new_rev = framework\Context::getRequest()->getParameter('rev');
            $commit_msg = trim(html_entity_decode(framework\Context::getRequest()->getParameter('commit_msg'), ENT_QUOTES), '"');
            $changed = trim(html_entity_decode(framework\Context::getRequest()->getParameter('changed'), ENT_QUOTES), '"');

            if (framework\Context::getRequest()->hasParameter('branch'))
            {
                $branch = trim(html_entity_decode(framework\Context::getRequest()->getParameter('branch'), ENT_QUOTES), '"');
            }
            else
            {
                $branch = null;
            }

            $project = Project::getB2DBTable()->selectByID($project_id);

            if (!$project)
            {
                echo 'Error: The project with the ID ' . $project_id . ' does not exist';
                exit;
            }

            if (framework\Settings::get('access_method_' . $project->getID(), 'vcs_integration') == Vcs_integration::ACCESS_DIRECT)
            {
                echo 'Error: This project uses the CLI access method, and so access via HTTP has been disabled';
                exit;
            }

            if (framework\Settings::get('access_passkey_' . $project->getID(), 'vcs_integration') != $passkey)
            {
                echo 'Error: The passkey specified does not match the passkey specified for this project';
                exit;
            }

            // Obtain previous revision
            if (!framework\Context::getRequest()->hasParameter('oldrev') && !ctype_digit($new_rev))
            {
                echo 'Error: If only the new revision is specified, it must be a number so that old revision can be calculated from it (by substracting 1 from new revision number).';
                exit;
            }
            else if (!framework\Context::getRequest()->hasParameter('oldrev'))
            {
                $old_rev = $new_rev - 1;
            }
            else
            {
                $old_rev = framework\Context::getRequest()->getParameter('oldrev'); // for git, etc. which use hashes
            }

            // Obtain date timestamp
            if (!framework\Context::getRequest()->hasParameter('date'))
            {
                $date = null;
            }
            else
            {
                $date = framework\Context::getRequest()->getParameter('date'); // posix timestamp of commit
            }

            // Validate fields
            if (empty($author) || empty($new_rev) || empty($commit_msg) || empty($changed))
            {
                echo 'Error: One of the required fields were not specified. The required fields are the author, revision number (or hash), commit log and a list of changed files';
                exit;
            }

            // Add commit
            echo Vcs_integration::processCommit($project, $commit_msg, $old_rev, $new_rev, $date, $changed, $author, $branch);
            exit;
        }

        public function runAddCommitGithub(framework\Request $request)
        {
            framework\Context::getResponse()->setContentType('text/plain');
            framework\Context::getResponse()->renderHeaders();

            $passkey = framework\Context::getRequest()->getParameter('passkey');
            $project_id = framework\Context::getRequest()->getParameter('project_id');

            try
            {
                $project = Project::getB2DBTable()->selectByID($project_id);
            }
            catch (\Exception $e)
            {
                $project = false;
            }

            // Validate access
            if (!$project)
            {
                echo 'Error: The project with the ID ' . $project_id . ' does not exist';
                exit;
            }

            if (framework\Settings::get('access_method_' . $project->getID(), 'vcs_integration') == Vcs_integration::ACCESS_DIRECT)
            {
                echo 'Error: This project uses the CLI access method, and so access via HTTP has been disabled';
                exit;
            }

            if (framework\Settings::get('access_passkey_' . $project->getID(), 'vcs_integration') != $passkey)
            {
                echo 'Error: The passkey specified does not match the passkey specified for this project';
                exit;
            }

            // Validate data
            $data = html_entity_decode(framework\Context::getRequest()->getParameter('payload'));
            if (empty($data) || $data == null)
            {
                //Need to check if payload is in unwrapped form from GitLab (until support is added)
                //Obtain raw input from request
                $data = file_get_contents("php://input");
                if (empty($data) || $data == null)
                {
                    die('Error: No payload was provided');
                }
            }

            $entries = json_decode($data);
            if ($entries == null)
            {
                die('Error: The payload could not be decoded');
            }

            $previous = $entries->before;

            // Branch is stored in the ref
            $ref = $entries->ref;
            $parts = explode('/', $ref);
            if (count($parts) == 3)
            {
                $branch = $parts[2];
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
                    $changed .= 'M' . $file . "\n";
                }

                foreach ($entries[1] as $file)
                {
                    $changed .= 'A' . $file . "\n";
                }

                foreach ($entries[2] as $file)
                {
                    $changed .= 'D' . $file . "\n";
                }

                // Add commit
                echo Vcs_integration::processCommit($project, $commit_msg, $old_rev, $new_rev, $time, $changed, $author, $branch);
                $previous = $commit->id;
            }
            exit();
        }

        /**
         * Bitbucket gateway - adding commit
         * @see http://confluence.atlassian.com/display/BITBUCKET/Setting+Up+the+bitbucket+POST+Service
         * @author AlmogBaku <almog.baku@gmail.com>
         *
         * @param \thebuggenie\core\framework\Request $request
         */
        public function runAddCommitBitbucket(framework\Request $request)
        {
            framework\Context::getResponse()->setContentType('text/plain');
            framework\Context::getResponse()->renderHeaders();

            $passkey = framework\Context::getRequest()->getParameter('passkey');
            $project_id = framework\Context::getRequest()->getParameter('project_id');

            try
            {
                $project = Project::getB2DBTable()->selectByID($project_id);
            }
            catch (\Exception $e)
            {
                $project = false;
            }

            // Validate access
            if (!$project)
            {
                echo 'Error: The project with the ID ' . $project_id . ' does not exist';
                exit;
            }

            if (framework\Settings::get('access_method_' . $project->getID(), 'vcs_integration') == Vcs_integration::ACCESS_DIRECT)
            {
                echo 'Error: This project uses the CLI access method, and so access via HTTP has been disabled';
                exit;
            }

            if (framework\Settings::get('access_passkey_' . $project->getID(), 'vcs_integration') != $passkey)
            {
                echo 'Error: The passkey specified does not match the passkey specified for this project';
                exit;
            }

            // Validate data
            $data = html_entity_decode(framework\Context::getRequest()->getParameter('payload'));
            if (empty($data) || $data == null)
            {
                die('Error: No payload was provided');
            }

            $entries = json_decode($data);
            if ($entries == null)
            {
                die('Error: The payload could not be decoded');
            }

            foreach ($entries->commits as $commit)
            {
                $changed = array();
                foreach ($commit->files as $file)
                {
                    switch ($file->type)
                    {
                        case "modified":
                            $changed[] = "M" . $file->file;
                            break;
                        case "removed":
                            $changed[] = "D" . $file->file;
                            break;
                        case "added":
                            $changed[] = "A" . $file->file;
                            break;
                    }
                }

                echo Vcs_integration::processCommit($project, $commit->message, $commit->parents[0], $commit->node, strtotime($commit->timestamp), implode("\n", $changed), $commit->author, $commit->branch);
                $previous = $commit->node;
            }
            exit();
        }

        public function runAddCommitGitorious(framework\Request $request)
        {
            framework\Context::getResponse()->setContentType('text/plain');
            framework\Context::getResponse()->renderHeaders();

            $passkey = framework\Context::getRequest()->getParameter('passkey');
            $project_id = framework\Context::getRequest()->getParameter('project_id');
            $project = Project::getB2DBTable()->selectByID($project_id);

            // Validate access
            if (!$project)
            {
                echo 'Error: The project with the ID ' . $project_id . ' does not exist';
                exit;
            }

            if (framework\Settings::get('access_method_' . $project->getID(), 'vcs_integration') == Vcs_integration::ACCESS_DIRECT)
            {
                echo 'Error: This project uses the CLI access method, and so access via HTTP has been disabled';
                exit;
            }

            if (framework\Settings::get('access_passkey_' . $project->getID(), 'vcs_integration') != $passkey)
            {
                echo 'Error: The passkey specified does not match the passkey specified for this project';
                exit;
            }

            // Validate data
            $data = html_entity_decode(framework\Context::getRequest()->getParameter('payload', null, false));
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
            $ref = $entries->ref;
            $parts = explode('/', $ref);

            if (count($parts) == 3)
            {
                $branch = $parts[2];
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
                echo Vcs_integration::processCommit($project, $commit_msg, $old_rev, $previous, $time, "", $author, $branch);
                $previous = $new_rev;
                exit;
            }
        }

        public function runConfigureProjectSettings(framework\Request $request)
        {
            $this->forward403unless($request->isPost());

            if ($this->access_level != framework\Settings::ACCESS_FULL)
            {
                $project_id = $request['project_id'];

                $fields = array('vcs_mode', 'match_keywords', 'access_method', 'access_passkey', 'commit_url', 'log_url',
                    'blob_url', 'diff_url', 'browser_url', 'vcs_workflow', 'browser_type');

                foreach ($fields as $field)
                {
                    framework\Context::getModule('vcs_integration')->saveSetting($field . '_' . $project_id, $request->getParameter($field));
                }

                switch ($request['browser_type'])
                {
                    case 'viewvc':
                        $base_url = $request['browser_url'];
                        $link_rev = '&amp;view=rev&amp;revision=%revno';
                        $link_file = '&amp;view=log';
                        $link_diff = '&amp;r1=%revno&amp;r2=%oldrev';
                        $link_view = '&amp;revision=%revno&amp;view=markup';
                        break;
                    case 'viewvc_repo':
                        $base_url = $request['browser_url'];
                        $link_rev = '/?view=rev&amp;revision=%revno';
                        $link_file = '/%file?view=log';
                        $link_diff = '/%file?r1=%revno&amp;r2=%oldrev';
                        $link_view = '/%file?revision=%revno&amp;view=markup';
                        break;
                    case 'websvn':
                        $base_url = $request['browser_url'];
                        $link_rev = '/revision.php?repname=' . $request['repository'] . '&amp;isdir=1&amp;rev=%revno';
                        $link_file = '/log.php?repname=' . $request['repository'] . '&amp;path=/%file';
                        $link_diff = '/comp.php?repname=' . $request['repository'] . '&amp;compare[]=/%file@%revno&amp;compare[]=/%file@%oldrev';
                        $link_view = '/filedetails.php?repname=' . $request['repository'] . '&path=/%file&amp;rev=%revno';
                        break;
                    case 'websvn_mv':
                        $base_url = $request['browser_url'];
                        $link_rev = '/' . '?repname=' . $request['repository'] . '&amp;op=log&isdir=1&amp;rev=%revno';
                        $link_file = '/%file?repname=' . $request['repository'];
                        $link_diff = '/%file?repname=' . $request['repository'] . '&amp;compare[]=/%file@%revno&amp;compare[]=/%file@%oldrev';
                        $link_view = '/%file?repname=' . $request['repository'] . '&amp;rev=%revno';
                        break;
                    case 'loggerhead':
                        $base_url = $request['browser_url'];
                        $link_rev = '/revision/%revno';
                        $link_file = '/changes';
                        $link_diff = '/revision/%revno?compare_revid=%oldrev';
                        $link_view = '/annotate/head:/%file';
                        break;
                    case 'gitweb':
                        $base_url = $request['browser_url'];
                        $link_rev = ';a=commitdiff;h=%revno';
                        $link_file = ';a=history;f=%file;hb=HEAD';
                        $link_diff = ';a=blobdiff;f=%file;hb=%revno;hpb=%oldrev';
                        $link_view = ';a=blob;f=%file;hb=%revno';
                        break;
                    case 'cgit':
                        $base_url = $request['browser_url'];
                        $link_rev = '/commit/?id=%revno';
                        $link_file = '/log';
                        $link_diff = '/diff/%file?id=%revno?id2=%oldrev';
                        $link_view = '/tree/%file?id=%revno';
                        break;
                    case 'hgweb':
                        $base_url = $request['browser_url'];
                        $link_rev = '/rev/%revno';
                        $link_file = '/log/tip/%file';
                        $link_diff = '/diff/%revno/%file';
                        $link_view = '/file/%revno/%file';
                        break;
                    case 'github':
                        $base_url = $request['browser_url'];
                        $link_rev = '/commit/%revno';
                        $link_file = '/commits/%branch/%file';
                        $link_diff = '/commit/%revno';
                        $link_view = '/blob/%revno/%file';
                        break;
                    case 'gitlab':
                        $base_url = $request['browser_url'];
                        $link_rev = '/commit/%revno';
                        $link_file = '/commits/%branch/%file';
                        $link_diff = '/commit/%revno';
                        $link_view = '/blob/%revno/%file';
                        break;
                    case 'bitbucket':
                        $base_url = $request['browser_url'];
                        $link_rev = '/commits/%revno';
                        $link_file = '/history/%file';
                        $link_diff = '/commits/%revno#chg-%file';
                        $link_view = '/src/%revno/%file';
                        break;
                    case 'gitorious':
                        $base_url = $request['browser_url'];
                        $link_rev = '/commit/%revno';
                        $link_file = '/blobs/history/%branch/%file';
                        $link_diff = '/commit/%revno';
                        $link_view = '/blobs/%revno/%file';
                        break;
                    case 'rhodecode':
                        $base_url = $request['browser_url'];
                        $link_rev = '/changeset/%revno';
                        $link_file = '/changelog/%revno/%file';
                        $link_diff = '/diff/%file?diff2=%revno&amp;diff1=%oldrev&amp;fulldiff=1&amp;diff=diff';
                        $link_view = '/files/%revno/%file';
                        break;
                }

                if ($request['browser_type'] != 'other')
                {
                    framework\Context::getModule('vcs_integration')->saveSetting('browser_url_' . $project_id, $base_url);
                    framework\Context::getModule('vcs_integration')->saveSetting('log_url_' . $project_id, $link_file);
                    framework\Context::getModule('vcs_integration')->saveSetting('blob_url_' . $project_id, $link_view);
                    framework\Context::getModule('vcs_integration')->saveSetting('diff_url_' . $project_id, $link_diff);
                    framework\Context::getModule('vcs_integration')->saveSetting('commit_url_' . $project_id, $link_rev);
                }

                return $this->renderJSON(array('failed' => false, 'message' => framework\Context::getI18n()->__('Settings saved')));
            }
            else
            {
                $this->forward403();
            }
        }

    }
