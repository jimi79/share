<?php
// create table data(id bigserial, data bytea, duration integer, end_valid timestamp, hash varchar(512));
require_once('conf/config.php');
require_once('lib/lib.php');

function init($conn) { 
	$sql = 'create table data(id integer auto_increment, duration integer, end_valid timestamp, hash varchar(512), primary key(id));';
	$query = $conn->prepare($sql);
	$query->execute(); 
}

//init(get_conn());
print("Database initialized\n");

?>
