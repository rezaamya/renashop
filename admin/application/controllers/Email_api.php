<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

	/**
	 * Dar in safhe gharar ast tamami darkhasthaye AJAX (ya sayer request ha) ra pasokh bedahim
	 */
	public function index()
	{
		if (!$this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$number_of_sent_emails = 0;
		$number_of_failed_emails = 0;

		$this->db->where(array('selected_emails !=' => '', 'condition' => 'sending'));
		$this->db->limit(1);
		$query = $this->db->get('email')->row();
		if(isset($query))
		{
			$selected_emails = json_decode($query->selected_emails);
			$email_count = count($selected_emails);
			$sent_emails = json_decode($query->sent_emails);
			$failed_emails = json_decode($query->failed_emails);
			$system_email = $query->email_from;

			if($system_email == '' || $system_email == null)
			{
				$this->db->select('system_email');
				$this->db->where(array('id' => 1));
				$system_email = $this->db->get('setting')->row();
				$system_email = $system_email->system_email;
			}

			$sent_emails_array = array();
			$failed_emails_array = array();
			$end = 1;
			for($i = 0; $i < $email_count; $i++)
			{
				if($end > 50)
				{
					break;
				}
				if($sent_emails != null && $sent_emails != '')
				{
					if(!in_array($selected_emails[$i], $sent_emails))
					{
						$this->load->library('email');
						$mail_config['mailtype'] = "html";
						$this->email->initialize($mail_config);
                        $this->email->from($system_email, $system_email);
                        $this->email->to($selected_emails[$i]);
                        $this->email->subject($query->subject);
                        $this->email->message($query->message);
                        $is_sent = $this->email->send();

                        if($is_sent == true)
						{
							array_push($sent_emails_array, $selected_emails[$i]);
							$number_of_sent_emails++;
						}
						else
						{
							array_push($failed_emails_array, $selected_emails[$i]);
							$number_of_failed_emails++;
						}
						$end++;
					}
				}
				else
				{
					$this->load->library('email');
					$mail_config['mailtype'] = "html";
					$this->email->initialize($mail_config);
                    $this->email->from($system_email, $system_email);
                    $this->email->to($selected_emails[$i]);
                    $this->email->subject($query->subject);
                    $this->email->message($query->message);
					$is_sent = $this->email->send();

					if($is_sent == true)
					{
						array_push($sent_emails_array, $selected_emails[$i]);
						$number_of_sent_emails++;
					}
					else
					{
						array_push($failed_emails_array, $selected_emails[$i]);
						$number_of_failed_emails++;
					}
					$end++;
				}
			}
			///////////////////////////////////////////////
			//////////create json of sent emails//////////
			/////////////////////////////////////////////
			if($sent_emails != null && $sent_emails != '')
			{
				foreach($sent_emails_array as $sent_row)
				{
					array_push($sent_emails, $sent_row);
				}
			}
			else
			{
				$sent_emails = $sent_emails_array;
			}

			if($sent_emails != array())
			{
				$sent_emails = json_encode($sent_emails);
			}
			else
			{
				$sent_emails = '';
			}
			$count_sent_emails = json_decode($sent_emails);
			$count_sent_emails = count($count_sent_emails);

			//////////////////////////////////////////////////
			//////////create json of failed email////////////
			////////////////////////////////////////////////
			if($failed_emails != null && $failed_emails != '')
			{
				foreach($failed_emails_array as $failed_row)
				{
					array_push($failed_emails, $failed_row);
				}
			}
			else
			{
				$failed_emails = $failed_emails_array;
			}
			if($failed_emails != array())
			{
				$failed_emails = json_encode($failed_emails);
			}
			else
			{
				$failed_emails = '';
			}

			/////////////////////////////////////
			//////////update database///////////
			///////////////////////////////////
			if($count_sent_emails == $email_count)
			{
				$this->db->set('condition', 'sent');
			}
			$this->db->set('sent_emails', $sent_emails);
			$this->db->set('failed_emails', $failed_emails);
			$this->db->where('id',$query->id);
			$this->db->update('email');
		}
		$response = array("sent_emails" => $number_of_sent_emails, "failed_emails" => $number_of_failed_emails);
		echo json_encode($response, true);
	}
}
