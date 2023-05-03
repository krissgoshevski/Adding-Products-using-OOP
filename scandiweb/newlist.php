<?php
require_once __DIR__ . "/consts.php";
require_once __DIR__ . "/classes/conndb.php";
require_once __DIR__ . "/functions.php";
require_once __DIR__ . "/classes/cd.php";
require_once __DIR__ . "/classes/furniture.php";
require_once __DIR__ . "/classes/book.php";
require_once __DIR__ . "/classes/insertval.php";

use MyConn\Conndb;
use MyClassCd\Cd;
use MyVlInsert\InsertValidate;

$conn = new Conndb();
$conn = $conn->connect();

$insval = new InsertValidate($conn);
$insval->getAllProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    
    <?php require_once __DIR__ . "/menu.php" ?>
<form method="post" action="delete.php" class="indexlistForm">
    <h1>View all products</h1>
    <div class="buttonContainer">
        <button type="submit" name="ADD">ADD</button> 
        <button type="submit" name="massdelete">MASS DELETE</button>
    </div>

    <?php 
    $insval->checkIfNoProducts();  
    
    echo "<div class='cdContainer'>";
    foreach ($insval->getAllProducts() as $product)
    {
        if(!is_null($product['size_cd'])){
            echo "<div class='cd' style='border:1px solid red; width:200px;'>";
            echo "<input type='checkbox' name='delete-checkbox[]' value='" . $product['product_id'] . "'> ";
            echo "<div class='inputContainer'>";
            echo '<label>SKU: ' . $product['sku'] . '</label> ';
            echo '<label>Name: ' . $product['name'] . '</label> ';
            echo '<label>Price: ' . $product['price'] . '$' . '</label> ';
            echo '<label>Size: ' . $product['size_cd'] . '(MB)'. '</label> </div> </div>';}

    else if(!is_null($product['height_f']) && !is_null($product['width_f']) && !is_null($product['length_f'])){
            echo "<div class='furniture' style='border:1px solid red; width:200px;'>";
            echo "<input type='checkbox' name='delete-checkbox[]' value='" . $product['product_id'] . "'>";
            echo "<div class='inputContainer'>";
            echo '<label>SKU: ' . $product['sku'] . '</label> ';
            echo '<label>Name: ' . $product['name'] . '</label> ';
            echo '<label>Price: ' . $product['price'] . '$' . '</label> ';
            echo '<label> Dimensions: '.$product['width_f'].'x'.$product['length_f'].'x'.$product['height_f'].'</label> </div> </div>';}
          
          else{
            echo "<div class='book' style='border:1px solid red; width:200px;'>";
            echo "<input type='checkbox' name='delete-checkbox[]' value='" . $product['product_id'] . "'> ";
            echo "<div class='inputContainer'>";
            echo '<label>SKU: ' . $product['sku'] . '</label> ';
            echo '<label>Name: ' . $product['name'] . '</label> ';
            echo '<label>Price: ' . $product['price'] . '$' . '</label> ';
            echo '<label>Size: ' . $product['weight_book'] . '(KG)'. '</label> </div> </div>';}
          }

          echo "</div>"
          ?>
    
</form>
</body>
</html>


