<?= (isset($message)) ? json_encode(array('error' => $message)) : json_encode(array('error' => __("This location doesn't exist, has been deleted or you don't have permission to see it"))); ?>
