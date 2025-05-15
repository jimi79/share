<html>
<body>
<?php
require_once('lib/lib.php');
require_once('lib/init.php');

$url = $_POST['url'];

$id = put_url($conn, $url);

print_link(page_url_shortener($id));
