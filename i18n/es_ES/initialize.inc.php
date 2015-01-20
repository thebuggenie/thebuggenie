<?php

	\thebuggenie\core\framework\Context::getI18n()->setCharset('UTF-8');
	setlocale(LC_ALL, array('es_ES@dolar', 'es_ES', 'es'));
// see \thebuggenie\core\entities\i18n::getDateTimeFormat for the list of all available formats
	\thebuggenie\core\framework\Context::getI18n()->setDateTimeFormats( array( 1 => '%H:%M - %a %d %b %Y'
													, 2 => '%H:%M - %a %d.m %Y'
													, 3 => '%a %d de %b %H:%M'
													, 4 => '%d de %b %H:%M'
													, 5 => '%d de %B de %Y'
													, 6 => '%d de %B de %Y (%H:%M)'
													, 7 => '%A %d de %B de %Y (%H:%M)'
													, 8 => '%d de %b de %Y %H:%M'
													, 9 => '%d de %b de %Y - %H:%M'
													, 10 => '%d de %b de %Y (%H:%M)'
													, 11 => '%B'
													, 12 => '%d de %b'
													, 13 => '%a'
													, 14 => '%H:%M'
													, 15 => '%d de %b de %Y'
													, 16 => '%Gh %im'
													, 17 => '%a, %d de %b de %Y %H:%M:%S GMT' ));