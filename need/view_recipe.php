<!--Project Name: Fork-->
<!--Team Members: Selena Liu, Helena Zhang, Sophie Zhang-->
<!--Date: 6/7/19-->
<!--Task Description: Intuitive website for users to upload, filter, and save recipes-->

<?php

// Initialize the session
session_start();

// Include config file
require_once "config.php";

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Check if recipe record is already saved before inserting into users_favorites
if(isset($_POST['Save'])){
    $check = "select * from users_favorites where '$_SESSION[recipe_id]' = recipe_id and '$_SESSION[username]' = username";
    $result = mysqli_query($link, $check);
    if(mysqli_num_rows($result)==0){
        $sql = "INSERT into users_favorites (username, recipe_id) VALUES ('" . $_SESSION[username]. "', $_SESSION[recipe_id])";
        mysqli_query($link, $sql);
        if (!$mysqli_query){
            echo(mysqli_error($link));
        }
        echo "<script type='text/javascript'>alert('Success! Recipe saved to favorites.');</script>";
    }else{
        echo "<script type='text/javascript'>alert('You already have this recipe saved in favorites.');</script>";
    }
};

// echo "<form method = post>";
//     echo "<input type=submit name=save value=Save>";
// echo "</form>";

// Select fields from recipes, ingredients, and instructions table that correspond to selected recipe_id
// $sql = "SELECT recipes.recipe_name, recipes.recipe_category, recipes.recipe_time, recipes.recipe_level, recipes.recipe_diet,
//         ingredients.ingredient, ingredients.unit, ingredients.amount, ingredients.extra,
//         instructions.step, instructions.instruction
//         FROM recipes
//         INNER JOIN ingredients ON recipes.recipe_id = ingredients.recipe_id
//         INNER JOIN instructions ON recipes.recipe_id = instructions.recipe_id
//         WHERE recipes.recipe_id = $_SESSION[recipe_id]";
        
// Select all information from recipes for recipe with current recipe_id
$sql = "SELECT * FROM recipes WHERE recipe_id = $_SESSION[recipe_id]";
$myData = mysqli_query($link, $sql);
if(!$myData){
    echo(mysqli_error($link));
}

// Record data in grid format
$gridOne = "<div id='long'><h1>";
$gridTwo = "<div class='grid_image'><img id='recipe_image' src='images/";
$gridThree = "<div class='grid_content'>";

// Show queried data
while($record = mysqli_fetch_array($myData)){
    $gridOne = $gridOne . $record['recipe_name'] . "</h1></div>";
    $gridTwo = $gridTwo . $record['recipe_image'] ."'></div>";
    $gridThree = $gridThree . "<div class='recipe_category'> category: " . $record['recipe_category'] . "</div><div><i class='fa fa-hourglass-half'></i>";
    $gridThree = $gridThree . $record['recipe_time'] . " min</div><div>difficulty: " . $record['recipe_level'] . "</div>";
    $gridThree = $gridThree . "<div>";
    if ($record['recipe_diet'][0] == '0') {
        $gridThree = $gridThree . "<div class='diet'>
        <i class='fas fa-bread-slice'></i>
        <div class='diet_description'>
        <span class='tip'>contains gluten</span>
        </div></div>"
        ;
    }
    if ($record['recipe_diet'][1] == '0') {
        $gridThree = $gridThree . "<div class='diet'>
        <i class='fas fa-cheese'></i>
        <div class='diet_description'>
        <span class='tip'>contains dairy</span>
        </div></div>"
        ;
    }
    if ($record['recipe_diet'][2] == '0') {
        $gridThree = $gridThree . "<div class='diet'>
        <i class='fas fa-seedling'></i>
        <div class='diet_description'>
        <span class='tip'>contains soy</span>
        </div></div>"
        ;;
    }
    $gridThree = $gridThree . "</div>";
}

