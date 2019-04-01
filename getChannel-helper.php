<?php
require_once('oci-dbhelper.php');

if (isset($_GET['selectedItemPart'])) {

  if ($_GET['selectedItemPart'] != ""){

    $queryToGetUniqueChannels = "SELECT DISTINCT
          \"A2\".\"CHANNEL\" \"CHANNEL\"
      FROM
          \"SPEND_ANALYSIS_DW\".\"CT_CAPACITY_CELL_PART_DUP\"   \"A2\",
          \"SPEND_ANALYSIS_DW\".\"SUPPLIERS\"                    \"A1\"
      WHERE
          \"A2\".\"SUPPLIER_CODE\" = \"A1\".\"SUPPLIER_CODE\" 
          AND \"A1\".\"SUPPLIER_NAME\" = '{$_GET['selectedSupplier']}'
          AND \"A2\".\"PART_NUMBER\" = '{$_GET['selectedItemPart']}' ";
    
    $stmtOfUniqueChannelList = runQuery($queryToGetUniqueChannels);

    echo "<option>Choose...</option>";

    while (($Channel = oci_fetch_array($stmtOfUniqueChannelList, OCI_BOTH)) != false ) { 
    //while the fetch is still return True, populate the options of the Item field of the Get Items form corresponding with the selectedSupplier
    	echo "<option value=\"{$Channel['CHANNEL']}\"> {$Channel['CHANNEL']} </option>";
      // In a loop, freeing the large variable before the 2nd fetch reduces PHP's peak memory usage
      unset($Channel);
    }
  }
}
?>