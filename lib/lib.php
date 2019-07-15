<?php 

require_once("conf/config.php");

function get_conn()
{ 
	$conn = new PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $conn;
} 

function clear($conn) {
	$sql = 'SELECT id, filename FROM data WHERE end_valid < NOW()';
	$query = $conn->prepare($sql);
	$query->execute();
	$qdel = $conn->prepare('DELETE FROM data WHERE id = :id?');
	$qdel = $conn->prepare($sql);
	while ($res = $query->fetch()) { 
		$id = $res['id'];
		$qdel->bindValue(":id", $id, PDO::PARAM_INT);
		if (file_exists($res['filename'])) {
			unlink($res['filename']); 
		}
		$qdel->execute(); 
	}
}

function init($conn) { 
	$sql = sprintf('CREATE TABLE data(id INTEGER AUTO_INCREMENT, filename VARCHAR(200), mime_type VARCHAR(200), duration INTEGER, end_valid TIMESTAMP, hash VARCHAR(512), PRIMARY KEY(id));');
	$query = $conn->prepare($sql);
	$query->execute(); 
	error_log('share initialized database');
} 

function init_if_needed($conn) {
	global $tablename;
	$sql = "show tables;";
	$query = $conn->prepare($sql);
	$query->execute(array($tablename));
	$count = $query->rowCount();
	if ($count == 0) {
		init($conn);
	} 
}

?>
