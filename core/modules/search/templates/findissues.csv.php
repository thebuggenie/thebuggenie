<?php

    \thebuggenie\core\framework\Context::getResponse()->addHeader('Content-Disposition: attachment; filename="'.$searchtitle.'.csv"');
    include_component('search/results_normal_csv', compact('search_object'));
