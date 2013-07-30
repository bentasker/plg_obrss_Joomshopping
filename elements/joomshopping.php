<?php
/**
 * @created: July 2013. 
 * @copyright	(C) 2013 B Tasker. All rights reserved.
 * @author: B Tasker.
 * @license	GNU/GPL, see LICENSE
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.parameter.element');

class JElementJShopCategory extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'JShopCategory';

	function fetchElement($name, $value, &$node, $control_name)
	{
		global $obIsJ15,$obIsJ16,$obIsJ17;
		
			$db	= &JFactory::getDBO();
			$sql	= 'SELECT category_id AS `value`, name_en-GB AS `text` FROM #__jshopping_categories ORDER BY `ordering`';
			$db->setQuery($sql);
			$cats = $db->loadObjectList();
		
		if ($cats) {
			array_unshift($cats, JHTML::_('select.option', '', JText::_('-- All Categories --')));
			$options_size = count($cats) <= 20 ? count($cats) : 20;
			return JHTML::_('select.genericlist',  $cats, ''.$control_name.'['.$name.'][]', 'multiple="true" size="'.$options_size.'"', 'value', 'text', $value, $control_name.$name );			
		} else {			
			return JText::_('OBRSS_ADDON_PARAMS_NO_DATA');
		}
	}
}
