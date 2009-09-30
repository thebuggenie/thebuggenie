<?php

if (isset($_COOKIE['BUGSlogin'])):
	$loginName = $_COOKIE['BUGSlogin'][1];
	$loginPass = $_COOKIE['BUGSlogin'][2];
endif;

session_name("BUGS_LOGIN");
session_start();

?>