<?php
require_once('oci-dbhelper.php');
session_start();

// Checks if the getItem button has been pressed
if (isset($_POST['getItemButton'])) {
    // to check if the Viewing Item object exists, if not we need to create it first in form of session
	if (!isset($_SESSION['viewing'])) {
		$_SESSION['viewing'] = array();
	}
    // Now, we can assume that the viewing exists

    // Retrieves the information entered in the form
	$Supplier = $_POST['selectedSupplier'];
	$Item = $_POST['selectedItemPart'];
	$Channel = $_POST['selectedChannel'];

	$queryToGetViewingItem = "SELECT
	\"A1\".\"SUPPLIER_NAME\"    \"SUPPLIER_NAME\",
	\"A2\".\"SUPPLIER_CODE\"    \"SUPPLIER_CODE\",
	\"A2\".\"PART_NUMBER\"      \"PART_NUMBER\",
	\"A2\".\"CHANNEL\"          \"CHANNEL\",
	\"A2\".\"EFFECTIVE_DATE\"   \"EFFECTIVE_DATE\"
	FROM
	\"SPEND_ANALYSIS_DW\".\"CT_CAPACITY_CELL_PART_DUP\"   \"A2\",
	\"SPEND_ANALYSIS_DW\".\"SUPPLIERS\"                    \"A1\"
	WHERE
	\"A2\".\"SUPPLIER_CODE\" = \"A1\".\"SUPPLIER_CODE\"
	AND \"A1\".\"SUPPLIER_NAME\" = '{$Supplier}'
	AND \"A2\".\"PART_NUMBER\" = '{$Item}'
	AND \"A2\".\"CHANNEL\" = '{$Channel}' ";

	$viewingItem = getOneRow($queryToGetViewingItem);

	if ($viewingItem == 'Error' ){
		header("Location: Main.php?mess=not");
	}
	else {

		//Before adding Item to Viewing, we need to check if item is already in the Viewing session
		$i = 0;
      	foreach ($_SESSION['viewing'] as $thingInsideViewing) {
        	if ($thingInsideViewing['SUPPLIER_NAME'] === $viewingItem['SUPPLIER_NAME'] AND $thingInsideViewing['PART_NUMBER'] === $viewingItem['PART_NUMBER'] AND $thingInsideViewing['CHANNEL'] === $viewingItem['CHANNEL']) {
          		$indexOfExisting = $i;
        	}
        	$i++;
      	}

      	// If the index exists
      	if(isset($indexOfExisting)) {
			header("Location: Main.php?messDuplicate=not");
      	} else {
        // Add the new item into the cart
      		$startingChannel = $viewingItem['CHANNEL'] ; 
      		$viewingItem['TRACKING_CHANNEL'] = $startingChannel;
      		array_push($_SESSION['viewing'], $viewingItem);
			header("Location: Main.php");
      	}
	}
}
?>