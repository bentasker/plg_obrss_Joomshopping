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
		
		# clients
		$clients 	= $itemCf->clients;
		if (!is_array($clients)) {
			$clients = array($clients);
		}
		if (!in_array('',$clients)) {
			$clients = implode(',',$clients);
			$qryClients	= " AND b.cid IN ($clients)";
		} else {
			$qryClients	= '';
		}
		
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
		
		$qry = "
			SELECT
				b.`id` AS id, b.`name` as title, 
				b.`params` AS `params`,
				b.`description` as `desc`, 
				UNIX_TIMESTAMP(b.`created`) AS s4rss_created  
			FROM
				`#__banners` AS b
			WHERE
				b.`state` = 1
				$qryClients
				$qryCats
			LIMIT $itemCf->limit
		";
		$db->setQuery($qry);
		$rows = $db->loadObjectList();
		if (isset($_REQUEST['x'])) {
			print_r($rows);#exit($qry);
		}
		return $rows;
	}
	
	function getLink($row)
	{
		return 'index.php?option=com_banners&task=click&id='. $row->id;
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