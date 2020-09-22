<?php
require_once "/home/customer/www/pauldowlingportfolio.com/public_html/opencart-3.0.3.1/upload/system/storage/vendor/autoload.php";
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

$curlRequest = new Logger('addOrder_pp_info_response.php');
	// Now add some handlers
$curlRequest->pushHandler(new StreamHandler('/home/customer/www/pauldowlingportfolio.com/public_html/opencart-3.0.3.1/upload/app.log', Logger::DEBUG));
$curlRequest->pushHandler(new FirePHPHandler());
//CORS Request handling
    if($_SERVER['REQUEST_METHOD'] == "GET") {

        $curlRequest->info('response= GET condition met');
        header('Content-Type: text/plain');
        echo json_encode("This HTTP resource is designed to handle POSTed XML input");

    } elseif($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
            $curlRequest->info('response= OPTIONS condition met');
      // Tell the Client we support invocations from arunranga.com and 
      // that this preflight holds good for only 20 days
      //exit(0);
       echo json_encode("options taken");
    } elseif($_SERVER['REQUEST_METHOD'] == "POST") {
        //$curlRequest->info('response= POST conditions met');
        $url = $_POST["url"];
        // Handle POST by first getting the XML POST blob, 
        // and then doing something to it, and then sending results to the client
        //my curl request goes here
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
    $fields = array(
    );

    $newVar = do_curl_request($url);

    $curlRequest->info('this is the response from pp_order:'.$newVar.__Line__);
    /*create file here*/
    //decode json
    $html_toWrite = json_decode($newVar);
    //create file
    $prefix = '/home/customer/www/pauldowlingportfolio.com/public_html/';
    $body_html = 'ran string again';
    $fp = fopen($prefix.'opencart-3.0.3.1/upload/upload_products/newestbody.html', 'a');
    fwrite($fp, $html_toWrite);
    fclose($fp);
    /******************/
    $newVar = json_encode($newVar);
    echo $newVar;
    $curlRequest->info("this is the encode response from pp_order:".$newVar);
  }
else{
    die("No Other Methods Allowed");
}

?>


