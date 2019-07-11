<!--Project Name: Fork-->
<!--Team Members: Selena Liu, Helena Zhang, Sophie Zhang-->
<!--Date: 6/7/19-->
<!--Task Description: Intuitive website for users to upload, filter, and save recipes-->

<?php

// Initialize the session
session_start();

require_once "config.php";
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Set recipe_id session variable and redirect to view_recipe page
if(isset($_POST['view'])){
    $_SESSION['recipe_id'] = $_POST['view'];
    header('location: view_recipe.php');
};

// Select all recipe information
$searchQuery = "select * from recipes";                    
$myData = mysqli_query($link, $searchQuery);

// Show collapsible category headers
$cakedata = "<h2 class='category_header' onclick=openmenu('cake')>Cakes</h2><div id='cake'>";
$cookiedata = "<h2 class='category_header' onclick=openmenu('cookie')>Cookies</h2><div id='cookie'>";
$piedata = "<h2 class='category_header' onclick=openmenu('pie')>Pies</h2><div id='pie'>";
$pastrydata = "<h2 class='category_header' onclick=openmenu('pastry')>Pastries</h2><div id='pastry'>";
$frozendata = "<h2 class='category_header' onclick=openmenu('frozen')>Frozen Desserts</h2><div id='frozen'>";
        
// replaced code below this line:
// $cookiedata = $cookiedata . "<div class='rec'><img src= 'images/" . $record['recipe_image'] . "' alt='An image of " . $record['recipe_name'] . "'><button type=submit name=view value='" . $record['recipe_id'] . "'>" . $record['recipe_name'] . "</div>";

function imageDisplay($id, $img, $name){
    return "<div class='rec'>
        <button type=submit name=view value='" . $id . "'>
        <div class='img__wrap'>
        <img src= 'images/" . $img . "' alt='An image of " . $name . "'>
        <div class='img__description_layer'>
            <p class='img__description'>" . $name . "</p>
        </div>
        </div>
        </button></div>";
}

// Execute select query and show recipes in each category
while($record = mysqli_fetch_array($myData)){
    if ($record['recipe_category'] == 'cake') {
        $cakedata = $cakedata . imageDisplay($record['recipe_id'],$record['recipe_image'],$record['recipe_name']);
    } elseif ($record['recipe_category'] == 'cookie') {
        $cookiedata = $cookiedata . imageDisplay($record['recipe_id'],$record['recipe_image'],$record['recipe_name']);
    } elseif ($record['recipe_category'] == 'pie') {
        $piedata = $piedata . imageDisplay($record['recipe_id'],$record['recipe_image'],$record['recipe_name']);
    } elseif ($record['recipe_category'] == 'pastry') {
        $pastrydata = $pastrydata . imageDisplay($record['recipe_id'],$record['recipe_image'],$record['recipe_name']);
    } elseif ($record['recipe_category'] == 'frozen') {
        $frozendata = $frozendata . imageDisplay($record['recipe_id'],$record['recipe_image'],$record['recipe_name']);
    }
}
$cakedata = $cakedata . '</div>';
$cookiedata = $cookiedata . '</div>';
$piedata = $piedata . '</div>';
$pastrydata = $pastrydata . '</div>';
$frozendata = $frozendata . '</div>';
$output = $cakedata . $cookiedata . $piedata . $pastrydata . $frozendata;
?>
<script>
    function hideRecipes() {
        document.getElementById('showallrecipes').style.visibility = "hidden";
    }
</script>

