<?php
class ControllerExtensionPaymentVerification extends Controller {
	public function index() {
		$this->load->language('extension/payment/verification');

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
        $this->load->model('extension/payment/Worldline');
        $merchant_details = $this->model_extension_payment_Worldline->get();
        $data['mrctCode'] = $merchant_details[0]['merchant_code'];

        $this->load->model('sale/order');
        
        $query = $this->db->query("SELECT order_id FROM " . DB_PREFIX . "order ORDER BY 1 DESC LIMIT 0,1");
       $order_info =  $this->model_sale_order->getOrder($query->rows[0]);
       $data['currency'] = $order_info['currency_code'];

     if($data['currency']==''){
     	$data['currency']= 'INR';
     }
        // calling header, footer and column_left for our template to render properly

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/payment/verification', $data	));
	}
}
?>
