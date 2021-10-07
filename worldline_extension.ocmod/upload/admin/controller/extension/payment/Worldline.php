<?php
class ControllerExtensionPaymentWorldline extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/Worldline');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('extension/payment/Worldline');

		$merchant_details = $this->model_extension_payment_Worldline->get();

		$data['error_warning'] = array();

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			if(count($this->validate()) == 0){


				$this->model_setting_setting->editSetting('payment_Worldline', $this->request->post);
				$this->session->data['success'] = $this->language->get('text_success');

				if(is_array($merchant_details) && !isset($merchant_details[0])){
					$response = $this->model_extension_payment_Worldline->add($this->request->post);
				}else{
					$response = $this->model_extension_payment_Worldline->edit($this->request->post);
				}

				if($response === true){
					$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'));
				}
			}else if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			}
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_for_Worldline'] = $this->language->get('text_for_Worldline');
		//values from text box
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['merchant_code'] = $this->language->get('merchant_code');
		$data['key'] = $this->language->get('key');
		$data['amount'] = $this->language->get('amount');
		$data['bank_code'] = $this->language->get('bank_code');
		$data['webservice_locator'] = $this->language->get('webservice_locator');
		$data['order_status'] = $this->language->get('order_status');
		$data['status'] = $this->language->get('status');
		$data['sort_order'] = $this->language->get('sort_order');
		$data['merchant_scheme_code'] = $this->language->get('merchant_scheme_code');

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_ip_add'] = $this->language->get('button_ip_add');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/Worldline', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['action'] = $this->url->link('extension/payment/Worldline', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['cancel'] = $this->url->link('marketplace/payment', 'user_token=' . $this->session->data['user_token'],true);


		if (isset($this->request->post['payment_Worldline_merchant_code'])) {
			$data['payment_Worldline_merchant_code'] = $this->request->post['payment_Worldline_merchant_code'];
		} else {
			$data['payment_Worldline_merchant_code'] = $this->config->get('payment_Worldline_merchant_code');
		}


		if (isset($this->request->post['payment_Worldline_key'])) {
			$data['payment_Worldline_key'] = $this->request->post['payment_Worldline_key'];
		} else {
			$data['payment_Worldline_key'] = $this->config->get('payment_Worldline_key');
		}


		if (isset($this->request->post['payment_Worldline_webservice_locator'])) {
			$data['payment_Worldline_webservice_locator'] = $this->request->post['payment_Worldline_webservice_locator'];
		} else {
			$data['payment_Worldline_webservice_locator'] = $this->config->get('payment_Worldline_webservice_locator');
		}


		if (isset($this->request->post['payment_Worldline_status'])) {
			$data['payment_Worldline_status'] = $this->request->post['payment_Worldline_status'];
		} else {
			$data['payment_Worldline_status'] = $this->config->get('payment_Worldline_status');
		}

		if (isset($this->request->post['payment_Worldline_sort_order'])) {
			$data['payment_Worldline_sort_order'] = $this->request->post['payment_Worldline_sort_order'];
		} else {
			$data['payment_Worldline_sort_order'] = $this->config->get('payment_Worldline_sort_order');
		}

		if (isset($this->request->post['payment_Worldline_primary_color_code'])) {
			$data['payment_Worldline_primary_color_code'] = $this->request->post['payment_Worldline_primary_color_code'];
		} else {
			$data['payment_Worldline_primary_color_code'] = $this->config->get('payment_Worldline_primary_color_code');
		}

		if (isset($this->request->post['payment_Worldline_secondary_color_code'])) {
			$data['payment_Worldline_secondary_color_code'] = $this->request->post['payment_Worldline_secondary_color_code'];
		} else {
			$data['payment_Worldline_secondary_color_code'] = $this->config->get('payment_Worldline_secondary_color_code');
		}

		if (isset($this->request->post['payment_Worldline_button_color_code_1'])) {
			$data['payment_Worldline_button_color_code_1'] = $this->request->post['payment_Worldline_button_color_code_1'];
		} else {
			$data['payment_Worldline_button_color_code_1'] = $this->config->get('payment_Worldline_button_color_code_1');
		}

		if (isset($this->request->post['payment_Worldline_button_color_code_2'])) {
			$data['payment_Worldline_button_color_code_2'] = $this->request->post['payment_Worldline_button_color_code_2'];
		} else {
			$data['payment_Worldline_button_color_code_2'] = $this->config->get('payment_Worldline_button_color_code_2');
		}

		if (isset($this->request->post['payment_Worldline_merchant_logo_url'])) {
			$data['payment_Worldline_merchant_logo_url'] = $this->request->post['payment_Worldline_merchant_logo_url'];
		} else {
			$data['payment_Worldline_merchant_logo_url'] = $this->config->get('payment_Worldline_merchant_logo_url');
		}

		if (isset($this->request->post['payment_Worldline_enableExpressPay'])) {
			$data['payment_Worldline_enableExpressPay'] = $this->request->post['payment_Worldline_enableExpressPay'];
		} else {
			$data['payment_Worldline_enableExpressPay'] = $this->config->get('payment_Worldline_enableExpressPay');
		}

		if (isset($this->request->post['payment_Worldline_separateCardMode'])) {
			$data['payment_Worldline_separateCardMode'] = $this->request->post['payment_Worldline_separateCardMode'];
		} else {
			$data['payment_Worldline_separateCardMode'] = $this->config->get('payment_Worldline_separateCardMode');
		}

		if (isset($this->request->post['payment_Worldline_enableNewWindowFlow'])) {
			$data['payment_Worldline_enableNewWindowFlow'] = $this->request->post['payment_Worldline_enableNewWindowFlow'];
		} else {
			$data['payment_Worldline_enableNewWindowFlow'] = $this->config->get('payment_Worldline_enableNewWindowFlow');
		}

		if (isset($this->request->post['payment_Worldline_merchantMsg'])) {
			$data['payment_Worldline_merchantMsg'] = $this->request->post['payment_Worldline_merchantMsg'];
		} else {
			$data['payment_Worldline_merchantMsg'] = $this->config->get('payment_Worldline_merchantMsg');
		}

		if (isset($this->request->post['payment_Worldline_disclaimerMsg'])) {
			$data['payment_Worldline_disclaimerMsg'] = $this->request->post['payment_Worldline_disclaimerMsg'];
		} else {
			$data['payment_Worldline_disclaimerMsg'] = $this->config->get('payment_Worldline_disclaimerMsg');
		}

		if (isset($this->request->post['payment_Worldline_paymentMode'])) {
			$data['payment_Worldline_paymentMode'] = $this->request->post['payment_Worldline_paymentMode'];
		} else {
			$data['payment_Worldline_paymentMode'] = $this->config->get('payment_Worldline_paymentMode');
		}

		if (isset($this->request->post['payment_Worldline_paymentModeOrder'])) {
			$data['payment_Worldline_paymentModeOrder'] = $this->request->post['payment_Worldline_paymentModeOrder'];
		} else {
			$data['payment_Worldline_paymentModeOrder'] = $this->config->get('payment_Worldline_paymentModeOrder');
		}

		if (isset($this->request->post['payment_Worldline_enableInstrumentDeRegistration'])) {
			$data['payment_Worldline_enableInstrumentDeRegistration'] = $this->request->post['payment_Worldline_enableInstrumentDeRegistration'];
		} else {
			$data['payment_Worldline_enableInstrumentDeRegistration'] = $this->config->get('payment_Worldline_enableInstrumentDeRegistration');
		}

		if (isset($this->request->post['payment_Worldline_txnType'])) {
			$data['payment_Worldline_txnType'] = $this->request->post['payment_Worldline_txnType'];
		} else {
			$data['payment_Worldline_txnType'] = $this->config->get('payment_Worldline_txnType');
		}

		if (isset($this->request->post['payment_Worldline_hideSavedInstruments'])) {
			$data['payment_Worldline_hideSavedInstruments'] = $this->request->post['payment_Worldline_hideSavedInstruments'];
		} else {
			$data['payment_Worldline_hideSavedInstruments'] = $this->config->get('payment_Worldline_hideSavedInstruments');
		}

		if (isset($this->request->post['payment_Worldline_saveInstrument'])) {
			$data['payment_Worldline_saveInstrument'] = $this->request->post['payment_Worldline_saveInstrument'];
		} else {
			$data['payment_Worldline_saveInstrument'] = $this->config->get('payment_Worldline_saveInstrument');
		}

		if (isset($this->request->post['payment_Worldline_displayErrorMessageOnPopup'])) {
			$data['payment_Worldline_displayErrorMessageOnPopup'] = $this->request->post['payment_Worldline_displayErrorMessageOnPopup'];
		} else {
			$data['payment_Worldline_displayErrorMessageOnPopup'] = $this->config->get('payment_Worldline_displayErrorMessageOnPopup');
		}

		if (isset($this->request->post['payment_Worldline_embedPaymentGatewayOnPage'])) {
			$data['payment_Worldline_embedPaymentGatewayOnPage'] = $this->request->post['payment_Worldline_embedPaymentGatewayOnPage'];
		} else {
			$data['payment_Worldline_embedPaymentGatewayOnPage'] = $this->config->get('payment_Worldline_embedPaymentGatewayOnPage');
		}

		

		if (isset($this->request->post['payment_Worldline_merchant_scheme_code'])) {
			$data['payment_Worldline_merchant_scheme_code'] = $this->request->post['payment_Worldline_merchant_scheme_code'];
		} else {
			$data['payment_Worldline_merchant_scheme_code'] = $this->config->get('payment_Worldline_merchant_scheme_code');
		}

		$data['button_colours'] = array(
			'orange' => $this->language->get('text_orange'),
			'tan'    => $this->language->get('text_tan')
		);

		$data['button_backgrounds'] = array(
			'white' => $this->language->get('text_white'),
			'light' => $this->language->get('text_light'),
			'dark'  => $this->language->get('text_dark'),
		);

		$data['button_sizes'] = array(
			'medium'  => $this->language->get('text_medium'),
			'large'   => $this->language->get('text_large'),
			'x-large' => $this->language->get('text_x_large'),
		);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/payment/Worldline', $data));
	}

	public function install() {
		if ($this->user->hasPermission('modify', 'marketplace/extension')) {
			$this->load->model('extension/payment/Worldline');
			
			$this->model_extension_payment_Worldline->install();
		}
	}

	public function uninstall() {
		if ($this->user->hasPermission('modify', 'marketplace/extension')) {
			$this->load->model('extension/payment/Worldline');
			$this->model_extension_payment_Worldline->uninstall();
		}
	}

	public function order() {
		$data['order_id'] = $this->request->get['order_id'];
		$this->load->model('sale/order');
	
        $query = $this->db->query("SELECT
  		o.comment,
  		DATE(o.date_added) AS mydate
		FROM
  		" . DB_PREFIX . "order_history o
		WHERE o.order_id = '" . $data['order_id'] . "'
  		AND o.order_status_id = '2'
		LIMIT 0, 1;");
            if(isset($query->rows[0]['comment']) != ''){
            $data['status'] = 'success';	
            $data['token'] = $query->rows[0]['comment'];
            $data['date'] = $query->rows[0]['mydate'];
            $data['mcode'] = $this->config->get('payment_Worldline_merchant_code');
            $order_info = $this->model_sale_order->getOrder($data['order_id']);
            $data['currency'] = $order_info['currency_code'];
            $data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        }else{
        	$data['status'] = 'fail';
        }
		return $this->load->view('extension/payment/Worldline_order', $data);

		
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/Worldline')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!trim($this->request->post['payment_Worldline_merchant_code'])) {
			$this->error['warning']['merchant_code'] = $this->language->get('error_merchant_code');
		}


		if (!trim($this->request->post['payment_Worldline_key'])) {
			$this->error['warning']['access_key'] = $this->language->get('error_key');
		}


		if (!trim($this->request->post['payment_Worldline_webservice_locator'])) {
			$this->error['warning']['access_webservice_locator'] = $this->language->get('error_webservice_locator');
		}


		if (!trim($this->request->post['payment_Worldline_sort_order'])) {
			$this->error['warning']['access_sort_order'] = $this->language->get('error_sort_order');
		}

		if (!trim($this->request->post['payment_Worldline_merchant_scheme_code'])) {
			$this->error['warning']['merchant_scheme_code'] = $this->language->get('error_merchant_scheme_code');
		}

		return $this->error;
	}
}