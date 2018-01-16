<?php 

require_once("conf/config.php");

function get_conn()
{ 
	$conn = new PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $conn;
} 

function clear($conn) {
	$sql = 'select id from data where end_valid < now()';
	$conn = get_conn();
	$query = $conn->prepare($sql);
	$query->execute();
	$sql = 'delete from data where id = ?;';
	$qdel = $conn->prepare($sql);
	while ($line = $query->fetch()) { 
		$id=$line[0];
		unlink(FILE_DIR.'/'.$id); 
		$qdel->execute(array($id)); 
	}
}

?>
