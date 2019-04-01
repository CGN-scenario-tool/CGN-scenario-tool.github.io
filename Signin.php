<?php
session_start();

require_once('oci-dbhelper.php');

if (isset($_SESSION['Email'])){
  header('Location: index.php?logInAlready=not');
}

// TESTING
// $user = unserialize(file_get_contents('CheckUser'));
// var_dump($user['check']);

// Check if User hit the signin Button
if (isset($_POST['signinButton'])) {

  $EmailFromForm = $_POST['inputEmail'];
  $PasswordFromForm = $_POST['inputPassword'];

  $queryLogin = "SELECT
  \"A1\".\"EMAIL\"      \"EMAIL\",
  \"A1\".\"PASSWORD\"   \"PASSWORD\"
  FROM
  \"SPEND_ANALYSIS_DW\".\"CT_CAPACITY_CELL_PART_LOGIN\" \"A1\"
  WHERE
  \"A1\".\"EMAIL\" = '{$EmailFromForm}'";

  // runs the query and returns the one record. We know there should only be one person with a given username
  $account = getOneRow($queryLogin);

  if ($account != 'Error' AND $account['EMAIL'] == $EmailFromForm AND $account['PASSWORD'] == $PasswordFromForm) {
    // they are authenticated store the session variable to log them in
    $_SESSION['Email'] = $EmailFromForm;

    //ALSO HAVE SO REPOPULATE THE COPIED TABLE HERE!!!!!! (do it later!)

    // redirect them to the homepage
    header('Location: index.php?mess1=not');
  }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="https://www.cgnglobal.com/hubfs/favicon_cgn.png">

    <title>CGN | Scheduling Tool</title>

    <!-- Bootstrap core CSS -->
    <!-- <link href="../../dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="signin.css" rel="stylesheet">

    <!-- Jquery -->
    <script
      src="https://code.jquery.com/jquery-3.3.1.js"
      integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
      crossorigin="anonymous">   
    </script>

    <!-- bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  </head>

  <body class="text-center">
    <?php
    // var_dump($_SESSION['Email']);
    // var_dump($_SESSION['LAST_ACTIVITY']);
    ?> 

    <!-- LogIn Please Message -->
    <div class="modal fade" id="logInPlsMess" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">HOLD ON!</h5>
                </div>
                <div class="modal-body">
                    Hey! You have to sign in first...  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_GET['mess3'])) {
        echo "<script type=\"text/javascript\">
              $(function() {
                $('#logInPlsMess').modal('show');
              });
          </script>";
    }
    ?>

    <!-- Logout Message -->
    <div class="modal fade" id="welcomeMess" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">YOU ARE LOGGED OUT!</h5>
                </div>
                <div class="modal-body">
                    All set! See you next time!    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_GET['mess6'])) {
        echo "<script type=\"text/javascript\">
              $(function() {
                $('#welcomeMess').modal('show');
              });
          </script>";
    }
    ?>

    <!-- Inactivity Message -->
    <div class="modal fade" id="InactiveMess" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">YOU ARE LOGGED OUT!</h5>
                </div>
                <div class="modal-body">
                    You are logged out due to 45 mins of inactivity 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_GET['messInactive'])) {
        echo "<script type=\"text/javascript\">
              $(function() {
                $('#InactiveMess').modal('show');
              });
          </script>";
    }
    ?>

  	<form class="form-signin" action="Signin.php" method="POST" >
  		<img class="mb-5" src="CGN-logo.png" alt="" style="width: 70%">
  		<h1  align="center" class="mb-5" style="color: #2a2665">Welcome to Scheduling Tool Webpage!</h1>
  		<h1 class="h3 mb-3 font-weight-normal lightblue">Please sign in</h1>
  		<label for="inputEmail" class="sr-only">Email address</label>
  		<input type="email" name="inputEmail" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
  		<label for="inputPassword" class="sr-only">Password</<label for=""></label>
  		<input type="password" name="inputPassword" id="inputPassword" class="form-control mb-3" placeholder="Password" required>
				<!--       <div class="checkbox mb-3">
				        <label>
				          <input type="checkbox" value="remember-me" class="whitetext"> Remember me
				        </label>
				    </div> -->
		  <button class="btn btn-lg btn-info btn-block" type="submit" name="signinButton">Sign in</button>
		  <p class="mt-5 mb-3 text-muted">&copy; 2018-2019</p>
    </form>

<!--     <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Stop!</strong> Seems like you are not in our database...
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
    </div> --><!-- data-toggle="modal" data-target="#myModal" -->

    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Stop!</h5>
          </div>
          <div class="modal-body">
            Seems like you're not in our database!       
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Stop!</h5>
          </div>
          <div class="modal-body">
            Invalid Password...     
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

<?php
if (isset($_POST['signinButton'])) {
  if ($account == 'Error'){ //There is nothing return or more than 1 row returns
    echo "<script type=\"text/javascript\">
              $(function() {
                $('#myModal1').modal('show');
              });
          </script>";
    // echo $result;
    // echo $EmailFromForm;
    // echo $PasswordFromForm;
  }
  else {
        echo "<script type=\"text/javascript\">
              $(function() {
                $('#myModal2').modal('show');
              });
            </script>";
  }
}
?>
  </body>
</html>
</html>