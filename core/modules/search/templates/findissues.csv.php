<?php
TBGContext::getResponse()->addHeader('Content-Disposition: attachment; filename="'.$searchtitle.'.csv"');
include_template('search/results_normal_csv', array('issues' => $issues));
?>
