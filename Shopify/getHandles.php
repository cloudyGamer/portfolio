<?php
//this code fetches product names from a list of product ids
//access input data
$string = file_get_contents(__DIR__."/product_ids.json");
//echo "string = ".$string;
$product_idWrapper = json_decode($string);
$product_ids = $product_idWrapper ->products;
//echo json_encode($product_ids);
$handleHolder = array();
foreach ($product_ids as $product_id) {
    $id = $product_id->id;
    echo "individual product".json_encode($id)."\n";
    //dynamic url
    $url = "https://6725a4fb40bf84a8a9354b7609324830:6a5a38773e910c6da427a292890c45bb@bubbles-and-co.myshopify.com/admin/api/2019-04/products/".$id.".json?fields=handle"; 
    //initialise curl
    $curl = curl_init( $url );
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //execute url
    $json_response = curl_exec($curl);
    curl_close($curl);
    echo "individual handle".json_encode($json_response)."\n";
    //save result
    $handleHolder[]=$json_response;
}

echo "handle holder".json_encode($handleHolder);
//
?>