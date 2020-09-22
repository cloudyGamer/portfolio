<?php
require __DIR__ . '/system/storage/vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
/*if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}*/

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient(){
    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAuthConfig('http://www.testonly.forevermecosmetics.ie/upload/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'http://www.testonly.forevermecosmetics.ie/upload/token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));
           // $authCode = "4/rgEoXhWL9tHA_BKvGJrQi_JHrmO381SX8GtH68i6_tcgpDYbthAq4PM";

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Sheets($client);

$spreadsheetId = '1kbKTaXsmKuF9fJCxNMedyXqadFSiGtuS97V4GgFrrTA';
      
for ($x = 60; $x <= 60; $x++) {
      
        $range = 'Sheet1!'.$x.':'.$x;
        //$range = 'Sheet1!F6:F6';=
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
        $encodedValues = json_encode($values);
        //echo "the response is $encodedValues <br>";
        //$valLength = count($values);
     
        foreach ($values as $value){
            //$valLength = count($value);
            //echo "count for values is  ".$valLength;
            //$combinedArrays = array_combine($keys, $value);
            //$json['products'][] = $combinedArrays;
            // call for keys
            $range1 = 'Sheet1!1:1';
            $response = $service->spreadsheets_values->get($spreadsheetId, $range1);
            $keys = $response->getValues();
            $curlRequest = new Logger('quickstart.php().php');
            // Now add some handlers
            $curlRequest->pushHandler(new StreamHandler('/home/customer/www/pauldowlingportfolio.com/public_html/opencart-3.0.3.1/upload/app.log', Logger::DEBUG));
            $curlRequest->pushHandler(new FirePHPHandler());
            $curlRequest->info('keys= '.json_encode($keys));
              
              
              
            foreach ($keys as $key){
                $length = count($key);
                $combinedArrays = array_combine($key, $value);
                 // echo 'description= '.json_encode($combinedArrays['descriptionFull']);
                $desc = $combinedArrays['descriptionFull'];
                $ing = $combinedArrays['Ingredients'];
                $body_html = "<div id='tabs'><ul><li><a href='#tabs-1'>Description</a></li><li><a href='#tabs-2'>Ingredients</a></li></ul><div id='tabs-1'><meta charset='utf-8' /><p>$desc</p></div><div id='tabs-2'><meta charset='utf-8' /><p>$ing</p></div></div>";
                //echo "src=".$combinedArrays['src']."\n";
                //encode image 
                $path = $combinedArrays['src'];
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = base64_encode($data);
                                //
                $jsonPairs = array (
                    'tags'    => $combinedArrays['tags'],
                     "title"    => $combinedArrays["title"],
                    'body_html'    => $body_html,
                    'vendor'    => $combinedArrays['vendor'],
                    'product_type'    => $combinedArrays['product_type'],
                    'src'    => $base64,
                    'sku' => $combinedArrays['id'],
                    'price' => $combinedArrays['price'],
                    'quantity' => $combinedArrays['quantity'],
                    'published_scope' => $combinedArrays['published_scope']

                );
                $jsonPairHolder[]=$jsonPairs;
                echo json_encode($combinedArrays["price"])."\n";
            }
        }
       
}
///// end of copied code

     $fp = fopen('http://www.testonly.forevermecosmetics.ie/upload/postman/results.json', 'w');
      fwrite($fp, json_encode($jsonPairHolder));
      fclose($fp);


 ?>

