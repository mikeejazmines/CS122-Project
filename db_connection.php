<?php
	/*
	 * Connects to the jamaldb database.
	 */
	
	$host = "localhost";
	$dbname = "jamaldb";
	$root = 3306;
	$user = "root";
	$pass = "";
	
	try {
		$db = new PDO("mysql:host=" . $host . ";dbname=" . $dbname . ";root=" . $root, $user, $pass);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch (Exception $e) {
		echo "Unable to connect";
		exit;
	}
?>