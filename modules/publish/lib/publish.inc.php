<?php

    function get_spaced_name($camelcased)
    {
        return TBGContext::getModule('publish')->getSpacedName($camelcased);
    }
