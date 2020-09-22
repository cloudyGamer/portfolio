<?php
//$prefix = '/Applications/MAMP/htdocs/';
$prefix = '/home/customer/www/pauldowlingportfolio.com/public_html/';
// <editor-fold defaultstate="collapsed" desc="det up logger">
require $prefix.'opencart-3.0.3.1/upload/system/storage/vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
$curlRequest = new Logger('downloadBathbombs.php');
// Now add some handlers
$curlRequest->pushHandler(new StreamHandler($prefix.'opencart-3.0.3.1/upload/app.log', Logger::DEBUG));
$curlRequest->pushHandler(new FirePHPHandler());// </editor-fold>
//query the opencart api for a specific collection of products e.g 'Bath Bombs'
$collection_id = '93761830995';
  ///dynamic url
$url = "https://[apitoken]"
            . "@bubbles-and-co.myshopify.com/admin/api/2020-04/collections/'.$collection_id.'/products.json"; 
      //initialise curl
$curl = curl_init( $url );
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      
//execute url
$json_response = curl_exec($curl);
curl_close($curl);
$curlRequest->info($json_response);

//write the response to JSON

$fp = fopen($prefix.'opencart-3.0.3.1/upload/upload_products/collections.json', 'w');
      fwrite($fp, $json_response);
      fclose($fp);

//access the JSON file, extract individual product ID's and get product information from Shopify
//API with ID      
////////////////////////
$fileName = $prefix.'opencart-3.0.3.1/upload/upload_products/collections.json';
     $fp = fopen($fileName, 'r');
     $contents = fread($fp, filesize($fileName));
     $contents = json_decode($contents);
     $contents = $contents->products;
     $i=0;
     foreach ($contents as $content){
         
        //remove this from shopify call and put with opencart code
        file_put_contents($img, file_get_contents($src));
        //////////////////////////////////////////////////////////
        //set up second curl
        ///dynamic url
        $product_id = $content->id;
        $prodUrl = "https://[apitoken]"
                . "@bubbles-and-co.myshopify.com/admin/api/2020-04/products/".$product_id.".json"; 
          //initialise curl
        $prodCurl = curl_init( $prodUrl );
        curl_setopt($prodCurl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($prodCurl, CURLOPT_RETURNTRANSFER, true);

        //execute product curl
        $json_response = curl_exec($prodCurl);
        
        //save to json file
        $fp = fopen($prefix.'opencart-3.0.3.1/upload/upload_products/products.json', 'a');
        fwrite($fp, $json_response);
        fclose($fp);
     
     }
      
      
