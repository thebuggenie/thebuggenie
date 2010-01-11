<?php

	define ('THEBUGGENIE_PATH', '../../');
	$page = 'calendar';
	
	require_once(THEBUGGENIE_PATH . 'include/checkcookie.inc.php');
	require_once(THEBUGGENIE_PATH . 'include/b2_engine.inc.php');
	require_once(TBGContext::getIncludePath() . 'include/ui_functions.inc.php');

	TBGContext::getModule('calendar')->activate();
	header ("Content-Type: text/html; charset=" . TBGContext::getI18n()->getCharset());
	
	if (!TBGContext::getRequest()->isAjaxCall())
	{
		exit();
	}
	
	if (TBGContext::getRequest()->getParameter('get_month') && TBGContext::getRequest()->getParameter('month') && TBGContext::getRequest()->getParameter('year'))
	{
		$month = (int) TBGContext::getRequest()->getParameter('month');
		$year = (int) TBGContext::getRequest()->getParameter('year');
		echo TBGContext::getModule('calendar')->html_calendar($year, $month, TBGContext::getRequest()->getParameter('mode'));
	}

	if (TBGContext::getRequest()->getParameter('get_week') && TBGContext::getRequest()->getParameter('day') && TBGContext::getRequest()->getParameter('month') && TBGContext::getRequest()->getParameter('year'))
	{
		$day = (int) TBGContext::getRequest()->getParameter('day');
		$month = (int) TBGContext::getRequest()->getParameter('month');
		$year = (int) TBGContext::getRequest()->getParameter('year');
		echo TBGContext::getModule('calendar')->html_week($day, $month, $year); 
	}
	
	if (TBGContext::getRequest()->getParameter('get_day') && TBGContext::getRequest()->getParameter('day') && TBGContext::getRequest()->getParameter('month') && TBGContext::getRequest()->getParameter('year'))
	{
		$day = (int) TBGContext::getRequest()->getParameter('day');
		$month = (int) TBGContext::getRequest()->getParameter('month');
		$year = (int) TBGContext::getRequest()->getParameter('year');
		echo TBGContext::getModule('calendar')->html_day($day, $month, $year); 
	}
	
	?>