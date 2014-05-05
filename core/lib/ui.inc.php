<?php

	/**
	 * UI functions
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 */
	
	/**
	 * Returns an <img> tag with a specified image
	 * 
	 * @param string $image image source
	 * @param array $params[optional] html parameters
	 * @param boolean $notheme[optional] whether this is a themed image or a top level path
	 * @param string $module whether this is a module image or in the core image set
	 * @param boolean $relative whether the path is relative or absolute
	 * 
	 * @return string
	 */
	function image_tag($image, $params = array(), $notheme = false, $module = 'core', $relative = true)
	{
		if ($notheme)
		{
			$params['src'] = $image;
		}
		else
		{
			if ($module != 'core' && !file_exists(THEBUGGENIE_PATH . 'iconsets/' . TBGSettings::getIconsetName() . "/{$module}/" . $image))
			{
				$params['src'] = TBGContext::getTBGPath() . "iconsets/" . TBGSettings::getIconsetName() . "/modules/{$module}/" . $image;
			}
			elseif ($module != 'core')
			{
				$params['src'] = TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . "/{$module}/" . $image;
			}
			else
			{
				$params['src'] = TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/' . $image;
			}
		}
		if (!$relative)
		{
			if ($notheme)
			{
				$params['src'] = TBGContext::getTBGPath() . $params['src'];
			}
			$params['src'] = TBGContext::getUrlHost() . $params['src'];
		}
		if (!isset($params['alt']))
		{
			$params['alt'] = $image;
		}
		return "<img " . parseHTMLoptions($params) . '>';
	}
	
	/**
	 * Returns the URL to a specified image
	 * 
	 * @param string $image image source
	 * @param bool $notheme[optional] whether this is a themed image or a top level path
	 * 
	 * @return string
	 */
	function image_url($image, $notheme = false, $module = 'core', $relative = true)
	{
		if ($notheme)
		{
			$params['src'] = $image;
		}
		else
		{
			if ($module != 'core' && !file_exists(THEBUGGENIE_PATH . 'iconsets/' . TBGSettings::getIconsetName() . "/{$module}/" . $image))
			{
				$params['src'] = TBGContext::getTBGPath() . "iconsets/" . TBGSettings::getIconsetName() . "/modules/{$module}/" . $image;
			}
			elseif ($module != 'core')
			{
				$params['src'] = TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . "/{$module}/" . $image;
			}
			else
			{
				$params['src'] = TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/' . $image;
			}
		}
		if (!$relative)
		{
			$params['src'] = TBGContext::getUrlHost() . $params['src'];
		}
		return $params['src'];
	}
	
	/**
	 * Returns an <a> tag linking to a specified url
	 * 
	 * @param string $url link target
	 * @param string $link_text the text displayed in the tag
	 * @param array $params[optional] html parameters
	 * 
	 * @return string
	 */
	function link_tag($url, $link_text = null, $params = array())
	{
		$params['href'] = $url;
		if ($link_text === null) $link_text = $url;
		return "<a " . parseHTMLoptions($params) . ">{$link_text}</a>";
	}
	
	
/**
* Returns an <iframe> tag linking to a specified url
*
* @param string $url link target
* @param string $width width of the frame
* @param string $height height of the frame
*
* @return string
*/
function iframe_tag($url, $width = 500, $height = 400) //Ticket #2308
{
if ($url == null) return;

return '<iframe width="'.$width.'" height="'.$height.'" src="'.$url.'" frameborder="0" allowfullscreen></iframe>';
}


