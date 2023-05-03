<?php 
namespace MainLogicClass;

require_once __DIR__ . "/conndb.php"; 
require_once __DIR__ . "/../functions.php";
require_once __DIR__ . "/myExceptions.php";
require_once __DIR__ . "/../interfaces/interface.php";

use MyConn\Conndb;
use PDO;
use MyInterface\AbstractMethods;
use MyExceptions\ExrequiredName as requiredName;
use MyExceptions\ExrequiredSku as requiredsku;
use MyExceptions\ExrequiredData;
use MyExceptions\ExchooseProduct;
use MyExceptions\ExinvalidFurnitureInputs;
use MyExceptions\Exinvalidprice;
use MyExceptions\ExuniqueSku;
use MyExceptions\Exinvalidname;
use MyExceptions\ExinvalidSize;
use MyExceptions\ExinvalidWeight;


abstract class ProductValidator implements AbstractMethods {

protected $conn;
protected $category;
protected $sku;
protected $name;
protected $price;

        // construct for connection with database
        public function __construct($conn)
        {
          $this->conn = $conn; 
        }

       // only POST method allowed 
        public function checkRequestMethod() 
        {
          if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
              header('HTTP/1.1 405 Method Not Allowed');
              exit('Only POST requests are allowed');
          }
        }

        // validation for Sku characters
        public function setSku($sku)
        {
            if(strlen($sku) != 9){  
              $ex = new requiredsku();
              throw $ex;
            }
              $this->sku = $sku;
              return $this;
          }



      // validation for unique Sku
        public function validateSkuUnique($sku)
        {
            $conn = $this->conn;
            $stmt = $conn->prepare("SELECT * FROM products WHERE sku = :sku");
            $stmt->bindParam(":sku", $sku);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                throw new ExuniqueSku();
            }
        }

        // if not exist rows in database 
        public function checkIfNoProducts() 
        {
          $sql = 'SELECT * FROM products';
          $stmt = $this->conn->prepare($sql);
          $stmt->execute();
          if ($stmt->rowCount() == 0) {
              echo "No added products yet!";
          }
        }


            // select all products 
          public function getAllProducts() 
          {
              $sql_sku = "SELECT * FROM products";
              $stmt = $this->conn->query($sql_sku);
              return $stmt->fetchAll(PDO::FETCH_ASSOC);
          }
    
   
          // firstly must choose category
          public function setCategory($category)
          {
            if(empty($category)){
              $ex = new ExchooseProduct();
              throw $ex;
              }
            $this->category = $category;
            return $this;
          }

          // validation for input price
          public function validateInputDataPrice($price) 
          {
            if (!preg_match('/^\d+(\.\d+)?$/', $price)){
                throw new Exinvalidprice();
            }
          }


        
        // validation for name - after strings support numbers
        public function validateInputDataName($name)
        {
             $name_pattern = '/^[A-Za-z][A-Za-z0-9\s]*$/';
          if (!preg_match($name_pattern, $name)){
            throw new Exinvalidname();
          }
        }


          // validation for input size support only whole numbers 
        public function validateInputDataSize($size, $width = null, $height = null, $length = null, $weight = null)
        {
          if(($weight == null) && ($width == null) && ($height == null) && ($length == null)){
            $size_pattern = '/^[0-9]+$/'; 
            if (!preg_match($size_pattern, $size)){
              throw new ExinvalidSize();
            } 
          }
        }


        // validation for inputs of furniture // support whole numbers and decimal numbers
        public function validateInputDataFurniture($size = null, $width, $heigh, $length, $weight = null)
        {
          if(($size == null) && ($weight == null)){
            $fur_pattern = '/^[0-9]+(?:\.[0-9]+)?$/'; 
            if (!preg_match($fur_pattern, $width) || !preg_match($fur_pattern, $heigh) || !preg_match($fur_pattern, $length)){
              throw new ExinvalidFurnitureInputs();
            }
          }
        }


        // validation for Weight // support whole numbers and only three decimal numbers after "dot"
        public function validateInputDataWeight($size = null, $width = null, $height = null, $length = null, $weight)
        {
          if(($size == null) && ($width == null) && ($height == null) && ($length == null)){
            $weight_pattern = '/^[0-9]+(?:\.[0-9]{1,3})?$/'; 
            if (!preg_match($weight_pattern, $weight)){
              throw new ExinvalidWeight();
            }
          }
        }




              // checking if is picked one of first three values from drop-down menu// required all data 
          public static function validateForm($category, $sku, $name, $price, $size = null, $width = null, $height = null, $length = null, $weight = null) 
          {
              $decrypted = decrypt($category);

              if($decrypted === "1") {
                  if (empty($sku) || empty($name) || empty($price) || empty($size)) {
                      throw new ExrequiredData();    
                  }
              }
              
              else if($decrypted === "2") {
                  if (empty($sku) || empty($name) || empty($price) || empty($width) || empty($height) || empty($length)) {
                      throw new ExrequiredData();
                }
              }
              
              else if($decrypted === "3") {
                  if (empty($sku) || empty($name) || empty($price) || empty($weight)) {
                      throw new ExrequiredData();
                  }
              }
           }


      
 
            // validation for name must be at least with 6 chars !
            public function setName($name)
            {
              if(strlen($name) <= 5){  
                $ex = new requiredName();
                throw $ex;
              }
                $this->name = $name;
                return $this;
            }

  
            // if size is empty give null value  
            public static function validateSize($size) 
            {
              if ($size == '') {
                return NULL;
              }
               return $size;
            }
            // if fur inputs are empty, then return all as null 
            public static function validateDimensions($height, $width, $length) 
            {
              if ($height === '' && $width === '' && $length === '') {
                  return array(NULL, NULL, NULL);
              }
              return array($height ?: NULL, $width ?: NULL, $length ?: NULL); // но ако некоја димензија е празен знак, таа димензија се заменува со NULL.
            }



              // also if is empty weight return null 
              public static function validateWeight($weight) 
              {
                if ($weight == '') {
                  return NULL;
                }
                 return $weight;
              }


              // insert categories in db' cat table
            public function insertCategory($categoryname) 
            {
              $conn = $this->conn;
              $sql_insert = "INSERT INTO category (name) VALUES(:name)";
              $statement = $conn->prepare($sql_insert); // to don't allow sql injection

              $data = ['name' => $categoryname];

              if($statement->execute($data)){
                return true;
              } else {
                return false;
              }
            }


          // adding product in db
          public function addProduct($category, $sku, $name, $price, $description, $size, $height, $width, $length, $weight) {
            $sql = "INSERT INTO products (category_id_fk, sku, name, price, description, size_cd, height_f, width_f, length_f, weight_book) VALUES(:category_param, :sku, :name, :price, :description, :size, :height, :width, :length, :weight)";

            $statement = $this->conn->prepare($sql);

            $data = [
              'category_param' => decrypt($category),
              'sku' => $sku,
              'name' => $name,
              'price'  => $price,
              'description' => $description,
              'size' => $size,
              'height' => $height,
              'width' => $width,
              'length' => $length,
              'weight' => $weight
            ];

            if($statement->execute($data)) {
              return true;
            } else {
              return false;
            }
          }


              // mass delete 
          public function deleteProducts($productIds) 
          {
            foreach ($productIds as $productId) 
            {
                $sql = 'DELETE FROM products WHERE product_id = :product_id';
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['product_id' => $productId]);
            }
          }


      public function getCategory()
          {
            $sql_select = "SELECT * FROM category";
            $conn = $this->conn;
            $statement = $conn->query($sql_select);
            return $statement;
          }

} ?>
