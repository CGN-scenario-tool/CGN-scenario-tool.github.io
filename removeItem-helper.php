<?php
require_once('oci-dbhelper.php');
session_start();


// if the removeButton is clicked
if (isset($_POST['removeButton'])) {
	// the value of the button is the index of the row in the session array
	array_splice($_SESSION['viewing'], $_POST['removeButton'], 1);
	header("Location: index.php");
}

?>