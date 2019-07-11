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

if(isset($_POST['update'])){
    $DeleteQuery = "DELETE FROM recipes WHERE recipe_id= '$_POST[delete]'";          
    mysqli_query($link, $DeleteQuery);
    if(!mysqli_query){
        echo(mysqli_error($link));
    }
    $DeleteQuery = "delete FROM users_uploads WHERE recipe_id='$_POST[delete]'";          
    mysqli_query($link, $DeleteQuery);
    if(!mysqli_query){
        echo(mysqli_error($link));
    }
    $DeleteQuery = "delete FROM ingredients WHERE recipe_id='$_POST[delete]'";          
    mysqli_query($link, $DeleteQuery);
    if(!mysqli_query){
        echo(mysqli_error($link));
    }
    $DeleteQuery = "delete FROM instructions WHERE recipe_id='$_POST[delete]'";          
    mysqli_query($link, $DeleteQuery);
    if(!mysqli_query){
        echo(mysqli_error($link));
    }
    
    $insert = "INSERT INTO recipes (recipe_id, recipe_name, recipe_category, recipe_time, recipe_level, recipe_diet, recipe_image) 
                VALUES ($_SESSION[recipe], $_POST(recipe), $_POST(category), $_POST(time), $_POST(level), $_POST(diet)";
    mysqli_query($link, $insert);
    if(!mysqli_query){
        echo(mysqli_error($link));
    }
    
    foreach($ingredient AS $key => $value){
            $query2 = "INSERT into ingredients(recipe_id, ingredient, unit, amount, extra)
            VALUES ( $_SESSION[recipe], '"
            . $mysqli->real_escape_string($value) .
            "','"
            . $mysqli->real_escape_string($unit[$key]) .
            "','"
            . $mysqli->real_escape_string($amount[$key]) .
            "','"
            . $mysqli->real_escape_string($extra[$key]) .
            "') ";
            $insert2 = $mysqli->query($query2);
            // Check for errors in insertion
            if(!$insert2){
                echo $mysqli->error;
            }
        }
    
    $insert = "INSERT INTO recipes (recipe_id, recipe_name, recipe_category, recipe_time, recipe_level, recipe_diet, recipe_image) 
                VALUES ($_SESSION[recipe], $_POST(recipe), $_POST(category), $_POST(time), $_POST(level), $_POST(diet)";
    mysqli_query($link, $insert);
    if(!mysqli_query){
        echo(mysqli_error($link));
    }
    
};

$sql = "select * from recipes where recipe_id=$_SESSION[recipe]";
    $data = mysqli_query($link, $sql);
    if($data){
        while($record = mysqli_fetch_array($data)){
            echo "<form method=post>";
            echo "<input type=text name=recipe value='" . $record['recipe_name'] . "' ><br>";
            echo "Cake: <input type=radio name=category value='cake'>"; 
            echo "Cookie: <input type=radio name=category value='cookie'>"; 
            echo "Pie: <input type=radio name=category value='pie'>"; 
            echo "Pastry: <input type=radio name=category value='pastry'>"; 
            echo "Frozen: <input type=radio name=category value='frozen'><br>"; 
            echo "<input type=number name=time value='" . $record['recipe_time'] . "'><br>"; 
            echo "Easy: <input type= radio name=level value='easy'>"; 
            echo "Medium: <input type=radio name=level value='medium'>"; 
            echo "Difficult: <input type=radio name=level value='difficult'><br>"; 
            echo "Gluten-free: <input type=checkbox name=diet value='gluten-free'>"; 
            echo "Dairy-free: <input type=checkbox name=diet value='dairy-free'>"; 
            echo "Soy-free: <input type=checkbox name=diet value='soy-free'><br>"; 
            echo "<input type=file name=image><br>";
        }
    }
    $sql = "select * from ingredients where recipe_id=$_SESSION[recipe]";
    $data = mysqli_query($link, $sql);
    if($data){
        while($record = mysqli_fetch_array($data)){
            echo "<input type=text name=update value='" . $record['ingredient'] . "'>"; 
            echo "<input type=text name=update value='" . $record['unit'] . "'>"; 
            echo "<input type=text name=update value='" . $record['amount'] . "'>"; 
            echo "<input type=text name=update value='" . $record['extra'] . "'><br>";
        
        }
    }
    $sql = "select * from instructions where recipe_id=$_SESSION[recipe]";
    $data = mysqli_query($link, $sql);
    if($data){
        while($record = mysqli_fetch_array($data)){
            echo "<input type=text name=update value='" . $record['instruction'] . "'><br>";
        }
        
    }
    echo "<button type=submit name=update value=$_SESSION[recipe]</button>Update";
    echo "</form>";

?>