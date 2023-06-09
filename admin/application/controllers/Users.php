<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

    /**
     * Dar in safhe customer ra modiriat (ADD, DELETE, EDIT) mikonim
     */
    public function index()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

        $main_db_name = "users";     
        $html_output = array();
		$this->session->set_userdata('page_title', 'کاربران');
        $task = $this->input->post('task');
        $list_items = $this->input->post('list_items[]');

        if ($task == 'active')
        {
            foreach ($list_items as $value)
            {
                $this->db->set('is_block', 'no');
                $this->db->where('id', $value);
                $this->db->update($main_db_name);
            }
        }
        else if ($task == 'inactive')
        {
            foreach ($list_items as $value)
            {
                $this->db->set('is_block', 'yes');
                $this->db->where('id', $value);
                $this->db->update($main_db_name);
            }
        }

        else if ($task == 'delete')
        {
            $this->load->model('user/user');
            foreach ($list_items as $value)
            {
                if($this->session->userdata('id') != $value)
				{
					$this->user->delete(array('id' => $value));
					$this->mylib->set_success(lang('deleted_successfully'));
				}
				else
				{
					$error_msg = "<div>شما نمیتوانید حساب کاربری خود را حذف کنید.</div>";
					$this->mylib->set_error($error_msg);
				}
            }
        }

       
        //Customize by user search keyword
        $search = $this->input->post('search');
        //Search query

        if($search != "")
        {
            $this->db->like('first_name' , $search);
            $this->db->or_like('last_name' , $search);
            $this->db->or_like('username' , $search);
            $this->db->or_like('email' , $search);
        }
        $html_output['search'] = $search;

		//////////////////
		// Set Per_Page //
		//////////////////
		//Set Number of items per_page
		$session_handler = $this->uri->segment(1).'per_page';
		if ($this->input->post('per_page') != null)
		{
			//Per page is posted (it mean that user has been changed the number of per page manually)
			$per_page = intval($this->input->post('per_page'));
		}
		else if ($this->session->has_userdata($session_handler))
		{
			//Per page is not posted but it was posted recently and we have it into the session
			//then we can use session
			$per_page = $this->session->userdata($session_handler);
		}
		else
		{
			//Per page is not posted and we don't have it in Session too
			//set default value for per_page
			$per_page = 20;
		}

		//Update Session
		$this->session->set_userdata($session_handler, $per_page);

        //Get Items from Database
        $page = ($this->uri->segment(2));
		$this->db->order_by('id', 'DESC');
        $this->db->limit($per_page, $page);
        $query = $this->db->get($main_db_name);

        $temp_html = "";
        if ($query->num_rows() > 0 ) 
        {
            foreach ($query->result() as $row) 
            {
                $temp_html .= "<tr>";
                $temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
                $temp_html .= '<td><a href="'.base_url("users/add/".$row->id).'">'. $row->first_name .'</a></td>';
                $temp_html .= '<td><a href="'.base_url("users/add/".$row->id).'">'. $row->last_name .'</a></td>';
                $temp_html .= '<td><a href="'.base_url("users/add/".$row->id).'">'. $row->username .'</a></td>';
                $temp_html .= '<td><a href="'.base_url("users/add/".$row->id).'">'. $row->email .'</a></td>';
				$temp_html .= '<td class="text-center"><i class="fa fa-'.($row->is_block =="yes" ? "lock" : "unlock").'"></i></td>';
                $temp_html .= '<td><a href="'.base_url("users/add/".$row->id).'">'. lang($row->status) .'</a></td>';
                $temp_html .= "</tr>";
            }
        }
        else
        {
            //We don't have any Item in our Database
            $temp_html = "<tr><td colspan='3'>".lang('there_is_not_any_item_to_show')."</td></tr>";
        }

        $html_output['main_table_rows'] = $temp_html;
        $html_output['pagination'] = $this->mylib->get_pagination ($this->uri->segment(1), $main_db_name, $this->session->userdata($this->uri->segment(1).'per_page'), 2);

        $data['page_name'] = 'users';
        ///////////////////////////////////////
        // Create Error and Success Messages //
        ///////////////////////////////////////
        $html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);

        $data['main_db_name'] = $main_db_name;
        $data['html_output'] = $html_output;

        $task = $this->input->post('task');
        if ($task == 'add') {
            redirect(base_url('list_users/add'), 'refresh');
        }

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/list', $data);
        $this->load->view('template/footer');
    }

    public function add($item_id = null)
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		//set error if user status==need_to_change_password and his not changed password
		if ($item_id)
		{
			$this->db->select('status');
			$this->db->where(array('id' => $item_id));
			$users_query = $this->db->get('users')->row();
			if(isset($users_query->status))
			{
				if($users_query->status == 'need_to_change_password')
				{
					$error_message = "<div>شما باید پسوورد خود را تغییر دهید.</div>";
					$this->mylib->set_error($error_message);
				}
			}
		}

        $this->load->model('user/user');
        $main_db_name = "users";
        $html_output = array();
        $task = $this->input->post('task');

         if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
        {
            //Are we editing current_item
            $posted_username= $this->input->post('username');
            $posted_email = $this->input->post('email');
            $query = $this->db->get($main_db_name);
            foreach ($query->result() as $row)
            {
                if($item_id==$row->id)
                {
                    $item_id_username = $row->username;
                    $item_id_email = $row->email;

                    break;
                }
            }

            //Set Form Rules:
        $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|min_length[2]|max_length[50]');

        $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required|min_length[3]|max_length[50]');

        $this->form_validation->set_rules('username', lang('username'), 'trim|required|callback__username_check|min_length[3]|max_length[100]'.($item_id==null || $posted_username!=$item_id_username ? "|is_unique[$main_db_name.username]" :""));
			$this->form_validation->set_message('_username_check', '«{field}» میتواند تنها شامل حروف و اعداد انگلیسی و علامت های @ و . و _ باشد. همچنین درج فاصله (Space) مجاز نمیباشد.');

        $this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[100]'.($item_id==null || $posted_email!=$item_id_email ? "|is_unique[$main_db_name.email]" :""));

        $this->form_validation->set_rules('status', lang('status'), 'required|in_list[accepted,need_to_change_password,need_to_confirm_email]');

        if($item_id == null || $this->input->post('password') != null || $this->input->post('confirm_password') != null)
        {
			$this->form_validation->set_rules('password', lang('password'), 'trim|required|min_length[5]|max_length[50]');

			$this->form_validation->set_rules('confirm_password', lang('confirm_password'), 'trim|matches[password]|required|min_length[5]|max_length[50]');
		}

        $this->form_validation->set_rules('is_block', lang('is_block'), 'required|in_list[yes,no]');

        $this->form_validation->set_rules('send_email', lang('send_email'), 'required|in_list[yes,no]');
        }

            if ($this->form_validation->run() == TRUE)
            {
                $dadeh = array
                (
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'username' => $this->input->post('username'),
                    'email' => $this->input->post('email'),
                    'status' => $this->input->post('status'),
                    'is_block' => $this->input->post('is_block'),
                    'send_email' => $this->input->post('send_email')
                );

                if($item_id == null || $this->input->post('password') != null)
				{
					$dadeh['password'] = md5($this->input->post('password'));
				}

                if ($item_id)
                {
                    //change user status to accepted if his status==need_to_change_password and his changed password
					$this->db->select('password,status');
					$this->db->where(array('id' => $item_id));
					$users_query = $this->db->get('users')->result();
					foreach($users_query as $user_row)
					{
						if($user_row->status == 'need_to_change_password')
						{
							if($user_row->password != md5($this->input->post('password')) && $this->input->post('password') != '')
							{
								$dadeh['status'] = 'accepted';
								//unset change password error
								$session_handler = $this->uri->segment(1).$this->uri->segment(2).'alert_msg';
								if($this->session->has_userdata($session_handler))
								{
									if($this->session->userdata($session_handler) == "<div>شما باید پسوورد خود را تغییر دهید.</div>")
									{
										$this->session->unset_userdata($session_handler);
									}
								}
							}
							else
							{
								$dadeh['status'] = 'need_to_change_password';
							}
						}
					}
                    //we should update this $edit_id into the database
                    $this->user->update($item_id, $dadeh);
                    //set name for menu
					if($this->session->userdata('id') == $item_id)
					{
						$this->session->set_userdata('first_name',$this->input->post('first_name'));
						$this->session->set_userdata('last_name',$this->input->post('last_name'));
					}
                }

                else
                {
                    //this is new Item
                    $item_id = $this->user->insert($dadeh);
                }

                if ($task == "save" || $task == "save_and_new")
                {
                    //set success message
                    $this->mylib->set_success(lang('success_msg'));
                }
                else if ($task == "save_and_close")
                {
                    //set success message
                    $this->mylib->set_success(lang('success_msg'), 'usersindexsuccess_msg');
                }

                if ($task == "save")
                {
                    //Go to Paretn Page
                    redirect(base_url("users/add/".$item_id));
                }

                else if ($task == "save_and_close")
                {
                    //Go to Paretn Page
                    redirect(base_url("users/index"));
                }
                else if ($task == "save_and_new")
                {
                    //Refresh current page
                    redirect(base_url("users/add"));
                }
            }

        //It's Edit state, then we need to simulate edit for user
        if ($item_id)
        {
			$this->session->set_userdata('page_title', 'ویرایش کاربر');
            $page_name = 'edit_user';
            $this->db->where('id', $item_id);
            $query = $this->db->get($main_db_name);
            $html_output['item_data'] = $query->row_array();
        }
        else
        {
			$this->session->set_userdata('page_title', 'افزودن کاربر');
        	$page_name = 'add_user';
            //Default Item Data
            $html_output['item_data'] = array(
                "id" => '',
                "first_name" => '',
                "last_name" => '',
                "username" => '',
                "email" => '',
                "status" => '',
                "is_block" => 'no',
                "send_email" => 'yes'
            );
        }
    
        $data['page_name'] = $page_name;

        ///////////////////////////////////////
        // Create Error and Success Messages //
        ///////////////////////////////////////

        $html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
        $data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/add' , $data);
        $this->load->view('template/footer');
    }

	//this function is used to validate entered Username
	function _username_check($str)
	{
		$Valid_character_list = array('.', '_', '@');

		if(!ctype_alnum(str_replace($Valid_character_list, '', $str))) {
			return FALSE;
		}

		return true;
	}

	//function for check email code for accept user
	public function authorize_user($code = null)
	{
		if($this->session->has_userdata('authorize_user_code') && $code != null)
		{
			if($this->session->userdata('authorize_user_code') == $code)
			{
				$this->db->set('status', 'accepted');
				$this->db->where(array('id' => $this->session->userdata('id')));
				$this->db->update('users');
				redirect(base_url());
			}
			else
			{
				redirect(base_url('?need_to_confirm_email=yes'));
			}
		}
		else
		{
			redirect(base_url('?need_to_confirm_email=yes'));
		}
	}
}
