<?php

    function get_spaced_name($camelcased)
    {
        return \thebuggenie\core\framework\Context::getModule('publish')->getSpacedName($camelcased);
    }
