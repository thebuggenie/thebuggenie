<?php

namespace thebuggenie\core\modules\import\controllers;

use thebuggenie\core\framework,
    thebuggenie\core\entities,
    thebuggenie\core\entities\tables;

class Main extends framework\Action
{

    const CSV_TYPE_ISSUES = 'issues';
    const CSV_TYPE_CLIENTS = 'clients';
    const CSV_TYPE_PROJECTS = 'projects';
    const CSV_PROJECT_NAME = 'name';
    const CSV_PROJECT_PREFIX = 'prefix';
    const CSV_PROJECT_SCRUM = 'scrum';
    const CSV_PROJECT_OWNER = 'owner';
    const CSV_PROJECT_OWNER_TYPE = 'owner_type';
    const CSV_PROJECT_LEAD = 'lead';
    const CSV_PROJECT_LEAD_TYPE = 'lead_type';
    const CSV_PROJECT_QA = 'qa';
    const CSV_PROJECT_QA_TYPE = 'qa_type';
    const CSV_PROJECT_DESCR = 'descr';
    const CSV_PROJECT_DOC_URL = 'doc_url';
    const CSV_PROJECT_WIKI_URL = 'wiki_url';
    const CSV_PROJECT_FREELANCE = 'freelance';
    const CSV_PROJECT_EN_BUILDS = 'en_builds';
    const CSV_PROJECT_EN_COMPS = 'en_comps';
    const CSV_PROJECT_EN_EDITIONS = 'en_editions';
    const CSV_PROJECT_WORKFLOW_ID = 'workflow_id';
    const CSV_PROJECT_CLIENT = 'client';
    const CSV_PROJECT_SHOW_SUMMARY = 'show_summary';
    const CSV_PROJECT_SUMMARY_TYPE = 'summary_type';
    const CSV_PROJECT_ISSUETYPE_SCHEME = 'issuetype_scheme';
    const CSV_PROJECT_ALLOW_REPORTING = 'allow_reporting';
    const CSV_PROJECT_AUTOASSIGN = 'autoassign';
    const CSV_CLIENT_NAME = 'name';
    const CSV_CLIENT_EMAIL = 'email';
    const CSV_CLIENT_TELEPHONE = 'telephone';
    const CSV_CLIENT_FAX = 'fax';
    const CSV_CLIENT_WEBSITE = 'website';
    const CSV_ISSUE_TITLE = 'title';
    const CSV_ISSUE_PROJECT = 'project';
    const CSV_ISSUE_DESCR = 'descr';
    const CSV_ISSUE_REPRO = 'repro';
    const CSV_ISSUE_STATE = 'state';
    const CSV_ISSUE_STATUS = 'status';
    const CSV_ISSUE_POSTED_BY = 'posted_by';
    const CSV_ISSUE_OWNER = 'owner';
    const CSV_ISSUE_OWNER_TYPE = 'owner_type';
    const CSV_ISSUE_ASSIGNED = 'assigned';
    const CSV_ISSUE_ASSIGNED_TYPE = 'assigned_type';
    const CSV_ISSUE_RESOLUTION = 'resolution';
    const CSV_ISSUE_ISSUE_TYPE = 'issue_type';
    const CSV_ISSUE_PRIORITY = 'priority';
    const CSV_ISSUE_CATEGORY = 'category';
    const CSV_ISSUE_SEVERITY = 'severity';
    const CSV_ISSUE_REPRODUCIBILITY = 'reproducability';
    const CSV_ISSUE_VOTES = 'votes';
    const CSV_ISSUE_PERCENTAGE = 'percentage';
    const CSV_ISSUE_ISSUENO = 'issue_no';
    const CSV_ISSUE_BLOCKING = 'blocking';
    const CSV_ISSUE_MILESTONE = 'milestone';
    const CSV_ISSUE_POSTED = 'posted';
    const CSV_IDENTIFIER_TYPE_USER = 1;
    const CSV_IDENTIFIER_TYPE_TEAM = 2;

    public function getAuthenticationMethodForAction($action)
    {
        return (framework\Settings::isElevatedLoginRequired()) ? framework\Action::AUTHENTICATION_METHOD_ELEVATED : framework\Action::AUTHENTICATION_METHOD_CORE;
    }

    public function getAccessLevel($section, $module)
    {
        return (framework\Context::getUser()->canSaveConfiguration($section, $module)) ? framework\Settings::ACCESS_FULL : framework\Settings::ACCESS_READ;
    }

    /**
     * Pre-execute function
     *
     * @param framework\Request     $request
     * @param string        $action
     */
    public function preExecute(framework\Request $request, $action)
    {
        // forward 403 if you're not allowed here
        if ($request->isAjaxCall() == false) // for avoiding empty error when an user disables himself its own permissions
        {
            $this->forward403unless(framework\Context::getUser()->canAccessConfigurationPage());
        }

        $this->access_level = $this->getAccessLevel(framework\Settings::CONFIGURATION_SECTION_IMPORT, 'core');

        if (!$request->isAjaxCall())
        {
            $this->getResponse()->setPage('config');
            framework\Context::loadLibrary('ui');
            $this->getResponse()->addBreadcrumb(framework\Context::getI18n()->__('Configure %thebuggenie_name', array('%thebuggenie_name' => framework\Settings::getSiteHeaderName())), framework\Context::getRouting()->generate('configure'), $this->getResponse()->getPredefinedBreadcrumbLinks('configure'));
        }
    }

