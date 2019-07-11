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

// Define variables and initialize with empty values
$recipe = $category = $time = $diet = $level= "";
$recipe_err = $category_err = $time_err = $diet_err = $level_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Set image file name
    $image = $_FILES['image']['name'];
    $target = "images/".basename($image);
    // Move image to images folder
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
  		$msg = "Image uploaded successfully";
  	}else{
  		$msg = "Failed to upload image";
  	}
  	
    // Validate recipe name
    if(empty(trim($_POST["recipe"]))){
        $recipe_err = "Please enter a recipe name okay.";     
    }else{
        $recipe = htmlspecialchars(trim($_POST["recipe"]));
    }
    
    // Validate category
    if(empty(trim($_POST["category"]))){
        $category_err = "Please enter a recipe category.";     
    } else{
        $category = trim($_POST["category"]);
    }
    
    // Validate time
    if(empty(trim($_POST["time"]))){
        $time_err = "Please enter a recipe time."; 
    } else{
        $time = htmlspecialchars(trim($_POST["time"]));
    }
    
    // Validate diet
    if(empty($_POST["diet"])){
        $diet= '000';
    } else{
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
    
    // Validate level
    if(empty(trim($_POST["level"]))){
        $level_err = "Please enter a recipe category.";     
    } else{
        $level = trim($_POST["level"]);
    }
    
    // Prepare an insert statement to add user inputs into recipes table
    if(empty($recipe_err)&&empty($level_err)&&empty($time_err)){
        
        $sql = "INSERT INTO recipes (recipe_name, recipe_category, recipe_time, recipe_level, recipe_diet, recipe_image) 
                VALUES (?, ?, ?, ?, ?, ?)";
        // Bind parameters
        if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "ssssss", $param_recipe, $param_category, $param_time, 
                                    $param_level, $param_diet, $param_image);
        
        $param_recipe = $recipe;
        $param_category = $category;
        $param_time = $time;
        $param_level = $level;
        $param_diet = $diet;
        $param_image = $image;

        if(!mysqli_stmt_execute($stmt)){
                echo "Something went wrong. Please try again later.";
            }
        }
    }
    
    // Insert new record into users_uploads table with the current username and most recent recipe_id
    $sql = "select recipe_id from recipes order by recipe_id desc limit 1";
    $recipe_id = mysqli_fetch_array(mysqli_query($link, $sql))['recipe_id'];
    $sql = "insert into users_uploads (username, recipe_id) values ('" . $_SESSION[username]. "', $recipe_id)";
    mysqli_query($link, $sql);
    if (!mysqli_query){
       echo (mysqli_error($link));
    }
        
        $mysqli = NEW MySQLi('localhost','root','','c9');
        
        // Select recipe_id of most recent recipe added
        $resultSet = $mysqli->query("select recipe_id from recipes order by recipe_id desc limit 1");
        $recipe_id = $resultSet->fetch_assoc()['recipe_id'];
        
        // Assign variables to input values
        $ingredient = $_POST['ingredient'];
        $unit =  $_POST['unit'];
        $amount =  $_POST['amount'];
        $extra =  $_POST['extra'];
  
        // Insert recipe_id, ingredient, unit, amount, extra values into ingredients table
        foreach($ingredient AS $key => $value){
            $query2 = "INSERT into ingredients(recipe_id, ingredient, unit, amount, extra)
            VALUES ($recipe_id, '"
            . $mysqli->real_escape_string(trim($value)) .
            "','"
            . $mysqli->real_escape_string(trim($unit[$key])) .
            "','"
            . $mysqli->real_escape_string(trim($amount[$key])) .
            "','"
            . $mysqli->real_escape_string(trim($extra[$key])) .
            "') ";
            $insert2 = $mysqli->query($query2);
            // Check for errors in insertion
            if(!$insert2){
                echo $mysqli->error;
            }
        }
        // Assign instruction variable to input value
        $instruction = $_POST['instruction'];
        // Insert recipe_id, step, instruction values into instructions table
        $i = 1;  
        foreach($instruction AS $key => $value){
            $query3 = "INSERT into instructions(recipe_id, step, instruction)
            VALUES ($recipe_id, $i, '"
            . $mysqli->real_escape_string(trim($value)) .
            "') ";
            $i++;
            $insert3 = $mysqli->query($query3);
            // Check for errors in insertion
            if(!$insert3){
                echo $mysqli->error;
            }
        }
        
        echo "<script type='text/javascript'>alert('Success! Recipe added.');</script>";
        
        // Close connection
        $mysqli->close();
}
?>

<html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<script>
  $(document).ready(function(e){
      // Add and remove additional input fields for instructions
      var html = '<p /><div>\
      <p style="font-color: #5B3D2A; font-size: 28px;">step:</p><input class = "in" type="TEXT" name="instruction[]" id="childinstruction" required/>\
      <a href="#" id="remove">remove step</a></div>';
      $("#add").click(function(e){
          $("#container").append(html);
      });
      $("#container").on('click','#remove',function(e){
          $(this).parent('div').remove();
      });
      
      // Add and remove additional input fields for ingredients
      var html2 = '<p /><div><table style="width:100%;"><tr><th title="required">ingredient<input class = "in" type="TEXT" name="ingredient[]" id="childingredient" required/></th>\
      <th title="optional">unit<input type="TEXT" name="unit[]" id="childunit"/></th>\
      <th title="required">amount<input class = "in" type="TEXT" name="amount[]" id="childamount" required/></th>\
      <th title="an optional extra detail about the ingredient, like &#34;softened&#34; for butter">extra<input type="TEXT" name="extra[]" id="childextra"/></th></tr></table>\
      <a href="#" id="remove">remove ingredient</a>\
      <br/></div>';
      $("#add2").click(function(e){
          $("#container2").append(html2);
      });
      $("#container2").on('click','#remove',function(e){
          $(this).parent('div').remove();
      });
      
  });
</script>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="add_recipe.css">

<title>Fork | Add Recipe</title>

<head>
    <!--Links to CSS files-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="base.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

    <header>
      <a href="welcome.php"><img src="fork-mint-cream-space.png" height="100" alt="logo in mint"></a>
    </header>
</head>

<body>

<!--Generate form for uploading a recipe-->
<form id="regForm" action="" method="POST" enctype="multipart/form-data">
  <h1>Upload Recipe</h1>
  
  <!--Show form for adding general recipe information-->
  <div class="tab"><h2>General Information</h2>
    <p><input class = "in" onkeyup="this.value = this.value.toUpperCase();" placeholder="recipe name" name="recipe"></p>
    <p><div align="center">
    <h2 align="left">Dessert Type</h2>
    <label class="container">cake<input class = "in" type="radio" name="category" value="cake" required=""><span class="radiomark"></span></label>
    <label class="container">cookie<input type="radio" name="category" value="cookie"><span class="radiomark"></span></label>
    <label class="container">pie<input type="radio" name="category" value="pie"><span class="radiomark"></span></label>
    <label class="container">pastry<input type="radio" name="category" value="pastry"><span class="radiomark"></span></label>
    <label class="container">frozen dessert<input type="radio" name="category" value="frozen"><span class="radiomark"></span></label></div></p>
    <p><input class = "in" placeholder="time in minutes" type="number" name="time" min="1" max="300"></p>
    <h2 align="left">Difficulty Level</h2>
    <label class="container">easy<input type="radio" name="level" value="easy" checked><span class="radiomark"></span></label>
    <label class="container">medium<input type="radio" name="level" value="medium"><span class="radiomark"></span></label>
    <label class="container">difficult<input type="radio" name="level" value="difficult"><span class="radiomark"></span></label>
    <h2 align="left">Dietary Restrictions (if applicable)</h2>
    <label class="container">gluten-free<input type="checkbox" name="diet[]" value="gluten-free"><span class="checkmark"></span></label>
    <label class="container">dairy-free<input type="checkbox" name="diet[]" value="dairy-free"><span class="checkmark"></span></label>
    <label class="container">soy-free<input type="checkbox" name="diet[]" value="soy-free"><span class="checkmark"></span></label>
    <input class = "in" type="file" name="image">
  </div>
  
  <!--Show form for adding ingredients-->
  <div class="tab"><h2>Ingredients</h2>
    <div id = "container2">
      <table style="width:100%;">
        <tr>
            <th title="required">ingredient<input class = "in" type="TEXT" name="ingredient[]" id="ingredient" required=""/></th>
            <th title="optional">unit<input type="TEXT" name="unit[]" id="unit"/></th>
            <th title="required">amount<input class = "in" type="TEXT" name="amount[]" id="amount" required=""/></th>
            <th title="an optional extra detail about the ingredient, like &#34;softened&#34; for butter">extra<input type="TEXT" name="extra[]" id="extra" /></th>
            <th><a href="#" id="add2">&#65291;</a></th>
        </tr>
      </table>
    </div>
  </div>
  
  <!--Show form for adding instructions-->
  <div class="tab"><h2>Instructions</h2>
    <div id = "container">
      <p style="font-color: #5B3D2A; font-size: 28px;">step:</p><input class = "in" type="TEXT" name="instruction[]" id="instruction" required=""/>
      <a href="#" id="add">&#65291;</a>
    </div>
  </div>
  
  <!--Buttons for moving between tabs-->
  <div style="overflow:auto;">
    <div style="float:right;">
      <button type="button" class="btn btn-default" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
      <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextPrev(1)">Next</button>
    </div>
  </div>

  <div style="text-align:center;margin-top:40px;">
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
  </div>
  
</form>

<script>
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
  // Display the specified tab of the form
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  // Fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    document.getElementById("nextBtn").innerHTML = "Next";
  }
  //Run a function that will display the correct step indicator
  fixStepIndicator(n)
}

function nextPrev(n) {
  // Figure out which tab to display
  var x = document.getElementsByClassName("tab");
  
  // Exit the function if any field in the current tab is invalid
  if (n == 1 && !validateForm())
    return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // If you have reached the end of the form, form gets submitted
  if (currentTab >= x.length) {
    document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tab
  showTab(currentTab);
}

function validateForm() {
  // Validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByClassName("in"); 
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty
    if (y[i].value == "") {
      // add an "invalid" class to the field
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = false;
    }
  }
  // If the valid status is true, mark the step as finished and valid
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // Removes the "active" class of all steps
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //Adds the "active" class on the current step
  x[n].className += " active";
}
</script>
    <footer> <!--contact us!-->
    <p><br/>&#169; Fork 2019<br/>Email us with questions, comments, and concerns! <a href="mailto:seliu@ctemc.org">customerservice@fork.org</a></p>
    </footer>
</body>
</html>