// Select all ingredients for recipe with current recipe_id
$sql = "SELECT * FROM ingredients WHERE recipe_id = $_SESSION[recipe_id]";
$myData = mysqli_query($link, $sql);
if(!$myData){
    echo(mysqli_error($link));
}

// Record data in grid format
$gridThree = $gridThree . "<div id='long'><hr style='border-top: dotted 3px;'/>";
while($record = mysqli_fetch_array($myData)){
    if ($record['extra'] != ''){
        $gridThree = $gridThree . $record['amount'] . " " . $record['unit'] . " " . $record['ingredient'] . " (" . $record['extra'] . ")<br>";
    }
    else{
        $gridThree = $gridThree . $record['amount'] . " " . $record['unit'] . " " . $record['ingredient'] . "<br>";
    }
}
$gridThree = $gridThree . "<hr style='border-top: dotted 3px;'/></div>";

// Select all instructions for recipe with current recipe_id
$sql = "SELECT * FROM instructions WHERE recipe_id = $_SESSION[recipe_id]";
$myData = mysqli_query($link, $sql);
if(!$myData){
    echo(mysqli_error($link));
}

// Record data in grid format
$gridThree = $gridThree . "<div id='long'><ol type='1'>";
while($record = mysqli_fetch_array($myData)){
    $gridThree = $gridThree . "<li>" . $record["instruction"] . "</li><br/>";
}
$gridThree = $gridThree . "</ol></div>";

$gridThree = $gridThree . "</div>";
$output = "<div class='recipe_container'>" . $gridOne . $gridTwo . $gridThree . "</div>";
// Close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Fork | View Recipe</title>
        
        <!--Links to CSS files-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">        
        <link rel="stylesheet" type='text/css' href='base.css'>
        <link rel="stylesheet" type='text/css' href='welcome.css'>
        <link rel="stylesheet" type='text/css' href='view_recipe.css'>
        
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
    <body onload="autoHeight()">
        
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
        
    <!--<div id="hamburger">-->
    <!--    <i class="fa fa-times" onclick="closeNav()"></i>-->
    <!--    <br/>-->
    <!--    <br/>-->
    <!--    <?php echo htmlspecialchars($_SESSION["username"]); ?>-->
    <!--    <a href="user_uploads.php">my recipes</a>-->
    <!--    <a href="repo.php">favorites</a>-->
    <!--    <a href="reset_password.php">reset password</a>-->
    <!--    <a href="reset_password.php">about fork</a>-->
    <!--    <a href="logout.php" style="color: #FCF8E8;">sign out</a>-->
    <!--</div>-->
    <!--<div id="main">-->
    <!--    <div class="page-header">-->
    <!--        <i class="fa fa-bars" onclick="myfunction()"></i>-->
    <!--        <a href="welcome.php"><img src="fork-mint-cream.png" height="40" alt="logo in mint"></a>-->
    <!--    </div>-->
    </div>
    <!--Display recipe information-->
    <?php echo $output; ?> 
        <!--Create Save button-->
        <form method = post>
            <input class ="btn btn-default" type=submit name=Save value='save to favorites'>
        </form>
    </body>
    
    <script>
    // function myfunction() {
    //     document.getElementById("hamburger").style.width = "250px";
    //     document.getElementById("main").style.marginLeft = "275px";
    // }
    // function closeNav() {
    //     document.getElementById("hamburger").style.width = "0px";
    //     document.getElementById("main").style.marginLeft = "25px";
    // }
        // Get image height
        var itemHeight = document.getElementById('recipe_image').style.height;
        document.getElementsByClassName('grid_content').style.maxheight = itemHeight;

    </script>
    
    <!--Contact information in footer-->
    <footer> <!--contact us!-->
    <p><br/>&#169; Fork 2019<br/>Email us with questions, comments, and concerns! <a href="mailto:seliu@ctemc.org">customerservice@fork.org</a></p>
    </footer>
</html>