<?php

class ControllerExtensionPaymentIngenico extends Controller {
    public function index() {

        $this->load->model('extension/payment/ingenico');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['continue'] = $this->url->link('checkout/success');
        $shipping_cost = isset($this->session->data['shipping_method']['cost']) ? $this->session->data['shipping_method']['cost'] : '0';
        $products = $this->cart->getProducts();
        $total_prod_count=count($products);
        $total_prod_init=0;
        $actual_cost=0;
        while($total_prod_init<$total_prod_count){
            $actual_cost += $products[$total_prod_init]['total'];
            $total_prod_init++;
        }
        $amount= $shipping_cost+$actual_cost;
        // Totalings
        $this->load->model('setting/extension');    

        $totaling_data = array();
        $totaling = 0;
        $taxes = $this->cart->getTaxes();

        // Display prices
        if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
            $sort_order = array();

            $results = $this->model_setting_extension->getExtensions('totaling');


            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('totaling/' . $result['code']);

                    $this->{'model_totaling_' . $result['code']}->getTotaling($totaling_data, $totaling, $taxes);
                }
            }


            $sort_order = array();  


            foreach ($totaling_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $totaling_data);
        }

        foreach ($totaling_data as $totaling) {
            if($totaling['title'] == 'Totaling') {
                $amount = $totaling['value'];
            }
        }

        $merchant_details = $this->model_extension_payment_ingenico->get();
       
        $merchant_txn_id = rand(1,1000000);
        $cur_date = date("d-m-Y");
        $returnUrl = $this->url->link('extension/payment/ingenico/getResponse');
        $returnUrl_2 = $this->url->link('extension/payment/ingenico/getResponse');

        if($merchant_details[0]['primary_color_code']){
            $data['primary_color_code'] = $merchant_details[0]['primary_color_code'];
        }
        else{
            $data['primary_color_code']='#3977b7';
        }
        if($merchant_details[0]['secondary_color_code']){
           $data['secondary_color_code'] = $merchant_details[0]['secondary_color_code'];
        }
        else{
            $data['secondary_color_code']='#FFFFFF';
        }
        if($merchant_details[0]['button_color_code_1']){
            $data['button_color_code_1'] = $merchant_details[0]['button_color_code_1'];
        }
        else{
            $data['button_color_code_1']='#1969bb';
        }
        if($merchant_details[0]['button_color_code_2']){
            $data['button_color_code_2'] = $merchant_details[0]['button_color_code_2'];
        }
        else{
            $data['button_color_code_2']='#FFFFFF';
        }
        $logo_url = $merchant_details[0]['merchant_logo_url'];
        if(!empty($logo_url) && @getimagesize($logo_url)){
            $data['merchant_logo_url'] = $logo_url; 
        }
        else{
            $data['merchant_logo_url'] = 'https://www.paynimo.com/CompanyDocs/company-logo-md.png';
        }
        if($merchant_details[0]['embedPaymentGatewayOnPage'] == '1'){
            $data['checkoutElement'] = '#ingenicopayment';
        } else {
            $data['checkoutElement'] = '';
        }
        if($merchant_details[0]['displayErrorMessageOnPopup'] == '1' && $merchant_details[0]['enableNewWindowFlow'] == '1'){
            $returnUrl='';
        } 
        $data['enableExpressPay'] = $this->checkTrueOrFalse($merchant_details[0]['enableExpressPay']);
        $data['separateCardMode'] = $this->checkTrueOrFalse($merchant_details[0]['separateCardMode']);
        $data['enableNewWindowFlow'] = $this->checkTrueOrFalse($merchant_details[0]['enableNewWindowFlow']);
        $data['enableInstrumentDeRegistration'] = $this->checkTrueOrFalse($merchant_details[0]['enableInstrumentDeRegistration']);
        $data['hideSavedInstruments'] = $this->checkTrueOrFalse($merchant_details[0]['hideSavedInstruments']);
        $data['saveInstrument'] = $this->checkTrueOrFalse($merchant_details[0]['saveInstrument']);
        $data['merchantMsg'] = $merchant_details[0]['merchantMsg'];
        $data['disclaimerMsg'] = $merchant_details[0]['disclaimerMsg'];
        $data['paymentMode'] = $merchant_details[0]['paymentMode'];
        $data['txnType'] = $merchant_details[0]['txnType'];

        if ($merchant_details[0]['paymentModeOrder']) {
            $paymentModeOrder = $merchant_details[0]['paymentModeOrder'];
            $paymentorderarray = explode(',', $paymentModeOrder);
            $data['paymentModeOrder_1'] = isset($paymentorderarray[0]) ? $paymentorderarray[0] : null;
            $data['paymentModeOrder_2'] = isset($paymentorderarray[1]) ? $paymentorderarray[1] : null;
            $data['paymentModeOrder_3'] = isset($paymentorderarray[2]) ? $paymentorderarray[2] : null;
            $data['paymentModeOrder_4'] = isset($paymentorderarray[3]) ? $paymentorderarray[3] : null;
            $data['paymentModeOrder_5']= isset($paymentorderarray[4]) ? $paymentorderarray[4] : null;
            $data['paymentModeOrder_6'] = isset($paymentorderarray[5]) ? $paymentorderarray[5] : null;
            $data['paymentModeOrder_7'] = isset($paymentorderarray[6]) ? $paymentorderarray[6] : null;
            $data['paymentModeOrder_8'] = isset($paymentorderarray[7]) ? $paymentorderarray[7] : null;
            $data['paymentModeOrder_9'] = isset($paymentorderarray[8]) ? $paymentorderarray[8] : null;
            $data['paymentModeOrder_10'] = isset($paymentorderarray[9]) ? $paymentorderarray[9] : null;
        } else {
            $data['paymentModeOrder_1'] = "cards";
            $data['paymentModeOrder_2'] = "netBanking";
            $data['paymentModeOrder_3'] = "imps";
            $data['paymentModeOrder_4'] = "wallets";
            $data['paymentModeOrder_5'] = "cashCards";
            $data['paymentModeOrder_6'] =  "UPI";
            $data['paymentModeOrder_7'] =  "MVISA";
            $data['paymentModeOrder_8'] = "debitPin";
            $data['paymentModeOrder_9'] = "emiBanks";
            $data['paymentModeOrder_10'] = "NEFTRTGS";
        }

        $data['mrctCode'] = $merchant_details[0]['merchant_code'];
        $data['merchantTxnRefNumber'] = $merchant_txn_id;
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $data['Amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        if($merchant_details[0]['webservice_locator'] == 'Test'){
            $data['Amount'] ='1';
        }
        $data['currency'] = $order_info['currency_code'];
        $data['CustomerId'] = 'cons'. rand(1,1000000);
        
        $data['orderid'] = $this->session->data['order_id'];
        if($this->customer->isLogged()){
            $getFirstName = $this->customer->getFirstName();
            $getLastName = $this->customer->getLastName();
            $data['fullname'] = $getFirstName.' '.$getLastName;
            $data['email'] = $this->customer->getEmail();
            $data['customerMobNumber'] = $this->customer->getTelephone(); 
        }else{
            $getFirstName = $this->session->data['guest']['firstname'];
            $getLastName = $this->session->data['guest']['lastname'];
            $data['fullname'] = $getFirstName.' '.$getLastName;
            $data['email'] = $this->session->data['guest']['email'];
            $data['customerMobNumber'] = $this->session->data['guest']['telephone']; 
        }
        if (strpos($data['customerMobNumber'], '+') !== false) {
            $data['customerMobNumber'] = str_replace("+", "", $data['customerMobNumber']);
        }
        $data['SALT'] = $merchant_details[0]['key'];
        $data['scheme'] = $merchant_details[0]['merchant_scheme_code'];
        $data['returnUrl'] = $returnUrl;
        $data['returnUrl_2'] = $returnUrl_2;
        $data['bankCode'] = 470;

        $datastring = $data['mrctCode'] . "|" . $data['merchantTxnRefNumber'] . "|" . $data['Amount'] . "|" . "|" . $data['CustomerId'] . "|" . $data['customerMobNumber'] . "|" . $data['email'] . "||||||||||" . $data['SALT'];

        $hashed = hash('sha512', $datastring);
        $log = new Log('Ingenico_' . date("Ymd") . '.log');
        $log->write('Request: ' . $datastring);
        $data['token'] = $hashed;
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1, $merchant_txn_id);       
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . 'extension/payment/ingenico')) {
            return $this->load->view($this->config->get('config_template') . 'extension/payment/ingenico', $data);
        } else { 
            return $this->load->view('extension/payment/ingenico', $data); 
        
        }
    }
          
    public function getResponse() {
        if($_POST){
            $salt = $this->config->get('payment_ingenico_key');
            $str = $_POST['msg'];
           //print_r($str); die;
            $log = new Log('Ingenico_' . date("Ymd") . '.log');
            $log->write('Response: ' . $str);
            $this->load->model('extension/payment/ingenico');
            $responseData = explode('|', $str);
            $responseData_1 = explode('|', $str);
            if($responseData[0] != ''){
            $verificationHash = array_pop($responseData_1);
            $hashableString = join('|', $responseData_1) . "|" . $salt;
            $hashedString = hash('sha512',  $hashableString);
            $orderId=$this->session->data['order_id'];
            
            if ($orderId == '') {
            $oid = explode('orderid:', $responseData[7]);
            $oid_1=$oid[1];
            $oid2=explode('}',$oid_1);
            $oidreceived=$oid2[0];
            $orderId = $oidreceived;
            }
            $txn_msg  = $this->getErrorStatusMessage($responseData[0]);
            if (!$txn_msg) {
                $txn_msg = $responseData[1];
            }
            $txn_err_msg = $responseData[2];
            if (!$txn_err_msg) {
                $txn_err_msg = 'Transaction Failed';
            }
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($orderId);
            $currencyCode = $order_info['currency_code'];
            $status = $responseData[0];
            if ($hashedString == $verificationHash) {
                $responsedate = explode(' ', $responseData[8]);
                $data_array = array(
                    "merchant" => array(
                        "identifier" => $this->config->get('payment_ingenico_merchant_code')
                    ),
                    "transaction" => array(
                        "deviceIdentifier" => "S",
                        "currency" => $currencyCode,
                        "dateTime" => $responsedate[0],
                        "token" => $responseData[5],
                        "requestType" => "S"
                    )
                );
                $url = "https://www.paynimo.com/api/paynimoV2.req";
                $options = array(
                    'http' => array(
                        'method'  => 'POST',
                        'content' => json_encode($data_array),
                        'header' =>  "Content-Type: application/json\r\n" .
                            "Accept: application/json\r\n"
                    )
                );
                $context     = stream_context_create($options);
                $result      = file_get_contents($url, false, $context);
                $response    = json_decode($result);
                $scallstatuscode = $response->paymentMethod->paymentTransaction->statusCode;
            if($status == '300' && $scallstatuscode ==  '0300') {
                $this->load->model('checkout/order');
                $this->model_checkout_order->addOrderHistory($orderId, 2, $responseData[5],true);
                $this->response->redirect($this->url->link('checkout/success', '', true));
            } else {
                //$this->cart->clear(); 
                $this->load->model('checkout/order');
                $this->model_checkout_order->addOrderHistory($orderId, 10, $responseData[5]);
                
                $this->session->data['error'] = 'Transaction Status: ' . $txn_msg . '<br> Transaction Error Message from Payment Gateway: ' . $txn_err_msg;
                $this->response->redirect($this->url->link('checkout/cart', '', true));
            }
        } else {
                $this->load->model('checkout/order');
                $this->model_checkout_order->addOrderHistory($orderId, 10);
                
                $this->session->data['error'] = 'Payment Failed Hash Verification Failed';
                $this->response->redirect($this->url->link('checkout/cart', '', true));
        }

        } else {
                $this->load->model('checkout/order');
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 10);
                
                $this->session->data['error'] = 'Payment Failed Empty Response';
                $this->response->redirect($this->url->link('checkout/cart', '', true));
        }

        }
    }

    public function getErrorStatusMessage($code)
    {
        $messages = [
            "0300" => "Successful Transaction",
            "0392" => "Transaction cancelled by user either in Bank Page or in PG Card /PG Bank selection",
            "0396" => "Transaction response not received from Bank, Status Check on same Day",
            "0397" => "Transaction Response not received from Bank. Status Check on next Day",
            "0399" => "Failed response received from bank",
            "0400" => "Refund Initiated Successfully",
            "0401" => "Refund in Progress (Currently not in used)",
            "0402" => "Instant Refund Initiated Successfully(Currently not in used)",
            "0499" => "Refund initiation failed",
            "9999" => "Transaction not found :Transaction not found in PG"
        ];

        if (in_array($code, array_keys($messages))) {
            return $messages[$code];
        }

        return null;
    }

    public function checkTrueOrFalse($data){

        if($data == '1'){
            return '1';
        }else{
            return '0';
        }
    }

    public function s2sverification(){
    
            $salt = $this->config->get('payment_ingenico_key');
            $str = $_GET['msg'];
            if ($str) {
            $log = new Log('Ingenico_' . date("Ymd") . '.log');
            $log->write('Response S2S: ' . $str);
            $this->load->model('extension/payment/ingenico');
            $responseData = explode('|', $str);
            $responseData_1 = explode('|', $str);
            
            $verificationHash = array_pop($responseData_1);
            $hashableString = join('|', $responseData_1) . "|" . $salt;
            $hashedString = hash('sha512',  $hashableString);
            $oid = explode('orderid:', $responseData[7]);
            $oid_1=$oid[1];
            $oid2=explode('}',$oid_1);
            $oidreceived=$oid2[0];
            $orderId = $oidreceived;
            if ($orderId == '') {
            $orderId=$this->session->data['order_id'];
            }
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($orderId);
            $currencyCode = $order_info['currency_code'];
            $status = $responseData[0];
                            
            if($status == '300' && $hashedString == $verificationHash) {
                $this->load->model('checkout/order');
                $this->model_checkout_order->addOrderHistory($orderId, 2, $responseData[5]);
                echo json_encode($responseData[3] . "|" . $responseData[5] . "|1");
                die;
            } else {
                //$this->cart->clear(); 
                $this->load->model('checkout/order');
                $this->model_checkout_order->addOrderHistory($orderId, 10, $responseData[5]);
                echo json_encode($responseData[3] . "|" . $responseData[5] . "|0");
                die;
                
            }
            } else {
            echo json_encode("INVALID DATA");
            die;
        }
      
    }
       
}













