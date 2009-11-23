<?php

	function get_spaced_name($camelcased)
	{
		return BUGScontext::getModule('publish')->getSpacedName($camelcased);
	}