<?php
    // Search form submitted
    if(isset($_POST['search'])){
        
    // Account for no text or only white space
    if(htmlspecialchars(trim($_POST['search']))==''){
        echo "<script type='text/javascript'>alert('No recipe entered.');</script>";

    } 
    else{
    // Select all from recipes table where name or category contains text submitted
    $_SESSION['search'] = htmlspecialchars(trim($_POST['search']));
    $sql = "SELECT * FROM recipes
            WHERE recipe_name LIKE CONCAT ('%', '" . htmlspecialchars(trim($_SESSION['search'])) . "','%') 
            OR recipe_category LIKE CONCAT ('%', '" . htmlspecialchars(trim($_SESSION['search'])) . "','%')";
    $myData = mysqli_query($link, $sql);
    $output = '';
    
    // If query returns data, show all relevant recipes
    if(mysqli_num_rows($myData)>0){
        
        echo "<script type='text/javascript'>alert('Success! Recipes found.');</script>";
        
        while($record = mysqli_fetch_array($myData)){
            
            // $output1 = $output1 . "<div class='rec'>
            // <img src= 'images/" . $record['recipe_image'] . "' alt='An image of " . $record['recipe_name'] . "'>
            // <button type=submit name=view value='" . $record['recipe_id'] . "'>" . $record['recipe_name'] . "</div>";
            $output = $output . "<div class='rec'>
        <button type=submit name=view value='" . $record['recipe_id'] . "'>
        <div class='img__wrap'>
        <img src= 'images/" . $record['recipe_image'] . "' alt='An image of " . $record['recipe_name'] . "'>
        <div class='img__description_layer'>
            <p class='img__description'>" . $record['recipe_name'] . "</p>
        </div>
        </div>
        </button></div>";
            
            
        }
    ?>
     
    <form method = post>  
    
    <!--Display collapsible form with additional filter criteria-->
    <div class="w3-container">
    <div class="collapse_header" style="width:20%">
      <label onclick="collapse('Demo1')" class="w3-button w3-block">Category</label>
      <div id="Demo1" class="w3-hide w3-container">
        <label class="container">cake<input type="radio" name="category" value="cake"><span class="radiomark"></span></label>
            <label class="container">cookie<input type="radio" name="category" value="cookie"><span class="radiomark"></span></label>
            <label class="container">pie<input type="radio" name="category" value="pie"><span class="radiomark"></span></label>
            <label class="container">pastry<input type="radio" name="category" value="pastry"><span class="radiomark"></span></label>
            <label class="container">frozen<input type="radio" name="category" value="frozen"><span class="radiomark"></span></label>
      </div>
    </div>
    <div class="collapse_header" style="width:20%">
      <label onclick="collapse('Demo2')" class="w3-button w3-block">Available Prep Time</label>
      <div id="Demo2" class="w3-hide w3-container">
        <p><input style = "width: 80%;" placeholder="time in mins" type="number" name="time" min="1" max="300"></p>
      </div>
    </div>
    <div class="collapse_header" style="width:20%">
      <label onclick="collapse('Demo3')" class="w3-button w3-block">Maximum Difficulty Level</label>
      <div id="Demo3" class="w3-hide w3-container">
        <label class="container">easy<input type="radio" name="level" value="easy"><span class="radiomark"></span></label>
            <label class="container">medium<input type="radio" name="level" value="medium"><span class="radiomark"></span></label>
            <label class="container">difficult<input type="radio" name="level" value="difficult"><span class="radiomark"></span></label><br/>
      </div>
    </div>
    <div class="collapse_header" style="width:20%">
      <label onclick="collapse('Demo4')" class="w3-button w3-block">Dietary Restrictions</label>
      <div id="Demo4" class="w3-hide w3-container">
        <label class="container">gluten-free<input type="checkbox" name="diet[]" value="gluten-free"><span class="checkmark"></span></label>
            <label class="container">dairy-free<input type="checkbox" name="diet[]" value="dairy-free"><span class="checkmark"></span></label>
            <label class="container">soy-free<input type="checkbox" name="diet[]" value="soy-free"><span class="checkmark"></span></label>
      </div>
    </div>
    </div>
    <input type="submit" class= "btn btn-primary" name = "search2">
    </form>

    <!--<label class="collapsible">Category</label>-->
    <!--    <div class="content">-->
            <!--<label class="container">cake<input type="radio" name="category" value="cake"><span class="checkmark"></span></label>-->
            <!--<label class="container">cookie<input type="radio" name="category" value="cookie"><span class="checkmark"></span></label>-->
            <!--<label class="container">pie<input type="radio" name="category" value="pie"><span class="checkmark"></span></label>-->
            <!--<label class="container">pastry<input type="radio" name="category" value="pastry"><span class="checkmark"></span></label>-->
            <!--<label class="container">frozen<input type="radio" name="category" value="frozen"><span class="checkmark"></span></label>-->
    <!--    </div>-->
    <!--<label class="collapsible">Time to Prepare</label>-->
    <!--    <div class="content">-->
    <!--        <p><input placeholder="Time in minutes" type="number" name="time" min="1" max="300"></p>-->
    <!--    </div>-->
    <!--<label class="collapsible">Difficulty Level</label>-->
    <!--    <div class="content">-->
    <!--        <label class="container">easy<input type="radio" name="level" value="easy"><span class="checkmark"></span></label>-->
    <!--        <label class="container">medium<input type="radio" name="level" value="medium"><span class="checkmark"></span></label>-->
    <!--        <label class="container">difficult<input type="radio" name="level" value="difficult"><span class="checkmark"></span></label><br/>-->
    <!--    </div>-->
    <!--<label class="collapsible">Dietary Restrictions</label>-->
    <!--    <div class="content">-->
            <!--<label class="container">gluten-free<input type="checkbox" name="diet[]" value="gluten-free"><span class="checkmark"></span></label>-->
            <!--<label class="container">dairy-free<input type="checkbox" name="diet[]" value="dairy-free"><span class="checkmark"></span></label>-->
            <!--<label class="container">soy-free<input type="checkbox" name="diet[]" value="soy-free"><span class="checkmark"></span></label>-->
    <!--    </div>-->
    
    <!--<function to open and close collapsibles>-->
    <script>
    function collapse(id) {
      var x = document.getElementById(id);
      if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
      } else { 
        x.className = x.className.replace(" w3-show", "");
      }
    }
    </script>
    <!--// var coll = document.getElementsByClassName("collapsible");-->
    <!--// var i;-->
    
    <!--// for (i = 0; i < coll.length; i++) {-->
    <!--//   coll[i].addEventListener("click", function() {-->
    <!--//     this.classList.toggle("active");-->
    <!--//     var content = this.nextElementSibling;-->
    <!--//     if (content.style.display === "table-cell") {-->
    <!--//       content.style.display = "none";-->
    <!--//     } else {-->
    <!--//       content.style.display = "table-cell";-->
    <!--//     }-->
    <!--//   });-->
    <!--// }-->
    
    <?php
    }else{
        // Display message if no recipes meet search criteria
        echo "<script type='text/javascript'>alert('No recipes found.');</script>";
        // $sql = "SELECT * FROM recipes";
        // $myData = mysqli_query($link, $sql);
        // while($record = mysqli_fetch_array($myData)){
        //     echo "<div class='rec'><img src= 'images/" . $record['recipe_image'] . "' alt='An image of " . $record['recipe_name'] . "'><button type=submit name=view value='" . $record['recipe_id'] . "'>" . $record['recipe_name'] . "</div>";
        // }
    }
    }
};

