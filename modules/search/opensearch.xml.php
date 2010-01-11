<?php

	define ('THEBUGGENIE_PATH', '../../');
	$page = 'search';

	require THEBUGGENIE_PATH . 'include/checkcookie.inc.php';
	require THEBUGGENIE_PATH . 'include/b2_engine.inc.php';

	TBGContext::getModule('search')->activate();
	
	header ("content-type: text/xml");
	print '<?xml version="1.0" encoding="UTF-8"?>';
	if (TBGContext::getModule('search')->getSetting('enable_opensearch') != 1)
	{
		exit();
	}
	
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
<ShortName><?php print TBGContext::getModule('search')->getSetting('opensearch_title'); ?></ShortName>
<LongName><?php print TBGContext::getModule('search')->getSetting('opensearch_longname'); ?></LongName>
<Description><?php print TBGContext::getModule('search')->getSetting('opensearch_description'); ?></Description>
<Image width="16" height="16">data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8%2F9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAAIgAAACIBB7P0uQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAKISURBVDiNpZNPiE1xFMc%2F5%2F7ufX%2B853nzZh6G%2BYPBaESDovxPilKabEhZsJOFLBRjITuFiAULkqRYsLAxooiEGAzR%2BPcmRmP8mWnejDf3vXfvu8fmjaiRhbM5ncX5dL6n71dUlf8p%2B%2FehslIS8cnsTFRS7%2FuUst94le3mTi6nT%2F8JmFgv06Yv42Zjc3pqQ%2FUikukw99tv8LZj0E1OkmMDPdo6GsACEJFQopozk2Yyta7JZt2aDbif6tm19ShzlkWiTYvZm0zL7r8CQlFaYklWhmNQ1M8MeZ1s2bSNh239LF64lJomqJnDAceRFaMCii4NhWHwCqDAs54T9FlX0bGv6Xj%2BmHQdjK8jWlHNcRGpEBH5BRCRFOBlv8BALwwPQXbI5dabVqzG03TcHSA%2FBJGxEK9iLrAf2CcizSNPXOc4TktvxuscN4FZ8RTYIfB9sG2YvxYuH4aqGiAAYDmQBw4B2Kp6XkR6ReRs1xP1bAfHL0B6CsTGQSEPue9hd%2Fa0RWKnXtrFxr7Zve%2FMtVKp5IhIkbKRDDADOBaK0N2wAF3Ygi7dTBCvsPpOXWjVN59v65Hr03XHGYJYwr4CbATMiA8U6AcuFfN0vW9nfXcnlV6e2rmrgtTX6EXaug5ixgREoogJ%2BV40Gl3iuu5lG0BVAxH5AfQBMcAr5qg1xoQsUyLTlaHjFNnBTH3MC3%2FoH%2FyOgjsfaEZVR2RsB9oSicRJYDNw1hhzXyxexFN8C0dsf8%2B5ZH7eatqADUAjYOT3MImIKV%2BQBmqBfZZleUEQ3AMmWDY%2FAp%2FXwAMgo6reH2FS1VJZig8MA4%2BMMZkgCLoBO%2FDJAT3AR1X1AGS0OJedZgMOECl3gAKQA3wtL%2F4EbzL%2FhCjT%2FIEAAAAASUVORK5CYII%3D</Image>
<Url type="text/html" template="<?php echo TBGSettings::get('url_host') . TBGSettings::get('url_subdir'); ?>modules/search/search.php?scope=<?php echo TBGContext::getScope()->getID(); ?>&amp;searchfor={searchTerms}"/>
<Url type="application/x-suggestions+json" template="<?php echo TBGSettings::get('url_host') . TBGSettings::get('url_subdir'); ?>modules/search/search_stripped.php?scope=<?php echo TBGContext::getScope()->getID(); ?>&amp;output=json&amp;searchfor={searchTerms}"/>
<AdultContent>false</AdultContent>
<OutputEncoding>UTF-8</OutputEncoding>
<Contact><?php print TBGContext::getModule('search')->getSetting('opensearch_contact'); ?></Contact>
<Query role="example" searchTerms="opensearch"/>
<Attribution>No copyright</Attribution>
<SyndicationRight>open</SyndicationRight>
</OpenSearchDescription>