    /**
     * Configuration import page
     *
     * @param framework\Request $request
     */
    public function runIndex(framework\Request $request)
    {
        if ($request->isPost())
        {
            if ($request['import_sample_data'])
            {
                $transaction = \b2db\Core::startTransaction();

                $users = array();

                $user1 = new entities\User();
                $user1->setUsername('john');
                $user1->setPassword('john');
                $user1->setBuddyname('John');
                $user1->setRealname('John');
                $user1->setActivated();
                $user1->setEnabled();
                $user1->save();
                $users[] = $user1;

                $user2 = new entities\User();
                $user2->setUsername('jane');
                $user2->setPassword('jane');
                $user2->setBuddyname('Jane');
                $user2->setRealname('Jane');
                $user2->setActivated();
                $user2->setEnabled();
                $user2->save();
                $users[] = $user2;

                $user3 = new entities\User();
                $user3->setUsername('jackdaniels');
                $user3->setPassword('jackdaniels');
                $user3->setBuddyname('Jack');
                $user3->setRealname('Jack Daniels');
                $user3->setActivated();
                $user3->setEnabled();
                $user3->save();
                $users[] = $user3;

                $project1 = new entities\Project();
                $project1->setName('Sample project 1');
                $project1->setOwner($users[rand(0, 2)]);
                $project1->setLeader($users[rand(0, 2)]);
                $project1->setQaResponsible($users[rand(0, 2)]);
                $project1->setDescription('This is a sample project that is awesome. Try it out!');
                $project1->setHomepage('http://www.google.com');
                $project1->save();

                $project2 = new entities\Project();
                $project2->setName('Sample project 2');
                $project2->setOwner($users[rand(0, 2)]);
                $project2->setLeader($users[rand(0, 2)]);
                $project2->setQaResponsible($users[rand(0, 2)]);
                $project2->setDescription('This is the second sample project. Not as awesome as the first one, but still worth a try!');
                $project2->setHomepage('http://www.bing.com');
                $project2->save();

                foreach (array($project1, $project2) as $project)
                {
                    for ($cc = 1; $cc <= 5; $cc++)
                    {
                        $milestone = new entities\Milestone();
                        $milestone->setName("Milestone {$cc}");
                        $milestone->setProject($project);
                        $milestone->setType(entities\Milestone::TYPE_REGULAR);
                        if ((bool) rand(0, 1))
                        {
                            $milestone->setScheduledDate(NOW + (100000 * (20 * $cc)));
                        }
                        $milestone->save();
                    }
                }

                $p1_milestones = $project1->getMilestones();
                $p2_milestones = $project2->getMilestones();

                $issues = array();
                $priorities = entities\Priority::getAll();
                $categories = entities\Category::getAll();
                $severities = entities\Severity::getAll();
                $statuses = entities\Status::getAll();
                $reproducabilities = entities\Reproducability::getAll();
                $lorem_ipsum = \thebuggenie\modules\publish\entities\tables\Articles::getTable()->getArticleByName('LoremIpsum');
                $lorem_words = explode(' ', $lorem_ipsum->getContent());

                foreach (array('bugreport', 'featurerequest', 'enhancement', 'idea') as $issuetype)
                {
                    $issuetype = entities\Issuetype::getByKeyish($issuetype);
                    for ($cc = 1; $cc <= 10; $cc++)
                    {
                        $issue1 = new entities\Issue();
                        $issue1->setProject($project1);
                        $issue1->setPostedBy($users[rand(0, 2)]);
                        $issue1->setPosted(NOW - (86400 * rand(1, 30)));
                        $title_string = '';
                        $description_string = '';
                        $rand_length = rand(4, 15);
                        $ucnext = true;
                        for ($ll = 1; $ll <= $rand_length; $ll++)
                        {
                            $word = str_replace(array(',', '.', "\r", "\n"), array('', '', '', ''), $lorem_words[array_rand($lorem_words)]);
                            $word = ($ucnext || (rand(1, 40) == 19)) ? ucfirst($word) : mb_strtolower($word);
                            $title_string .= $word;
                            $ucnext = false;
                            if ($ll == $rand_length || rand(1, 15) == 5)
                            {
                                $title_string .= '.';
                                $ucnext = true;
                            }
                            $title_string .= ' ';
                        }
                        $rand_length = rand(40, 500);
                        $ucnext = true;
                        for ($ll = 1; $ll <= $rand_length; $ll++)
                        {
                            $word = str_replace(array(',', '.', "\r", "\n"), array('', '', '', ''), $lorem_words[array_rand($lorem_words)]);
                            $word = ($ucnext || (rand(1, 40) == 19)) ? ucfirst($word) : mb_strtolower($word);
                            $description_string .= $word;
                            $ucnext = false;
                            if ($ll == $rand_length || rand(1, 15) == 5)
                            {
                                $description_string .= '.';
                                $ucnext = true;
                                $description_string .= ($ll != $rand_length && rand(1, 15) == 8) ? "\n\n" : ' ';
                            }
                            else
                            {
                                $description_string .= ' ';
                            }
                        }
                        $issue1->setTitle(ucfirst($title_string));
                        $issue1->setDescription($description_string);
                        $issue1->setIssuetype($issuetype);
                        $issue1->setMilestone($p1_milestones[array_rand($p1_milestones)]);
                        $issue1->setPriority($priorities[array_rand($priorities)]);
                        $issue1->setCategory($categories[array_rand($categories)]);
                        $issue1->setSeverity($severities[array_rand($severities)]);
                        $issue1->setReproducability($reproducabilities[array_rand($reproducabilities)]);
                        $issue1->setPercentCompleted(rand(0, 100));
                        $issue1->save();
                        $issue1->setStatus($statuses[array_rand($statuses)]);
                        if (rand(0, 1))
                            $issue1->setAssignee($users[array_rand($users)]);
                        $issue1->save();
                        $issues[] = $issue1;

                        $issue2 = new entities\Issue();
                        $issue2->setProject($project2);
                        $issue2->setPostedBy($users[rand(0, 2)]);
                        $issue2->setPosted(NOW - (86400 * rand(1, 30)));
                        $title_string = '';
                        $description_string = '';
                        $rand_length = rand(4, 15);
                        $ucnext = true;
                        for ($ll = 1; $ll <= $rand_length; $ll++)
                        {
                            $word = str_replace(array(',', '.', "\r", "\n"), array('', '', '', ''), $lorem_words[array_rand($lorem_words)]);
                            $word = ($ucnext || (rand(1, 40) == 19)) ? ucfirst($word) : mb_strtolower($word);
                            $title_string .= $word;
                            $ucnext = false;
                            if ($ll == $rand_length || rand(1, 15) == 5)
                            {
                                $title_string .= '.';
                                $ucnext = true;
                            }
                            $title_string .= ' ';
                        }
                        $rand_length = rand(40, 500);
                        $ucnext = true;
                        for ($ll = 1; $ll <= $rand_length; $ll++)
                        {
                            $word = str_replace(array(',', '.', "\r", "\n"), array('', '', '', ''), $lorem_words[array_rand($lorem_words)]);
                            $word = ($ucnext || (rand(1, 40) == 19)) ? ucfirst($word) : mb_strtolower($word);
                            $description_string .= $word;
                            $ucnext = false;
                            if ($ll == $rand_length || rand(1, 15) == 5)
                            {
                                $description_string .= '.';
                                $ucnext = true;
                                $description_string .= ($ll != $rand_length && rand(1, 15) == 8) ? "\n\n" : ' ';
                            }
                            else
                            {
                                $description_string .= ' ';
                            }
                        }
                        $issue2->setTitle(ucfirst($title_string));
                        $issue2->setDescription($description_string);
                        $issue2->setIssuetype($issuetype);
                        $issue2->setMilestone($p2_milestones[array_rand($p2_milestones)]);
                        $issue2->setPriority($priorities[array_rand($priorities)]);
                        $issue2->setCategory($categories[array_rand($categories)]);
                        $issue2->setSeverity($severities[array_rand($severities)]);
                        $issue2->setReproducability($reproducabilities[array_rand($reproducabilities)]);
                        $issue2->setPercentCompleted(rand(0, 100));
                        if (rand(0, 1))
                            $issue1->setAssignee($users[array_rand($users)]);
                        $issue2->save();
                        $issue2->setStatus($statuses[array_rand($statuses)]);
                        $issue2->save();
                        $issues[] = $issue2;
                    }
                }

                $rand_issues_to_close = rand(8, 40);
                $resolutions = entities\Resolution::getAll();

                for ($cc = 1; $cc <= $rand_issues_to_close; $cc++)
                {
                    $issue = array_slice($issues, array_rand($issues), 1);
                    $issue = $issue[0];
                    $issue->setResolution($resolutions[array_rand($resolutions)]);
                    $issue->close();
                    $issue->save();
                }

                $this->imported_data = true;
                $roles = entities\Role::getAll();

                foreach (array($project1, $project2) as $project)
                {
                    foreach ($users as $user)
                    {
                        $project->addAssignee($user, $roles[array_rand($roles)]);
                    }
                }

                $transaction->commitAndEnd();
            }
        }

        $project1 = entities\Project::getByKey('sampleproject1');
        $project2 = entities\Project::getByKey('sampleproject2');
        $this->canimport = (!$project1 instanceof entities\Project && !$project2 instanceof entities\Project);
    }

