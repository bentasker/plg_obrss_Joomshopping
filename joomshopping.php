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
		$cats_query = '';
		$descfield = 'short_description_en-GB';

		if (isset($_REQUEST['x'])) {
			print_r($itemCf);
			die;
		}

		# categories
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


		if (!$itemCf->Description){
		  $descfield = 'description_en-GB';
		}
	/*	
		# categories
		$cats 	= $itemCf->categories;
		if (!is_array($cats)) {
			$cats = array($cats);
		}
		if (!in_array('',$cats)) {
			$cats = implode(',',$cats);
			$qryCats	= " AND b.catid IN ($cats)";
		} else {
			$qryCats	= '';
		}
		

#__jshopping_products_to_categories AS c

*/
		$sql = "SELECT a.product_id as id, a.`name_en-GB` as title, a.`$descfield` as `desc`,".
			" c.category_id as catid FROM #__jshopping_products AS a ".
			"LEFT JOIN #__jshopping_products_to_categories AS c ".
			"ON a.product_id = c.product_id ".
			"WHERE a.product_publish='1' ".
			$cats_query .
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
		return 'index.php?option=com_jshopping&controller=product&task=view&category_id='. $row->catid."&product_id=".$row->id;
	}
	
	function getDesc($row,$itemCf)
	{
		$desc = '';
		$desc .= '<a href="'.$this->getLink($row).'"><img src="'.$this->getImage($row,$itemCf).'" /></a><br />';
		$desc .= $row->desc;
		return $desc;
	}
	
	function getImage($row,$itemCf) {
		$params = json_decode($row->params);
		#var_dump($row->params);
		#echo 'Kha ko dep trai:';
		#var_dump($params);
		#exit();
		return $params->imageurl;#exit();
	}
}