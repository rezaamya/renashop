<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
	/**************************************
	 * Login Page
	 *************************************/
	public function index ()
	{
        if(!$this->session->has_userdata('session_lifetime') || !$this->session->has_userdata('last_activity') || $this->session->userdata('session_lifetime') - $this->session->userdata('last_activity') < 0)
        {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $this->session->set_userdata('page_title', 'ورود');

            if (isset($username) && isset($password))
            {
                $password = md5($password);
                //username va password ersal shode ast
                //banabarin be database vasl mishavim ta sehate etelaeate ersali ra barresi konim
                //$query = $this->db->query("SELECT * FROM `users` WHERE `username` = '". $username ."' AND `password` = '". $password ."'");
                $this->db->where(array('username' => $username, 'password' => $password));
                $query = $this->db->get('users');

                $result = $query->row_array();
                if ($query->num_rows() == 1 and $result['is_block'] != 'yes')
                {
                    //we found a user
                    $session_data = array (
                        'id' => $result['id'],
                        'username' => $result['username'],
                        'user_type' => $result['user_type'],
                        'first_name' => $result['first_name'],
                        'last_name' => $result['last_name']
                    );

                    //set session lifetime
                    if ($this->input->post('remember_me'))
                    {
                        //set session life time to a year
                        $session_data['session_lifetime'] = 60*60*24*365 + time(); //one year
                        $session_data['remember_me'] = 'yes';
                    }
                    else
                    {
                        //set session lifetime to two hours
                        $session_data['session_lifetime'] = 60*60*2 + time(); //two hours
                    }

                    //Array ( [id] => 5 [username] => admin [user_type] => admin [first_name] => کریم [last_name] => کریمی [session_lifetime] => 1524712710 )
                    $this->session->set_userdata($session_data);
                    //print_r($this->session->userdata());
                    //user is logged in now. we should redirect him
                    redirect(base_url(), 'location');
                    return true;
                    /*if ($this->session->userdata('user_type') == 'admin')
                    {
                        //redirect(base_url(), 'location');
                        //$error_msg = "user is admin";
                        //$this->mylib->set_error($error_msg);
                    }
                    else
                    {
                        //redirect(base_url(), 'location');
                        //$error_msg = "user is nooot admin";
                        //$this->mylib->set_error($error_msg);
                    }*/
                }
                else
                {
                    if($result['is_block'] == 'yes')
                    {
                        $error_msg = "دسترسی شما نامعتبر است.";
                        $this->mylib->set_error($error_msg);
                    }
                    else
                    {
                        $error_msg = "نام کاربری یا کلمه عبور  اشتباه است.";
                        $this->mylib->set_error($error_msg);
                    }

                }
            }
            else
            {
                //username va password ersal nashode ast
                /*$error_msg = "نام کاربری و رمز عبور ارسال نشده است.";
                $this->mylib->set_error($error_msg);
                //redirect(base_url('login'), 'location');*/
            }

            //check if user is login, redirect him to another page
            if ($this->mylib->is_login())
            {
                redirect(base_url(), 'location');
            }

            $data = array('page_name' => 'login');
            $data['sys_msg'] = $this->load->view('template/sys_msg', array('error_type' => 'danger'), TRUE);
            $this->load->view('template/header', $data);
            $this->load->view('login/login', $data);
            $this->load->view('template/footer');
        }
	    else
        {
            redirect(base_url());
        }
	}

	/*************************************
	 * Logout
	 ************************************/
	public function logout ()
	{
		//session_destroy();
		$this->session->sess_destroy();
		redirect(base_url(), 'location');
	}
}


