<?php
//this code fetches product data from a list of product sku's
//access input data
$string = file_get_contents(__DIR__."/results.json");
//echo "string = ".$string;
$product_idWrapper = json_decode($string);

//echo json_encode($product_ids);
$skuHolder = array();
 foreach ($product_idWrapper as $product_id) {
    $sku = $product_id->sku;
    echo "individual product".json_encode($sku)."\n";
    //dynamic url
    $url = "https://[apitoken]@bubbles-and-co.myshopify.com/admin/variants/search.json?query=sku:".$sku; 
    //initialise curl
    $curl = curl_init( $url );
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //execute url
    $json_response = curl_exec($curl);
    curl_close($curl);
    //save result
     echo "skus=".$json_response."\n";
     $skuHolder[]=$json_response;
    
}
echo "sku holder".json_encode($skuHolder);
//
?>