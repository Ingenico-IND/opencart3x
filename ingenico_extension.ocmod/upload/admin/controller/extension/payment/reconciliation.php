<?php
class ControllerExtensionPaymentReconciliation extends Controller {
	public function index() {
		$this->load->language('extension/payment/reconciliation');

		$this->document->setTitle($this->language->get('heading_title'));
		// breadcrumbs
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('custom/page', 'user_token=' . $this->session->data['user_token'], true)
        );
        $this->load->model('extension/payment/ingenico');
        $merchant_details = $this->model_extension_payment_ingenico->get();
        $data['mrctCode'] = $merchant_details[0]['merchant_code'];

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $data['todate'] = $this->request->post['todate'];
            $data['fromdate'] = $this->request->post['fromdate'];
            $sql = "SELECT
                    o.order_id,
                    oh.comment,
                    DATE(o.date_added) AS mydate,
                    o.currency_code
                    FROM
                    " . DB_PREFIX . "order o
                    LEFT JOIN " . DB_PREFIX . "order_history oh
                    ON o.order_id = oh.order_id
                    WHERE o.order_status_id = '1' AND o.payment_code = 'ingenico' AND oh.order_status_id = '1' AND oh.comment !='' AND o.`date_added` BETWEEN '" . $data['fromdate'] .' 00:00:00'. "' AND '" . $data['todate'] .' 23:59:59'. "'
                    ORDER BY 1 DESC
                    LIMIT 0, 1000;";
            $query = $this->db->query($sql);
            $myorderdata= $query->rows;
            $successFullOrdersIds = [];
        if($myorderdata != ''){

            foreach ($myorderdata as $order_array) {
            $order_id = $order_array['order_id'];
            $currency = $order_array['currency_code'];           
            $date_input = $order_array['mydate'];            
            $merchantTxnRefNumber = $order_array['comment'];
            $request_array = array("merchant"=>array("identifier"=>$data['mrctCode']),
                                    "transaction"=>array(
                                        "deviceIdentifier"=>"S",
                                        "currency"=>$currency,
                                        "identifier"=>$merchantTxnRefNumber,
                                        "dateTime"=>$date_input,
                                        "requestType"=>"O"          
                                ));
            $refund_data = json_encode($request_array);
            $url = "https://www.paynimo.com/api/paynimoV2.req";
            $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($request_array),
                'header' =>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
                )
            );
            $context     = stream_context_create($options);
            $response_array = json_decode(file_get_contents($url, false, $context));
            $status_code = $response_array->paymentMethod->paymentTransaction->statusCode; 
            $status_message = $response_array->paymentMethod->paymentTransaction->statusMessage;
            $txn_id = $response_array->paymentMethod->paymentTransaction->identifier;

            if($status_code=='0300'){
                $success_ids = $order_array['order_id'];
                
                $this->addOrderHistory($success_ids, 2, $txn_id,true);
                array_push($successFullOrdersIds, $success_ids);
                    
            }else if($status_code=="0397" || $status_code=="0399" || $status_code=="0396" || $status_code=="0392"){
                $success_ids = $order_array['order_id'];
               
                $this->addOrderHistory($success_ids, 10, $txn_id);
                array_push($successFullOrdersIds, $success_ids);
               
            }else{
                null;
            }
                        
        }

        if($successFullOrdersIds){
            $data['message'] = "Updated Order Status for Order ID:  " . implode(", ", $successFullOrdersIds);
        }else{
            $data['message'] = "Updated Order Status for Order ID: None";
        }
    }
    else{
        $data['message'] = "Updated Order Status for Order ID: None";
    }
        
        }
        // calling header, footer and column_left for our template to render properly
        $data['action']= $this->url->link('extension/payment/reconciliation', 'user_token=' . $this->session->data['user_token'], true);
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/payment/reconciliation', $data	));
	}

    public function addOrderHistory($order_id, $order_status_id, $comment = '', $notify = false){

        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
    }
}


?>
