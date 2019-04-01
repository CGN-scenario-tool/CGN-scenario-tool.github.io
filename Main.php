<?php 
session_start();
require_once('oci-dbhelper.php');

if (!isset($_SESSION['Email'])) {
    //Kick them our if they are not logged in
    header('Location: Signin.php?mess3=not');
    die();
}

$access = true;

//If the file does not exist, create one, and set the 'check' to user's IP. 
// If another user tries to log in, and their IP will be different from 'check' => Access Denied
//If you user logs out, set 'check' to "Ready to log in again"

if (file_exists('CheckUser')) {
    $user = unserialize(file_get_contents('CheckUser'));
    if ($user['check'] != "Ready to log in again" AND $_SERVER['REMOTE_ADDR'] != $user['check']) {
        $access = false;
    }
}

if (!$access) {
    exit('Access denied. Someone else is using the tool');
} else {
    file_put_contents('CheckUser', serialize([
        'check' => $_SERVER['REMOTE_ADDR']
    ]));
}

// Testing
// var_dump($user['check']);

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 2700)) {
    // last request was more than 45 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
    header('Location: Signin.php?messInactive=not');
    file_put_contents('CheckUser', serialize([
        'check' => "Ready to log in again"
    ]));
    die();
}

$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

// OLD VERSION WITH CHANGES LOGS
// if (!isset($_SESSION['LogId'])) {
//     $_SESSION['LogId'] = array();
// }

if (!isset($_SESSION['viewing'])) {
    $_SESSION['viewing'] = array();
}

if (isset($_GET['mess'])) {
	// Show a warning error pop up
	echo "<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">
	<strong>Oh no!</strong> There are more than 1 row return or there is nothing return at all! Check the database for duplicate...
	<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
	<span aria-hidden=\"true\">&times;</span>
	</button>
	</div>";
}

if (isset($_GET['messDuplicate'])) {
    // Show a warning error pop up
    echo "<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">
    The Part Item is already in your viewing!
    <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
    <span aria-hidden=\"true\">&times;</span>
    </button>
    </div>";
}

// PHP code to always populate the Supplier seletion box when this page is load
$queryToGetUniqueSuppliers = "SELECT DISTINCT
	    \"A1\".\"SUPPLIER_NAME\" \"SUPPLIER_NAME\"
	FROM
	    \"SPEND_ANALYSIS_DW\".\"CT_CAPACITY_CELL_PART_DUP\"   \"A2\",
	    \"SPEND_ANALYSIS_DW\".\"SUPPLIERS\"                    \"A1\"
	WHERE
	    \"A1\".\"SUPPLIER_CODE\" = \"A2\".\"SUPPLIER_CODE\"";

$stmtOfUniqueSupplierList = runQuery($queryToGetUniqueSuppliers);
?>


<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="https://www.cgnglobal.com/hubfs/favicon_cgn.png">

	<title>CGN | Scenario Tool</title>

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<!-- Custom styles for this template -->
	<link href="Main.css" rel="stylesheet">

	<!-- Jquery -->
	<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
	<script
		src="https://code.jquery.com/jquery-3.3.1.js"
		integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
		crossorigin="anonymous">   
	</script>

	<!-- select2 -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

	<!-- datepicker
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" /> -->

    <!-- datepicker -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.standalone.min.css" rel="stylesheet"/>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>

    <!-- bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <!-- bootstrap 2 -->
    <!--<script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js"></script>
    <script src="https://getbootstrap.com/docs/4.0/dist/js/bootstrap.min.js"></script>
    <script src="https://getbootstrap.com/docs/4.0/assets/js/vendor/holder.min.js"></script> -->

    <!-- Script of Tableau Viz -->

   <script type="text/javascript" src="https://bi.cgnglobal.com/javascripts/api/tableau-2.2.2.min.js"></script>
    
    <script type="text/javascript">
        function initViz() {
            var containerDiv = document.getElementById("vizContainer"),
                url = "https://bi.cgnglobal.com/views/FEICapacityAnalysis-TESTV2/Dashboard3?iframeSizedToWindow=true&:embed=y&:showAppBanner=false&:display_count=no&:showVizHome=no&:refresh=yes",
                options = {
                    onFirstInteractive: function () {
                        console.log("Run this code when the viz has finished loading.");
                    }
                };
            
            // Create a viz object and embed it in the container div.
            var viz = new tableau.Viz(containerDiv, url, options); 
        }
    </script>

    