/**
* Returns an <object> tag linking to a specified url
*
* @param string $url link target
* @param string $width width of the frame
* @param string $height height of the frame
*
* @return string
*/
function object_tag($url, $width = 500, $height = 400) //Ticket #2308
{
if ($url == null) return;

return '<object width="'.$width.'" height="'.$height.'">
<param name="movie" value="'.$url.'?hl=en_US&amp;version=3"></param>
<param name="allowFullScreen" value="true"></param>
<param name="allowscriptaccess" value="always"></param>
<embed src="'.$url.'?hl=en_US&amp;version=3" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" allowscriptaccess="always" allowfullscreen="true"></embed>
</object>';
}

	/**
	 * Returns a csrf_token hidden input tag to use in forms
	 *
	 * @return string
	 */
	function csrf_tag()
	{
		return '<input type="hidden" name="csrf_token" value="' . TBGContext::generateCSRFtoken() . '">';
	}

	/**
	 * Return a javascript link tag
	 *
	 * @see link_tag()
	 * 
	 * @param string $link_text the text displayed in the tag
	 * @param array $params[optional] html parameters
	 *
	 * @return string
	 */
	function javascript_link_tag($link_text, $params = array())
	{
		return link_tag('javascript:void(0);', $link_text, $params);
	}
	
	/**
	 * Returns an <input type="image"> tag
	 * 
	 * @param string $image image source
	 * @param array $params[optional] html parameters
	 * @param bool $notheme[optional] whether this is a themed image or a top level path
	 * 
	 * @return string
	 */
	function image_submit_tag($image, $params = array(), $notheme = false)
	{
		$params['src'] = (!$notheme) ? TBGContext::getTBGPath() . 'iconsets/' . TBGSettings::getIconsetName() . '/' . $image : $image;
		return '<input type="image" ' . parseHTMLoptions($params) . ' />';
	}
	
	/**
	 * Includes a template with specified parameters
	 *
	 * @param string	$template	name of template to load, or module/template to load
	 * @param array 	$params  	key => value pairs of parameters for the template
	 */
	function include_template($template, $params = array())
	{
		return TBGActionComponent::includeTemplate($template, $params);
	}

	/**
	 * Return a rendered template with specified parameters
	 *
	 * @param string	$template	name of template to load, or module/template to load
	 * @param array 	$params  	key => value pairs of parameters for the template
	 */
	function get_template_html($template, $params = array())
	{
		return TBGAction::returnTemplateHTML($template, $params);
	}

	/**
	 * Includes a component with specified parameters
	 *
	 * @param string	$component	name of component to load, or module/component to load
	 * @param array 	$params  	key => value pairs of parameters for the template
	 */
	function include_component($component, $params = array())
	{
		return TBGActionComponent::includeComponent($component, $params);
	}

	/**
	 * Return a rendered component with specified parameters
	 *
	 * @param string	$component	name of component to load, or module/component to load
	 * @param array 	$params  	key => value pairs of parameters for the template
	 */
	function get_component_html($component, $params = array())
	{
		return TBGAction::returnComponentHTML($component, $params);
	}

	/**
	 * Generate a url based on a route
	 * 
	 * @param string	$name 	The route key
	 * @param array 	$params	key => value pairs of route parameters
	 * 
	 * @return string
	 */
	function make_url($name, $params = array(), $relative = true)
	{
		return TBGContext::getRouting()->generate($name, $params, $relative);
	}
	
	/**
	 * Returns a string with html options based on an array
	 * 
	 * @param array	$options an array of options
	 * 
	 * @return string
	 */
	function parseHTMLoptions($options)
	{
		$option_strings = array();
		if (!is_array($options))
		{
			throw new Exception('Invalid HTML options. Must be an array with key => value pairs corresponding to html attributes');
		}
		foreach ($options as $key => $val)
		{
			$option_strings[$key] = "{$key}=\"{$val}\"";
		}
		return implode(' ', array_values($option_strings));
	}

    /**
     * Easy way to highlight stuff. Behaves just like highlight_string
     *
     * @param string The code to highlight
     * @param string The language to highlight the code in
     * @param string The path to the language files. You can leave this blank if you need
     *               as from version 1.0.7 the path should be automatically detected
     * @param boolean Whether to return the result or to echo
     * @return string The code highlighted (if $return is true)
     * @since 1.0.2
     */
    function geshi_highlight($string, $language, $path = null, $return = false)
	{
		defined('GESHI_ROOT') || define('GESHI_ROOT', THEBUGGENIE_CORE_PATH . 'geshi' . DS);

        $geshi = new GeSHi($string, $language, $path);
        $geshi->set_header_type(GESHI_HEADER_NONE);

        if ($return)
		{
            return '<code>' . $geshi->parse_code() . '</code>';
        }

        echo '<code>' . $geshi->parse_code() . '</code>';

        if ($geshi->error())
		{
            return false;
        }
        return true;
    }
