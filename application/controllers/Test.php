<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller
{
    public function index()
    {
        //$this->load->library('sms');
        //$this->sms->send_sms('09198976874', 'کد ورود: 12345');
		echo '<form target="'.base_url('test').'" method="post"><input type="text" name="username"><button type="submit">submit</button></form>';

		$code_sent_to = 'email';
		$valid = 'no';
		$validation_errors = "";

		$username = $this->input->post('username');
		//$password = $this->input->post('password');

		//$code = substr(str_shuffle("0123456789"), 0, 6);
		//$this->session->set_userdata('code1', $code);

		if(isset($username))
		{
			$this->form_validation->set_rules('username', 'نام کاربری', 'trim|required|valid_email|min_length[6]|max_length[60]');

			if ($this->form_validation->run() == TRUE)
			{
				echo "yes";
			}
			else {
				echo validation_errors();
			}
		}

		echo "<br/><br/>$username";

    }
}
