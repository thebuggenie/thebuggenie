<?php

	// BEGIN Step management
	if (BUGScontext::getRequest()->getParameter('setstep'))
	{
		$_SESSION['rni_step' . BUGScontext::getRequest()->getParameter('step') . '_set'] = true;
	}
	if (BUGScontext::getRequest()->getParameter('unsetstep'))
	{
		unset($_SESSION['rni_step' . BUGScontext::getRequest()->getParameter('step') . '_set']);
	}

	if (BUGScontext::getRequest()->getParameter('restart'))
	{
		reportIssue_clearAll();
		reportIssue_clearInfo();
	}
	
	function reportIssue_clearAll()
	{
		unset($_SESSION['rni_step1_project']);
		unset($_SESSION['rni_step1_edition']);
		unset($_SESSION['rni_step1_build']);
		unset($_SESSION['rni_step2_issuetype']);
		unset($_SESSION['rni_step2_component']);
		unset($_SESSION['rni_step2_category']);
		unset($_SESSION['rni_step2_severity']);
		unset($_SESSION['rni_step1_set']);
		unset($_SESSION['rni_step2_set']);
		$step1_set = false;
		$step2_set = false;
	}
	
	function reportIssue_clearInfo()
	{
		unset($_SESSION['rni_step3_title']);
		unset($_SESSION['rni_step3_description']);
		unset($_SESSION['rni_step3_repro']);
		unset($_SESSION['rni_step4_links']);
		unset($_SESSION['rni_step4_files']);
		unset($_SESSION['rni_step3_set']);
		unset($_SESSION['rni_step4_set']);
		unset($_SESSION['rni_step5_set']);
		$step3_set = false;
		$step4_set = false;
		$step5_set = false;
	}
	
	$step1_set = (isset($_SESSION['rni_step1_set'])) ? true : false;
	$step2_set = (isset($_SESSION['rni_step2_set'])) ? true : false;
	$step3_set = (isset($_SESSION['rni_step3_set'])) ? true : false;
	$step4_set = (isset($_SESSION['rni_step4_set'])) ? true : false;
	$step5_set = (isset($_SESSION['rni_step5_set'])) ? true : false;
	// END step management
	
	// BEGIN STEP 1 *****************
	// BEGIN Step 1 "set" actions
	if (BUGScontext::getRequest()->getParameter('rni_step1_project'))
	{
		$_SESSION['rni_step1_project'] = BUGScontext::getRequest()->getParameter('rni_step1_project');
	}
	if (BUGScontext::getRequest()->getParameter('rni_step1_edition'))
	{
		$_SESSION['rni_step1_edition'] = BUGScontext::getRequest()->getParameter('rni_step1_edition');
	}
	if (BUGScontext::getRequest()->getParameter('rni_step1_build'))
	{
		$_SESSION['rni_step1_build'] = BUGScontext::getRequest()->getParameter('rni_step1_build');
	}
	// END Step 1 "set" actions
	
	// BEGIN Step 1 logic
	try
	{
		$selectedProject = (isset($_SESSION['rni_step1_project'])) ? BUGSfactory::projectLab($_SESSION['rni_step1_project']) : BUGSfactory::projectLab(BUGSproject::getDefaultProject());
	}
	catch (Exception $e) 
	{
		$selectedProject = null;
		unset($_SESSION['rni_step1_project']);
	}
	
	if ($selectedProject instanceof BUGSproject)
	{
		try
		{
			$selectedEdition = (isset($_SESSION['rni_step1_edition'])) ? BUGSfactory::editionLab($_SESSION['rni_step1_edition']) : $selectedProject->getDefaultEdition();
			if ($selectedEdition instanceof BUGSedition && $selectedEdition->getProject()->getID() != $selectedProject->getID())
			{
				$selectedEdition = $selectedProject->getDefaultEdition();
			}
		}
		catch (Exception $e) 
		{ 
			$selectedEdition = null;
			unset($_SESSION['rni_step1_edition']);			
		}
	}

	if ($selectedEdition instanceof BUGSedition && $selectedProject->isBuildsEnabled())
	{
		try
		{
			$selectedBuild = (isset($_SESSION['rni_step1_build'])) ? BUGSfactory::buildLab($_SESSION['rni_step1_build']) : $selectedEdition->getDefaultBuild();
			if ($selectedBuild instanceof BUGSbuild && $selectedBuild->getEdition()->getID() != $selectedEdition->getID())
			{
				$selectedBuild = $selectedEdition->getDefaultBuild();
			}
		}
		catch (Exception $e) 
		{ 
			$selectedBuild = null;
			unset($_SESSION['rni_step1_build']);			
		}
	}
	elseif ($selectedEdition instanceof BUGSedition && !$selectedProject->isBuildsEnabled())
	{
		unset($_SESSION['rni_step1_build']);
	}
	
	if (!isset($_SESSION['rni_step1_project']) && $selectedProject instanceof BUGSproject && $selectedEdition instanceof BUGSedition && ($selectedBuild instanceof BUGSbuild || !$selectedProject->isBuildsEnabled()))
	{
		$_SESSION['rni_step1_project'] = $selectedProject->getID();
		$_SESSION['rni_step1_set'] = true;
		$step1_set = true;
	}
	if (!isset($_SESSION['rni_step1_edition']) && $selectedEdition instanceof BUGSedition)
	{
		$_SESSION['rni_step1_edition'] = $selectedEdition->getID();
	}
	if (!isset($_SESSION['rni_step1_build']) && $selectedBuild instanceof BUGSbuild)
	{
		$_SESSION['rni_step1_build'] = $selectedBuild->getID();
	}

	if (!($selectedProject instanceof BUGSproject) || !($selectedEdition instanceof BUGSedition) || (!($selectedBuild instanceof BUGSbuild) && !($selectedProject instanceof BUGSproject)) || (!($selectedBuild instanceof BUGSbuild) && $selectedProject instanceof BUGSproject && $selectedProject->isBuildsEnabled()))
	{
		$step1_set = false;
		unset($_SESSION['rni_step1_set']);
	}
	
	// END Step 1 logic
	
	// BEGIN Step 1 "get" actions
	if (BUGScontext::getRequest()->getParameter('getproject'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/projectselection.inc.php';
	}
	if (BUGScontext::getRequest()->getParameter('getedition'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/editionselection.inc.php';
	}
	if (BUGScontext::getRequest()->getParameter('getbuild'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/buildselection.inc.php';
	}
	// END Step 1 "get" actions
	// END STEP 1 *******************
	
	// BEGIN STEP 2 *****************
	// BEGIN Step 2 "set" actions
	if (BUGScontext::getRequest()->getParameter('rni_step2_setissuetype'))
	{
		$_SESSION['rni_step2_issuetype'] = BUGScontext::getRequest()->getParameter('rni_step2_setissuetype');
	}
	elseif (count(BUGSissuetype::getAll()) == 1 && !isset($_SESSION['rni_step2_issuetype']))
	{
		$issuetypes = BUGSissuetype::getAll();
		$_SESSION['rni_step2_issuetype'] = array_shift($issuetypes)->getID();
	}
	elseif (BUGSsettings::get('defaultissuetypefornewissues') && !isset($_SESSION['rni_step2_issuetype']))
	{
		$_SESSION['rni_step2_issuetype'] = BUGSsettings::get('defaultissuetypefornewissues');
	}
	
	if (BUGScontext::getRequest()->getParameter('rni_step2_setcomponent'))
	{
		$_SESSION['rni_step2_component'] = BUGScontext::getRequest()->getParameter('rni_step2_setcomponent');
	}
	elseif ($selectedEdition instanceof BUGSedition && count($selectedEdition->getComponents()) == 1 && !isset($_SESSION['rni_step2_component']))
	{
		$components = $selectedEdition->getComponents();
		$_SESSION['rni_step2_component'] = array_shift($components)->getID(); 
	}
	
	if (BUGScontext::getRequest()->getParameter('rni_step2_setcategory'))
	{
		$_SESSION['rni_step2_category'] = BUGScontext::getRequest()->getParameter('rni_step2_setcategory');
	}
	elseif (count(BUGSdatatype::getAll(BUGSdatatype::CATEGORY)) == 1 && !isset($_SESSION['rni_step2_category']))
	{
		$categories = BUGSdatatype::getAll(BUGSdatatype::CATEGORY);
		$_SESSION['rni_step2_category'] = BUGSfactory::datatypeLab(array_shift($categories), BUGSdatatype::CATEGORY)->getID();
	}
	
	if (BUGScontext::getRequest()->getParameter('rni_step2_setseverity'))
	{
		$_SESSION['rni_step2_severity'] = BUGScontext::getRequest()->getParameter('rni_step2_setseverity');
	}
	elseif (count(BUGSdatatype::getAll(BUGSdatatype::SEVERITY)) == 1 && !isset($_SESSION['rni_step2_severity']))
	{
		$severities = BUGSdatatype::getAll(BUGSdatatype::SEVERITY);
		$_SESSION['rni_step2_severity'] = BUGSfactory::datatypeLab(array_shift($severities), BUGSdatatype::SEVERITY)->getID();
	}
	elseif (BUGSsettings::get('defaultseverityfornewissues') && !isset($_SESSION['rni_step2_severity']))
	{
		$_SESSION['rni_step2_severity'] = BUGSsettings::get('defaultseverityfornewissues');
	}
	// END Step 2 "set" actions
	
	// BEGIN Step 2 logic
	$theIssuetype = null;
	if (isset($_SESSION['rni_step2_issuetype']))
	{
		try
		{
			$theIssuetype = BUGSfactory::BUGSissuetypeLab($_SESSION['rni_step2_issuetype'], BUGSdatatype::ISSUETYPE);
		}
		catch (Exception $e) 
		{ 
			$step2_set = false;
			$_SESSION['rni_step2_set'] = false;
			unset($_SESSION['rni_step2_issuetype']);
		}
	}
	$theComponent = null;
	if (isset($_SESSION['rni_step2_component']))
	{
		try
		{
			$theComponent = BUGSfactory::componentLab($_SESSION['rni_step2_component']);
			if ($theComponent->getProject()->getID() != $selectedProject->getID())
			{
				unset($_SESSION['rni_step2_component']);
				$theComponent = null;
			}
		}
		catch (Exception $e) 
		{
			$step2_set = false;
			$_SESSION['rni_step2_set'] = false;
			unset($_SESSION['rni_step2_component']);
		}
	}
	$theCategory = null;
	if (isset($_SESSION['rni_step2_category']))
	{
		try
		{
			$theCategory = BUGSfactory::datatypeLab($_SESSION['rni_step2_category'], BUGSdatatype::CATEGORY);
		}
		catch (Exception $e) 
		{
			$step2_set = false;
			$_SESSION['rni_step2_set'] = false;
			unset($_SESSION['rni_step2_category']);
		}
	}
	$theSeverity = null;
	if (isset($_SESSION['rni_step2_severity']))
	{
		try
		{
			$theSeverity = BUGSfactory::datatypeLab($_SESSION['rni_step2_severity'], BUGSdatatype::SEVERITY);
		}
		catch (Exception $e) 
		{
			$step2_set = false;
			$_SESSION['rni_step2_set'] = false;
			unset($_SESSION['rni_step2_severity']);
		}
	}
	
	if ($theIssuetype instanceof BUGSissuetype && $theComponent instanceof BUGScomponent && $theSeverity instanceof BUGSdatatype && $theCategory instanceof BUGSdatatype)
	{
		$_SESSION['rni_step2_set'] = true;
		$step2_set = true;		
	}
	// END Step 2 logic

	// BEGIN Step 2 "get" actions
	if (BUGScontext::getRequest()->getParameter('getissuetype'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/issuetypeselection.inc.php';
	}
	if (BUGScontext::getRequest()->getParameter('getcomponent'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/componentselection.inc.php';
	}
	if (BUGScontext::getRequest()->getParameter('getcategory'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/categoryselection.inc.php';
	}
	if (BUGScontext::getRequest()->getParameter('getseverity'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/severityselection.inc.php';
	}
	if (BUGScontext::getRequest()->getParameter('updatestep2button'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/step2_button.inc.php';
	}
	// END Step 2 "get" actions
	// END STEP 2 *******************
	
	
	// BEGIN STEP 3 *****************
	// BEGIN Step 3 "set" actions
	if (BUGScontext::getRequest()->getParameter('rni_step3_title'))
	{
		$_SESSION['rni_step3_title'] = BUGScontext::getRequest()->getParameter('rni_step3_title');
		unset($_SESSION['rni_step5_set']);
	}
	if (BUGScontext::getRequest()->getParameter('rni_step3_description') && trim(bugs_BBDecode(BUGScontext::getRequest()->getParameter('rni_step3_description'))))
	{
		$_SESSION['rni_step3_description'] = BUGScontext::getRequest()->getParameter('rni_step3_description', null, false);
		unset($_SESSION['rni_step5_set']);
	}
	if (BUGScontext::getRequest()->getParameter('rni_step3_repro'))
	{
		$_SESSION['rni_step3_repro'] = BUGScontext::getRequest()->getParameter('rni_step3_repro', null, false);
	}
	// END Step 3 "set" actions
	
	// BEGIN Step 3 logic
	$step3_title = (isset($_SESSION['rni_step3_title'])) ? $_SESSION['rni_step3_title'] : null;
	$step3_description = (isset($_SESSION['rni_step3_description'])) ? $_SESSION['rni_step3_description'] : null;
	$step3_repro = (isset($_SESSION['rni_step3_repro'])) ? $_SESSION['rni_step3_repro'] : null;
	
	if ($step3_title !== null && $step3_description !== null && $step3_repro !== null)
	{
		$_SESSION['rni_step3_set'] = true;
		$step3_set = true;		
	}
	// END Step 3 logic
	
	// BEGIN Step 3 "get" actions
	if (BUGScontext::getRequest()->getParameter('gettitle'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/inputsummary.inc.php';
	}
	if (BUGScontext::getRequest()->getParameter('getdescription'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/inputdescription.inc.php';
	}
	if (BUGScontext::getRequest()->getParameter('getrepro'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/inputrepro.inc.php';
	}
	if (BUGScontext::getRequest()->getParameter('updatestep3button'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/step3_button.inc.php';
	}
	// END Step 3 "get" actions
	
	// END STEP 3 *******************
	
	
	// BEGIN STEP 4 *****************
	// BEGIN Step 4 "set" actions
	if (BUGScontext::getRequest()->getParameter('rni_step4_link_url'))
	{
		if (!isset($_SESSION['rni_step4_links']))
		{
			$_SESSION['rni_step4_links'] = array();
		}
		$url = BUGScontext::getRequest()->getParameter('rni_step4_link_url');
		$desc = (trim(BUGScontext::getRequest()->getParameter('rni_step4_link_desc')) != '') ? BUGScontext::getRequest()->getParameter('rni_step4_link_desc') : $url;

		$_SESSION['rni_step4_links'][count($_SESSION['rni_step4_links'])] = array("url" => $url, "desc" => $desc);
	}
	if (BUGScontext::getRequest()->getParameter('rni_step4_removelink') !== null)
	{
		$removelink = BUGScontext::getRequest()->getParameter('rni_step4_removelink');
		unset($_SESSION['rni_step4_links'][$removelink]);
		if (count($_SESSION['rni_step4_links']) == 0)
		{
			unset($_SESSION['rni_step4_links']);
		}
	}
	if (isset($_FILES['rni_step4_file']))
	{
		$thefile = &$_FILES['rni_step4_file'];
		try
		{
			$new_filename = BUGSrequest::handleUpload($thefile);
			$description = (trim(BUGScontext::getRequest()->getParameter('rni_step4_file_desc')) !== '') ? BUGScontext::getRequest()->getParameter('rni_step4_file_desc') : basename($thefile['name']);
			$_SESSION['rni_step4_files'][] = array("filename" => $new_filename, "description" => $description);
		}
		catch (Exception $e)
		{
			$upload_error = $e->getMessage();
		}
	}
	if (BUGScontext::getRequest()->getParameter('rni_step4_removefile') !== null)
	{
		$removefile = BUGScontext::getRequest()->getParameter('rni_step4_removefile');
		if (isset($_SESSION['rni_step4_files'][$removefile]))
		{
			$filename = $_SESSION['rni_step4_files'][$removefile]['filename'];
			unlink(BUGScontext::getIncludePath() . 'files/' . $filename);
		}
		unset($_SESSION['rni_step4_files'][$removefile]);
		if (count($_SESSION['rni_step4_files']) == 0)
		{
			unset($_SESSION['rni_step4_files']);
		}
	}
	// END Step 4 "set" actions
	
	// BEGIN Step 4 logic
	$step4_links = (is_array($_SESSION['rni_step4_links'])) ? $_SESSION['rni_step4_links'] : null;
	$step4_files = (is_array($_SESSION['rni_step4_files'])) ? $_SESSION['rni_step4_files'] : null;
	// END Step 4 logic
	
	// BEGIN Step 4 "get" actions
	if (BUGScontext::getRequest()->getParameter('getlinks'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/linklist.inc.php';
	}
	if (BUGScontext::getRequest()->getParameter('getfiles'))
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/filelist.inc.php';
	}
	// END Step 4 "get" actions
	
	// END STEP 4 *******************
	
	
	// BEGIN STEP 5 *****************
	// BEGIN Step 5 "set" actions
	// END Step 5 "set" actions
	
	// BEGIN Step 5 "get" actions
	// END Step 5 "get" actions
	
	// BEGIN Step 5 logic
	// END Step 5 logic
	// END STEP 5 *******************

	if (BUGScontext::getRequest()->getParameter('rni_report_issue'))
	{
		$no_report = false;
		if (!$step1_set || !$step2_set || !$step3_set || !$step4_set || !$step5_set)
		{
			$no_report = true;
		}
		if ($selectedProject === null || $selectedEdition === null || ($selectedBuild === null && (!$selectedProject instanceof BUGSproject || ($selectedProject instanceof BUGSproject && $selectedProject->isBuildsEnabled()))))
		{
			$no_report = true;
		}
		if ($theIssuetype === null || $theComponent === null || $theCategory === null || $theSeverity === null)
		{
			$no_report = true;
		}
		if ($step3_title === null || $step3_description === null)
		{
			$no_report = true;
		}

		if (!$no_report)
		{
			$build_id = ($selectedBuild instanceof BUGSbuild) ? $selectedBuild->getID() : null;
			$posted_issue = BUGSissue::createNew($selectedProject->getID(), $selectedEdition->getID(), $build_id, $theIssuetype->getID(), $theComponent->getID(), $theCategory->getID(), $theSeverity->getID(), $step3_title, $step3_description, $step3_repro);
			if ($step4_links !== null)
			{
				foreach ($step4_links as $aLink)
				{
					$posted_issue->attachLink($aLink['url'], $aLink['desc']);
				}
			}
			if ($step4_files !== null)
			{
				foreach ($step4_files as $aFile)
				{
					$posted_issue->attachFile($aFile['filename'], $aFile['description']);
				}
			}
			
			reportIssue_clearInfo();
			if (!BUGScontext::getRequest()->getParameter('rni_preserve_info'))
			{
				reportIssue_clearAll();
			}
		}
	}
	
	if (isset($posted_issue) && $posted_issue instanceof BUGSissue)
	{
		require BUGScontext::getIncludePath() . 'include/report_issue/success.inc.php';
	}
	
?>