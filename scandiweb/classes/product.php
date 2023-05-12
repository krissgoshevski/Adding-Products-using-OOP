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

private $conn;
private $category;
private $sku;
private $name;
private $price;        
private $description;        
private $size;
private  $width;
private  $height;
private $length;
private $weight;


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
             $this->sku = $sku;
            if(strlen($this->sku) != 9){  
              $ex = new requiredsku();
              throw $ex;
            }
             
              return $this;
          }



      // validation for unique Sku
        public function validateSkuUnique($sku)
        {
                $this->sku = $sku;
                
            $conn = $this->conn;
            $stmt = $conn->prepare("SELECT * FROM products WHERE sku = :sku");
            $stmt->bindParam(":sku", $this->sku);
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
            $this->category = $category;
            if(empty($this->category)){
              $ex = new ExchooseProduct();
              throw $ex;
              }
            
            return $this;
          }

          // validation for input price
          public function validateInputDataPrice($price) 
          {
                  $this->price = $price;
            if (!preg_match('/^\d+(\.\d+)?$/', $this->price)){
                throw new Exinvalidprice();
            }
          }


        
        // validation for name - after strings support numbers
        public function validateInputDataName($name)
        {
                $this->name = $name;
             $name_pattern = '/^[A-Za-z][A-Za-z0-9\s]*$/';
          if (!preg_match($name_pattern, $this->name)){
            throw new Exinvalidname();
          }
        }


          // validation for input size support only whole numbers 
        public function validateInputDataSize($size, $width = null, $height = null, $length = null, $weight = null)
        {
           $this->size = $size;
                
          if(($weight == null) && ($width == null) && ($height == null) && ($length == null)){
            $size_pattern = '/^[0-9]+$/'; 
            if (!preg_match($size_pattern, $this->size)){
              throw new ExinvalidSize();
            } 
          }
        }


        // validation for inputs of furniture // support whole numbers and decimal numbers
        public function validateInputDataFurniture($size = null, $width, $height, $length, $weight = null)
        {
                $this->width = $width;
                $this->height = $height;
                $this->length = $length;
                
          if(($size == null) && ($weight == null)){
            $fur_pattern = '/^[0-9]+(?:\.[0-9]+)?$/'; 
            if (!preg_match($fur_pattern, $this->width) || !preg_match($fur_pattern, $this->height) || !preg_match($fur_pattern, $this->length)){
              throw new ExinvalidFurnitureInputs();
            }
          }
        }


        // validation for Weight // support whole numbers and only three decimal numbers after "dot"
        public function validateInputDataWeight($size = null, $width = null, $height = null, $length = null, $weight)
        {
                $this->weight = $weight;
                
          if(($size == null) && ($width == null) && ($height == null) && ($length == null)){
                  
            $weight_pattern = '/^[0-9]+(?:\.[0-9]{1,3})?$/'; 
                  
            if (!preg_match($weight_pattern, $this->weight)){
              throw new ExinvalidWeight();
            }
          }
        }




              // checking if is picked one of first three values from drop-down menu// required all data 
          public function validateForm($category, $sku, $name, $price, $size = null, $width = null, $height = null, $length = null, $weight = null) 
          {
              $this->category = $category;
              $this->sku = $sku;
              $this->name = $name;
              $this->price = $price;
              
              $decrypted = decrypt($this->category);

              if($decrypted === "1") {
                  if (empty($this->sku) || empty($this->name) || empty($this->price) || empty($size)) {
                      throw new ExrequiredData();    
                  }
              }
              
              else if($decrypted === "2") {
                  if (empty($this->sku) || empty($this->name) || empty($this->price) || empty($width) || empty($height) || empty($length)) {
                      throw new ExrequiredData();
                }
              }
              
              else if($decrypted === "3") {
                  if (empty($this->sku) || empty($this->name) || empty($this->price) || empty($weight)) {
                      throw new ExrequiredData();
                  }
              }
           }


      
 
            // validation for name must be at least with 6 chars !
            public function setName($name)
            {
              $this->name = $name;
                    
              if(strlen($this->name) <= 5){  
                $ex = new requiredName();
                throw $ex;
              }
               
                return $this;
            }

  
            // if size is empty give null value  
            public function validateSize($size) 
            {
                $this->size = $size;
                    
              if ($this->size == '') {
                return NULL;
              }
               return $this->size;
            }
            // if fur inputs are empty, then return all as null 
            public function validateDimensions($height, $width, $length) 
            {
              $this->height = $height;
              $this->width = $width;
              $this->length = $length;

              if ($this->height === '' && $this->width === '' && $this->length === '') {
                  return array(NULL, NULL, NULL);
              }
              return array($this->height ?: NULL, $this->width ?: NULL, $this->length ?: NULL); // но ако некоја димензија е празен знак, таа димензија се заменува со NULL.
            }



              // also if is empty weight return null 
              public function validateWeight($weight) 
              {   
                $this->weight = $weight;
                if ($this->weight == '') {
                  return NULL;
                }
                 return $this->weight;
              }


              // insert categories in db' cat table
            public function insertCategory($categoryname) 
            {
              $this->category = $categoryname;
                    
              $conn = $this->conn;
              $sql_insert = "INSERT INTO category (name) VALUES(:name)";
              $statement = $conn->prepare($sql_insert); // to don't allow sql injection

              $data = ['name' => $this->category];

              if($statement->execute($data)){
                return true;
              } else {
                return false;
              }
            }


          // adding product in db
          public function addProduct($category, $sku, $name, $price, $description, $size, $height, $width, $length, $weight) {
                  
                  $this->category = $category;
                  $this->sku = $sku;
                  $this->name = $name;
                  $this->price = $price;
                  $this->description = $description;
                  $this->size = $size;
                  $this->height = $height;
                  $this->width = $width;
                  $this->length = $length;
                  $this->weight = $weight;
                  
            $sql = "INSERT INTO products (category_id_fk, sku, name, price, description, size_cd, height_f, width_f, length_f, weight_book) VALUES(:category_param, :sku, :name, :price, :description, :size, :height, :width, :length, :weight)";

            $statement = $this->conn->prepare($sql);

            $data = [
              'category_param' => decrypt($this->category),
              'sku' => $this->sku,
              'name' => $this->name,
              'price'  => $this->price,
              'description' => $this->description,
              'size' => $this->size,
              'height' => $this->height,
              'width' => $this->width,
              'length' => $this->length,
              'weight' => $this->weight
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
