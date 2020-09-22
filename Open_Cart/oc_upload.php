<?php
//$prefix = '/Applications/MAMP/htdocs/';
$prefix = '/home/customer/www/pauldowlingportfolio.com/public_html/';

// <editor-fold defaultstate="collapsed" desc="set up logger">
require $prefix.'opencart-3.0.3.1/upload/system/storage/vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
$curlRequest = new Logger('oc_upload.php');
// Now add some handlers
$curlRequest->pushHandler(new StreamHandler($prefix.'opencart-3.0.3.1/upload/app.log', Logger::DEBUG));
$curlRequest->pushHandler(new FirePHPHandler());
$curlRequest->info('a message to you lucille');// </editor-fold>

// set up curl request
$url = "https://pauldowlingportfolio.com/opencart-3.0.3.1/upload/index.php?route=api/product/addEnMass&api_token=[apitoken]";

function do_curl_request($url, $params=array()) {
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/apicookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/apicookie.txt');

$params_string = '';

if (is_array($params) && count($params)) {
foreach($params as $key=>$value) {
$params_string .= $key.'='.$value.'&';
}
rtrim($params_string, '&'); 

curl_setopt($ch,CURLOPT_POST, count($params));
curl_setopt($ch,CURLOPT_POSTFIELDS, $params_string);
}

//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);

return $result;
} 

//open file containing individual products
     $fileName = $prefix.'opencart-3.0.3.1/upload/upload_products/products.json';
     $fp = fopen($fileName, 'r');
     $contents = fread($fp, filesize($fileName));
     $contents = json_decode($contents);
     $i=0;
     foreach ($contents as $content){
         
        $variants = $content->product->variants;
        $quantity = $variants[0]->inventory_quantity;
        $price = $variants[0]->price;
        $sku = $variants[0]->sku;
        
        $title = $content->product->title;
        $title = strtolower($title);
        $title= ucwords($title); 
        
        $src = $content->image->src;
        $img = $prefix.'opencart-3.0.3.1/upload/image/catalog/'.$title.'.png';
        file_put_contents($img, file_get_contents($src));
        
        $curlRequest->info($title).'\n';
        
        $body_html = $content->product->body_html;
        $body_html = strip_tags($body_html);
        $body_html = stripslashes($body_html);
        
        $body_html = str_replace("\n\nDescription\nIngredients","",$body_html);
        //$curlRequest->info($body_html);

        //$uncapBody = strtolower($body_html);
        $ingMatch = array ('INGREDIENTS:','ingredients:','Ingredients:');
        foreach ($ingMatch as $spelling) {
            if (strpos($body_html, $spelling)  !== false) {
                $curlRequest->info('contains ing');
                $explodeBody = explode($spelling,$body_html);
                $description = $explodeBody[0];
                $curlRequest->info('description\n\n'.$description);
                
            }
        }
  
        $ingredients = $explodeBody[1];
        $curlRequest->info('ingredients:');
        $curlRequest->info($ingredients); //
        //$ingredients = trim($ingredients,"<>/"); //
        $ingredients = explode(",",$ingredients);
        
        //look for natural ingredients and add an asterix to them
        $naturalIngFile = $prefix.'opencart-3.0.3.1/upload/upload_products/natural_ingredients.json';
        $fp = fopen($naturalIngFile, 'r');
        $naturalIngredients = fread($fp, filesize($naturalIngFile));
        $naturalIngredients = json_decode($naturalIngredients);
        $naturalIngredients = $naturalIngredients->natural_ingredients;
        

        foreach($naturalIngredients as $naturalIngredient){
            //$curlRequest->info('natural Ingredient'.json_encode($naturalIngredient));
            $naturalIngredient = ' '.$naturalIngredient;
            foreach($ingredients as &$ingredient){
                if($ingredient===$naturalIngredient)
                    {
                     $ingredient = trim($ingredient,' ');
                     $ingredient = ' *'.$ingredient;
                    }
                    else{
                        //$curlRequest->info('No similiarity'.$ingredient."!==".$naturalIngredient);
                    }
                }
                //if(++$g > 5) break;
            }
         //$curlRequest->info('ingredients= '.json_encode($ingredients));

        $ingredients = implode(",", $ingredients);

        $body = $description.'INGREDIENTS:'.$ingredients;
        //$curlRequest->info('body='.json_encode($body));

        curl_close($prodCurl);
        //$curlRequest->info('product item: '.json_encode($content));
       
        //$encodedData = json_encode($data);
        $fields = array(
            'image' => 'catalog/'.$title.'.png',
            'quantity' => $quantity,
            'price' => $price,
            'upc' => $variants[0]-> barcode,
            'sku' => $variants[0] -> sku,
            'meta_title' => $title.'meta_tag',
            'description' => utf8_encode($body),
            'name' => $title
            );
        //$curlRequest->info('fields: '.json_encode($fields));

        $response = do_curl_request($url, $fields);
        //$curlRequest->info(json_encode($data));
        if(++$i > 20) break;
     }


     fclose($fp);

 ?>


