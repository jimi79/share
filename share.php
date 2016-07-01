<?php
////////// ok, i should store the file as a file, not as blob.
// the postgre db is useful to get a uniq ID, and some timestamp and hash stuff
// so, if i append, i just store the file
// if get, i output the file
// if clear, then i select, and remove the concerned file, before removing the record

// except that it is dangerous. well in fact, i should copy that into another dir than 'html', to be sure ppl won't upload a php and then run it

// todo : limit per ip 

// need php5-pgsql 

// usage : date|curl -F "password=blah" -F "duration=1" -F data=@- http://localhost/share/share.php

// ressources https://wiki.php.net/rfc/password_hash 

// db structure : table data(id bigserial, data bytea, duration integer, end_valid timestamp, hash varchar(512));

require "common.php";

function append($conn, $duration, $password)
{
	if (isset($duration)) {
		$duration = intval($duration);
	} else {
		$duration = 24*60; // one day
	}
	// we calculate the end
	$end = strtotime("+".$duration." minutes");
	$send = date('Y-m-d H:i:s', $end);

	if (isset($password)) {
		$hash = password_hash($password, PASSWORD_DEFAULT);
	}
	else { $hash=""; } 
	$sql = 'insert into data(duration, end_valid, hash) values(?, ?, ?);';
	$params = array($duration, $send, $hash);
	$query = $conn->prepare($sql);
	$query->execute($params); 
	return ($conn->lastInsertId('data_id_seq')); 
}

function get($conn, $id, $password)
{
	$sql = 'select id, end_valid, hash from data where id = ?';
	$query = $conn->prepare($sql);
	$query->execute([$id]);
	$line = $query->fetch();
	if ($line !== null) {
		$err=null; 
		$end=strtotime($line[1]);
		$now=time();
		if ($end < $now) {
			$line = NULL;
		} 

		// if we got a password in the record, then we test, otherwise we don't
		$hash = $line[2];
		if ($hash != "") {
			if (!password_verify($password, $hash)) {
				$object = NULL;
				$err = "incorrect password";
			}
		} 
	}; 
	if (!isset($line) && (!isset($err))) {
		$err="not found"; }

	global $file_dir;
	if (isset($err)) {
		return $err."\n"; }
	else { 
		return file_get_contents($file_dir.$line[0]);
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

function page_url() { 
	$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	if (($_SERVER["SERVER_PORT"] != "80") and ($_SERVER["SERVER_PORT"] != "443"))
	{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} 
	else 
	{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL; 
}

function print_help() {

	print("Syntax :\n");
	print("\n");
	print("  To upload somethg\n");
	print("somethg | curl -F data=@- ".page_url()."\n");
	print("\n");
	print("Will return the url to reach the posted data\n");
	print("\n");
	print("  To upload somethg with a limited duration\n");
	print("somethg | curl -F data=@- -F 'duration=1' ".page_url()." \n");
	print("\n");
	print("Will return the url to reach the posted data. After the duration written, the posted element won't be available\n");
	print("\n");
	print("  To upload somethg with a password\n");
	print("somethg | curl -F data=@- -F 'password=foobar' ".page_url()."\n");
	print("\n");
	print("Will return the url to reach the posted data, and the parameter password ready to be filled.\n");
	print("\n");
print("Notes :\n");
	print("  - you can have duration and password\n");
	print("\n");
	print("\n");
	print("  To download somethg\n");
	print("\n");
	print("curl [url given when uploading]\n");
	print("\n");
	print("If the url has a password parameter, you have to fill it with the password used to upload the element.\n"); 
	print("\n");
	print("Notes :\n");
	print("- if the password is wrong, the message 'incorrect password' will be returned\n");
	print("- if the element doesn't exists anymore, or never existed, the message 'not found' will be returned\n"); 
	print("\n"); 
}

$conn=get_conn();
if (isset($_GET['id'])) { 
	$password = '';
	if (isset($_GET['password'])) {
		$password = $_GET['password']; }
	print(get($conn, $_GET['id'], $password));
}
else {
	$duration = Null;
	if (isset($_POST['duration'])) {
		$duration = $_POST['duration']; }

	$password = Null;
	if (isset($_POST['password'])) {
		$password = $_POST['password']; } 
	if (isset($_FILES['data'])) {
		#$id=append($conn, file_get_contents($_FILES['data']['tmp_name']), $duration, $password);
		$id=append($conn, $duration, $password); 
		# now we store the file	
		move_uploaded_file($_FILES['data']['tmp_name'], $file_dir.$id); 
		if (isset($_POST['password'])) {
			print(page_url().'?id='.$id."&password="."\n");
		}
		else {
			print(page_url().'?id='.$id."\n");
		}
	}
	else {
		print_help();
	}
} 
?>
