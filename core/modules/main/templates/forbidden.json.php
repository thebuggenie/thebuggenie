<?php echo (isset($message)) ? json_encode(array('error' => $message)) : json_encode(array('error' => __("You do not have access to this action")));
