<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mylib {
	protected $CI;

	public function __construct()
	{
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$this->CI->load->helper('url');
	}

	public function set_error($error_msg = '', $session_handler = null)
	{
		if (is_null($session_handler))
		{
			//we should use defaulte controler/method as handler name
			$session_handler = $this->CI->uri->segment(1).$this->CI->uri->segment(2).'alert_msg';
		}

		$temp_error = "";
		if ($this->CI->session->has_userdata($session_handler))
		{
			$temp_error = $this->CI->session->userdata($session_handler);
			if($error_msg == "<div>شما باید پسوورد خود را تغییر دهید.</div>" && $temp_error == "<div>شما باید پسوورد خود را تغییر دهید.</div>")
			{
				$temp_error = '';
			}
		}

		//set new error message
		$temp_error = is_array($temp_error) ? implode(" ",$temp_error) : $temp_error;
		$error_msg = is_array($error_msg) ? implode(" ",$error_msg) : $error_msg;

		$this->CI->session->set_userdata($session_handler, $temp_error.$error_msg);
	}

	public function set_success($success_msg = '', $session_handler = null)
	{
		if (is_null($session_handler))
		{
			//we should use defaulte controler/method as handler name
			$session_handler = $this->CI->uri->segment(1).$this->CI->uri->segment(2).'success_msg';
		}

		/*$temp_success_msg = "";
		if ($this->CI->session->has_userdata($session_handler))
		{
			$temp_success_msg = $this->CI->session->userdata($session_handler);
		}

		//set new success message
		$this->CI->session->set_userdata($session_handler, $temp_success_msg.$success_msg);*/
		//set new success message
		$this->CI->session->set_userdata($session_handler, $success_msg);
	}

	public function get_pagination ($base_url, $db_table_name, $per_page, $uri_segment = 3)
        {
            $config = array();
            $config["base_url"] = base_url($base_url);
            $config["total_rows"] =  $this->CI->db->count_all($db_table_name);
            $config["per_page"] = $per_page;
            $config["uri_segment"] = $uri_segment;

            //Style Pagination
            $config['attributes'] = array('class' => 'page-link');

            $config['full_tag_open'] = '<nav><ul class="pagination">';
            $config['full_tag_close'] = '</ul></nav>';

            $config['num_tag_open'] = '<li class="page-item">';
            $config['num_tag_close'] = '</li>';

            $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
            $config['cur_tag_close'] = '</a></li>';
            
            $config['first_link'] = $this->CI->lang->line('first'); 
            $config['first_tag_open'] = '<li class="page-item">';
            $config['first_tag_close'] = '</li>';

            $config['last_link'] = $this->CI->lang->line('last');
            $config['last_tag_open'] = '<li class="page-item">';
            $config['last_tag_close'] = '</li>';

            //$config['next_link'] = '&gt;';
            $config['next_tag_open'] = '<li class="page-item">';
            $config['next_tag_close'] = '</li>';

            //$config['prev_link'] = '&lt;';
            $config['prev_tag_open'] = '<li class="page-item">';
            $config['prev_tag_close'] = '</li>';


            $this->CI->pagination->initialize($config);
            return $this->CI->pagination->create_links();
        }

	//$params are an array. in $parrams we will set location for login or not login state
	public function is_login(
		$redirect = false,
		$admin_login_direction = '',
		$client_login_direction = '',
		$not_login_direction = 'login'
	)
	{
		//redirect user to profile to change password if his status==need_to_change_password
		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$this->CI->db->select('status');
		$this->CI->db->where(array('id' => $this->CI->session->userdata('id')));
		$users_query = $this->CI->db->get('users')->row();
		if($redirect == false && $this->CI->session->has_userdata('id') && isset($users_query->status))
		{
			if($actual_link != base_url('users/add/'.$this->CI->session->userdata('id')) && $users_query->status == 'need_to_change_password')
			{
				redirect(base_url('users/add/'.$this->CI->session->userdata('id')));
			}
			if($users_query->status == 'need_to_confirm_email')
			{
				if($actual_link != base_url() && $actual_link != base_url('?need_to_confirm_email=yes'))
				{
					redirect(base_url());
				}
			}
		}

		//by default we won't redirect user to login page or to correct page related to his user_type
		if ($this->CI->session->has_userdata('last_activity'))
		{
			//we had at least one user before on this computer
			//check session life time to see this user status
			$last_activity = $this->CI->session->userdata('last_activity');
			$lifetime = $this->CI->session->userdata('session_lifetime');

			if ($lifetime - $last_activity > 0)
			{
				//user have time yet
				//update his next lifetime because he is visiting the site currently
				if ($this->CI->session->has_userdata('remember_me'))
				{
					//set life time for one year
					$this->CI->session->set_userdata('session_lifetime', time() + (60*60*24*365));
				}
				else
				{
					//set lifetime for two hours
					$this->CI->session->set_userdata('session_lifetime', time() + (60*60*2));
				}

				if ($redirect && isset($users_query->status))
				{
					//if there is any redirection link, redirect to that link related to 'user_type'
					if($users_query->status != 'need_to_change_password' && $users_query->status != 'need_to_confirm_email')
					{
						if ($this->is_admin())
						{
							redirect(base_url(), 'location');
						}
						else
						{
							redirect(base_url(), 'location');
						}
					}
				}
				else
				{
					//if there is no direction link, return true
					return true;
				}
			}
			else
			{
				//user's session expired
				//we can redirect to somewhere (maybe login page)
			}
		}

		//user is not logged in (or session is dead)
		//destroy all saved sessions
        //in local there is no problem with destroying the session multiple times
        //but when the site is online, it will this error:
        /*
        A PHP Error was encountered
        Severity: Warning
        Message: session_destroy(): Trying to destroy uninitialized session
        Filename: Session/Session.php
        */
        //to solve this error, I found bellow way.
        //we need to check if session library is exist, then try to destroy it!
        if (class_exists('Session'))
        {
            $this->CI->session->sess_destroy();
        }

		//if user requested, goto login page
		if ($redirect)
		{
			redirect(base_url($not_login_direction), 'location');
		}

		return false;
	}

	public function is_admin()
	{
		if ($this->CI->session->has_userdata('user_type'))
		{
			if ($this->CI->session->userdata('user_type') == 'admin')
			{
				return true;
			}
		}
		else
		{
			//user_type is not detected
			//destroy all saved sessions
			$this->CI->session->sess_destroy();
			redirect(base_url(), 'location');
		}

		return false;
	}
}
