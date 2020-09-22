<?php
//this is a new function written into the Open Cart product handling API.
//file path upload/catalog/controller/api/product

//$prefix = '/Applications/MAMP/htdocs/';
$prefix = '/home/customer/www/pauldowlingportfolio.com/public_html/';
require $prefix.'opencart-3.0.3.1/upload/system/storage/vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
$curlRequest = new Logger('quickstart.php');
// Now add some handlers
$curlRequest->pushHandler(new StreamHandler($prefix.'opencart-3.0.3.1/upload/app.log', Logger::DEBUG));
$curlRequest->pushHandler(new FirePHPHandler());

class ControllerApiProduct extends Controller {
     public function addEnMass() {
            //$prefix = '/Applications/MAMP/htdocs/';
             $prefix = '/home/customer/www/pauldowlingportfolio.com/public_html/';
             $curlRequest = new Logger('product_controller_addEnMass().php');
	// Now add some handlers
             $curlRequest->pushHandler(new StreamHandler($prefix.'opencart-3.0.3.1/upload/app.log', Logger::DEBUG));
             $curlRequest->pushHandler(new FirePHPHandler());
             if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // collect value of input field
                 
                    if (empty($_POST['image'])) {
                        $curlRequest->info('product addEnMass called and recieving: empty value');
                    } else {
                        $data = array (
                            'product_description' => array(
                                    '1' => array (
                                        'name' => $_POST['name'],
                                        'description' => utf8_encode( ' '.$_POST['description'].' '),
                                        'meta_title' =>  $_POST['meta_title'].'meta_tag',
                                        'meta_description' => '',
                                        'meta_keyword' => '',
                                        'tag' => 'bath_bomb',
                                    )
                                ),
                            'model' => 'x100',
                            'sku' =>  $_POST['sku'],
                            'upc' =>  $_POST['upc'],
                            'ean' => '',
                            'ean' => '',
                            'jan' => '',
                            'isbn' => '',
                            'mpn' => '',
                            'location' => '',
                            'price' =>  $_POST['price'],
                            'tax_class_id' => '',
                            'quantity' =>  $_POST['quantity'],
                            'minimum' => '',
                            'subtract' => '1',
                            'stock_status_id' => '7',
                            'shipping' => '1',
                            'date_available' => '2020-05-09',
                            'length'=>'',
                            'width'=>'',
                            'height'=>'',
                            'length_class_id'=>'1',
                            'weight'=>'',
                            'weight_class_id'=>'1',
                            'status'=>'1',
                            'sort_order'=>'1',
                            'manufacturer'=>'Bomb Cosmetics',
                            'manufacturer_id'=>'8',
                            'category'=>'',
                            'product_category'=>['59'],
                            'filter'=>'',
                            'product_store'=>['0'],
                            'download'=>'',
                            'related'=>'',
                            'option'=>'',
                            'image'=> $_POST['image'],
                            'points'=>'',
                            'product_reward' => array(
                                '1' => array(
                                    'points' => ''
                                )
                            ),
                            'product_seo_url' => [array('1'=>'')],
                            'product_layout' => ['']
                        );
                        //call model
                        if (isset($data['image'])) {
                            $curlRequest->info('data[image] controller set');
                        }else{
                            $curlRequest->info('data[image] controller not set');
                        }
//                        $curlRequest->info('product addEnMass called and recieving'.json_encode($decodedData->image));
                        $this->load->model('catalog/product');
                        $response = $this->model_catalog_product->addProducts($data);
                       $curlRequest->info('product addProducts model returns:'.$response);
                    }

            }else{
                $curlRequest->info("method is not post");
            }
             

//		$this->load->language('catalog/product');
//
//		$this->document->setTitle($this->language->get('heading_title'));
//
//		$this->load->model('catalog/product');
//
//		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
//			$this->model_catalog_product->addProduct($this->request->post);
//
//			$this->session->data['success'] = $this->language->get('text_success');
//
//			$url = '';
//
//			if (isset($this->request->get['filter_name'])) {
//				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
//			}
//
//			if (isset($this->request->get['filter_model'])) {
//				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
//			}
//
//			if (isset($this->request->get['filter_price'])) {
//				$url .= '&filter_price=' . $this->request->get['filter_price'];
//			}
//
//			if (isset($this->request->get['filter_quantity'])) {
//				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
//			}
//
//			if (isset($this->request->get['filter_status'])) {
//				$url .= '&filter_status=' . $this->request->get['filter_status'];
//			}
//
//			if (isset($this->request->get['sort'])) {
//				$url .= '&sort=' . $this->request->get['sort'];
//			}
//
//			if (isset($this->request->get['order'])) {
//				$url .= '&order=' . $this->request->get['order'];
//			}
//
//			if (isset($this->request->get['page'])) {
//				$url .= '&page=' . $this->request->get['page'];
//			}
//
//			$this->response->redirect($this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true));
//		}
//
//		$this->getForm();
	}
    } 