    public function runImportCSV(framework\Request $request)
    {
        $content = $this->getComponentHTML('import/importcsv', array('type' => $request['type']));
        return $this->renderJSON(array('content' => $content));
    }

    public function runGetIDsForImportCSV(framework\Request $request)
    {
        $content = $this->getComponentHTML('import/import_ids');
        return $this->renderJSON(array('content' => $content));
    }

    public function runDoImportCSV(framework\Request $request)
    {
        try
        {
            if ($request['csv_data'] == '')
            {
                throw new \Exception($this->getI18n()->__('No data supplied to import'));
            }

            $csv = str_replace("\r\n", "\n", $request['csv_data']);
            $csv = html_entity_decode($csv);

            $headerrow = null;
            $data = array();
            $errors = array();

            // Parse CSV
            $handle = fopen("php://memory", 'r+');
            fputs($handle, $csv);
            rewind($handle);
            $i = 0;
            while (($row = fgetcsv($handle, 1000)) !== false)
            {
                if (!$headerrow)
                {
                    $headerrow = $row;
                }
                else
                {
                    if (count($headerrow) == count($row))
                    {
                        $data[] = array_combine($headerrow, $row);
                    }
                    else
                    {
                        $errors[] = $this->getI18n()->__('Row %row does not have the same number of elements as the header row', array('%row' => $i));
                    }
                }
                $i++;
            }
            fclose($handle);

            if (empty($data))
            {
                throw new \Exception($this->getI18n()->__('Insufficient data to import'));
            }

            // Verify required columns are present based on type
            $requiredcols = array(
                self::CSV_TYPE_CLIENTS => array(self::CSV_CLIENT_NAME),
                self::CSV_TYPE_PROJECTS => array(self::CSV_PROJECT_NAME),
                self::CSV_TYPE_ISSUES => array(self::CSV_ISSUE_TITLE, self::CSV_ISSUE_PROJECT, self::CSV_ISSUE_ISSUE_TYPE),
            );

            if (!isset($requiredcols[$request['type']]))
            {
                throw new \Exception('Sorry, this type is unimplemented');
            }

            foreach ($requiredcols[$request['type']] as $col)
            {
                if (!in_array($col, $headerrow))
                {
                    $errors[] = $this->getI18n()->__('Required column \'%col\' not found in header row', array('%col' => $col));
                }
            }

            // Check if rows are long enough and fields are not empty
            for ($i = 0; $i != count($data); $i++)
            {
                $activerow = $data[$i];

                // Check if fields are empty
                foreach ($activerow as $col => $val)
                {
                    if (strlen($val) == 0)
                    {
                        $errors[] = $this->getI18n()->__('Row %row column %col has no value', array('%col' => $col, '%row' => $i + 1));
                    }
                }
            }

            if (count($errors) == 0)
            {
                // Check if fields are valid
                switch ($request['type'])
                {
                    case self::CSV_TYPE_PROJECTS:
                        for ($i = 0; $i != count($data); $i++)
                        {
                            $activerow = $data[$i];

                            // Check if project exists
                            $key = str_replace(' ', '', $activerow[self::CSV_PROJECT_NAME]);
                            $key = mb_strtolower($key);

                            $tmp = entities\Project::getByKey($key);

                            if ($tmp !== null)
                            {
                                $errors[] = $this->getI18n()->__('Row %row: A project with this name already exists', array('%row' => $i + 1));
                            }

                            // First off are booleans
                            $boolitems = array(self::CSV_PROJECT_SCRUM, self::CSV_PROJECT_ALLOW_REPORTING, self::CSV_PROJECT_AUTOASSIGN, self::CSV_PROJECT_FREELANCE,
                                self::CSV_PROJECT_EN_BUILDS, self::CSV_PROJECT_EN_COMPS, self::CSV_PROJECT_EN_EDITIONS, self::CSV_PROJECT_SHOW_SUMMARY);

                            foreach ($boolitems as $boolitem)
                            {
                                if (array_key_exists($boolitem, $activerow) && isset($activerow[$boolitem]) && $activerow[$boolitem] != 1 && $activerow[$boolitem] != 0)
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be 1/0)', array('%col' => $boolitem, '%row' => $i + 1));
                                }
                            }

                            // Now identifiables
                            $identifiableitems = array(
                                array(self::CSV_PROJECT_QA, self::CSV_PROJECT_QA_TYPE),
                                array(self::CSV_PROJECT_LEAD, self::CSV_PROJECT_LEAD_TYPE),
                                array(self::CSV_PROJECT_OWNER, self::CSV_PROJECT_OWNER_TYPE)
                            );

                            foreach ($identifiableitems as $identifiableitem)
                            {

                                if ((!array_key_exists($identifiableitem[1], $activerow) && array_key_exists($identifiableitem[0], $activerow)) || (array_key_exists($identifiableitem[1], $activerow) && !array_key_exists($identifiableitem[0], $activerow)))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row: Both the type and item ID must be supplied for owner/lead/qa fields', array('%row' => $i + 1));
                                    continue;
                                }

                                if (array_key_exists($identifiableitem[1], $activerow) && isset($activerow[$identifiableitem[1]]) !== null && $activerow[$identifiableitem[1]] != self::CSV_IDENTIFIER_TYPE_USER && $activerow[$identifiableitem[1]] != self::CSV_IDENTIFIER_TYPE_TEAM)
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be 1 for a user or 2 for a team)', array('%col' => $identifiableitem[1], '%row' => $i + 1));
                                }

                                if (array_key_exists($identifiableitem[0], $activerow) && isset($activerow[$identifiableitem[0]]) && !is_numeric($activerow[$identifiableitem[0]]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => $identifiableitem[0], '%row' => $i + 1));
                                }
                                elseif (array_key_exists($identifiableitem[0], $activerow) && isset($activerow[$identifiableitem[0]]) && is_numeric($activerow[$identifiableitem[0]]))
                                {
                                    // check if they exist
                                    switch ($activerow[$identifiableitem[1]])
                                    {
                                        case self::CSV_IDENTIFIER_TYPE_USER:
                                            try
                                            {
                                                entities\User::getB2DBTable()->selectByID($activerow[$identifiableitem[0]]);
                                            }
                                            catch (\Exception $e)
                                            {
                                                $errors[] = $this->getI18n()->__('Row %row column %col: user does not exist', array('%col' => $identifiableitem[0], '%row' => $i + 1));
                                            }
                                            break;
                                        case self::CSV_IDENTIFIER_TYPE_TEAM:
                                            try
                                            {
                                                entities\Team::getB2DBTable()->selectById($activerow[$identifiableitem[0]]);
                                            }
                                            catch (\Exception $e)
                                            {
                                                $errors[] = $this->getI18n()->__('Row %row column %col: team does not exist', array('%col' => $identifiableitem[0], '%row' => $i + 1));
                                            }
                                            break;
                                    }
                                }
                            }

                            // Now check client exists
                            if (array_key_exists(self::CSV_PROJECT_CLIENT, $activerow) && isset($activerow[self::CSV_PROJECT_CLIENT]))
                            {
                                if (!is_numeric($activerow[self::CSV_PROJECT_CLIENT]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_PROJECT_CLIENT, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        entities\Client::getB2DBTable()->selectById($activerow[self::CSV_PROJECT_CLIENT]);
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: client does not exist', array('%col' => self::CSV_PROJECT_CLIENT, '%row' => $i + 1));
                                    }
                                }
                            }

                            // Now check if workflow exists
                            if (array_key_exists(self::CSV_PROJECT_WORKFLOW_ID, $activerow) && isset($activerow[self::CSV_PROJECT_WORKFLOW_ID]))
                            {
                                if (!is_numeric($activerow[self::CSV_PROJECT_WORKFLOW_ID]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_PROJECT_WORKFLOW_ID, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        entities\WorkflowScheme::getB2DBTable()->selectById($activerow[self::CSV_PROJECT_WORKFLOW_ID]);
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: workflow scheme does not exist', array('%col' => self::CSV_PROJECT_WORKFLOW_ID, '%row' => $i + 1));
                                    }
                                }
                            }

                            // Now check if issuetype scheme
                            if (array_key_exists(self::CSV_PROJECT_ISSUETYPE_SCHEME, $activerow) && isset($activerow[self::CSV_PROJECT_ISSUETYPE_SCHEME]))
                            {
                                if (!is_numeric($activerow[self::CSV_PROJECT_ISSUETYPE_SCHEME]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_PROJECT_ISSUETYPE_SCHEME, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        entities\IssuetypeScheme::getB2DBTable()->selectById($activerow[self::CSV_PROJECT_ISSUETYPE_SCHEME]);
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: issuetype scheme does not exist', array('%col' => self::CSV_PROJECT_ISSUETYPE_SCHEME, '%row' => $i + 1));
                                    }
                                }
                            }

                            // Finally check if the summary type is valid. At this point, your error list has probably become so big it has eaten up all your available RAM...
                            if (array_key_exists(self::CSV_PROJECT_SUMMARY_TYPE, $activerow) && isset($activerow[self::CSV_PROJECT_SUMMARY_TYPE]))
                            {
                                if ($activerow[self::CSV_PROJECT_SUMMARY_TYPE] != 'issuetypes' && $activerow[self::CSV_PROJECT_SHOW_SUMMARY] != 'milestones')
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be \'issuetypes\' or \'milestones\')', array('%col' => self::CSV_PROJECT_SUMMARY_TYPE, '%row' => $i + 1));
                                }
                            }
                        }
                        break;
                    case self::CSV_TYPE_ISSUES:
                        for ($i = 0; $i != count($data); $i++)
                        {
                            $activerow = $data[$i];

                            // Check if project exists
                            try
                            {
                                $prjtmp = entities\Project::getB2DBTable()->selectByID($activerow[self::CSV_ISSUE_PROJECT]);
                            }
                            catch (\Exception $e)
                            {
                                $errors[] = $this->getI18n()->__('Row %row column %col: Project does not exist', array('%col' => self::CSV_ISSUE_PROJECT, '%row' => $i + 1));
                                break;
                            }

                            // First off are booleans
                            $boolitems = array(self::CSV_ISSUE_STATE, self::CSV_ISSUE_BLOCKING);

                            foreach ($boolitems as $boolitem)
                            {
                                if (array_key_exists($boolitem, $activerow) && isset($activerow[$boolitem]) && $activerow[$boolitem] != 1 && $activerow[$boolitem] != 0)
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be 1/0)', array('%col' => $boolitem, '%row' => $i + 1));
                                }
                            }

                            // Now numerics
                            $numericitems = array(self::CSV_ISSUE_VOTES, self::CSV_ISSUE_PERCENTAGE, self::CSV_ISSUE_ISSUENO);

                            foreach ($numericitems as $numericitem)
                            {
                                if (array_key_exists($numericitem, $activerow) && isset($activerow[$numericitem]) && !(is_numeric($activerow[$numericitem])))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => $numericitem, '%row' => $i + 1));
                                }
                            }

                            // Percentage must be 0-100
                            if (array_key_exists(self::CSV_ISSUE_PERCENTAGE, $activerow) && isset($activerow[self::CSV_ISSUE_PERCENTAGE]) && (($activerow[self::CSV_ISSUE_PERCENTAGE] < 0) || ($activerow[self::CSV_ISSUE_PERCENTAGE] > 100)))
                            {
                                $errors[] = $this->getI18n()->__('Row %row column %col: Percentage must be from 0 to 100 inclusive', array('%col' => self::CSV_ISSUE_PERCENTAGE, '%row' => $i + 1));
                            }

                            // Now identifiables
                            $identifiableitems = array(
                                array(self::CSV_ISSUE_OWNER, self::CSV_ISSUE_OWNER_TYPE),
                                array(self::CSV_ISSUE_ASSIGNED, self::CSV_ISSUE_ASSIGNED_TYPE)
                            );

                            foreach ($identifiableitems as $identifiableitem)
                            {
                                if ((!array_key_exists($identifiableitem[1], $activerow) && array_key_exists($identifiableitem[0], $activerow)) || (array_key_exists($identifiableitem[1], $activerow) && !array_key_exists($identifiableitem[0], $activerow)))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row: Both the type and item ID must be supplied for owner/lead/qa fields', array('%row' => $i + 1));
                                    continue;
                                }

                                if (array_key_exists($identifiableitem[1], $activerow) && isset($activerow[$identifiableitem[1]]) && $activerow[$identifiableitem[1]] != self::CSV_IDENTIFIER_TYPE_USER && $activerow[$identifiableitem[1]] != self::CSV_IDENTIFIER_TYPE_TEAM)
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be 1 for a user or 2 for a team)', array('%col' => $identifiableitem[1], '%row' => $i + 1));
                                }

                                if (array_key_exists($identifiableitem[0], $activerow) && isset($activerow[$identifiableitem[0]]) && !is_numeric($activerow[$identifiableitem[0]]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => $identifiableitem[0], '%row' => $i + 1));
                                }
                                elseif (array_key_exists($identifiableitem[0], $activerow) && isset($activerow[$identifiableitem[0]]) && is_numeric($activerow[$identifiableitem[0]]))
                                {
                                    // check if they exist
                                    switch ($activerow[$identifiableitem[1]])
                                    {
                                        case self::CSV_IDENTIFIER_TYPE_USER:
                                            try
                                            {
                                                entities\User::getB2DBTable()->selectByID($activerow[$identifiableitem[0]]);
                                            }
                                            catch (\Exception $e)
                                            {
                                                $errors[] = $this->getI18n()->__('Row %row column %col: user does not exist', array('%col' => $identifiableitem[0], '%row' => $i + 1));
                                            }
                                            break;
                                        case self::CSV_IDENTIFIER_TYPE_TEAM:
                                            try
                                            {
                                                entities\Team::getB2DBTable()->selectById($activerow[$identifiableitem[0]]);
                                            }
                                            catch (\Exception $e)
                                            {
                                                $errors[] = $this->getI18n()->__('Row %row column %col: team does not exist', array('%col' => $identifiableitem[0], '%row' => $i + 1));
                                            }
                                            break;
                                    }
                                }
                            }

                            // Now timestamps
                            if (array_key_exists(self::CSV_ISSUE_POSTED, $activerow) && isset($activerow[self::CSV_ISSUE_POSTED]) && ((string) (int) $activerow[self::CSV_ISSUE_POSTED] !== $activerow[self::CSV_ISSUE_POSTED]) && $activerow[self::CSV_ISSUE_POSTED] >= PHP_INT_MAX && $activerow[self::CSV_ISSUE_POSTED] <= ~PHP_INT_MAX)
                            {
                                $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a Unix timestamp)', array('%col' => self::CSV_ISSUE_POSTED, '%row' => $i + 1));
                            }

                            // Now check user exists for postedby
                            if (array_key_exists(self::CSV_ISSUE_POSTED_BY, $activerow) && isset($activerow[self::CSV_ISSUE_POSTED_BY]))
                            {
                                if (!is_numeric($activerow[self::CSV_ISSUE_POSTED_BY]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_ISSUE_POSTED_BY, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        entities\User::getB2DBTable()->selectByID($activerow[self::CSV_ISSUE_POSTED_BY]);
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: user does not exist', array('%col' => self::CSV_ISSUE_POSTED_BY, '%row' => $i + 1));
                                    }
                                }
                            }

                            // Now check milestone exists and is valid
                            if (array_key_exists(self::CSV_ISSUE_MILESTONE, $activerow) && isset($activerow[self::CSV_ISSUE_MILESTONE]))
                            {
                                if (!is_numeric($activerow[self::CSV_ISSUE_MILESTONE]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_ISSUE_MILESTONE, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        $milestonetmp = entities\Milestone::getB2DBTable()->selectById($activerow[self::CSV_ISSUE_MILESTONE]);
                                        if ($milestonetmp->getProject()->getID() != $activerow[self::CSV_ISSUE_PROJECT])
                                        {
                                            $errors[] = $this->getI18n()->__('Row %row column %col: milestone does not apply to the specified project', array('%col' => self::CSV_ISSUE_MILESTONE, '%row' => $i + 1));
                                        }
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: milestone does not exist', array('%col' => self::CSV_ISSUE_MILESTONE, '%row' => $i + 1));
                                    }
                                }
                            }

                            // status
                            if (array_key_exists(self::CSV_ISSUE_STATUS, $activerow) && isset($activerow[self::CSV_ISSUE_STATUS]))
                            {
                                if (!is_numeric($activerow[self::CSV_ISSUE_STATUS]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_ISSUE_STATUS, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        entities\Status::getB2DBTable()->selectById($activerow[self::CSV_ISSUE_STATUS]);
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: status does not exist', array('%col' => self::CSV_ISSUE_STATUS, '%row' => $i + 1));
                                    }
                                }
                            }

                            // resolution
                            if (array_key_exists(self::CSV_ISSUE_RESOLUTION, $activerow) && isset($activerow[self::CSV_ISSUE_RESOLUTION]))
                            {
                                if (!is_numeric($activerow[self::CSV_ISSUE_RESOLUTION]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_ISSUE_RESOLUTION, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        entities\Resolution::getB2DBTable()->selectById($activerow[self::CSV_ISSUE_RESOLUTION]);
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: resolution does not exist', array('%col' => self::CSV_ISSUE_RESOLUTION, '%row' => $i + 1));
                                    }
                                }
                            }

                            // priority
                            if (array_key_exists(self::CSV_ISSUE_PRIORITY, $activerow) && isset($activerow[self::CSV_ISSUE_PRIORITY]))
                            {
                                if (!is_numeric($activerow[self::CSV_ISSUE_PRIORITY]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_ISSUE_PRIORITY, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        entities\Priority::getB2DBTable()->selectById($activerow[self::CSV_ISSUE_PRIORITY]);
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: priority does not exist', array('%col' => self::CSV_ISSUE_PRIORITY, '%row' => $i + 1));
                                    }
                                }
                            }

                            // category
                            if (array_key_exists(self::CSV_ISSUE_CATEGORY, $activerow) && isset($activerow[self::CSV_ISSUE_CATEGORY]))
                            {
                                if (!is_numeric($activerow[self::CSV_ISSUE_CATEGORY]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_ISSUE_CATEGORY, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        entities\Category::getB2DBTable()->selectById($activerow[self::CSV_ISSUE_CATEGORY]);
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: category does not exist', array('%col' => self::CSV_ISSUE_CATEGORY, '%row' => $i + 1));
                                    }
                                }
                            }

                            // severity
                            if (array_key_exists(self::CSV_ISSUE_SEVERITY, $activerow) && isset($activerow[self::CSV_ISSUE_SEVERITY]))
                            {
                                if (!is_numeric($activerow[self::CSV_ISSUE_SEVERITY]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_ISSUE_SEVERITY, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        entities\Severity::getB2DBTable()->selectById($activerow[self::CSV_ISSUE_SEVERITY]);
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: severity does not exist', array('%col' => self::CSV_ISSUE_SEVERITY, '%row' => $i + 1));
                                    }
                                }
                            }

                            // reproducability
                            if (array_key_exists(self::CSV_ISSUE_REPRODUCIBILITY, $activerow) && isset($activerow[self::CSV_ISSUE_REPRODUCIBILITY]))
                            {
                                if (!is_numeric($activerow[self::CSV_ISSUE_REPRODUCIBILITY]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_ISSUE_REPRODUCIBILITY, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        entities\Reproducability::getB2DBTable()->selectById($activerow[self::CSV_ISSUE_REPRODUCIBILITY]);
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: reproducability does not exist', array('%col' => self::CSV_ISSUE_REPRODUCIBILITY, '%row' => $i + 1));
                                    }
                                }
                            }

                            // type
                            if (array_key_exists(self::CSV_ISSUE_ISSUE_TYPE, $activerow) && isset($activerow[self::CSV_ISSUE_ISSUE_TYPE]))
                            {
                                if (!is_numeric($activerow[self::CSV_ISSUE_ISSUE_TYPE]))
                                {
                                    $errors[] = $this->getI18n()->__('Row %row column %col: invalid value (must be a number)', array('%col' => self::CSV_ISSUE_ISSUE_TYPE, '%row' => $i + 1));
                                }
                                else
                                {
                                    try
                                    {
                                        $typetmp = entities\Issuetype::getB2DBTable()->selectById($activerow[self::CSV_ISSUE_ISSUE_TYPE]);
                                        if (!($prjtmp->getIssuetypeScheme()->isSchemeAssociatedWithIssuetype($typetmp)))
                                            $errors[] = $this->getI18n()->__('Row %row column %col: this project does not support issues of this type (%type)', array('%type' => $typetmp->getName(), '%col' => self::CSV_ISSUE_ISSUE_TYPE, '%row' => $i + 1));
                                    }
                                    catch (\Exception $e)
                                    {
                                        $errors[] = $this->getI18n()->__('Row %row column %col: issue type does not exist', array('%col' => self::CSV_ISSUE_ISSUE_TYPE, '%row' => $i + 1));
                                    }
                                }
                            }
                        }
                        break;
                }
            }

            // Handle errors
            if (count($errors) != 0)
            {
                $errordiv = '<ul>';
                foreach ($errors as $error)
                {
                    $errordiv .= '<li>' . $error . '</li>';
                }
                $errordiv .= '</ul>';
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('errordetail' => $errordiv, 'error' => $this->getI18n()->__('Errors occured while importing, see the error list in the import screen for further details')));
            }
        }
        catch (\Exception $e)
        {
            $this->getResponse()->setHttpStatus(400);
            return $this->renderJSON(array('errordetail' => $e->getMessage(), 'error' => $e->getMessage()));
        }

        if ($request['csv_dry_run'])
        {
            return $this->renderJSON(array('message' => $this->getI18n()->__('Dry-run successful, you can now uncheck the dry-run box and import your data.')));
        }
        else
        {
            switch ($request['type'])
            {
                case self::CSV_TYPE_CLIENTS:
                    for ($i = 0; $i != count($data); $i++)
                    {
                        try
                        {
                            $activerow = $data[$i];

                            $client = new entities\Client();
                            $client->setName($activerow[self::CSV_CLIENT_NAME]);

                            if (isset($activerow[self::CSV_CLIENT_EMAIL]))
                                $client->setEmail($activerow[self::CSV_CLIENT_EMAIL]);

                            if (isset($activerow[self::CSV_CLIENT_WEBSITE]))
                                $client->setWebsite($activerow[self::CSV_CLIENT_WEBSITE]);

                            if (isset($activerow[self::CSV_CLIENT_FAX]))
                                $client->setFax($activerow[self::CSV_CLIENT_FAX]);

                            if (isset($activerow[self::CSV_CLIENT_TELEPHONE]))
                                $client->setTelephone($activerow[self::CSV_CLIENT_TELEPHONE]);

                            $client->save();
                        }
                        catch (\Exception $e)
                        {
                            $errors[] = $this->getI18n()->__('Row %row failed: %err', array('%row' => $i + 1, '%err' => $e->getMessage()));
                        }
                    }
                    break;
                case self::CSV_TYPE_PROJECTS:
                    for ($i = 0; $i != count($data); $i++)
                    {
                        try
                        {
                            $activerow = $data[$i];

                            $project = new entities\Project();
                            $project->setName($activerow[self::CSV_PROJECT_NAME]);

                            $project->save();

                            if (isset($activerow[self::CSV_PROJECT_PREFIX]))
                            {
                                $project->setPrefix($activerow[self::CSV_PROJECT_PREFIX]);
                                $project->setUsePrefix(true);
                            }

                            if (isset($activerow[self::CSV_PROJECT_SCRUM]))
                            {
                                if ($activerow[self::CSV_PROJECT_SCRUM] == '1')
                                    $project->setUsesScrum(true);
                            }

                            if (isset($activerow[self::CSV_PROJECT_OWNER]) && isset($activerow[self::CSV_PROJECT_OWNER_TYPE]))
                            {
                                switch ($activerow[self::CSV_PROJECT_OWNER_TYPE])
                                {
                                    case self::CSV_IDENTIFIER_TYPE_USER:
                                        $user = new entities\User($activerow[self::CSV_PROJECT_OWNER]);
                                        $project->setOwner($user);
                                        break;
                                    case self::CSV_IDENTIFIER_TYPE_TEAM:
                                        $team = new entities\Team($activerow[self::CSV_PROJECT_OWNER]);
                                        $project->setOwner($team);
                                        break;
                                }
                            }

                            if (isset($activerow[self::CSV_PROJECT_LEAD]) && isset($activerow[self::CSV_PROJECT_LEAD_TYPE]))
                            {
                                switch ($activerow[self::CSV_PROJECT_LEAD_TYPE])
                                {
                                    case self::CSV_IDENTIFIER_TYPE_USER:
                                        $user = new entities\User($activerow[self::CSV_PROJECT_LEAD]);
                                        $project->setLeader($user);
                                        break;
                                    case self::CSV_IDENTIFIER_TYPE_TEAM:
                                        $team = new entities\Team($activerow[self::CSV_PROJECT_LEAD]);
                                        $project->setLeader($team);
                                        break;
                                }
                            }

                            if (isset($activerow[self::CSV_PROJECT_QA]) && isset($activerow[self::CSV_PROJECT_QA_TYPE]))
                            {
                                switch ($activerow[self::CSV_PROJECT_QA_TYPE])
                                {
                                    case self::CSV_IDENTIFIER_TYPE_USER:
                                        $user = new entities\User($activerow[self::CSV_PROJECT_QA]);
                                        $project->setQaResponsible($user);
                                        break;
                                    case self::CSV_IDENTIFIER_TYPE_TEAM:
                                        $team = new entities\Team($activerow[self::CSV_PROJECT_QA]);
                                        $project->setQaResponsible($team);
                                        break;
                                }
                            }

                            if (isset($activerow[self::CSV_PROJECT_DESCR]))
                                $project->setDescription($activerow[self::CSV_PROJECT_DESCR]);

                            if (isset($activerow[self::CSV_PROJECT_DOC_URL]))
                                $project->setDocumentationUrl($activerow[self::CSV_PROJECT_DOC_URL]);

                            if (isset($activerow[self::CSV_PROJECT_WIKI_URL]))
                                $project->setWikiUrl($activerow[self::CSV_PROJECT_WIKI_URL]);

                            if (isset($activerow[self::CSV_PROJECT_FREELANCE]))
                            {
                                if ($activerow[self::CSV_PROJECT_FREELANCE] == '1')
                                    $project->setChangeIssuesWithoutWorkingOnThem(true);
                            }

                            if (isset($activerow[self::CSV_PROJECT_EN_BUILDS]))
                            {
                                if ($activerow[self::CSV_PROJECT_EN_BUILDS] == '1')
                                    $project->setBuildsEnabled(true);
                            }

                            if (isset($activerow[self::CSV_PROJECT_EN_COMPS]))
                            {
                                if ($activerow[self::CSV_PROJECT_EN_COMPS] == '1')
                                    $project->setComponentsEnabled(true);
                            }

                            if (isset($activerow[self::CSV_PROJECT_EN_EDITIONS]))
                            {
                                if ($activerow[self::CSV_PROJECT_EN_EDITIONS] == '1')
                                    $project->setEditionsEnabled(true);
                            }

                            if (isset($activerow[self::CSV_PROJECT_CLIENT]))
                                $project->setClient(entities\Client::getB2DBTable()->selectById($activerow[self::CSV_PROJECT_CLIENT]));

                            if (isset($activerow[self::CSV_PROJECT_SHOW_SUMMARY]))
                            {
                                if ($activerow[self::CSV_PROJECT_SHOW_SUMMARY] == '1')
                                    $project->setFrontpageSummaryVisibility(true);
                            }

                            if (isset($activerow[self::CSV_PROJECT_SUMMARY_TYPE]))
                                $project->setFrontpageSummaryType($activerow[self::CSV_PROJECT_SUMMARY_TYPE]);

                            if (isset($activerow[self::CSV_PROJECT_ALLOW_REPORTING]))
                                $project->setLocked($activerow[self::CSV_PROJECT_ALLOW_REPORTING]);

                            if (isset($activerow[self::CSV_PROJECT_AUTOASSIGN]))
                                $project->setAutoassign($activerow[self::CSV_PROJECT_AUTOASSIGN]);

                            if (isset($activerow[self::CSV_PROJECT_ISSUETYPE_SCHEME]))
                                $project->setIssuetypeScheme(entities\IssuetypeScheme::getB2DBTable()->selectById($activerow[self::CSV_PROJECT_ISSUETYPE_SCHEME]));

                            if (isset($activerow[self::CSV_PROJECT_WORKFLOW_ID]))
                                ;
                            $project->setWorkflowScheme(entities\WorkflowScheme::getB2DBTable()->selectById($activerow[self::CSV_PROJECT_WORKFLOW_ID]));

                            $project->save();
                        }
                        catch (\Exception $e)
                        {
                            $errors[] = $this->getI18n()->__('Row %row failed: %err', array('%row' => $i + 1, '%err' => $e->getMessage()));
                        }
                    }
                    break;
                case self::CSV_TYPE_ISSUES:
                    for ($i = 0; $i != count($data); $i++)
                    {
                        try
                        {
                            $activerow = $data[$i];

                            $issue = new entities\Issue();
                            $issue->setTitle($activerow[self::CSV_ISSUE_TITLE]);
                            $issue->setProject($activerow[self::CSV_ISSUE_PROJECT]);
                            $issue->setIssuetype($activerow[self::CSV_ISSUE_ISSUE_TYPE]);

                            $issue->save();

                            if (isset($activerow[self::CSV_ISSUE_DESCR]))
                                $issue->setDescription($activerow[self::CSV_ISSUE_DESCR]);

                            if (isset($activerow[self::CSV_ISSUE_REPRO]))
                                $issue->setReproductionSteps($activerow[self::CSV_ISSUE_REPRO]);

                            if (isset($activerow[self::CSV_ISSUE_STATE]))
                                $issue->setState($activerow[self::CSV_ISSUE_STATE]);

                            if (isset($activerow[self::CSV_ISSUE_STATUS]))
                                $issue->setStatus($activerow[self::CSV_ISSUE_STATUS]);

                            if (isset($activerow[self::CSV_ISSUE_POSTED_BY]))
                                $issue->setPostedBy(entities\User::getB2DBTable()->selectByID($activerow[self::CSV_ISSUE_POSTED_BY]));

                            if (isset($activerow[self::CSV_ISSUE_OWNER]) && isset($activerow[self::CSV_ISSUE_OWNER_TYPE]))
                            {
                                switch ($activerow[self::CSV_ISSUE_OWNER_TYPE])
                                {
                                    case self::CSV_IDENTIFIER_TYPE_USER:
                                        $user = new entities\User($activerow[self::CSV_ISSUE_OWNER]);
                                        $issue->setOwner($user);
                                        break;
                                    case self::CSV_IDENTIFIER_TYPE_TEAM:
                                        $team = new entities\Team($activerow[self::CSV_ISSUE_OWNER]);
                                        $issue->setOwner($team);
                                        break;
                                }
                            }

                            if (isset($activerow[self::CSV_ISSUE_ASSIGNED]) && isset($activerow[self::CSV_ISSUE_ASSIGNED_TYPE]))
                            {
                                switch ($activerow[self::CSV_ISSUE_ASSIGNED_TYPE])
                                {
                                    case self::CSV_IDENTIFIER_TYPE_USER:
                                        $user = new entities\User($activerow[self::CSV_ISSUE_ASSIGNED]);
                                        $issue->setAssignee($user);
                                        break;
                                    case self::CSV_IDENTIFIER_TYPE_TEAM:
                                        $team = new entities\Team($activerow[self::CSV_ISSUE_ASSIGNED]);
                                        $issue->setAssignee($team);
                                        break;
                                }
                            }

                            if (isset($activerow[self::CSV_ISSUE_RESOLUTION]))
                                $issue->setResolution($activerow[self::CSV_ISSUE_RESOLUTION]);

                            if (isset($activerow[self::CSV_ISSUE_PRIORITY]))
                                $issue->setPriority($activerow[self::CSV_ISSUE_PRIORITY]);

                            if (isset($activerow[self::CSV_ISSUE_CATEGORY]))
                                $issue->setCategory($activerow[self::CSV_ISSUE_CATEGORY]);

                            if (isset($activerow[self::CSV_ISSUE_BLOCKING]))
                                $issue->setBlocking($activerow[self::CSV_ISSUE_BLOCKING]);

                            if (isset($activerow[self::CSV_ISSUE_SEVERITY]))
                                $issue->setSeverity($activerow[self::CSV_ISSUE_SEVERITY]);

                            if (isset($activerow[self::CSV_ISSUE_REPRODUCIBILITY]))
                                $issue->setReproducability($activerow[self::CSV_ISSUE_REPRODUCIBILITY]);

                            if (isset($activerow[self::CSV_ISSUE_VOTES]))
                                $issue->setVotes($activerow[self::CSV_ISSUE_VOTES]);

                            if (isset($activerow[self::CSV_ISSUE_PERCENTAGE]))
                                $issue->setPercentCompleted($activerow[self::CSV_ISSUE_PERCENTAGE]);

                            if (isset($activerow[self::CSV_ISSUE_ISSUENO]))
                                $issue->setIssueNo((int) $activerow[self::CSV_ISSUE_ISSUENO]);

                            if (isset($activerow[self::CSV_ISSUE_MILESTONE]))
                                $issue->setMilestone($activerow[self::CSV_ISSUE_MILESTONE]);

                            if (isset($activerow[self::CSV_ISSUE_POSTED]))
                                $issue->setPosted((int) $activerow[self::CSV_ISSUE_POSTED]);

                            $issue->save();
                        }
                        catch (\Exception $e)
                        {
                            $errors[] = $this->getI18n()->__('Row %row failed: %err', array('%row' => $i + 1, '%err' => $e->getMessage()));
                        }
                    }
                    break;
            }

            // Handle errors
            if (count($errors) != 0)
            {
                $errordiv = '<ul>';
                foreach ($errors as $error)
                {
                    $errordiv .= '<li>' . $error . '</li>';
                }
                $errordiv .= '</ul>';
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(array('errordetail' => $errordiv, 'error' => $this->getI18n()->__('Errors occured while importing, see the error list in the import screen for further details')));
            }
            else
            {
                return $this->renderJSON(array('message' => $this->getI18n()->__('Successfully imported %num rows!', array('%num' => count($data)))));
            }
        }
    }

}
