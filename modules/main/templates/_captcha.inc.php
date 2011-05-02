<?php
	$_SESSION['activation_number'] = tbg_printRandomNumber();
	
	if (function_exists('imagecreatetruecolor'))
	{
			// use of timestamped paramter in the captcha route for preventing image cache
			echo image_tag(TBGContext::getRouting()->generate('captcha', array(time())), null, true, 'core', true);
	}
	else 
	{
		$chain = str_split($_SESSION['activation_number'],1);
		foreach ($chain as $number)
		{
			echo image_tag('numbers/' . $number . '.png');
		}
	}