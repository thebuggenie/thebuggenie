<?php

    $_SESSION['activation_number'] = tbg_printRandomNumber();
    
    if (function_exists('imagecreatetruecolor'))
    {
            // use of timestamped paramter in the captcha route for preventing image cache
            echo image_tag(\thebuggenie\core\framework\Context::getRouting()->generate('captcha', array(time())), array(), true, 'core', true);
    }
    else 
    {
        $chain = str_split($_SESSION['activation_number'],1);
        foreach ($chain as $number)
        {
            echo image_tag('numbers/' . $number . '.png');
        }
    }
    
?>
<li class="security_check">
    <label for="verification_no"><?php echo __('Enter the number you see above'); ?></label>
    <input type="text" class="required" id="verification_no" name="verification_no" maxlength="6" value="" autocomplete="off" style="width: 100px;"><br><br>
</li>
