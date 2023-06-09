<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_lib
{
    protected $CI;

    public function __construct()
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();
        $this->CI->load->library('session');
        $this->CI->load->helper('url');
    }

    public function send_email($system_email = null, $email_to = null, $subject = null, $message = null)
    {
        $this->CI->load->library('email');
        $mail_config['mailtype'] = "html";
        $mail_config['charset'] = "utf8";
        $this->CI->email->initialize($mail_config);
        $this->CI->email->from($system_email, $system_email);
        $this->CI->email->to($email_to);
        $this->CI->email->subject($subject);
        $this->CI->email->message($message);
        $is_sent = $this->CI->email->send();

        return $is_sent;
    }
}