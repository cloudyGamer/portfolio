<?php
require_once "/home/customer/www/pauldowlingportfolio.com/public_html/opencart-3.0.3.1/upload/system/storage/vendor/autoload.php";
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

class ControllerApiOrder extends Controller {
    public function pp_Order(){
        $pp_order = new Logger('my_logger');
        // Now add some handlers
        $pp_order->pushHandler(new StreamHandler('/home/customer/www/pauldowlingportfolio.com/public_html/opencart-3.0.3.1/upload/app.log', Logger::DEBUG));
        $pp_order->pushHandler(new FirePHPHandler());
        $pp_order->info('pp_order ran ');
    
        $this->load->language('api/shipping');
        //check that we have our token
        if (!isset($this->session->data['api_id'])) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {
            /////////////////create customer/ taken from register/index
            $this->load->language('checkout/checkout');
            $data['customer_groups'] = array();

            if (is_array($this->config->get('config_customer_group_display'))) {
                    $this->load->model('account/customer_group');
                    $customer_groups = $this->model_account_customer_group->getCustomerGroups();
                    foreach ($customer_groups  as $customer_group) {
                        if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
                            $data['customer_groups'][] = $customer_group;
                        }
                    }
		} 
	}
	//end of api id validation
	$data['customer_group_id'] = $this->config->get('config_customer_group_id');

        if (isset($this->session->data['shipping_address']['postcode'])) {
                $data['postcode'] = $this->session->data['shipping_address']['postcode'];
        } else {
                $data['postcode'] = '';
        }

        if (isset($this->session->data['shipping_address']['country_id'])) {
                $data['country_id'] = $this->session->data['shipping_address']['country_id'];
        } else {
                $data['country_id'] = $this->config->get('config_country_id');
        }

        if (isset($this->session->data['shipping_address']['zone_id'])) {
                $data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
        } else {
                $data['zone_id'] = '';
        }

        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        // Custom Fields
        $this->load->model('account/custom_field');

        $data['custom_fields'] = $this->model_account_custom_field->getCustomFields();

        if ($this->config->get('config_account_id')) {
                $this->load->model('catalog/information');

                $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

        $data['shipping_required'] = $this->cart->hasShipping();

	}
	////////////// from checkout/register save
	$this->load->language('checkout/checkout');
            
        $json = array();

