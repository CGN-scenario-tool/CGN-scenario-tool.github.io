<?php
require_once('oci-dbhelper.php');

if (isset($_GET['selectedSupplier'])) {
  if ($_GET['selectedSupplier'] != ""){

    $queryToGetUniqueItems = "SELECT DISTINCT
    \"A1\".\"PART_NUMBER\" \"PART_NUMBER\"
    FROM
    \"SPEND_ANALYSIS_DW\".\"CT_CAPACITY_CELL_PART_DUP\" \"A1\",
    \"SPEND_ANALYSIS_DW\".\"SUPPLIERS\"                    \"A2\"
    WHERE
    \"A2\".\"SUPPLIER_NAME\" = '{$_GET['selectedSupplier']}' 
    AND \"A1\".\"SUPPLIER_CODE\" = \"A2\".\"SUPPLIER_CODE\"";
    
    $stmtOfUniqueItemsList = runQuery($queryToGetUniqueItems);

    echo "<option>Choose...</option>";

    while (($Item = oci_fetch_array($stmtOfUniqueItemsList, OCI_BOTH)) != false ) { 
    //while the fetch is still return True, populate the options of the Item field of the Get Items form corresponding with the selectedSupplier
    	echo "<option value=\"{$Item['PART_NUMBER']}\">{$Item['PART_NUMBER']}</option>";
        // In a loop, freeing the large variable before the 2nd fetch reduces PHP's peak memory usage
        unset($Item);
    }
  }
}
?>