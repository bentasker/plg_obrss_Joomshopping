<?php
/**
 * @created: July 2013. 
 * @copyright	(C) 2013 B Tasker. All rights reserved.
 * @author: B Tasker.
 * @license	GNU/GPL, see LICENSE
 */
defined('_JEXEC') or die();

class addonRss_joomshopping
{
	function getItems($itemCf)
	{
		$db = &JFactory::getDBO();
		$categories = array();
		$manufacturers = array();
		$labels = array();
		$cats_query = '';
		$manu_query = '';
		$label_query = '';
		$descfield = 'short_description_en-GB';

		if (isset($_REQUEST['x'])) {
			print_r($itemCf);
			die;
		}

		// Categories
		$categs = $itemCf->categories;
		if (!is_array($categs) && !empty($categs)) {
			$categories[] = $categs;
		}elseif(count($categs) > 0){
			foreach ($categs as $c){
			  $categories[] = $c;
			}
		}

		if (count($categories) > 0){
		  $cats_query = " AND c.category_id IN (" . implode(",",$categories) .")"; 
		}

		// Manufacturers
		$manus = $itemCf->manufacturers;

		if (!is_array($manus) && !empty($manus)) {
			$manufacturers[] = $categs;
		}elseif(count($manus) > 0){
			foreach ($manus as $c){
			  $manufacturers[] = $c;
			}
		}

		if (count($manufacturers) > 0){
		  $manu_query = " AND a.product_manufacturer_id IN (" . implode(",",$manufacturers) .")"; 
		}


		// Labels
		$label = $itemCf->label;

		if (!is_array($label) && !empty($label)) {
			$labels[] = $label;
		}elseif(count($label) > 0){
			foreach ($label as $c){
			  $labels[] = $c;
			}
		}

		if (count($labels) > 0){
		  $label_query = " AND a.label_id IN (" . implode(",",$labels) .")"; 
		}



		// Which description are we showing?
		if (!$itemCf->Description){
		  $descfield = 'description_en-GB';
		}


		$sql = "SELECT a.product_id as id, a.`name_en-GB` as title, a.`product_price` AS price, a.`$descfield` as `desc`, i.`image_name` as `image`,".
			" c.category_id as catid, m.`name_en-GB` AS manufacturer ".
			" FROM #__jshopping_products AS a ".
			"LEFT JOIN #__jshopping_products_to_categories AS c ".
			"ON a.product_id = c.product_id ".
			"LEFT JOIN #__jshopping_manufacturers AS m ".
			"ON a.product_manufacturer_id = m.manufacturer_id ".
			"LEFT JOIN #__jshopping_products_images AS i ".
			"ON a.product_id = i.product_id " .
			"WHERE a.product_publish='1' AND ( i.ordering=1 OR i.ordering IS NULL ) ".
			$cats_query . $manu_query . $label_query .
			" LIMIT {$itemCf->limit}";
    
		$db->setQuery($sql);
		$rows = $db->loadObjectList();
		if (isset($_REQUEST['x'])) {
			print_r($rows);#exit($qry);
		}
		return $rows;
	}
	
	function getLink($row)
	{
		return JRoute::_('index.php?option=com_jshopping&controller=product&task=view&category_id='. $row->catid."&product_id=".$row->id);
	}
	
	function getDesc($row,$itemCf)
	{
		$desc = '';
		$desc .= '<a href="'.$this->getLink($row).'">';

		if (!empty($row->image)){
		  $desc .= '<img src="'.rtrim(JUri::base(false),"/").'/components/com_jshopping/files/img_products/'.$row->image.'" />';
		}else{
		  $desc .= $row->title;
		}

		$desc .='</a><br />';

		if ($itemCf->ShowPrice && !empty($row->price)){
		  $desc .= "<b>Price:</b> ".money_format('%.2n',$row->price)."<br />";
		}

		if ($itemCf->ShowManu && !empty($row->manufacturer)){
		  $desc .= "<b>Manufacturer:</b> {$row->manufacturer}<br />";
		}

		$desc .= $row->desc;

		if ($itemCf->ViewButton){
		  $desc .= "<br /><a href='".$this->getLink($row) . "'><button>View Product</button></a>";
		}

		if ($itemCf->BuyNow){
		  $desc .= "<br /><a href='".JUri::base(false).ltrim(JRoute::_("index.php?option=com_jshopping&controller=cart&task=add&category_id={$row->catid}&product_id={$row->id}"),"/") . "'><button>Buy Now</button></a>";
		}

		return $desc;
	}
	

}