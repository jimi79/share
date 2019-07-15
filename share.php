<?php

// Preparation :
// i need a DB, with conf/config.php describing the connection id
// inside, i nned a table that is like 
// create table data(id bigserial, data bytea, duration integer, end_valid timestamp, hash varchar(512));

// need php-mysql 
// the help is displayed if we call this php without any parameter
// clear.php can be called manually to clean the db of outdated stuff 
// all files and dir should be readable by www-data

require_once('lib/lib.php');

function append($conn, $duration, $file, $password)
{ 
	// insert
	$conn->query("INSERT INTO data() VALUES()");

	// calculate values
	$id = $conn->lastInsertId('data_id_seq'); 

	if (isset($duration)) {
		$duration = intval($duration);
	} else {
		$duration = 24*60; // one day
	}
	$end = strtotime("+".$duration." minutes");
	$send = date('Y-m-d H:i:s', $end);

	$filename = FILE_DIR.'/'.$id;
	$mime_type = mime_content_type($file['tmp_name']);

	if (!(is_dir(FILE_DIR))) {
		mkdir(FILE_DIR);
	}
	move_uploaded_file($_FILES['data']['tmp_name'], $filename); 
	chmod(FILE_DIR.'/'.$id,0640);

	if (isset($password)) {
		$hash = password_hash($password, PASSWORD_DEFAULT);
	}
	else { $hash=""; } 

	$query = $conn->prepare('UPDATE data SET duration = :duration, filename = :filename, mime_type = :mime_type, end_valid = :end_valid, hash = :hash WHERE id = :id'); 
	$query->bindValue(":duration", $duration, PDO::PARAM_INT);
	$query->bindValue(":filename", $filename, PDO::PARAM_STR);
	$query->bindValue(":mime_type", $mime_type, PDO::PARAM_STR);
	$query->bindValue(":end_valid", $send, PDO::PARAM_STR);
	$query->bindValue(":hash", $hash, PDO::PARAM_STR);
	$query->bindValue(":id", $id, PDO::PARAM_INT);
	$query->execute(); 
	return $id;
}

function get($conn, $id, $password)
{
	$sql = 'SELECT * FROM data WHERE id = :id';
	$query = $conn->prepare($sql);
	$query->bindValue(":id", $id, PDO::PARAM_INT);
	$query->execute();
	$res = $query->fetch();
	if ($res !== null) {
		$err = null; //TODO replace that with DateTime
		$end = strtotime($res['end_valid']);
		$now = time();
		if ($end < $now) {
			$res = NULL;
		} 

		// if we got a password in the record, then we test, otherwise we don't
		$hash = $res['hash'];
		if ($hash != "") {
			if (!password_verify($password, $hash)) {
				$object = NULL;
				$err = "incorrect password";
			}
		} 
	}; 
	if (!isset($res) && (!isset($err))) {
		$err="not found"; }

	if (isset($err)) {
		return $err."\n"; }
	else { 
		header(sprintf('Content-Type: %s', $res['mime_type']));
		//header(sprintf('Content-Disposition: attachment; filename=%s', $res['filename']));
		$filename = $res['filename'];
		return file_get_contents($filename);
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
	print("  To upload somethg with a limited duration, in minutes\n");
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
init_if_needed($conn);
clear($conn);

if (isset($_GET['id'])) {  // fetching something
	$password = '';
	if (isset($_GET['password'])) {
		$password = $_GET['password']; }
	print(get($conn, $_GET['id'], $password));
}
else { // storing something
	$duration = Null;
	if (isset($_POST['duration'])) {
		$duration = $_POST['duration']; }

	$password = Null;
	if (isset($_POST['password'])) {
		$password = $_POST['password']; } 
	if (isset($_FILES['data'])) { 
		$id = append($conn, $duration, $_FILES['data'], $password);
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
