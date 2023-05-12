
<?php
session_start();
if(isset($_POST['category_id_fk'])){
    $category = $_POST['category_id_fk'];
}
if(isset($_POST['cancel'])){
    $cancel = $_POST['cancel'];
}
if(isset($_POST['save'])){
    $save = $_POST['save'];
}

$sku = $_POST['sku'];
$name = $_POST['name'];
$price = $_POST['price'];
$description = $_POST['description'];

// CD/DVD product
$size = $_POST['size']; // ovoa e greska treba dvd

// Furniture product
$height = $_POST['height'];
$width = $_POST['width'];
$length = $_POST['length'];

// Book product
$weight = $_POST['weight'];
?>
<?php
require_once __DIR__ . "/functions.php";

require_once __DIR__ . "/classes/myExceptions.php";
require_once __DIR__ . "/classes/conndb.php"; 
require_once __DIR__ . "/classes/cd.php";
require_once __DIR__ . "/classes/furniture.php";
require_once __DIR__ . "/classes/book.php";
require_once __DIR__ . "/classes/product.php";
require_once __DIR__ . "/classes/insertval.php";


use MyConn\Conndb;
use MainLogicClass\ProductValidator;
use MyClassCd\Cd;
use MyClassBook\Book;
use MyClassFurniture\Furniture;
use MyVlInsert\InsertValidate;
use MyExceptions\ExrequiredData;
use MyExceptions\ExrequiredName;
use MyExceptions\ExrequiredSku;
use MyExceptions\ExchooseProduct;
use MyExceptions\ExinvalidFurnitureInputs;
use MyExceptions\Exinvalidname;
use MyExceptions\ExInvalidPrefixBook;
use MyExceptions\ExInvalidPrefixCd;
use MyExceptions\ExInvalidPrefixFur;
use MyExceptions\Exinvalidprice;
use MyExceptions\ExinvalidSize;
use MyExceptions\Exinvalidsku;
use MyExceptions\ExinvalidWeight;
use MyExceptions\ExuniqueSku;

    $conn = new Conndb();
    $conn = $conn->connect();

    $insval = new InsertValidate($conn);
    $cd = new Cd($conn); // connection do database constructor
    $fur = new Furniture($conn);
    $book = new Book($conn); 
    $insval->checkRequestMethod();

  if (isset($cancel)) {
    header("Location: newlist.php"); die();
} else 
  {
   

      $size = $cd->validateSize($size);
      $dimensions = $fur->validateDimensions($height, $width, $length);
      $weight = $book->validateWeight($weight);

       // list($height, $width, $length) = $dimensions;
       // ($height === NULL && $width === NULL && $length === NULL) ?: $dimensions = NULL;

    try {
        $insval->setCategory($category);
        $insval->validateForm($category, $sku, $name, $price, $size, $width, $height, $length, $weight);
        $insval->validateSkuUnique($sku);
        $insval->setSku($sku);
        $insval->setName($name);
        $insval->validateInputDataPrice($price);
        $insval->validateInputDataName($name);
        $cd->validateInputDataSize($size, $width, $height, $length, $weight);
        $fur->validateInputDataFurniture($size, $width, $height, $length, $weight);
        $book->validateInputDataWeight($size, $width, $height, $length, $weight); 
    } 

    catch(ExchooseProduct $ex){
        $_SESSION['exception'] = $ex;
        header("Location: createproduct.php?status=error&reason=chooseproduct"); die();
    }
    catch (ExrequiredData $ex) {
        $_SESSION['exception'] = $ex;
        header("Location: createproduct.php?status=error&reason=required"); die(); 
    } 
    catch (ExrequiredSku $ex) {
        $_SESSION['exception'] = $ex;
        header("Location: createproduct.php?status=error&reason=reqLengthSku"); die();
    }
    catch (ExrequiredName $ex) {
        $_SESSION['exception'] = $ex;
        header("Location: createproduct.php?status=error&reason=reqLengthName"); die();
    } 
    catch (ExuniqueSku $ex) {
    $_SESSION['exception'] = $ex;
    header("Location: createproduct.php?status=error&reason=skurequiredUnique");
    die();
    } 
    catch(Exinvalidprice $ex){
    $_SESSION['exception'] = $ex;
    header("Location: createproduct.php?status=error&reason=invalidPricedata"); die();
    }
    catch(Exinvalidname $ex){
    $_SESSION['exception'] = $ex;
    header("Location: createproduct.php?status=error&reason=invalidNamedata"); die();
    }
    catch(ExinvalidSize $ex){
    $_SESSION['exception'] = $ex;
    header("Location: createproduct.php?status=error&reason=invalidSizedata"); die();
    }
    catch(ExinvalidFurnitureInputs $ex){
    $_SESSION['exception'] = $ex;
    header("Location: createproduct.php?status=error&reason=invalidFurnituredata"); die();
    }
    catch(ExinvalidWeight $ex){
    $_SESSION['exception'] = $ex;
    header("Location: createproduct.php?status=error&reason=invalidWeightedata"); die(); }

  

    try {
        $decryptCat = decrypt($category);
    
        $decryptCat === '1' ? $cd->validateInputDataSku($sku) :
    ($decryptCat === '2' ? $fur->validateInputDataSku($sku) :
    ($decryptCat === '3' ? $book->validateInputDataSku($sku) : null));

    }catch (ExInvalidPrefixCd $ex) {
        $_SESSION['exception'] = $ex;
        header("Location: createproduct.php?status=error&reason=invalidPrefixCd"); die();
    }catch (ExInvalidPrefixFur $ex) {
        $_SESSION['exception'] = $ex;
        header("Location: createproduct.php?status=error&reason=invalidPrefixFur"); die();
    }catch (ExInvalidPrefixBook $ex) {
        $_SESSION['exception'] = $ex;
        header("Location: createproduct.php?status=error&reason=invalidPrefixBook"); die(); 
    } catch(Exinvalidsku $ex){
        $_SESSION['exception'] = $ex;
        header("Location: createproduct.php?status=error&reason=invalidSkudata"); die();
        }



    if($insval->addProduct($category, $sku, $name, $price, $description, $size, $width, $height, $length, $weight))
    {
        header("Location: newlist.php?status=addednewProduct=$name"); die();
    }

}
?>

