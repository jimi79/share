<html>
<body>
<?php
require_once('lib/lib.php');
require_once('lib/init.php');

function print_link($link) {
	printf("<a href='%s' target='_blank'>%s</a>", $link, $link);
}


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
	print_link(page_url_download($id, isset($password)));
} 

?>
</body>
</html>
