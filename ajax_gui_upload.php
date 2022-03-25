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
	$id = put($conn, $duration, $_FILES['data'], $password);
	print(page_url_download($id, isset($password)));
} 

?>
