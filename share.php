<?php


function init()
{
	$m = new MongoClient();
	$db = $m->share;
	$coll = $db->main;
	return $coll;
}

function append($data)
{
	$data=array("data"=>$data);
	init()->insert($data);
	return $data['_id'];
}

function get($id)
{
	$object=init()->findOne(array("_id" => new MongoId($id)));
	$res=$object['data'];
	return $res;
}


function test()
{
	// récupère tout de la collection
	$cursor = $coll->find();

	// traverse les résultats
	foreach ($cursor as $document) {
				echo $document["id"] . "\n";
	}

} 

// requires php5-mongo and apache2 restart

if (isset($_GET['id'])) {
	print(get($_GET['id']));
}
else {
	// get ip authorized with _POST
	if (isset($_FILES['data'])) {
		$id=append(file_get_contents($_FILES['data']['tmp_name']));
		print("https://jimi79.hd.free.fr:5443/share/share.php?id=$id\n");
	}
	else {
		print("Error\n");
	}
}


// TODO

// if somethg gets too old, remove it. there is a default timeout of 24h. you can change it withg a form value of timeout, in minutes.
// a crontab php will remove all these records, with a find (how ?)



?>
