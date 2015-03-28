<?php

	\thebuggenie\core\framework\Context::getI18n()->setCharset('utf-8');
	setlocale(LC_ALL, array('fr_FR@euro', 'fr_FR', 'fr'));
	// see \thebuggenie\core\entities\i18n::getDateTimeFormat for the list of all available formats
	\thebuggenie\core\framework\Context::getI18n()->setDateTimeFormats( array( 1 => '%H:%M - %a %d %b %Y'
													, 2 => '%H:%M - %a %d.m %Y'
													, 3 => '%a %d %b %H:%M'
													, 4 => '%d %b %H:%M'
													, 5 => '%d %B %Y'
													, 6 => '%d %B %Y (%H:%M)'
													, 7 => '%A %d %B %Y (%H:%M)'
													, 8 => '%d %b %Y %H:%M'
													, 9 => '%d %b %Y - %H:%M'
													, 10 => '%d %b %Y (%H:%M)'
													, 11 => '%B'
													, 12 => '%d %b'
													, 13 => '%a'
													, 14 => '%H:%M'
													, 15 => '%d %b %Y'
													, 16 => '%Gh %im'
													, 17 => '%a, %d %b %Y %H:%M:%S GMT' ));