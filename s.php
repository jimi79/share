<?php
require_once('lib/lib.php');
require_once('lib/init.php');

$id = $_GET['id'];
$url = get_url($conn, $id);
if ($url === "") {
	print("Wrong id.");
} else {
	$url = add_https_if_needed($url);
	header(sprintf("Location: %s", $url));
}