        if (!$json) {
            $this->load->model('account/customer');

            $save_logger = new Logger('api/order/pp_order');
            // Now add some handlers
            $save_logger->pushHandler(new StreamHandler('/home/customer/www/pauldowlingportfolio.com/public_html/opencart-3.0.3.1/upload/app.log', Logger::DEBUG));
            $save_logger->pushHandler(new FirePHPHandler());

            if ((utf8_strlen(trim($this->session->data['payment_address']['firstname'])) < 1) || (utf8_strlen(trim($this->session->data['payment_address']['firstname'])) > 32)) {
                    $json['error']['payment_address']['firstname'] = $this->language->get('error_firstname');
            }

            if ((utf8_strlen(trim($this->session->data['payment_address']['lastname'])) < 1) || (utf8_strlen(trim($this->session->data['payment_address']['lastname'])) > 32)) {
                    $json['error']['payment_address']['lastname'] = $this->language->get('error_lastname');
            }

            if ((utf8_strlen($this->session->data['customer']['email']) > 96) || !filter_var($this->session->data['customer']['email'], FILTER_VALIDATE_EMAIL)) {
                    $json['error']['customer']['email'] = $this->language->get('error_email');
            }

            if ($this->model_account_customer->getTotalCustomersByEmail($this->session->data['customer']['email'])) {
                    $json['error']['warning'] = $this->language->get('error_exists');
            }

            if ((utf8_strlen($this->session->data['customer']['telephone']) < 3) || (utf8_strlen($this->session->data['customer']['telephone']) > 32)) {
                    $json['error']['customer']['telephone'] = $this->language->get('error_telephone');
            }

            if ((utf8_strlen(trim($this->session->data['shipping_address']['address_1'])) < 3) || (utf8_strlen(trim($this->session->data['shipping_address']['address_1'])) > 128)) {
                    $json['error']['shipping_address']['address_1'] = $this->language->get('error_address_1');
            }

            if ((utf8_strlen(trim($this->session->data['payment_address']['city'])) < 2) || (utf8_strlen(trim($this->session->data['payment_address']['city'])) > 128)) {
                    $json['error']['payment_address']['city'] = $this->language->get('error_city');
            }

            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry($this->session->data['shipping_address']['country_id']);

            if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->session->data['shipping_address']['postcode'])) < 2 || utf8_strlen(trim($this->session->data['shipping_address']['postcode'])) > 10)) {
                    $json['error']['shipping_address']['postcode'] = $this->language->get('error_postcode');
            }

            if ($this->session->data['shipping_address']['country_id'] == '') {
                    $json['error']['country'] = $this->language->get('error_country');
            }

            if (!isset($this->session->data['shipping_address']['zone_id']) || $this->session->data['shipping_address']['zone_id'] == '' || !is_numeric($this->session->data['shipping_address']['zone_id'])) {
                    $json['error']['zone'] = $this->language->get('error_zone');
            }


            // Customer Group
            if (isset($this->session->data['customer']['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->session->data['customer']['customer_group_id'], $this->config->get('config_customer_group_display'))) {
                    $customer_group_id = $this->session->data['customer']['customer_group_id'];
            } else {
                    $customer_group_id = $this->config->get('config_customer_group_id');
            }

        }
        ///////////////////////////////////// from chekout/guest/save

        $pp_order = new Logger('checkout/guest/save_logger'.__Line__);
        // Now add some handlers
        $pp_order->pushHandler(new StreamHandler('/home/customer/www/pauldowlingportfolio.com/public_html/opencart-3.0.3.1/upload/app.log', Logger::DEBUG));
        $pp_order->pushHandler(new FirePHPHandler());

        $this->load->language('checkout/checkout');

        $json = array();
		
        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
                $pp_order->info('condition failed'.__Line__);
                $json['redirect'] = $this->url->link('checkout/cart');
        }

        // Check if guest checkout is available.
        if (!$this->config->get('config_checkout_guest') || $this->config->get('config_customer_price') || $this->cart->hasDownload()) {
                $pp_order->info('condition failed'.__Line__);
                $json['redirect'] = $this->url->link('checkout/checkout', '', true);
        }

        if (!$json) {
            $pp_order->info('pp_order/save_  if(!$json)'.__Line__);

            if ((utf8_strlen(trim($this->session->data['shipping_address']['address_1'])) < 3) || (utf8_strlen(trim($this->session->data['shipping_address']['address_1'])) > 128)) {
                    $json['error']['address_1'] = $this->language->get('error_address_1');
            }

            if ((utf8_strlen(trim($this->session->data['shipping_address']['city'])) < 2) || (utf8_strlen(trim($this->session->data['shipping_address']['city'])) > 128)) {
                    $json['error']['city'] = $this->language->get('error_city');
            }

            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry($this->session->data['shipping_address']['country_id']);

            if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->session->data['shipping_address']['postcode'])) < 2 || utf8_strlen(trim($this->session->data['shipping_address']['postcode'])) > 10)) {
                    $json['error']['postcode'] = $this->language->get('error_postcode');
            }

            if ($this->session->data['shipping_address']['country_id'] == '') {
                    $json['error']['country'] = $this->language->get('error_country');
            }

            if (!isset($this->session->data['shipping_address']['zone_id']) || $this->session->data['shipping_address']['zone_id'] == '' || !is_numeric($this->session->data['shipping_address']['zone_id'])) {
                    $json['error']['zone'] = $this->language->get('error_zone');
            }

            // Customer Group
            if (isset($this->session->data['customer']['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->session->data['customer']['customer_group_id'], $this->config->get('config_customer_group_display'))) {
                    $pp_order->info('customer group found'.__Line__);
                    $customer_group_id = $this->session->data['customer']['customer_group_id'];
            } else {
                    $customer_group_id = $this->config->get('config_customer_group_id');
            }

            // Custom field validation
            $this->load->model('account/custom_field');

            $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

            foreach ($custom_fields as $custom_field) {
                    if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
                            $json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
                    } elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
                        $json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
                }
            }

            // Captcha
            if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('guest', (array)$this->config->get('config_captcha_page'))) {
                    $captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

                    if ($captcha) {
                            $json['error']['captcha'] = $captcha;
                    }
            }
        }
         //removing from code
        if (!$json) {
            $this->session->data['payment_address']['custom_field'] = array();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

        ///////////////////////////////////// from confirm/index
        $redirect = '';

        if ($this->cart->hasShipping()) {
                // Validate if shipping address has been set.
                if (!isset($this->session->data['shipping_address'])) {
                    $redirect = $this->url->link('checkout/checkout', '', true);
                    $pp_order->info('condition failed'.__Line__);
                }

                // Validate if shipping method has been set.
                if (!isset($this->session->data['shipping_method'])) {
                    $redirect = $this->url->link('checkout/checkout', '', true);
                    $pp_order->info('condition failed'.__Line__);
                }
        } else {
                unset($this->session->data['shipping_address']);
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
        }
        // Validate if payment address has been set.
        if (!isset($this->session->data['payment_address'])) {
                $redirect = $this->url->link('checkout/checkout', '', true);
                $pp_order->info('condition failed'.__Line__);
        }

        // Validate if payment method has been set.
        if (!isset($this->session->data['payment_method'])) {
                $redirect = $this->url->link('checkout/checkout', '', true);
                $pp_order->info('condition failed'.__Line__);
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
                $pp_order->info('condition failed'.__Line__);
                $redirect = $this->url->link('checkout/cart');
        }

        // Validate minimum quantity requirements.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $redirect = $this->url->link('checkout/cart');
                $pp_order->info('condition failed'.__Line__);
                    break;
            }
        }
        //interesting - creates order data array
        if (!$redirect) {
            $order_data = array();
            $totals = array();
            $taxes = $this->cart->getTaxes();
            $total = 0;
            // Because __call can not keep var references so we put them into an array.
            $total_data = array(
                    'totals' => &$totals,
                    'taxes'  => &$taxes,
                    'total'  => &$total
            );
            //looks like the extension is called here
            $this->load->model('setting/extension');

            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get('total_' . $result['code'] . '_status')) {
                    $this->load->model('extension/total/' . $result['code']);
                    // We have to put the totals in an array so that they pass by reference.
                    $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
                }
            }

            $sort_order = array();

            foreach ($totals as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $totals);

            $order_data['totals'] = $totals;

            $this->load->language('checkout/checkout');

            $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
            $order_data['store_id'] = $this->config->get('config_store_id');
            $order_data['store_name'] = $this->config->get('config_name');

            if ($order_data['store_id']) {
                    $order_data['store_url'] = $this->config->get('config_url');
            } else {
                if ($this->request->server['HTTPS']) {
                    $order_data['store_url'] = HTTPS_SERVER;
                } else {
                    $order_data['store_url'] = HTTP_SERVER;
                }
            }

            $this->load->model('account/customer');

            if ($this->customer->isLogged()) {
                //addition//
                $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
                //addition
                $order_data['customer_id'] = $this->session->data['guest']['customer_id']; 

                $order_data['customer_group_id'] = $customer_info['customer_group_id'];
                $order_data['firstname'] = $customer_info['firstname'];
                $order_data['lastname'] = $customer_info['lastname'];
                $order_data['email'] = $customer_info['email'];
                $order_data['telephone'] = $customer_info['telephone'];
                $order_data['custom_field'] = json_decode($customer_info['custom_field'], true);
            } elseif (isset($this->session->data['guest'])) {
                $order_data['customer_id'] = 0;
                $order_data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
                $order_data['firstname'] = $this->session->data['guest']['firstname'];
                $order_data['lastname'] = $this->session->data['guest']['lastname'];
                $order_data['email'] = $this->session->data['guest']['email'];
                $order_data['telephone'] = $this->session->data['guest']['telephone'];
                $order_data['custom_field'] = $this->session->data['guest']['custom_field'];
            }

            $order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
            $order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
            $order_data['payment_company'] = $this->session->data['payment_address']['company'];
            $order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
            $order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
            $order_data['payment_city'] = $this->session->data['payment_address']['city'];
            $order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
            $order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
            $order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
            $order_data['payment_country'] = $this->session->data['payment_address']['country'];
            $order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
            $order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
            $order_data['payment_custom_field'] = (isset($this->session->data['payment_address']['custom_field']) ? $this->session->data['payment_address']['custom_field'] : array());

            if (isset($this->session->data['payment_method']['title'])) {
                    $order_data['payment_method'] = $this->session->data['payment_method']['title'];
            } else {
                    $order_data['payment_method'] = '';
            }

            if (isset($this->session->data['payment_method']['code'])) {
                    $order_data['payment_code'] = $this->session->data['payment_method']['code'];
            } else {
                    $order_data['payment_code'] = '';
            }

            if ($this->cart->hasShipping()) {
                $order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
                $order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
                $order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
                $order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
                $order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
                $order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
                $order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
                $order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
                $order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
                $order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
                $order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
                $order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
                $order_data['shipping_custom_field'] = (isset($this->session->data['shipping_address']['custom_field']) ? $this->session->data['shipping_address']['custom_field'] : array());

                if (isset($this->session->data['shipping_method']['title'])) {
                        $order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
                } else {
                        $order_data['shipping_method'] = '';
                }

                if (isset($this->session->data['shipping_method']['code'])) {
                        $order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
                } else {
                        $order_data['shipping_code'] = '';
                }
            } else {
                $order_data['shipping_firstname'] = '';
                $order_data['shipping_lastname'] = '';
                $order_data['shipping_company'] = '';
                $order_data['shipping_address_1'] = '';
                $order_data['shipping_address_2'] = '';
                $order_data['shipping_city'] = '';
                $order_data['shipping_postcode'] = '';
                $order_data['shipping_zone'] = '';
                $order_data['shipping_zone_id'] = '';
                $order_data['shipping_country'] = '';
                $order_data['shipping_country_id'] = '';
                $order_data['shipping_address_format'] = '';
                $order_data['shipping_custom_field'] = array();
                $order_data['shipping_method'] = '';
                $order_data['shipping_code'] = '';
            }

            $order_data['products'] = array();

            foreach ($this->cart->getProducts() as $product) {
                $option_data = array();

                foreach ($product['option'] as $option) {
                    $option_data[] = array(
                        'product_option_id'       => $option['product_option_id'],
                        'product_option_value_id' => $option['product_option_value_id'],
                        'option_id'               => $option['option_id'],
                        'option_value_id'         => $option['option_value_id'],
                        'name'                    => $option['name'],
                        'value'                   => $option['value'],
                        'type'                    => $option['type']
                    );
                }

                $order_data['products'][] = array(
                    'product_id' => $product['product_id'],
                    //addition - throws error
                    //$pp_order->info('$product[product_id]'.$product['product_id'].__Line__);
                    //////////
                    'name'       => $product['name'],
                    'model'      => $product['model'],
                    'option'     => $option_data,
                    'download'   => $product['download'],
                    'quantity'   => $product['quantity'],
                    'subtract'   => $product['subtract'],
                    'price'      => $product['price'],
                    'total'      => $product['total'],
                    'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
                    'reward'     => $product['reward']
                );
            }

            // Gift Voucher
            $order_data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $order_data['vouchers'][] = array(
                            'description'      => $voucher['description'],
                            'code'             => token(10),
                            'to_name'          => $voucher['to_name'],
                            'to_email'         => $voucher['to_email'],
                            'from_name'        => $voucher['from_name'],
                            'from_email'       => $voucher['from_email'],
                            'voucher_theme_id' => $voucher['voucher_theme_id'],
                            'message'          => $voucher['message'],
                            'amount'           => $voucher['amount']
                    );
                }
            }

            if (isset($this->request->cookie['tracking'])) {
                $order_data['tracking'] = $this->request->cookie['tracking'];

                $subtotal = $this->cart->getSubTotal();

                // Affiliate
                $affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

                if ($affiliate_info) {
                    $order_data['affiliate_id'] = $affiliate_info['customer_id'];
                    $order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
                } else {
                    $order_data['affiliate_id'] = 0;
                    $order_data['commission'] = 0;
                }

                // Marketing
                $this->load->model('checkout/marketing');

                $marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

                if ($marketing_info) {
                    $order_data['marketing_id'] = $marketing_info['marketing_id'];
                } else {
                    $order_data['marketing_id'] = 0;
                }
            } else {
                $order_data['affiliate_id'] = 0;
                $order_data['commission'] = 0;
                $order_data['marketing_id'] = 0;
                $order_data['tracking'] = '';
            }

            $order_data['language_id'] = $this->config->get('config_language_id');
            $order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
            $order_data['currency_code'] = $this->session->data['currency'];
            $order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
            $order_data['ip'] = $this->request->server['REMOTE_ADDR'];
            //interesting code - I think this is posting the data to payment people
            if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
            } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
            } else {
                $order_data['forwarded_ip'] = '';
            }

            if (isset($this->request->server['HTTP_USER_AGENT'])) {
                $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
            } else {
                $order_data['user_agent'] = '';
            }

            if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
            } else {
                $order_data['accept_language'] = '';
            }

            $this->load->model('checkout/order');

            $this->load->model('tool/upload');

            $data['products'] = array();

            foreach ($this->cart->getProducts() as $product) {
                    $option_data = array();
                    foreach ($product['option'] as $option) {
                        if ($option['type'] != 'file') {
                                $value = $option['value'];
                        } else {
                            $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                            if ($upload_info) {
                                    $value = $upload_info['name'];
                            } else {
                                    $value = '';
                            }
                        }

                        $option_data[] = array(
                            'name'  => $option['name'],
                            'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                        );
                    }

                    $recurring = '';

                    if ($product['recurring']) {
                        $frequencies = array(
                            'day'        => $this->language->get('text_day'),
                            'week'       => $this->language->get('text_week'),
                            'semi_month' => $this->language->get('text_semi_month'),
                            'month'      => $this->language->get('text_month'),
                            'year'       => $this->language->get('text_year'),
                        );

                        if ($product['recurring']['trial']) {
                            $recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
                        }

                        if ($product['recurring']['duration']) {
                            $recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                        } else {
                            $recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                        }
                    }

                    $data['products'][] = array(
                            'cart_id'    => $product['cart_id'],
                            'product_id' => $product['product_id'],
                            'name'       => $product['name'],
                            'model'      => $product['model'],
                            'option'     => $option_data,
                            'recurring'  => $recurring,
                            'quantity'   => $product['quantity'],
                            'subtract'   => $product['subtract'],
                            'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                            'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),
                            'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])
                    );
            }

            // Gift Voucher
            $data['vouchers'] = array();

            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $voucher) {
                    $data['vouchers'][] = array(
                            'description' => $voucher['description'],
                            'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency'])
                    );
                }
            }

            $data['totals'] = array();

            foreach ($order_data['totals'] as $total) {
                $data['totals'][] = array(
                        'title' => $total['title'],
                        'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
                );
            }
            //addition///////////

            $data['payment'] = $this->load->controller('extension/payment/' . $this->session->data['payment_method']['code']);
            //create an array to hold the cURL request fields

            // Create the logger
            $myNewLogger = new Logger('pp_order/curlRequest/json_encode(data[])');
            // Now add some handlers
            $myNewLogger->pushHandler(new StreamHandler('/home/customer/www/pauldowlingportfolio.com/public_html/opencart-3.0.3.1/upload/app.log', Logger::DEBUG));
            $myNewLogger->pushHandler(new FirePHPHandler());

            $stringifiedData = json_encode($data['payment']);

            $myNewLogger->info("my new logger functional");
            //working datadump to log
            $myNewLogger->info('this is the name of the first product in basket pp_standard= '.$data['payment']['products'][0]['name']);
            $myNewLogger->info('this is the new data dump from pp_standard= '.json_encode($data['payment']['products']));


                        ///////////////////// all code checked for conditionals

            $url = "https://www.sandbox.paypal.com/cgi-bin/webscr&pal=[id]"; 

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

            /////// pulling data from pp_standard function

            //data dump

            $formData = $data['payment'];
            //$stringResponse = json_encode($response);

            $curlRequest = new Logger('curl_request');
            // Now add some handlers
            $curlRequest->pushHandler(new StreamHandler('/home/customer/www/pauldowlingportfolio.com/public_html/opencart-3.0.3.1/upload/app.log', Logger::DEBUG));
            $curlRequest->pushHandler(new FirePHPHandler());

            //////////////////////////////////////////////
            $fields = array(
            //'api_token'=> '4adecfb2d129b58e10d010ad67',
            'cmd'=> '_cart',
            'upload'=> $formData['testmode'],  
            'business'=> $formData['business'],
            'currency_code'=>$formData['currency_code'],
            'first_name'=>$formData['first_name'],
            'last_name'=>$formData['last_name'],
            'address1'=>$formData['address1'],
            'address2'=>$formData['address2'],
            'city'=>$formData['city'],
            'zip'=>$formData['zip'],
            'country'=>$formData['country'],
            'address_override'=>0,
            'email'=>0,
            'invoice'=>$formData['invoice'],
            'lc'=>$formData['lc'],
            'rm'=> 2,
            'no_note'=>1,
            'no_shipping'=>1,
            'charset'=>'utf-8',
            'return'=>$formData['return'],
            'notify_url'=>$formData['notify_url'],
            'cancel_return'=>$formData['cancel_return'],
            'paymentaction'=>$formData['paymentaction'],
            'custom'=>$formData['custom'],
            'bn'=>'OpenCart_2.0_WPS'

            );
            $itemCount = 0;
            foreach ($data['payment']['products'] as $product) {                    
                $shipping = 'Shipping, Handling, Discounts & Taxes';
                //conditional
                if($product['name']!==$shipping){
                //
                $itemCount = $itemCount + 1;
                $itemName = 'item_name_'.$itemCount;
                $itemNumber = 'item_number_'.$itemCount;
                $itemAmount = 'amount_'.$itemCount;
                $itemQuantity = 'quantity_'.$itemCount;
                $itemWeight = 'weight_'.$itemCount;

                $fields[$itemName] = $product['name'];
                $fields[$itemNumber] = $product['model'];
                $fields[$itemAmount] = $product['price'];
                $fields[$itemQuantity] =  $product['quantity'];
                $fields[$itemWeight] = $product['weight'];
                //
                }
            }
            $curlRequest->info("this is the sample object i am adding things to:  ".json_encode($fields));
            $response = do_curl_request($url, $fields);             


            $curlRequest->info("response from api".json_encode($response).__Line__);
            $curlRequest->info("proof of logging change".__Line__);
            //trimming the response to leave only the redirect url
            $redirectUrlArray = explode(" ", $response);	
            //$curlRequest->info("trimmed string:____".var_dump($redirectUrl).__Line__);
            $redirectUrl = $redirectUrlArray[3];
            $redirectObj = array(
            );
            $redirectObj['redirect_url'] = $response;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($response));
            /**************************/
            $curlRequest->info("Value Sent to cURL".$redirectUrl.__Line__);
		} else {
                    $data['redirect'] = $redirect;
                    //echo "you should have been redirected here";	
		}
    
    }
}

