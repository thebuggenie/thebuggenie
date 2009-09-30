<?php

	define ('BUGS2_INCLUDE_PATH', '../../');
	$page = 'calendar';
	
	require_once(BUGS2_INCLUDE_PATH . 'include/checkcookie.inc.php');
	require_once(BUGS2_INCLUDE_PATH . 'include/b2_engine.inc.php');
	require_once(BUGScontext::getIncludePath() . 'include/ui_functions.inc.php');

	BUGScontext::getModule('calendar')->activate();
	header ("Content-Type: text/html; charset=" . BUGScontext::getI18n()->getCharset());
	
	if (!BUGScontext::getRequest()->isAjaxCall())
	{
		exit();
	}
	
	if (BUGScontext::getRequest()->getParameter('get_month') && BUGScontext::getRequest()->getParameter('month') && BUGScontext::getRequest()->getParameter('year'))
	{
		$month = (int) BUGScontext::getRequest()->getParameter('month');
		$year = (int) BUGScontext::getRequest()->getParameter('year');
		echo BUGScontext::getModule('calendar')->html_calendar($year, $month, BUGScontext::getRequest()->getParameter('mode'));
	}

	if (BUGScontext::getRequest()->getParameter('get_week') && BUGScontext::getRequest()->getParameter('day') && BUGScontext::getRequest()->getParameter('month') && BUGScontext::getRequest()->getParameter('year'))
	{
		$day = (int) BUGScontext::getRequest()->getParameter('day');
		$month = (int) BUGScontext::getRequest()->getParameter('month');
		$year = (int) BUGScontext::getRequest()->getParameter('year');
		echo BUGScontext::getModule('calendar')->html_week($day, $month, $year); 
	}
	
	if (BUGScontext::getRequest()->getParameter('get_day') && BUGScontext::getRequest()->getParameter('day') && BUGScontext::getRequest()->getParameter('month') && BUGScontext::getRequest()->getParameter('year'))
	{
		$day = (int) BUGScontext::getRequest()->getParameter('day');
		$month = (int) BUGScontext::getRequest()->getParameter('month');
		$year = (int) BUGScontext::getRequest()->getParameter('year');
		echo BUGScontext::getModule('calendar')->html_day($day, $month, $year); 
	}
	
	?>