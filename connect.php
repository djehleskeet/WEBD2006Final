<?php
	define('DB_DSN' , 'mysql:host=localhost;port=3306;dbname=serverside');
	define('DB_USER' , 'serveruser');
	define('DB_PASS', 'gorgonzola7!');
	
function connect()
{
	try{
		$db = new PDO(DB_DSN, DB_USER, DB_PASS);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $db;
	}catch (PDOException $e){
		print "Error: " . $e->getMessage();
		die(); 
	}
}
?>