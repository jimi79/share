<?php

// Preparation :
// i need a DB, with conf/config.php describing the connection id

// need php-mysql 
// the help is displayed if we call this php without any parameter
// clear.php can be called manually to clean the db of outdated stuff 
// all files and dir should be readable by www-data

require_once('lib/lib.php');
require_once('lib/init.php');

function debug()
{
	// récupère tout de la collection
	$cursor = $coll->find();

	// traverse les résultats
	foreach ($cursor as $document) {
				echo $document["id"] . "\n";
	} 
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
	print("  To upload somethg with the gui\n");
	printf("go to %s/gui.php", (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']));
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

if (isset($_GET['id'])) {
	// fetching something
	$password = '';
	if (isset($_GET['password'])) {
		$password = $_GET['password']; }
	print(get($conn, $_GET['id'], $password));
}
elseif (isset($_FILES['data'])) {
	// storing something
	$duration = Null;
	if (isset($_POST['duration'])) {
		if ($_POST['duration'] != '') {
		$duration = $_POST['duration'];
		}
	}

	$password = Null;
	if (isset($_POST['password'])) {
		if ($_POST['password'] != '') {
			$password = $_POST['password'];
		}
	} 
	if (isset($_FILES['data'])) { 
		$id = append($conn, $duration, $_FILES['data'], $password);
		if (isset($password)) {
			adaptive_print(page_url().'?id='.$id."&password="."\n");
		}
		else {
			adaptive_print(page_url().'?id='.$id."\n");
		}
	}
}
else {
	print_help();
}
?>
