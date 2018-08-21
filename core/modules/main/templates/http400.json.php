<?= (isset($message)) ? json_encode(array('error' => $message)) : json_encode(array('error' => __("You have sent an invalid request."))); ?>
