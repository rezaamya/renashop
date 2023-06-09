<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
	/**************************************
	 * Login Page
	 *************************************/
	public function index ()
	{
        if(!$this->session->has_userdata('id'))
        {
            $this->session->set_userdata('page_title', 'ورود');
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            if (isset($username) && isset($password))
            {
                $password = md5($password);
                //username va password ersal shode ast
                //banabarin be database vasl mishavim ta sehate etelaeate ersali ra barresi konim
                //$query = $this->db->query("SELECT * FROM `customer` WHERE `username` = '". $username ."' AND `password` = '". $password ."'");
                $this->db->where(array('username' => $username, 'password' => $password));
                $query = $this->db->get('customer');

                $result = $query->row_array();
                if ($query->num_rows() == 1 and $result['condition'] != 'inactive')
                {
                    //we found a user
                    $session_data = array (
                        'id' => $result['id']
                    );

                    //set session lifetime
                    if ($this->input->post('remember'))
                    {
                        //set session life time to a year
                        $session_data['session_lifetime'] = 60*60*24*365 + time(); //one year
                        $session_data['remember_me'] = 'yes';
                    }
                    else
                    {
                        //set session lifetime to two hours
                        $session_data['session_lifetime'] = 2*60*60 + time(); //two hours
                    }

                    //Array ( [id] => 5 [username] => admin [user_type] => admin [first_name] => کریم [last_name] => کریمی [session_lifetime] => 1524712710 )
                    $this->session->set_userdata($session_data);
                    //user is logged in now. we should redirect him
                    $get_redirect = $this->input->get('redirect');
                    if(isset($get_redirect))
                    {
                        redirect($get_redirect);
                    }
                    elseif($this->session->has_userdata('previous_page_link'))
                    {
                        $previous_page_link = $this->session->userdata('previous_page_link');
                        $this->session->unset_userdata('previous_page_link');
                        redirect($previous_page_link);
                    }
                    else
                    {
                        redirect(base_url('profile'));
                    }
                    return true;
                }
                else
                {
                    if($result['condition'] == 'inactive')
                    {
                        $error_msg = "دسترسی شما نامعتبر است.";
                        $this->mylib->set_error($error_msg);
                    }
                    else
                    {
                        $error_msg = "نام کاربری یا کلمه عبور  اشتباه است.";
                        $this->mylib->set_error($error_msg);
                    }

                    $get_redirect = $this->input->get('redirect');
                    if(isset($get_redirect))
                    {
                        redirect($get_redirect);
                    }
                }
            }
            $data = array('page_name' => 'login');
            $data['sys_msg'] = $this->load->view('template/sys_msg',  array('error_type' => 'danger'), TRUE);

            $content = $this->load->view('pages/login', $data, true);
            $position_out = $this->mylib->replace_modules_in_position($content);
            $this->load->view('template/header', $data);
            $this->output->append_output($position_out['html_content']);
            $this->load->view('template/footer', array('bottom_scripts' => $position_out['bottom_scripts']));
        }
	    else
        {
            redirect(base_url('profile'));
        }
	}

	/*************************************
	 * Logout
	 ************************************/
	public function logout ()
	{
		//session_destroy();
		$this->session->sess_destroy();
		$get_redirect = $this->input->get('redirect');
		if(isset($get_redirect))
		{
			redirect($get_redirect);
		}
		else
		{
			redirect(base_url(), 'location');
		}
	}
}


