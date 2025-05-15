<?php
require_once('lib/lib.php');
require_once('lib/init.php');

$id = $_GET['id'];
$url = get_url($conn, $id);
$url = add_https_if_needed($url);
if ($url === "") {
	print("Wrong id.");
} else {
	header(sprintf("Location: %s", $url));
}

