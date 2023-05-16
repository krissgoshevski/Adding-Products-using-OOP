
This app is build with next technologies: SASS, Javascript, PHP, MySQL.

- Procedural PHP code within PHP classes. Rest logic is placed within class methods.
- Interface class
- Parent abstract class extends interface
- Child classes are extends from Parent class
- Example of Polymorphism provision
- example of using sql injection
- example of using encrypt and decrypt functions, all values for client is encrypted, after that i decrypted values and store in database.
- many validations for all inputs, you can view it in my Abstract class product.php, in my methods!

  1. Product page is with next requirements and functionalities:

- SKU (id: #sku)
- Name (id: #name)
- Price (id: #price)

- Product type switcher (id: #productType) with following options:
    - DVD 
    - Book 
    - Furniture 
    
- Product type-specific attribute
    - Size input field (in MB) for DVD-disc
    - Weight input field (in Kg) for Book 
    - Each from Dimensions input fields (HxWxL) for Furniture 
        
        ### **Add product page requirements:**

- The form is dynamically changed when the type is switched
- Special attributes have a description, related to their type, e.g.: “Please, provide dimensions” / “Please, provide weight” / “Please, provide size” when related product type is selected
- All fields are mandatory for submission, missing values trigger notification “Please, submit required data”
- Implement input field value validation, invalid data trigger notification “Please, provide the data of indicated type”
- The page have a “Save” button to save the product. Once saved, return to the “Product List” page with the new product added.
- The page have a “Cancel” button to cancel adding the product action. Once canceled, returned to the “Product List” page with no new products added.
- SKU is unique for each product and it is not possible to save products if already any product with the same SKU exists.
        
        
  2. List page is with next requirements and functionalities:
 
-  - SKU (unique for each product)
-  - Name
-  - Price in $
- One of the product-specific attributes and its value
    - Size (in MB) for DVD-disc
    - Weight (in Kg) for Book
    - Dimensions (HxWxL) for Furniture
    - 
### Required UI elements:

- “ADD” button, which is lead to the “Product Add” page
- “MASS DELETE” action, implemented as checkboxes next to each product (have a class: delete-checkbox) and a button “MASS DELETE” triggering delete action for the selected products.