// If second submit button is clicked
if(isset($_POST['search2'])){
    // Format diet(s) from words to bits
    if(!empty($_POST['diet'])){
        $diet_array = $_POST["diet"];
        $diet = "";
        if (in_array("gluten-free", $diet_array)){
          $diet = $diet . '1';
        }else{
          $diet = $diet . '0';
        }
        if (in_array("dairy-free", $diet_array)){
          $diet = $diet . '1';
        }else{
          $diet = $diet . '0';
        }
        if (in_array("soy-free", $diet_array)){
          $diet = $diet . '1';
        }else{
          $diet = $diet . '0';
        }
    }
    // Adjust query section for maximum difficulty level
    if(!empty($_POST['level'])){
        if ($_POST[level] == "easy"){
          $level = "(recipe_level = 'easy')";
        }elseif ($_POST[level] == "medium"){
          $level = "(recipe_level = 'easy' OR recipe_level = 'medium')";
        }elseif ($_POST[level] == "difficult"){
          $level = "(recipe_level = 'easy' OR recipe_level = 'medium' OR recipe_level = 'difficult')";
        }
    }
    
    // Select all recipes meeting initial and specific criteria
    $sql2 = "SELECT * FROM recipes
             WHERE (recipe_name LIKE CONCAT ('%', '" . htmlspecialchars(trim($_SESSION['search'])) . "','%') 
             OR recipe_category LIKE CONCAT ('%', '" . htmlspecialchars(trim($_SESSION['search'])) . "','%'))";
    if(!empty($_POST[category])) { $sql2 = $sql2 . " AND '" .$_POST[category] . "' = recipe_category";}
    if(!empty($_POST[time])) {$sql2 = $sql2 . " AND  ($_POST[time] >= recipe_time)";}
    if(!empty($_POST[level])) {$sql2 = $sql2 . " AND $level";}
    if(!empty($_POST[diet])) {$sql2 = $sql2 . " AND ~CONV(recipe_diet,2,10) & CONV($diet,2,10) = 0";}

    // Display query results
    $myData2 = mysqli_query($link, $sql2);
    $output = '';
    if(mysqli_num_rows($myData2)>0){
        echo "<script type='text/javascript'>alert('Success! Recipes found with requirements entered.');</script>";
        while($record = mysqli_fetch_array($myData2)){
            // $output1 = $output1 . "<div class='rec'><img src= 'images/" . $record['recipe_image'] . "' alt='An image of " . $record['recipe_name'] . "'><button type=submit name=view value='" . $record['recipe_id'] . "'>" . $record['recipe_name'] . "</div>";
        $output = $output . "<div class='rec'>
        <button type=submit name=view value='" . $record['recipe_id'] . "'>
        <div class='img__wrap'>
        <img src= 'images/" . $record['recipe_image'] . "' alt='An image of " . $record['recipe_name'] . "'>
        <div class='img__description_layer'>
            <p class='img__description'>" . $record['recipe_name'] . "</p>
        </div>
        </div>
        </button></div>";
            
        }
    }else{
        // Show message that no recipes were found
        echo "<script type='text/javascript'>alert('No recipes found with requirements entered.');</script>";
        // Only select and display recipes existing from the initial search query
        $sql2 = "SELECT * FROM recipes 
                 WHERE recipe_name LIKE CONCAT ('%', '" . htmlspecialchars(trim($_SESSION['search'])) . "','%') 
                 OR recipe_category LIKE CONCAT ('%', '" . htmlspecialchars(trim($_SESSION['search'])) . "','%')";
        $myData2 = mysqli_query($link, $sql2);
        while($record = mysqli_fetch_array($myData2)){
            // $output1 = $output1 . "<div class='rec'><img src= 'images/" . $record['recipe_image'] . "' alt='An image of " . $record['recipe_name'] . "'><button type=submit name=view value='" . $record['recipe_id'] . "'>" . $record['recipe_name'] . "</div>";
        $output = $output . "<div class='rec'>
        <button type=submit name=view value='" . $record['recipe_id'] . "'>
        <div class='img__wrap'>
        <img src= 'images/" . $record['recipe_image'] . "' alt='An image of " . $record['recipe_name'] . "'>
        <div class='img__description_layer'>
            <p class='img__description'>" . $record['recipe_name'] . "</p>
        </div>
        </div>
        </button></div>";
            
        }
    }
}

