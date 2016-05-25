<?php

// need php5-mongo and apache restart




// usage : date|curl -F "password=blah" -F "duration=1" -F data=@- http://localhost/share/share.php

// ressources https://wiki.php.net/rfc/password_hash 


function init()
{
	$m = new MongoClient();
	$db = $m->share;
	$coll = $db->main;
	return $coll;
}


function append($coll, $data, $duration, $password)
{
	if (isset($duration)) {
		$duration = intval($duration);
	} else {
		$duration = 60; // one hour
	}
	// we calculate the end
	$end = strtotime("+".$duration." minutes");

	if (isset($password)) {
		$hash = password_hash($password, PASSWORD_DEFAULT);
	}
	else { $hash=""; } 

	$data=array("data"=>$data, "duration"=>$duration, "end"=>$end, "hash"=>$hash);
	$coll->insert($data);
	return $data['_id'];
}

function get($coll, $id, $password)
{
	$object=$coll->findOne(array("_id" => new MongoId($id))); 
	// is it outdated ?

	$err=null;
	if (isset($object)) { 
		$end=$object['end'];
		$now=time();
		if ($end < $now) {
			$object = NULL;
		} 

		// if we got a password in the record, then we test, otherwise we don't
		$hash = $object['hash'];
		if ($hash != "") {
			if (!password_verify($password, $hash)) {
				$object = NULL;
				$err = "incorrect password";
			}
		} 
	}; 
	if (!isset($object) && (!isset($err))) {
		$err="not found"; }

	if (isset($err)) {
		return $err."\n"; }
	else { 
		$res=$object['data'];
		return $res;
	}
}


function debug()
{
	// récupère tout de la collection
	$cursor = $coll->find();

	// traverse les résultats
	foreach ($cursor as $document) {
				echo $document["id"] . "\n";
	} 
} 


function pageUrl() { 
	$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	if ($_SERVER["SERVER_PORT"] != "80")
	{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} 
	else 
	{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL; 
}


print("Activate mongodb first !");

$coll=init();
if (isset($_GET['id'])) { 
	$password = '';
	if (isset($_GET['password'])) {
		$password = $_GET['password']; }
	print(get($coll, $_GET['id'], $password));
}
else {
	// get ip authorized with _POST
	if (isset($_FILES['data'])) {
		$id=append($coll, file_get_contents($_FILES['data']['tmp_name']), $_POST['duration'], $_POST['password']);
		if (isset($_POST['password'])) {
			print(pageUrl().'?id='.$id."&password="."\n");
		}
		else {
			print(pageUrl().'?id='.$id."\n");
		}
	}
	else {
		print("Error\n");
	}
}


?>
