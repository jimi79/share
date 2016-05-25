<?php

require "common.php";

function main() {
	global $file_dir;
	$sql = 'select id from data where end_valid < now()';
	$conn = get_conn();
	$query = $conn->prepare($sql);
	$query->execute();
	$sql = 'delete from data where id = ?;';
	$qdel = $conn->prepare($sql);
	while ($line = $query->fetch()) { 
		$id=$line[0];
		unlink($file_dir.$id); 
		$qdel->execute(array($id));


	}
}

main();

?>
