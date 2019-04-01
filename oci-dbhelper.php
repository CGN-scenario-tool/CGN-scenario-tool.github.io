<?php

// Used to connect to the database -- DO NOT CALL THIS FUNCTION OUTSIDE OF THIS FILE
error_reporting(E_ALL);
ini_set('display_errors', 'On');

function makeTheConnection() {

	// FILL IN YOUR DATABASE NAME - CONNECTION STRING (ORACLE)
	$db = "//129.213.89.92:1521/cgnpdb.sub10081939060.vcncgnglobal.oraclevcn.com";  // and the connect string to connect to your database

	// ENTER YOUR INFORMATION FOR LOGGING IN
	$user = "spend_analysis_dw";
	$pwd = "spend_DW_2019";
	
	// UNCOMMENT THIS IF YOU ARE USING SQL SERVER
	// $connection = "dblib:host=mssqllab.rhsmith.umd.edu:9407;dbname=$db";

	// UNCOMMENT THIS IF YOU ARE USING MYSQL
	//$connection = "mysql:host=bmgt407.rhsmith.umd.edu;dbname=".$db; 


	
	$GLOBALS['conn']  = oci_connect($user, $pwd, $db);

		// COMMENT OUT THE LINE BELOW IF YOU ARE USING SQL SERVER
		//$GLOBALS['conn']->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	if (!$GLOBALS['conn']) {
    	$e = oci_error();   // For oci_connect errors do not pass a handle
    	trigger_error(htmlentities($e['message']), E_USER_ERROR);
	}
	else {
		return $GLOBALS['conn'];
	}
}

// DO NOT MODIFY ANY OF THE FUNCTIONS BELOW

// Run the query and return a prepared statement
function runQuery($query) {
	if (!isset($GLOBALS['conn'])) {
		$GLOBALS['conn'] = makeTheConnection();
	}

	$stmt = oci_parse($GLOBALS['conn'], $query);
	if (!$stmt) {
    	$e = oci_error($GLOBALS['conn']);  // For oci_parse errors, pass the connection handle
    	trigger_error(htmlentities($e['message']), E_USER_ERROR);
	}
	else {
		$r = oci_execute($stmt);
		if (!$r) {
    		$e = oci_error($stmt);  // For oci_execute errors, pass the statement handle
		    print htmlentities($e['message']);
		    print "\n<pre>\n";
		    print htmlentities($e['sqltext']);
		    printf("\n%".($e['offset']+1)."s", "^");
		    print  "\n</pre>\n";
		}
		return $stmt;
	}
}

//For SELECT query that returns ONLY ONE row
function getOneRow($query) {
	if (!isset($GLOBALS['conn'])) {
		$GLOBALS['conn'] = makeTheConnection();
	}

	$stmt = oci_parse($GLOBALS['conn'], $query);
	if (!$stmt) {
    	$e = oci_error($GLOBALS['conn']);  // For oci_parse errors, pass the connection handle
    	trigger_error(htmlentities($e['message']), E_USER_ERROR);
    }
    else {
    	$r = oci_execute($stmt);
    	if (!$r) {
    		$e = oci_error($stmt);  // For oci_execute errors, pass the statement handle
    		print htmlentities($e['message']);
    		print "\n<pre>\n";
    		print htmlentities($e['sqltext']);
    		printf("\n%".($e['offset']+1)."s", "^");
    		print  "\n</pre>\n";
    	}
    	$nrows = oci_fetch_all($stmt, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    	if ($nrows == 1) {
    		return $result[0];
    	}
    	else {
    		return 'Error';
    	}
    }
}
?>



