<?php
	$_SESSION['activation_number'] = tbg_printRandomNumber();
	
	if (function_exists('imagecreatetruecolor'))
	{
			echo image_tag(TBGContext::getRouting()->generate('captcha'), null, true, 'core', false);
	}
	else 
	{
		$chain = str_split($_SESSION['activation_number'],1);
		foreach ($chain as $number)
		{
			echo image_tag('numbers/' . $number . '.png');
		}
	}