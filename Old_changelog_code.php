<?php

<!-- OLD VERSION WITH CHANGES LOGS -->
<ul class="list-group">
<?php

if (!isset($_SESSION['LogId']) OR empty($_SESSION['LogId'])){
	echo "<li class=\"list-group-item d-flex justify-content-between lh-condensed\">";
	echo "Let's build a scenario!";
	echo "</li>";
	echo "</ul>"; 
}

foreach ($_SESSION['LogId'] as $LogIdSession) {
	$valueOfReversetButton = 0;
	$queryToGetLogGeneralInfo = "SELECT * FROM ct_capacity_cell_part_change_log WHERE LOG_ID = '{$LogIdSession}' ORDER BY LOG_TIMESTAMP DESC";
	$stmtToGetLogGeneralInfo = runQuery($queryToGetLogGeneralInfo);
	$stmtToGetLogGeneralInfo = oci_fetch_all($stmtToGetLogGeneralInfo, $LOGINFO, null, null, OCI_FETCHSTATEMENT_BY_ROW);

                        //TESTING, IGNORE
	var_dump($_SESSION['LogId']);
                        // echo "<p>break</p>";
                        // echo "<p>{$LOGINFO[0]['LOG_TIMESTAMP']}</p>";
                        // var_dump($LogIdSession);
                        // echo "<p>break</p>";
                        // var_dump($LOGINFO);
                        // echo "<p>break</p>";

	$queryToGetLogDetail = "SELECT * FROM ct_capacity_cell_part_change_log_detail 
	WHERE LOG_ID = '{$LogIdSession}'";
	$stmtToGetLogDetailRow = runQuery($queryToGetLogDetail);
	$rows = oci_fetch_all($stmtToGetLogDetailRow, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
	?>

	<li class="list-group-item d-flex justify-content-between lh-condensed">
		<div class="table-responsive">
			<h6 class="d-flex justify-content-between align-items-center mb-3">
				<span>Timestamp | <?php echo $LOGINFO[0]['LOG_TIMESTAMP'] ?></span>
				<!-- Number of items -->
				<span class="badge badge-secondary badge-pill"><?php echo $rows ?> </span> 
			</h6>

			<div class="table-responsive">
				<table class="table table-bordered table-hover table-sm">
					<thead>
						<tr class="table-active">
							<th>Items Part Number</th>
							<th>Supplier Name</th>
							<th>Old Channel</th>
							<th>New Channel</th>
							<th>Old Effective Date</th>
							<th>New Effective Date</th>
						</tr>
					</thead>
					<?php
					$stmtToGetLogDetail = runQuery($queryToGetLogDetail);
					while (($logDetail = oci_fetch_array($stmtToGetLogDetail, OCI_BOTH)) != false) { 
						?>
						<tbody>
							<tr class="table-light">
								<th scope="row"> <?php echo $logDetail['PART_NUMBER'] ?> </th>
								<td><?php echo $logDetail['SUPPLIER_NAME'] ?></td>
								<td><?php echo $logDetail['OLD_CHANNEL'] ?></td>
								<?php
								if($logDetail['NEW_CHANNEL'] == $logDetail['OLD_CHANNEL']){
									echo "<td> SAME </td>";
								}
								else {
									echo "<td> {$logDetail['NEW_CHANNEL']} </td>";
								}
								?>
								<td> <?php echo $logDetail['OLD_EFFECTIVE_DATE'] ?></td>
								
								<?php
								if($logDetail['NEW_EFFECTIVE_DATE'] == $logDetail['OLD_EFFECTIVE_DATE']){
									echo "<td> SAME </td>";
								}
								else {
									echo "<td> {$logDetail['NEW_EFFECTIVE_DATE']} </td>";
								}
								?>
							</tr>
						</tbody>
						<?php
					}; 
					?>
				</table>
				<form action="removeLog-helper.php" method="POST"> 
					<button name="removeLogButton" value="<?php echo $i; ?>" type="submit" class="btn btn-secondary btn-sm mt-4">Remove</button>
				</form>
			</div>
		</div>
	</li>
	<?php    
}
?>
</ul>

?>