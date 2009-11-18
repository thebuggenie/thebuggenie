<br>
<h2 style="margin-bottom: 0px;"><?php echo __('About The Bug Genie'); ?></h2>
<?php echo __('Version %bugs_version%', array('%bugs_version%' => BUGSsettings::getVersion(true))); ?><br>
<br>
<a href="http://www.thebuggenie.com" target="_blank">The Bug Genie</a>, Copyright 2002 &copy; 09 <a href="http://www.zegeniestudios.net" target="_blank">zegenie Studios</a>
<?php

	$text = 'This is a text that is CamelCased, !CamelCased, AndAnotherOne do you not ThinkIt?';

	function fu($string)
	{
		return '{link:'.$string.'}';
	}

	echo preg_replace('/((?<=[a-z])(?=[A-Z]))+/e', 'fu("\\1")', $text);
	echo '<br>';
	echo '<br>';
	echo preg_replace('/(?<=\\w)(?=[A-Z])/e', 'fu("\\0")', $text);
	echo '<br>';
	echo '<br>';
	echo preg_replace('/!{0,0}([A-Z]+[a-z]+[A-Z][A-Za-z]*)\b/e', 'fu("\\0")', $text);
	echo '<br>';
	echo '<br>';
	echo preg_replace('/\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/e', 'fu("\\0")', $text);