</head>

<body onload="initViz();" class="bg-light">
    <!-- TESTING -->
    <?php
    // var_dump($_SESSION['Email']);
    // var_dump($_SESSION['LAST_ACTIVITY']);
    ?>

    <!-- Welcome Message -->
    <div class="modal fade" id="welcomeMess" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">WELCOME!</h5>
                </div>
                <div class="modal-body">
                    Welcome back <?php echo $_SESSION['Email']; ?>!    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_GET['mess1'])) {
        echo "<script type=\"text/javascript\">
              $(function() {
                $('#welcomeMess').modal('show');
              });
          </script>";
    }
    ?>

    <!-- Welcome Again Message/already logged In -->
    <div class="modal fade" id="welcomeAgainMess" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">HI AGAIN!!!</h5>
                </div>
                <div class="modal-body">
                    YOU ARE STILL LOGGED IN!    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_GET['logInAlready'])) {
        echo "<script type=\"text/javascript\">
              $(function() {
                $('#welcomeAgainMess').modal('show');
              });
          </script>";
    }
    ?>

<!-- PAGE STARTS HERE -->
    <div class="container">
    	<!-- Description of the page and Logo  -->
    	<div class="py-5 text-center">
    		<img class="d-block mx-auto mb-2" src="./CGN-logo.png" style="width: 20%"  alt="">
    		<h2>Scheduling Tool</h2>
    		<p class="lead">Description of how the tool works goes here!</p>
    	</div>
    	<!-- END of Description of the page and Logo  -->
  		
  		<!-- Div with class row to create 1 ROW for all page, then 2 COLS in this row, Bootstrap syntax  -->
    	<div class="row">
    		<!-- Get items/Items Viewing (COL1) -->
    		<div class="col-md-7 order-md-1 mb-4">

    			<!-- Start of the Get Item part -->
    			<h4 class="d-flex justify-content-between align-items-center mb-3">
    				<span>Get Part Item</span>
    			</h4>

    			<!-- UserForm with Supplier, Item, Channel Option Boxes and Get Item Button -->
    			<form class="needs-validation" novalidate action="getItem-helper.php" method="POST">

		    		<!-- Supplier options box ROW -->
		            <div class="row-md-6 mb-2">
		            	<div>
		            		<label for="selectedSupplier">Supplier</label>

		            		<select class="js-example-basic-single custom-select d-block w-100 " name="selectedSupplier" id="selectedSupplier" required onChange="displaceItem(this.value)" style="display:inline-block;width:100%;height:calc(2.25rem + 2px);padding:.375rem 1.75rem .375rem .75rem;line-height:1.5;color:#495057;vertical-align:middle;background:#fff url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E") no-repeat right .75rem center;background-size:8px 10px;border:1px solid #ced4da;border-radius:.25rem;-webkit-appearance:none;-moz-appearance:none;appearance:none}"> <!-- as we click throught the options, displaceItem function runs -->
		            			<option value="">Choose...</option>
		            			<!-- while the fetch is still return True, populate the options of the Supplier field of the Get Items form    -->
		            			<?php 
		            			while (($Supplier = oci_fetch_array($stmtOfUniqueSupplierList, OCI_BOTH)) != false) {          
		            				echo "<option value=\"{$Supplier['SUPPLIER_NAME']}\">{$Supplier['SUPPLIER_NAME']}</option>";
		                      		// In a loop, freeing the large variable before the 2nd fetch reduces PHP's peak memory usage
		            				unset($Supplier);
		            			}
		            			?>
		            		</select>

			            	<div class="invalid-feedback">
			            		Valid Supplier required.
			            	</div>
		            	</div>
		        	</div>
		        	<!-- END OF Supplier options box ROW -->

		            <!-- Part number options box ROW -->
		            <div class="row-md-6 mb-2">
		              	<div>
		                	<label for="selectedItemPart">Item Part Number</label>

		                	<select class="js-example-basic-single custom-select d-block w-100" name="selectedItemPart" id="selectedItemPart" required required onChange="displaceChannel(this.value)" style="display:inline-block;width:100%;height:calc(2.25rem + 2px);padding:.375rem 1.75rem .375rem .75rem;line-height:1.5;color:#495057;vertical-align:middle;background:#fff url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E") no-repeat right .75rem center;background-size:8px 10px;border:1px solid #ced4da;border-radius:.25rem;-webkit-appearance:none;-moz-appearance:none;appearance:none}">
		                  		<option value="">Choose...</option>
		                	</select>

			                <div class="invalid-feedback">
			                  Valid Item Part Number is required.
			                </div>
		              </div>
		            </div>
					<!-- END OF Part number options box ROW -->

					<!-- Channel options box ROW -->
		            <div class="row-md-6 mb-3">
		              	<div>
		                	<label for="selectedChannel">Channel</label>

		                	<select class="js-example-basic-single custom-select d-block w-100" name="selectedChannel" id="selectedChannel" required style="display:inline-block;width:100%;height:calc(2.25rem + 2px);padding:.375rem 1.75rem .375rem .75rem;line-height:1.5;color:#495057;vertical-align:middle;background:#fff url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E") no-repeat right .75rem center;background-size:8px 10px;border:1px solid #ced4da;border-radius:.25rem;-webkit-appearance:none;-moz-appearance:none;appearance:none}">
		                  		<option value="">Choose...</option>
		                	</select>

			                <div class="invalid-feedback">
			                  Valid Channel is required.
			                </div>
		              	</div>
		            </div>
		            <!-- END OF Channel options box ROW -->

		            <!-- Get Item Button -->
		            <div class="card p-2 mb-3" class="input-group">
		              <button type="submit" class="btn btn-secondary btn-block" name="getItemButton" value="">Get Item</button>
		            </div>
		            <!-- END OF Get Item Button -->
        		</form>
        		<!-- END OF User Form -->

          		<!-- END OF Get Item part -->

	          	<!-- Start of Viewing Part (same col with get Item Part) -->

          		<h4 class="d-flex justify-content-between align-items-center mb-3">
	            	<span>Building Scenario</span>
	            	<!-- Number of items -->
	           		<span class="badge badge-secondary badge-pill" id="sessionQuantity">
		            	<?php
		            	if (!isset($_SESSION['viewing']) OR empty($_SESSION['viewing'])) {
		            		echo "0";
		            	}
		            	else {
		            		$count = count($_SESSION['viewing']);
		            		echo $count;
		            	}
		            	?>
	            	</span> 
          		</h4>


            	<!-- begin of Viewing items SESSION -->
                <?php
                	// For testing
                  	// $_SESSION['viewing'] = array();
                  	// unset($_SESSION['viewing']);

                	//var_dump($_SESSION['viewing']);
                 	//  	echo "</br> </br></br> Break </br></br></br>";
                 	//  	var_dump($viewingItem);
                  
                // if there is no item in Viewing Session, dont even show the submit button
                if (!isset($_SESSION['viewing']) OR empty($_SESSION['viewing'])) {
                	echo '<ul class="list-group mb-3">';
                	echo '<li class="list-group-item d-flex justify-content-between lh-condensed">';
                	echo '<p>There is no Part Item to build... Please add some!</p>';
                	echo '</ul>';
                	echo '</li>';
                }
                else {?>
                    <div id="viewingHeight" style="max-height: 370px; overflow-y: auto; background-color: white;">
                	<ul class="list-group">
                		<li class="list-group-item d-flex justify-content-between lh-condensed">
                			<div class="table-responsive">
                				<table class="table table-bordered table-hover table-sm">
                					<thead>
                						<tr class="table-active">
                							<th scope="col">Item Part Number</th>
                							<th scope="col">Supplier Name</th>
                							<th scope="col" style="width: 100px">Current Channel</th>
                							<th scope="col" style="width: 100px">Effective Date</th>
                							<th scope="col" style="width: 200px">Edit Channel & Date</th>
                						</tr>
                					</thead>

                					<tbody>
                						<?php
                						$i = 0;
                                        $x = count($_SESSION['viewing']);
                						foreach ($_SESSION['viewing'] as $ItemInViewing) {
                							echo "<tr class=\"table-light\">";
                							echo "<th scope=\"row\">{$ItemInViewing['PART_NUMBER']}";
                							?>
<!--                 							<form action="removeItem-helper.php" method="POST"> 
                								<button name="removeButton" value="<?php echo $i; ?>" type="submit" class="btn btn-secondary btn-sm mt-4">Remove</button>
                							</form> -->
                							<?php	
                							echo "</th>";

                							echo "<td>{$ItemInViewing['SUPPLIER_NAME']}</td>";
                							echo "<td>{$ItemInViewing['CHANNEL']}</td>";
					                            // Query to get all the possible channel of a corresponding Supplier
                							$queryToGetAllPossibleChannel = "SELECT DISTINCT
                							\"A2\".\"CHANNEL\" \"CHANNEL\"
                							FROM
                							\"SPEND_ANALYSIS_DW\".\"CT_CAPACITY_CELL_PART_DUP\"   \"A2\",
                							\"SPEND_ANALYSIS_DW\".\"SUPPLIERS\"                    \"A1\"
                							WHERE
                							\"A2\".\"SUPPLIER_CODE\" = \"A1\".\"SUPPLIER_CODE\"
                							AND \"A1\".\"SUPPLIER_NAME\" = '{$ItemInViewing['SUPPLIER_NAME']}'
                							AND \"A2\".\"CHANNEL\" !='{$ItemInViewing['CHANNEL']}'
                                            AND \"A2\".\"CHANNEL\" <> ALL (
                                                SELECT DISTINCT
                                                    \"A4\".\"CHANNEL\" \"CHANNEL\"
                                                FROM
                                                    \"SPEND_ANALYSIS_DW\".\"CT_CAPACITY_CELL_PART_DUP\"   \"A4\",
                                                    \"SPEND_ANALYSIS_DW\".\"SUPPLIERS\"                    \"A3\"
                                                WHERE
                                                    \"A4\".\"SUPPLIER_CODE\" = \"A3\".\"SUPPLIER_CODE\"
                                                    AND \"A3\".\"SUPPLIER_NAME\" = '{$ItemInViewing['SUPPLIER_NAME']}'
                                                    AND \"A4\".\"PART_NUMBER\" = '{$ItemInViewing['PART_NUMBER']}'
                                            )
                							ORDER BY
                							\"A2\".\"CHANNEL\"";
                                            echo "<td>{$ItemInViewing['EFFECTIVE_DATE']}</td>";
                							echo "<td><select class=\"custom-select\" name=\"{$i}\" id=\"{$i}\">";
                							echo "<option value=\"{$ItemInViewing['CHANNEL']}\">{$ItemInViewing['CHANNEL']}</option>"; //The first one is the existing channel

                							$stmtOfAllPossibleChannel = runQuery($queryToGetAllPossibleChannel);
                							while (($PossibleChannel = oci_fetch_array($stmtOfAllPossibleChannel, OCI_BOTH)) != false) {
                								echo "<option value=\"{$PossibleChannel['CHANNEL']}\">{$PossibleChannel['CHANNEL']}</option>";
                							}	
                							echo "</select>";
                                            echo "<div class=\"input-group date mt-1\">
                                                    <input type=\"text\" class=\"form-control\" class=\"datepicker\" id=\"datepicker{$x}\" placeholder=\"{$ItemInViewing['EFFECTIVE_DATE']}\" value=\"{$ItemInViewing['EFFECTIVE_DATE']}\">
                                                </div>";
                                            echo "</td>";
                							echo "</tr>";
                							$i++;
                                            $x++;
                						}
                						?> 
                					</tbody>
                				</table>
                			</div>
                		</li>
                	</ul>
                    </div>
                	<!-- Key in the Password and Submit button -->
                	<form id ="submitScenarioForm">
                        <div class="card p-2 mt-3">
    	                	<button type="submit" id="submitButton" name ="submitButton" class="btn btn-secondary btn-block">Submit Scenario</button>
                    	</div>
                    </form>
                <?php } ?>
                <!-- closing bracket of the else statement -->
                <!-- End of Viewing items SESSION -->
                <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                	<div class="modal-dialog" role="document">
                		<div class="modal-content">
                			<div class="modal-header">
                				<h5 class="modal-title" id="exampleModalLabel">Success!</h5>
                                <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                					<span aria-hidden="true">&times;</span>
                				</button> -->
                			</div>
                			<div class="modal-body">
                				All the changes have been recorded!
                			</div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                            </div>
                		</div>
                	</div>
                </div>

                <div class="modal fade" id="nothingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Wait a minute!</h5>
                            </div>
                            <div class="modal-body">
                                You did not change anything in this scenario!
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="test1"></div>
			</div>
    		<!-- END of Get items/Items Viewing BOX (COL1) -->

        	<!--Scenario Log BOXE (COL2) -->
        	<div class="col-md-5 order-md-2 mb-4">
        		<h4 class="d-flex justify-content-between align-items-center mb-3">
        			<span>Scenario Logs</span>
        		</h4>
                
                <div id="changLogHeight" style="background-color: white;">
            		<ul class="list-group">
                        <?php
                        // Querry to get all the Scenario Logs in the DB Change_log

                        $queryToGetAllLogs = "SELECT * from CT_CAPACITY_CELL_PART_CHANGE_LOG";
                        $stmtToGetAllLogs = runQuery($queryToGetAllLogs);
                        $nrow = oci_fetch_all($stmtToGetAllLogs, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

                        // If there is nothing returned
                        if ($nrow == 0) {
                            echo "<li class=\"list-group-item d-flex justify-content-between lh-condensed\">";
                            echo "There is no scenario log yet";
                            echo "</li>";
                            echo "</ul>"; 
                        }

                        else {
                            //Get Logs of each item in the Viewing in both change_log and change_log_detail
                            foreach ($_SESSION['viewing'] as $ItemInViewing) {
                                $queryToGetLogDetailOfItem = "SELECT
                                \"A2\".\"SUPPLIER_CODE\"        \"SUPPLIER_CODE\",
                                \"A2\".\"PART_NUMBER\"          \"PART_NUMBER\",
                                \"A2\".\"OLD_CHANNEL\"          \"OLD_CHANNEL\",
                                \"A2\".\"NEW_CHANNEL\"          \"NEW_CHANNEL\",
                                \"A2\".\"OLD_EFFECTIVE_DATE\"   \"OLD_EFFECTIVE_DATE\",
                                \"A2\".\"SUPPLIER_NAME\"        \"SUPPLIER_NAME\",
                                \"A2\".\"NEW_EFFECTIVE_DATE\"   \"NEW_EFFECTIVE_DATE\",
                                \"A1\".\"LOG_TIMESTAMP\"        \"LOG_TIMESTAMP\",
                                \"A2\".\"TRACKING_CHANNEL\"     \"TRACKING_CHANNEL\"
                                FROM
                                \"SPEND_ANALYSIS_DW\".\"CT_CAPACITY_CELL_PART_CHANGE_LOG_DETAIL\"   \"A2\",
                                \"SPEND_ANALYSIS_DW\".\"CT_CAPACITY_CELL_PART_CHANGE_LOG\"          \"A1\"
                                WHERE
                                \"A2\".\"LOG_ID\" = \"A1\".\"LOG_ID\"
                                AND \"A2\".\"PART_NUMBER\" = '{$ItemInViewing['PART_NUMBER']}'
                                AND \"A2\".\"SUPPLIER_NAME\" = '{$ItemInViewing['SUPPLIER_NAME']}'
                                AND \"A2\".\"TRACKING_CHANNEL\" = '{$ItemInViewing['TRACKING_CHANNEL']}'
                                ORDER BY
                                \"A1\".\"LOG_TIMESTAMP\"";

                                //prepare the statment        
                                $stmtToGetNumberOfLog = runQuery($queryToGetLogDetailOfItem);
                                $nrows = oci_fetch_all($stmtToGetNumberOfLog, $resultArrayofLogs, null, null, OCI_FETCHSTATEMENT_BY_ROW);
                                //var_dump($resultArrayofLogs);
                                if ($nrows != 0){
                                ?>
                                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                                        <div class="table-responsive">
                                            <h6 class="d-flex justify-content-between align-items-center mb-3">
                                                <span><?php echo $ItemInViewing['PART_NUMBER'] ?> | <?php echo $ItemInViewing['SUPPLIER_NAME'] ?></span>
                                                <!-- Number of items -->
                                                <span class="badge badge-secondary badge-pill"><?php echo $nrows ?> </span> 
                                            </h6>

                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover table-sm table-sm">
                                                    <thead>
                                                        <tr class="table-active">
                                                            <th>Timestamp</th>
                                                            <th>Channel</th>
                                                            <th>Effective Date</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <tr>
                                                            <th></th>
                                                            <th><?php echo $ItemInViewing['TRACKING_CHANNEL'] ?></th>
                                                            <th><?php echo $resultArrayofLogs[0]['OLD_EFFECTIVE_DATE'] ?></th>
                                                        </tr>
                                                        <?php
                                                        $stmtToGetLogDetailOfItem = runQuery($queryToGetLogDetailOfItem);
                                                        while (($itemLogDetail = oci_fetch_array($stmtToGetLogDetailOfItem, OCI_BOTH)) != false) {
                                                            ?>
                                                            <tr>
                                                                <th><?php echo $itemLogDetail['LOG_TIMESTAMP'] ?></th>
                                                                <?php
                                                                    if($itemLogDetail['NEW_CHANNEL'] == $itemLogDetail['OLD_CHANNEL']){
                                                                        echo "<td> SAME </td>";
                                                                    }
                                                                    else {
                                                                        echo "<td> {$itemLogDetail['NEW_CHANNEL']} </td>";
                                                                    }
                                                                ?>
                                                                <?php
                                                                    if($itemLogDetail['NEW_EFFECTIVE_DATE'] == $itemLogDetail['OLD_EFFECTIVE_DATE']){
                                                                        echo "<td> SAME </td>";
                                                                    }
                                                                    else {
                                                                        echo "<td> {$itemLogDetail['NEW_EFFECTIVE_DATE']} </td>";
                                                                    }
                                                                ?>
                                                            </tr>
                                                        <?php    
                                                        }
                                                        ?>
                                                        <!-- end of while statement -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </li>   
                                <?php
                                }   
                            }
                        }
                        ?>
                    </ul>
                </div>

                <div class="card p-2 mt-3" class="input-group">
                    <a class="btn btn-secondary btn-block" href="logout.php" role="button" id="LogOutButton">Log Out</a>
                    <!-- <button type="submit" class="btn btn-secondary btn-block" href="logout.php">Log Out</button> -->
                </div>
        	</div>
        	<!-- END OF Scenario Logs BOX (COL2) -->
        </div>
        <!-- END OF div class row  -->
        
        <!-- TESTING -->
        <div id="test"></div>

        <!-- Div row and col for Tableau VIZ -->
<!--         <div class="row">
            <div class="col"> -->
                <h4>
                    <span>Tableau Dashboard</span>
                </h4>
                <div id="vizContainer" class='mt-3'></div>
<!--             </div>
        </div> --> 

    </div>
      

                    
                
    <!-- END OF div class container  -->

        <!-- TABLEAU -->
<!--         <script type='text/javascript' src='https://bi.cgnglobal.com/javascripts/api/viz_v1.js'></script>
                <div class='tableauPlaceholder' style='width: 950px; height: 950px;'>
                    <object class='tableauViz' width='1920' height='950' style='display:none;'>
                        <param name='host_url' value='https%3A%2F%2Fbi.cgnglobal.com%2F' />
                        <param name='embed_code_version' value='3' />
                        <param name='site_root' value='' />
                        <param name='name' value='FEICapacityAnalysis&#47;CapacityAnalysisPcs' />
                        <param name='tabs' value='yes' /><param name='toolbar' value='yes' />
                        <param name='showAppBanner' value='false' />
                        <param name='filter' value='iframeSizedToWindow=true' />
                    </object>
                </div> 
        </div> 
 -->
<!--         <script type='text/javascript' src='https://bi.cgnglobal.com/javascripts/api/viz_v1.js'></script>
        <div class='tableauPlaceholder' style='width: 1536px; height: 604px;'>
            <object class='tableauViz' width='1536' height='604' style='display:none;'>
                <param name='host_url' value='https%3A%2F%2Fbi.cgnglobal.com%2F' /> 
                <param name='embed_code_version' value='3' /> 
                <param name='site_root' value='' />
                <param name='name' value='ProductionPlanningTool&#47;Channel' />
                <param name='tabs' value='yes' />
                <param name='toolbar' value='yes' />
                <param name='showAppBanner' value='false' />
                <param name='filter' value='iframeSizedToWindow=false' />
            </object>
        </div>   -->

        <!-- <script type='text/javascript' src='https://bi.cgnglobal.com/javascripts/api/viz_v1.js'></script>
            <div class='tableauPlaceholder' style='width: 1536px; height: 604px;'>
                <object class='tableauViz' width='1536' height='604' style='display:none;'>
                    <param name='host_url' value='https%3A%2F%2Fbi.cgnglobal.com%2F' /> 
                    <param name='embed_code_version' value='3' /> 
                    <param name='site_root' value='' />
                    <param name='name' value='TEST&#47;Sheet1' />
                    <param name='tabs' value='no' />
                    <param name='toolbar' value='yes' />
                    <param name='showAppBanner' value='false' />
                    <param name='filter' value='iframeSizedToWindow=true' />
                </object>
            </div> -->
        <!-- End of div class container -->


    

    <footer class="my-5 pt-5 text-muted text-center text-small">
    	<p class="mb-1">&copy; 2018-2019 CGN Global</p>
    	<ul class="list-inline">
    		<li class="list-inline-item"><a href="#">Privacy</a></li>
    		<li class="list-inline-item"><a href="#">Terms</a></li>
    		<li class="list-inline-item"><a href="#">Support</a></li>
    	</ul>
    </footer>

    <!-- script to run functions to update the item part options box according to the selectedSupplier -->
    <script type="text/javascript">
      function displaceItem(value){
        $.ajax ({ 
          type: "GET",
          url: "getItemPart-helper.php",
          data: {selectedSupplier: value},
          success: function(result) {
            $("#selectedItemPart").html(result);
          }
        });
      };
    </script>

    <!-- script to run functions to update the channel options box according to the selectedSupplier and selectedItemPart -->
    <script type="text/javascript">
      function displaceChannel(value){
        
        $.ajax ({ 
          type: "GET",
          url: "getChannel-helper.php",
          data: {selectedItemPart: value, selectedSupplier: $("#selectedSupplier").val(),},
          success: function(result) {
            $("#selectedChannel").html(result);
          }
        });
      };
    </script>

    <!-- Script for select 2 -->
    <script type="text/javascript">
      $(document).ready(function() {
        $('.js-example-basic-single').select2();
      });
    </script>

    <!-- Script for updating viewing -->
    <script type="text/javascript">
    	$( document ).ready(function() {
    		$( "#submitScenarioForm" ).submit(function(e) {
    			e.preventDefault();
    			var data = {};
    			var i;
                var x;
    			for (i = 0; i < $("#sessionQuantity").text(); i++) {
    				data[i] = $("#" + i).val();	
    			};

                for (x = parseInt($("#sessionQuantity").text()); x < (2*$("#sessionQuantity").text()); x++) {
                    data[x] = $("#datepicker" + x).val(); 
                };
    			$.ajax({
    				type: "POST",
    				url: "updateViewing-helper.php",
    				// traditional: true,
    				// processData: false,
    				data: data,
    				success: function(data) {
                        // console.log(data);
                        //if return success aka 1, then show the modal, then refesh the page
                        if ($.trim(data) === '1'){
                            //$('#test').html(data);
                            $('#successModal').modal('show');
                            $('#successModal').on('hidden.bs.modal', function (e) {
                               location.reload();
                            })
                        }
                        else {
                            //$('#test').html(data);
                            $('#nothingModal').modal('show');
                        }
    				}
    			});
    		});
    	});	
    </script>

    <!-- Script for Datepicker  -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('[id^=datepicker]').datepicker({
                format: "dd-M-yy",
                startDate: "yesterday",
                todayBtn: true,
                autoclose: true,
                todayHighlight: true
            });
        });
    </script>

    <!-- Script for Responsive height of Scenario Logs Box  -->
    <script type="text/javascript">
        $(function() {
            var i;
            i = $('#viewingHeight').height();
            $( "#changLogHeight" ).css({
                "max-height": i + 328,
                "overflow-y": "auto"
            });
        });
    </script>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
    <!-- <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script> -->
 
    
    <script>
      // Example starter JavaScript for disabling form submissions if there are invalid fields
      (function() {
        'use strict';

        window.addEventListener('load', function() {
          // Fetch all the forms we want to apply custom Bootstrap validation styles to
          var forms = document.getElementsByClassName('needs-validation');

          // Loop over them and prevent submission
          var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
              if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
              }
              form.classList.add('was-validated');
            }, false);
          });
        }, false);
      })();
    </script>

  </body>
</html>
