<!--Project Name: Fork-->
<!--Team Members: Selena Liu, Helena Zhang, Sophie Zhang-->
<!--Date: 6/7/19-->
<!--Task Description: Intuitive website for users to upload, filter, and save recipes-->
    
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

if(isset($_POST['view'])){
    $_SESSION['recipe_id'] = $_POST['view'];
    header('location: view_recipe.php');
};

// Delete recipe record from users_favorites table if button is pressed
if(isset($_POST['delete'])){
$DeleteQuery = "delete FROM users_favorites WHERE recipe_id= '" . $_POST['delete'] . "'";   
mysqli_query($link, $DeleteQuery);
if(!mysqli_query($link,$DeleteQuery)){
        echo(mysqli_error($link));
}else{
    // Confirmation message
    echo "<script type='text/javascript'>alert('Success! Recipe deleted from your favorites.');</script>";
}
};

// Select fields from the users_favorites table that correspond to the current user
$sql = "SELECT recipes.recipe_id, recipes.recipe_name, recipes.recipe_category, recipes.recipe_image
        FROM recipes
        INNER JOIN users_favorites
        ON users_favorites.recipe_id = recipes.recipe_id
        WHERE users_favorites.username = '" . $_SESSION['username'] . "'";
$myData = mysqli_query($link, $sql);
if(!$myData){
        echo(mysqli_error($link));
}

// Show queried data and buttons to delete each record
/////////////////////
// if($myData){
//         while($record = mysqli_fetch_array($myData)){
//                 echo "<form method=post>";
//                 // echo "<tr>";
//                 // echo "<td>" . $record['recipe_name'] . "</td>";
//                 // echo "<td>" . $record['recipe_category'] . "</td>";
//                 // echo "<td>" . "<input type=hidden name=hidden value='" . $record[recipe_id] . "' </td>";
//                 // echo "<td>" . "<input type=submit name=delete value=delete" . " </td>";
//                 // echo "</tr>";
//                 echo "<div id='showallrecipes'>";
//                 echo "<div class='rec'>
//                 <img src= 'images/" . $record['recipe_image'] . "' alt='An image of " . $record['recipe_name'] . "'>
//                 <button type=submit name=view value='" . $record['recipe_id'] . "'>" . $record['recipe_name'] . "</button>
//                 <button type=submit name=delete value='" . $record['recipe_id'] . "'>Delete</button>
//                 "."</div>";
//                 echo "</div>";
//                 echo "</form>";
//         }
// }else{
//         echo "You have no favorite recipes saved.";
// }

// // Close connection
// mysqli_close($link);
//////////////////
?>

<html>
    <title>Fork | My Favorites</title>
        <head>
                <!--Links to CSS files-->
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                <link rel="stylesheet" type='text/css' href='base.css'>
                <link rel="stylesheet" type='text/css' href='repo.css'>
                
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
        
        
        <h1>My Favorites</h1>
        <?php
             echo "<form method=post><div id='showallrecipes'>";
                if($myData){
            // Show all recipes and delete buttons for each recipe
            while($record = mysqli_fetch_array($myData)){
                echo "<div class='rec'>
                <button type=submit name=view value='" . $record['recipe_id'] . "'>
                <div class='img__wrap'><img src= 'images/" . $record['recipe_image'] . "' alt='An image of " . $record['recipe_name'] . "'>
                    <div class='img__description_layer'>
                        <p class='img__description'>" . $record['recipe_name'] . "</p>
                    </div>
                </div>
                </button>
                <button type=submit title = 'delete from my favorites' name=delete value='" . $record['recipe_id'] . "'><i class='fa fa-trash'></i></button>";
                echo "</div>";
                
            }
            echo "</div></form>";
        }else{
            echo "You have no recipes uploaded.";
        }
        
        // Close connection
        mysqli_close($link);
        
        ?>
        </body>
        
    <!--Contact information in footer-->
    <footer> <!--contact us!-->
    <p><br/>&#169; Fork 2019<br/>Email us with questions, comments, and concerns! <a href="mailto:seliu@ctemc.org">customerservice@fork.org</a></p>
    </footer>
</html>