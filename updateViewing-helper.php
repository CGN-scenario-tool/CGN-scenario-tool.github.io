<?php
require_once('oci-dbhelper.php');
session_start();


if (isset($_POST['0'])){  

// Dummy variable to check if there is anthing to changes in the database, cause user didnt make any new changes
$checkSame = "";

//Generate a randome Log ID
$uniqueLogID = uniqid();
    		//do {

    	 	//    $queryToGetAllUniqueLogID = "SELECT LOG_ID FROM ct_capacity_cell_part_change_log WHERE LOG_ID ='{$uniqueLogID}'";
    	 	//    $result = getOneRow($queryToGetAllUniqueLogID);
    	 	//  	} while ($result = 'Error');

$x = count($_SESSION['viewing']);
for ($i = 0; $i < count($_SESSION['viewing']); $i++) {
  if ($_POST[$i] != $_SESSION['viewing'][$i]['CHANNEL'] OR $_POST[$x] != $_SESSION['viewing'][$i]['EFFECTIVE_DATE']){
    $checkSame = 'Change True';
    // if this If Statment is running, there is something to change in the database. Thus, set the $checkSame to 'Change True'
  }
  $x++;
}

if ($checkSame == 'Change True') {
  // Only $checkSame is set, then Put the log in database
  // Insert a new log to the Change_Log table
  $queryToUpdateLog = "INSERT INTO ct_capacity_cell_part_change_log(LOG_TIMESTAMP, LOG_ID, USER_EMAIL)
  VALUES ( TO_CHAR(CURRENT_TIMESTAMP, 'HH24:MI:SS'), '{$uniqueLogID}', '{$_SESSION['Email']}')";
  $stmtToUpdateLog = runquery($queryToUpdateLog);

  // After all the queries above are executed successfully, add a new log into SESSION array ['LogId'] to update the Chang Log Box (code of that is in Main.php)
  //array_unshift($_SESSION['LogId'], $uniqueLogID);
}

$x = count($_SESSION['viewing']);
for ($i = 0; $i < count($_SESSION['viewing']); $i++) {

	// this is when user click on the calendar thing but dont pick any date, that action set the default value of the SELECTION back "", and also means that they do not want to change the date
  if ($_POST[$x] == ""){
    $_POST[$x] = $_SESSION['viewing'][$i]['EFFECTIVE_DATE'];
  }

  if ($_POST[$i] != $_SESSION['viewing'][$i]['CHANNEL'] OR $_POST[$x] != $_SESSION['viewing'][$i]['EFFECTIVE_DATE']){
    
    // Update the CELL_PART_TEST database with new or same Channel/Date and the logID
    $queryToUpdateChannelDB = "UPDATE CT_CAPACITY_CELL_PART_DUP
    SET CHANNEL = '$_POST[$i]', EFFECTIVE_DATE = '$_POST[$x]'
    WHERE SUPPLIER_CODE ='{$_SESSION['viewing'][$i]['SUPPLIER_CODE']}' AND PART_NUMBER = '{$_SESSION['viewing'][$i]['PART_NUMBER']}' AND CHANNEL = '{$_SESSION['viewing'][$i]['CHANNEL']}'";
    $stmtOfUpdateChannelBD = runquery($queryToUpdateChannelDB);

    // Insert Log in to Change_Log_Detail
    $queryToUpdateLogDetail = "INSERT INTO ct_capacity_cell_part_change_log_detail(LOG_ID, SUPPLIER_CODE, SUPPLIER_NAME, PART_NUMBER, OLD_CHANNEL, NEW_CHANNEL,OLD_EFFECTIVE_DATE, NEW_EFFECTIVE_DATE, TRACKING_CHANNEL)
    VALUES ('{$uniqueLogID}','{$_SESSION['viewing'][$i]['SUPPLIER_CODE']}','{$_SESSION['viewing'][$i]['SUPPLIER_NAME']}','{$_SESSION['viewing'][$i]['PART_NUMBER']}','{$_SESSION['viewing'][$i]['CHANNEL']}','$_POST[$i]','{$_SESSION['viewing'][$i]['EFFECTIVE_DATE']}','$_POST[$x]','{$_SESSION['viewing'][$i]['TRACKING_CHANNEL']}') ";
    $stmtToUpdateLogDetail = runquery($queryToUpdateLogDetail);

    //Update the viewing with new or same Channe/Date
    $_SESSION['viewing'][$i]['CHANNEL'] = ($_POST[$i]);
    $_SESSION['viewing'][$i]['EFFECTIVE_DATE'] = ($_POST[$x]);
  }
  $x++;
}

if ($checkSame == 'Change True'){
  echo 1; //there is a change at least
}
else {
  echo 2; //nothing to change
}

// TESTING
// echo var_dump($_POST);
// echo var_dump($_SESSION['LogId']);
}
?>