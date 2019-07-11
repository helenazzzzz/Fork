<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: login.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
    <title>Fork | Reset Password</title>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <!--Links to CSS files-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type='text/css' href="base.css">
    <link rel="stylesheet" type='text/css' href="reset.css">

    
    <script>
                function openmenu(category) {
                if (document.getElementById(category).style.display == "none"){
                    document.getElementById(category).style.display = "block";
                } 
                else {
                    document.getElementById(category).style.display = "none";
                }
            }
            
            
            // Open and close hamburger menu on click
            function myFunction() {
              var x = document.getElementById("hamburger");
              if (x.style.width < "10px") {
                 document.getElementById("hamburger").style.width = "250px";
                document.getElementById("main").style.marginLeft = "275px";
                // x.style.display = "none";
              } else {
                // x.style.display = "block";
                document.getElementById("hamburger").style.width = "0px";
                document.getElementById("main").style.marginLeft = "25px";
              }
            }
        </script>
</head>
<body>
    <!--Links to other pages in hamburger menu-->
    <div id="hamburger">
            <br/>
            <br/>
            <a href="about.html">about</a>
            <a href="user_uploads.php">my uploads</a>
            <a href="repo.php">favorites</a>
            <a href="map.html">map</a>
            <a href="reset_password.php">reset password</a>
            <a href="logout.php" style="color: #FCF8E8;">sign out</a>
        </div>
        <!--Logo in header-->
        <div id="main">
        <div class="page-header">
            <i class="fa fa-bars" onclick="myFunction()"></i>
            <a href="welcome.php"><img src="fork-mint-cream.png" height="40" alt="logo in mint"></a>
            </div>
        </div>
    <div class="wrapper">
        <!--Reset password form checks input boxes are complete-->
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control txtbox" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control txtbox">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="submit">
                <a class="btn btn-default" href="welcome.php">cancel</a>
            </div>
        </form>
    </div>
    
    <!--Contact information in footer-->
    <footer> <!--contact us!-->
    <p><br/>&#169; Fork 2019<br/>Email us with questions, comments, and concerns! <a href="mailto:seliu@ctemc.org">customerservice@fork.org</a></p>
    </footer>
</body>
</html>