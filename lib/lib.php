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

$tablename = 'data';

function init($conn) { 
	global $tablename;
	$sql = sprintf('create table %s(id integer auto_increment, duration integer, end_valid timestamp, hash varchar(512), primary key(id));', $tablename);
	$query = $conn->prepare($sql);
	$query->execute(); 
	error_log('share initialized database');
} 

function init_if_needed($conn) {
	global $tablename;
	$sql = "show tables like ?;";
	$query = $conn->prepare($sql);
	$query->execute(array($tablename));
	$count = $query->rowCount();
	if ($count == 0) {
		init($conn);
	} 
}

?>
