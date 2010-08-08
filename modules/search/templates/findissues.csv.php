<?php
TBGContext::getResponse()->addHeader('Content-Disposition: attachment; filename="'.$searchtitle.'.csv"');
include_template('search/'.$templatename.'_csv', array('issues' => $issues));
?>