mysqli_close($link);

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fork | Welcome</title>
    <!--Links to CSS files-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" type='text/css' href='base.css'>
    <link rel="stylesheet" type='text/css' href='welcome.css'>
    <script>
    // function myfunction() {
    //     document.getElementById("hamburger").style.width = "250px";
    //     document.getElementById("main").style.marginLeft = "275px";
    // }
    // function closeNav() {
    //     document.getElementById("hamburger").style.width = "0px";
    //     document.getElementById("main").style.marginLeft = "25px";
    // }
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
            <!--<i class="fa fa-times" onclick="closeNav()"></i>-->
            <br/>
            <br/>
            <a href="about.html">about</a>
            <a href="user_uploads.php">my uploads</a>
            <a href="repo.php">favorites</a>
            <a href="map.html">map</a>
            <a href="reset_password.php">reset password</a>
            <a href="logout.php" style="color: #FCF8E8;">sign out</a>
        </div>
    
    
    <div id="main">
        <div class="page-header">
            <i class="fa fa-bars" onclick="myFunction()"></i>
            <a href="welcome.1.php"><img src="fork-mint-cream.png" height="40" alt="logo in mint"></a>
            <br/>
            <br/>
            </div>
            <!--Search box for recipes-->
            <form action=welcome.1.php method=post>
            
            
            <input class="search txtbox" onkeyup="this.value = this.value.toUpperCase();" type="text" name = "search" placeholder="search for a recipe">
            <!--<input type = "submit" class="btn btn-primary" name = "search1">-->
            
            </form>
        
        <!--Display all recipes-->
        <form method=post>
            <!--<div id='showselectedrecipes'><?php echo $output1 ?></div>-->
            <div id='showallrecipes'><?php echo $output ?></div>
        </form>
    </div>
    
    <!--Contact information in footer-->
    <footer> <!--contact us!-->
    <p><br/>&#169; Fork 2019<br/>
    Email us with questions, comments, and concerns! <a href="mailto:seliu@ctemc.org">customerservice@fork.org</a></p>
    </footer>
</body>
</html>