<?php
require_once('oci-dbhelper.php');
	// to logout, simply visit this page
session_start();
session_destroy();

$QueryToDeleteLogs = "DELETE FROM CT_CAPACITY_CELL_PART_CHANGE_LOG";

$result = runQuery($QueryToDeleteLogs);

    // whenever a user log out, delete the duplicate table and re-populate with a copied version of the origial table
$QueryToDeleteCellPartDup = "DELETE FROM CT_CAPACITY_CELL_PART_DUP";

$result1 = runQuery($QueryToDeleteCellPartDup); 


$QueryToRepopulateCellPartDup = "INSERT INTO ct_capacity_cell_part_dup (SUPPLIER_CODE,PART_NUMBER,CHANNEL,PROCESSING_TIME_MINS,SETUP_TIME_MINS,EFFECTIVE_DATE,MOQ)
SELECT SUPPLIER_CODE,PART_NUMBER,CHANNEL,PROCESSING_TIME_MINS,SETUP_TIME_MINS,EFFECTIVE_DATE,MOQ
FROM ct_capacity_cell_part";

$result2 = runQuery($QueryToRepopulateCellPartDup);  

file_put_contents('CheckUser', serialize([
	'check' => "Ready to log in again"
]));

// in this case, when logging out we redirect them back to the home page. It could be whichever page you want.
header('Location: Signin.php?mess6=not');
?>