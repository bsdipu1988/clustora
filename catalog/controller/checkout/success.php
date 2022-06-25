<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');

		if (isset($this->session->data['order_id'])) {
			


			// ##for sms starts
			function sendsmsPOST($mobileNumber,$senderId,$routeId,$message,$serverUrl,$authKey)
			{  
			    //Prepare you post parameters
			    $postData = array(      
			        'mobileNumbers' => $mobileNumber,        
			        'smsContent' => $message,
			        'senderId' => $senderId,
			        'routeId' => $routeId,		
			        "smsContentType" =>'english'
			    );
			    $data_json = json_encode($postData);
			    $url="http://".$serverUrl."/rest/services/sendSMS/sendGroupSms?AUTH_KEY=".$authKey;
			    // init the resource
			    $ch = curl_init();
			    curl_setopt_array($ch, array(
			        CURLOPT_URL => $url,
			        CURLOPT_HTTPHEADER => array('Content-Type: application/json','Content-Length: ' . strlen($data_json)),
			        CURLOPT_RETURNTRANSFER => true,
			        CURLOPT_POST => true,
			        CURLOPT_POSTFIELDS => $data_json,
			        CURLOPT_SSL_VERIFYHOST => 0,
			        CURLOPT_SSL_VERIFYPEER => 0
			    ));
			    //get response
			    $output = curl_exec($ch);
			    //Print error if any
			    if(curl_errno($ch))
			    {
			        echo 'error:' . curl_error($ch);
			    }
			    curl_close($ch);
			    return $output;
			}
			//$name = "SEKHAR	";
			//$message = $_REQUEST["message"];
			$mobileNumber=$this->customer->getTelephone().",8270030502";
			$dtime="very soon";
			$name=$this->customer->getFirstName();
			$totalcost=$this->session->data['totals']['value'];
			$order=$this->session->data['order_id'];//echo $name . $order . $totalcost .$mobileNumber;
			$message="Hi ".$name." Your order id=".$order." of Rs:".$totalcost." has been placed successfully Deliver time ".$dtime." Any Query call clustora.com";//echo $message;
			$senderId="CLUSTO";
			$routeId="1";
			$serverUrl="msg.msgclub.net";
			$authKey="57a8f957d491b9a0c358312d1b698";

			sendsmsPOST($mobileNumber,$senderId,$routeId,$message,$serverUrl,$authKey);
			##sms ends


			$this->cart->clear();


			// Add to activity log
			$this->load->model('account/activity');

			if ($this->customer->isLogged()) {
				$activity_data = array(
					'customer_id' => $this->customer->getId(),
					'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
					'order_id'    => $this->session->data['order_id']
				);

				$this->model_account_activity->addActivity('order_account', $activity_data);
			} else {
				$activity_data = array(
					'name'     => $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'],
					'order_id' => $this->session->data['order_id']
				);

				$this->model_account_activity->addActivity('order_guest', $activity_data);
			}




			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['button_continue'] = $this->language->get('button_continue');

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/success.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/common/success.tpl', $data));
		}
	}
}