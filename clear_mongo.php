<?php


function init()
{
	$m = new MongoClient();
	$db = $m->share;
	$coll = $db->main;
	return $coll;
}

$query=array('end' => array('$lt'=>time()));
init()->remove($query); 


?>
