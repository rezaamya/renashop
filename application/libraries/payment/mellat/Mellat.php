<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mellat
{
    protected $CI;

    public function __construct()
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();
        //$this->CI->load->library('session');
        $this->CI->load->helper('url');

        #$this->SMS_API = new \Kavenegar\KavenegarApi(KAVEHNEGAR_API_KEY);
        #$this->sender = KAVEHNEGAR_SENDER;
    }

    public function send_to_bank($merchantCode, $terminalCode, $password, $amount, $invoiceNumber, $user_id)
    {
		$client = new SoapClient("https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl");
//		echo "<b>The functions are:</b> <br/>";
//		echo "<pre>";
//		var_dump($client->__getFunctions());
//		echo "</pre>";
//
//		echo "<b>The types are:</b> <br />";
//		echo "<pre>";
//		var_dump($client->__getTypes());
//		echo "</pre>";

//		$client = new soapclient('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
//		$namespace='http://interfaces.core.sw.bps.com/';

		$callBackUrl = base_url("cart/finishing_message/mellat");
		$currentTime = time();
		$localDate = date("Ymd",$currentTime);
		$localTime = date("hms",$currentTime);

		$parameters = array(
			'terminalId' => $terminalCode,
			'userName' => $merchantCode,
			'userPassword' => $password,
			'orderId' => $invoiceNumber,
			'amount' => $amount,
			'localDate' => $localDate,
			'localTime' => $localTime,
			'additionalData' => '',
			'callBackUrl' => $callBackUrl,
			'payerId' => $user_id);

		// Call the SOAP method
		$result = $client->bpPayRequest($parameters);

		// Display the result
		if (isset($result->return)) {
			$res = explode (',',$result->return);

			$ResCode = $res[0];

			if ($ResCode == "0") {
				// Update table, Save RefId
				$redirect_form = "<form Id='payment_redirect_form' Method='post' Action='https://bpm.shaparak.ir/pgwchannel/startpay.mellat'>
						<input type='hidden' name='RefId' value='".$res[1]."' />
					</form>";

				return $redirect_form;
			}
			else {
				// log error in app
				// Update table, log the error
				// Show proper message to user
				return "response code : " . $ResCode;
			}
		}
		else {
			// we could not connect to bank!
			// var_dump($result);
		}
    }

    public function verify_payment($merchantCode, $terminalCode, $password, $invoiceNumber, $saleReferenceId)
    {
		$client = new SoapClient("https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl");

		$parameters = array(
			'terminalId' => $terminalCode,
			'userName' => $merchantCode,
			'userPassword' => $password,
			'orderId' => $invoiceNumber,
			'saleOrderId' => $invoiceNumber,
			'saleReferenceId' => $saleReferenceId);

		// Call the SOAP method
		$result = $client->bpVerifyRequest($parameters);

		// Display the result
		if (isset($result->return)) {
			$res = explode (',',$result->return);

			$ResCode = $res[0];

			if ($ResCode == "0") {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			// we could not connect to bank!
			// var_dump($result);
		}
    }

    public function settle_request($merchantCode, $terminalCode, $password, $invoiceNumber, $saleReferenceId)
    {
		$client = new SoapClient("https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl");

		$parameters = array(
			'terminalId' => $terminalCode,
			'userName' => $merchantCode,
			'userPassword' => $password,
			'orderId' => $invoiceNumber,
			'saleOrderId' => $invoiceNumber,
			'saleReferenceId' => $saleReferenceId);

		// Call the SOAP method
		$result = $client->bpSettleRequest($parameters);

		// Display the result
		if (isset($result->return)) {
			$res = explode (',',$result->return);

			$ResCode = $res[0];

			if ($ResCode == "0") {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			// we could not connect to bank!
			// var_dump($result);
		}
    }
}
