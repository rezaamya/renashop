<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms {
	protected $CI;
	protected $SMS_API;
	protected $sender;

	public function __construct()
	{
		// Assign the CodeIgniter super-object
		//$this->CI =& get_instance();
		//$this->CI->load->library('session');
		//$this->CI->load->helper('url');
        require(APPPATH.'/libraries/sms_lib/autoload.php');
        $this->SMS_API = new \Kavenegar\KavenegarApi(KAVEHNEGAR_API_KEY);
        $this->sender = KAVEHNEGAR_SENDER;
	}

    public function send_sms($receptor, $message)
    {
    	//$receptor should be something like this: 09193498734
        $this->SMS_API->Send($this->sender, $receptor, $message);
    }
}
