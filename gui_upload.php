<?php
require_once('lib/lib.php');
require_once('lib/init.php');

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
	if (isset($_POST['password'])) {
		print(dirname(page_url()).'/share.php?id='.$id."&password=");
	}
	else {
		print(dirname(page_url()).'/share.php?id='.$id);
	}
} 

?>
