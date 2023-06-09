<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketing extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

    /**
     * Dar in safhe Marketing ra modiriat (ADD, DELETE, EDIT) mikonim
     */
    public function index()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'لیست کمپین‌ها');
		$main_db_name = "campaignes";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("marketing/add/".$list_items[0]), 'location');
			}
			elseif ($task == 'publish')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('publish', 'yes');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}
			}
			elseif ($task == 'unpublish')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('publish', 'no');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}

			}
			elseif ($task == 'delete')
			{
				$this->load->model('marketing/campaign');

				foreach ($list_items as $value)
				{
					//delete item
					$this->campaign->delete(array('id' => $value));
					//set success message
					$this->mylib->set_success(lang('deleted_successfully'));
				}
			}
		}

		//Customize by user search keyword
		$search = $this->input->post('search');
		//Search query
		if($search != "")
		{
			$this->db->like('campaign_name' , $search);
			$this->db->or_like('campaign_description' , $search);
			$this->db->or_like('tracking_code' , $search);
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
				$insert_date = '';
				if($row->insert_date != null && $row->insert_date != '' && $row->insert_date != 0)
				{
					$insert_date = $this->date_shamsi->jdate('o/m/j', $row->insert_date,'','Asia/Tehran', 'fa');
				}

				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("marketing/add/".$row->id).'">'. $row->campaign_name .'</a></td>';
				$temp_html .= '<td>'. $row->number_of_click .'</td>';
				$temp_html .= '<td>'. $insert_date .'</td>';
				$temp_html .= '<td class="text-center"><i class="'.($row->publish =="yes" ? "fas" : "far").' fa-star"></i></td>';
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

		$data['page_name'] = 'campaigns_list';
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);

		$data['main_db_name'] = $main_db_name;
		$data['html_output'] = $html_output;

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

        $main_db_name = "campaignes";
		$html_output = array();
        $this->load->model('marketing/campaign');
        $task = $this->input->post('task');

        if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
        {
            $posted_campaign_name=$this->input->post('campaign_name');
            $posted_tracking_code = $this->input->post('tracking_code');
			$query=$this->db->get($main_db_name);
            foreach ($query->result() as $row)
            {
                if($item_id !=null && $item_id==$row->id)
                {
                    $item_id_campaign_name=$row->campaign_name;
                    $item_id_tracking_code=$row->tracking_code;
                }
            }
            //Set Form Rules:
            $this->form_validation->set_rules('campaign_name', lang('campaign_name'), 'trim|required|min_length[2]|max_length[50]'.($item_id==null || $item_id_campaign_name!=$posted_campaign_name ? "|is_unique[$main_db_name.campaign_name]" :""));

            $this->form_validation->set_rules('campaign_description', lang('campaign_description'), 'trim|min_length[2]');

            $this->form_validation->set_rules('tracking_code', lang('tracking_code'), 'trim|required|min_length[1]|max_length[30]'.($item_id==null || $item_id_tracking_code!=$posted_tracking_code ? "|is_unique[$main_db_name.tracking_code]" :""));

            $this->form_validation->set_rules('example', lang('example'), 'trim|min_length[3]|max_length[100]');

            $this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

            if ($this->form_validation->run() == TRUE)
            {
                $dadeh = array
                (
                    'campaign_name' => $this->input->post('campaign_name'),
                    'campaign_description' => $this->input->post('campaign_description'),
                    'tracking_code' => $this->input->post('tracking_code'),
                    'publish' => $this->input->post('publish')
                );

                if ($item_id)
                {
                    //we should update this $edit_id into the database
                    $this->campaign->update($item_id, $dadeh);
                }
                else
                {
					$dadeh['insert_date'] = time();
                    $item_id = $this->campaign->insert($dadeh);
                }

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'marketingindexsuccess_msg');
				}

                if ($task == "save")
                {
                    //Go to Paretn Page
                    redirect(base_url("marketing/add/".$item_id));

                }

                else if ($task == "save_and_close")
                {
                    //Go to Paretn Page
                    redirect(base_url("marketing/index"));
                }
                else if ($task == "save_and_new")
                {
                    //Refresh current page
                    redirect(base_url("marketing/add"));
                }
            }
        }

		$html_output['item_data']['example'] = 'test';

        if ($item_id)
        {
			$this->session->set_userdata('page_title', 'ویرایش کمپین');
            $page_name = 'edit_campaign';
            $this->db->where('id', $item_id);
            $query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
			$html_output['item_data']['example'] ='http://amya.ir/demo/keshavarz/client/?tr='.$html_output['item_data']['tracking_code'];

        }
		else
		{
			$this->session->set_userdata('page_title', 'افزودن کمپین');
			$page_name = 'add_campaign';
			//Default Item Data
			$html_output['item_data'] = array(
				"id" => '',
				"campaign_name" => '',
				"campaign_description" => '',
				"tracking_code" => '',
				"example" => '',
				"publish" => 'yes'
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
        $this->load->view($this->uri->segment(1) . '/add', $data);
        $this->load->view('template/footer');

    }

    public function affiliates()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

        $task = $this->input->post('task');
        if ($task == 'edit_affiliates')
        {
            redirect(base_url('marketing/edit_affiliates'), 'refresh');
        }

        $data = array('page_name' => 'affiliates_list');

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/affiliates/list');
        $this->load->view('template/footer');
    }

    public function add_affiliates()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

        $data = array('page_name' => 'add_affiliates');

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/affiliates/add');
        $this->load->view('template/footer');

    }

    public function edit_affiliates()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

        $data = array('page_name' => 'edit_categories');

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/affiliates/edit');
        $this->load->view('template/footer');

    }

    public function email()
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$this->session->set_userdata('page_title', 'لیست ایمیل‌ها');
		$main_db_name = "email";
		$html_output = array();

		$this->load->model('marketing/email_model');

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("marketing/add_email/".$list_items[0]), 'location');
			}
			elseif ($task == 'continue_sending')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('condition', 'sending');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}
			}
			elseif ($task == 'stop_sending')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('condition', 'stopped');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}

			}
			elseif ($task == 'delete')
			{
				foreach ($list_items as $value)
				{
					//delete item
					$this->email_model->delete(array('id' => $value));
					//set success message
					$this->mylib->set_success(lang('deleted_successfully'));
				}
			}
		}

		//Customize by user search keyword
		$search = $this->input->post('search');
		//Search query
		if($search != "")
		{
			$this->db->like('title' , $search);
			$this->db->or_like('email_from' , $search);
			$this->db->or_like('email_to' , $search);
			$this->db->or_like('subject' , $search);
			$this->db->or_like('message' , $search);
		}
		$html_output['search'] = $search;

		//////////////////
		// Set Per_Page //
		//////////////////
		//Set Number of items per_page
		$session_handler = $this->uri->segment(1).$this->uri->segment(2).'per_page';
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
		$page = ($this->uri->segment(3)) ;
		$this->db->order_by('modify_date', 'DESC');
		$this->db->limit($per_page, $page);
		$query = $this->db->get($main_db_name);

		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$insert_date = '';
				$modify_date = '';

				if($row->insert_date != null)
				{
					$insert_date = $this->date_shamsi->jdate('o/m/j', $row->insert_date,'','Asia/Tehran', 'fa');
				}

				if($row->modify_date != null)
				{
					$modify_date = $this->date_shamsi->jdate('o/m/j', $row->modify_date, '', 'Asia/Tehran', 'fa');
				}

				$selected_emails = json_decode($row->selected_emails);
				$sent_emails = json_decode($row->sent_emails);
				$customer_count = count($selected_emails);
				$sent_email_count = count($sent_emails);

				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("marketing/add_email/".$row->id).'">'. $row->title .'</a></td>';
				$temp_html .= '<td>'. $customer_count .'</td>';
				$temp_html .= '<td>'. $sent_email_count .'</td>';
				$temp_html .= '<td>'. $insert_date .'</td>';
				$temp_html .= '<td>'. $modify_date .'</td>';
				$temp_html .= '<td>'. lang($row->condition) .'</td>';
				$temp_html .= "</tr>";
			}
		}
		else
		{
			//We don't have any Item in our Database
			$temp_html = "<tr><td colspan='3'>".lang('there_is_not_any_item_to_show')."</td></tr>";
		}

		$html_output['main_table_rows'] = $temp_html;
		$html_output['pagination'] = $this->mylib->get_pagination ($this->uri->segment(1).'/'.$this->uri->segment(2), $main_db_name, $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'per_page'));

		$data['page_name'] = 'emails_list';
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/email/list', $data);
        $this->load->view('template/footer');
    }

    public function add_email($item_id = null)
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$this->session->set_userdata('page_title', 'ارسال ایمیل');
		$main_db_name = "email";
		$html_output = array();
		$selected_id = '';

		$this->load->model('marketing/email_model');
		$this->load->model('customer/customer_category');
		$task = $this->input->post('task');

		if($task == 'save' || $task == 'send_and_new' || $task == 'send_and_close' || $task == 'send')
		{
			//Set Form Rules:
			$this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[2]|max_length[200]');

			$this->form_validation->set_rules('email_from', 'از طرف','trim|valid_email|min_length[6]|max_length[100]');

			$this->form_validation->set_rules('email_to', lang('email_to'), 'trim|required|in_list[all_newsletter_subscribers,customer_group,all_customers,selected_customers,all_affiliates,selected_affiliates,products]');

			$required_customer_group = '';
			if($this->input->post('email_to') == 'customer_group')
			{
				$required_customer_group = 'required|';
			}
			$this->form_validation->set_rules('customer_group', lang('customer_group'), $required_customer_group.'trim|'. $this->customer_category->get_inlist_string());

			$this->form_validation->set_rules('subject', lang('subject'), 'trim|required|min_length[2]|max_length[200]');

			$this->form_validation->set_rules('message', lang('message'), 'trim');

			$required_selected_customer_item = '';
			if($this->input->post('email_to') == 'selected_customers')
			{
				$required_selected_customer_item = '|required';
				$selected_id = json_encode($this->input->post('selected_customer_item[]'));
			}
			$this->form_validation->set_rules('selected_customer_item[]', lang('selected_customer_item[]'), 'trim'.$required_selected_customer_item);

			$required_selected_product_item = '';
			if($this->input->post('email_to') == 'products')
			{
				$required_selected_product_item = '|required';
				$selected_id = json_encode($this->input->post('selected_product_item[]'));
			}
			$this->form_validation->set_rules('selected_product_item[]', lang('selected_product_item[]'), 'trim'.$required_selected_product_item);

			if ($this->form_validation->run() == TRUE)
			{
				if($task == 'send_and_new' || $task == 'send_and_close' || $task == 'send')
				{
					$condition = 'sending';
				}
				elseif($task == 'save')
				{
					$condition = 'stopped';
				}

				$emails = array();
				if($this->input->post('email_to') == 'customer_group')
				{
					$customer_group = $this->input->post('customer_group');
					$this->db->where(array('customer_group' => $customer_group, 'email !=' => ''));
					$this->db->select('id,email');
					$customer_query = $this->db->get('customer')->result();
					foreach($customer_query as $cust_email)
					{
						array_push($emails, $cust_email->email);
					}
				}
				elseif($this->input->post('email_to') == 'all_customers')
				{
					$this->db->where(array('email !=' => ''));
					$this->db->select('id,email');
					$customer_query = $this->db->get('customer')->result();
					foreach($customer_query as $cust_email)
					{
						array_push($emails, $cust_email->email);
					}
				}
				elseif($this->input->post('email_to') == 'selected_customers')
				{
					$selected_customer_item = $this->input->post('selected_customer_item[]');
					$this->db->group_start();
					$this->db->where(array('email !=' => ''));
					$this->db->group_end();
					if(count($selected_customer_item) > 0)
					{
						$i = 1;
						$this->db->group_start();
						foreach($selected_customer_item as $cust_id)
						{
							if($i == 1)
							{
								$this->db->where(array('id' => $cust_id));
							}
							else
							{
								$this->db->or_where(array('id' => $cust_id));
							}
							$i++;
						}
						$this->db->group_end();
						$this->db->select('id,email');
						$customer_query = $this->db->get('customer')->result();
						foreach($customer_query as $cust_email)
						{
							array_push($emails, $cust_email->email);
						}
					}
				}
				elseif($this->input->post('email_to') == 'products')
                {
					$this->db->select('id,email');
					$this->db->where(array('email !=' => ''));
					$customer_query = $this->db->get('customer')->result();

                	$product_id_array = $this->input->post('selected_product_item[]');
                    $orders_query = $this->db->get('orders')->result();
                    foreach($orders_query as $order_row)
					{
						$cart = json_decode($order_row->cart);
						if($cart != '' && $cart != array() && $cart != null)
						{
							foreach($cart as $in_cart => $val_cart)
							{
								if(in_array($in_cart, $product_id_array))
								{
									foreach($customer_query as $cust_row)
									{
										if($order_row->user_id == $cust_row->id)
										{
											array_push($emails, $cust_row->email);
											break;
										}
									}
								}
							}
						}
					}
                }

				$dadeh = array
				(
					'title' => $this->input->post('title'),
					'email_from' => $this->input->post('email_from'),
					'email_to' => $this->input->post('email_to'),
					'customer_group' => $this->input->post('customer_group'),
					'subject' => $this->input->post('subject'),
					'message' => $this->input->post('message'),
					'selected_id' => $selected_id,
					'selected_emails' => json_encode($emails),
					'condition' => $condition
				);

				if ($item_id)
				{
					//we should update this $edit_id into the database
					$dadeh['modify_date'] = time();
					$this->email_model->update($item_id, $dadeh);
				}
				else
				{
					//this is new Item
					$dadeh['modify_date'] = time();
					$dadeh['insert_date'] = time();
					$item_id = $this->email_model->insert($dadeh);
				}

				if ($task == "save" || $task == "send_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "send_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'marketingemailsuccess_msg');
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("marketing/add_email/".$item_id));
				}

				else if ($task == "send_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("marketing/email"));
				}
				else if ($task == "send_and_new")
				{
					//Refresh current page
					redirect(base_url("marketing/add_email"));
				}
			}
		}


		//It's Edit state, then we need to simulate edit for user
		if ($item_id)
		{
			//$page_name = 'edit_content';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();

			////////////////////////////////////////
			////////set value for product//////////
			//////////////////////////////////////
			$html_output['item_data']['items_id'] = '';
			if($html_output['item_data']['email_to'] == 'products')
			{
				$json_selected_id = json_decode($html_output['item_data']['selected_id']);
				if(is_array($json_selected_id))
				{
					$pr = 0;
					foreach($json_selected_id as $pr_se_row)
					{
						$pr = 1;
					}
					if($pr == 1)
					{
						$o = 1;
						$this->db->select('id,title');
						foreach($json_selected_id as $pr_se_row)
						{
							if($o == 1)
							{
								$this->db->where(array('id' => $pr_se_row));
							}
							else
							{
								$this->db->or_where(array('id' => $pr_se_row));
							}
							$o++;
						}
						$pr_selected_title = $this->db->get('add_products')->result();
					}

					foreach($json_selected_id as $pr_se_row)
					{
						$pr_title = '';
						if(isset($pr_selected_title))
						{
							foreach($pr_selected_title as $pr_tit_row)
							{
								if($pr_se_row == $pr_tit_row->id)
								{
									$pr_title = $pr_tit_row->title;
								}
							}
						}
						$html_output['item_data']['items_id'] .= '<div class="selected_item_holder">
							<button type="button" class="close" onclick="this.parentNode.parentNode.removeChild(this.parentNode)">
								<span>×</span>
							</button>
							<span>'.$pr_title.'</span>
							<input class="selected_item" value="'.$pr_se_row.'" name="selected_product_item[]" type="hidden">
						</div>';
					}
				}
			}

			////////////////////////////////////////
			////////set value for customer/////////
			//////////////////////////////////////
			$html_output['item_data']['selected_customer'] = '';
			if($html_output['item_data']['email_to'] == 'selected_customers')
			{
				$json_selected_id = json_decode($html_output['item_data']['selected_id']);
				if(is_array($json_selected_id))
				{
					$pr = 0;
					foreach($json_selected_id as $pr_se_row)
					{
						$pr = 1;
					}
					if($pr == 1)
					{
						$o = 1;
						$this->db->select('id,first_name,last_name');
						foreach($json_selected_id as $pr_se_row)
						{
							if($o == 1)
							{
								$this->db->where(array('id' => $pr_se_row));
							}
							else
							{
								$this->db->or_where(array('id' => $pr_se_row));
							}
							$o++;
						}
						$pr_selected_title = $this->db->get('customer')->result();
					}

					foreach($json_selected_id as $pr_se_row)
					{
						$pr_title = '';
						if(isset($pr_selected_title))
						{
							foreach($pr_selected_title as $pr_tit_row)
							{
								if($pr_se_row == $pr_tit_row->id)
								{
									$pr_title = $pr_tit_row->first_name.' '.$pr_tit_row->last_name;
								}
							}
						}
						$html_output['item_data']['selected_customer'] .= '<div class="selected_item_holder">
							<button type="button" class="close" onclick="this.parentNode.parentNode.removeChild(this.parentNode)">
								<span>×</span>
							</button>
							<span>'.$pr_title.'</span>
							<input class="selected_item" value="'.$pr_se_row.'" name="selected_customer_item[]" type="hidden">
						</div>';
					}
				}
			}
		}
		else
		{
			//$page_name = 'add_article';
			//Default Item Data
			$html_output['item_data'] = array(
				"id" => '',
				"title" => '',
				"email_from" => '',
				"email_to" => '',
				"customer_group" => '',
				"subject" => '',
				"message" => '',
				"selected_id" => '',
				'selected_emails' => '',
				'condition' => ''
			);

			////////////////////////////////////////
			////////set value for product//////////
			//////////////////////////////////////
			$html_output['item_data']['items_id'] = '';
			if($this->input->post('email_to') == 'products')
			{
				$json_selected_id = $this->input->post('selected_product_item[]');
				if(is_array($json_selected_id))
				{
					$pr = 0;
					foreach($json_selected_id as $pr_se_row)
					{
						$pr = 1;
					}
					if($pr == 1)
					{
						$o = 1;
						$this->db->select('id,title');
						foreach($json_selected_id as $pr_se_row)
						{
							if($o == 1)
							{
								$this->db->where(array('id' => $pr_se_row));
							}
							else
							{
								$this->db->or_where(array('id' => $pr_se_row));
							}
							$o++;
						}
						$pr_selected_title = $this->db->get('add_products')->result();
					}

					foreach($json_selected_id as $pr_se_row)
					{
						$pr_title = '';
						if(isset($pr_selected_title))
						{
							foreach($pr_selected_title as $pr_tit_row)
							{
								if($pr_se_row == $pr_tit_row->id)
								{
									$pr_title = $pr_tit_row->title;
								}
							}
						}
						$html_output['item_data']['items_id'] .= '<div class="selected_item_holder">
							<button type="button" class="close" onclick="this.parentNode.parentNode.removeChild(this.parentNode)">
								<span>×</span>
							</button>
							<span>'.$pr_title.'</span>
							<input class="selected_item" value="'.$pr_se_row.'" name="selected_product_item[]" type="hidden">
						</div>';
					}
				}
			}

			////////////////////////////////////////
			////////set value for customer/////////
			//////////////////////////////////////
			$html_output['item_data']['selected_customer'] = '';
			if($this->input->post('email_to') == 'selected_customers')
			{
				$json_selected_id = $this->input->post('selected_customer_item[]');
				if(is_array($json_selected_id))
				{
					$pr = 0;
					foreach($json_selected_id as $pr_se_row)
					{
						$pr = 1;
					}
					if($pr == 1)
					{
						$o = 1;
						$this->db->select('id,first_name,last_name');
						foreach($json_selected_id as $pr_se_row)
						{
							if($o == 1)
							{
								$this->db->where(array('id' => $pr_se_row));
							}
							else
							{
								$this->db->or_where(array('id' => $pr_se_row));
							}
							$o++;
						}
						$pr_selected_title = $this->db->get('customer')->result();
					}

					foreach($json_selected_id as $pr_se_row)
					{
						$pr_title = '';
						if(isset($pr_selected_title))
						{
							foreach($pr_selected_title as $pr_tit_row)
							{
								if($pr_se_row == $pr_tit_row->id)
								{
									$pr_title = $pr_tit_row->first_name.' '.$pr_tit_row->last_name;
								}
							}
						}
						$html_output['item_data']['selected_customer'] .= '<div class="selected_item_holder">
							<button type="button" class="close" onclick="this.parentNode.parentNode.removeChild(this.parentNode)">
								<span>×</span>
							</button>
							<span>'.$pr_title.'</span>
							<input class="selected_item" value="'.$pr_se_row.'" name="selected_customer_item[]" type="hidden">
						</div>';
					}
				}
			}
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of email_to ////
		////////////////////////////////////////////////////
		$html_output['email_to_list'] = "";

		$email_to = '';
		if(! is_null($item_id))
		{
			$current_item = $this->email_model->get_where(array('id'=>$item_id))->row();
			$email_to = $current_item->email_to;
		}

		//.'<option value="all_affiliates" '.set_select('email_to', 'all_affiliates', ('all_affiliates' == $email_to ? true : false)).'>'.lang("all_affiliates").'</option>'
		//			.'<option value="selected_affiliates" '.set_select('email_to', 'selected_affiliates', ('selected_affiliates' == $email_to ? true : false)).'>'.lang("selected_affiliates").'</option>'

		$html_output['email_to_list'] .=
			'<option value="all_newsletter_subscribers" '.set_select('email_to', 'all_newsletter_subscribers', ('all_newsletter_subscribers' == $email_to ? true : false)).'>'.lang("all_newsletter_subscribers").'</option>'
			.'<option value="customer_group" '.set_select('email_to', 'customer_group', ('customer_group' == $email_to ? true : false)).'>'.lang("customer_group").'</option>'
			.'<option value="all_customers" '.set_select('email_to', 'all_customers', ('all_customers' == $email_to ? true : false)).'>'.lang("all_customers").'</option>'
			.'<option value="selected_customers" '.set_select('email_to', 'selected_customers', ('selected_customers' == $email_to ? true : false)).'>'.lang("selected_customers").'</option>'
			.'<option value="products" '.set_select('email_to', 'products', ('products' == $email_to ? true : false)).'>'.lang("products").'</option>';

        ////////////////////////////////////////////////////////////////
        // Create A list (HTML Select list) of customer_category list //
        ///////////////////////////////////////////////////////////////
		$this->db->where(array('publish' => 'yes'));
		$category_list = $this->db->get('customer_category')->result();
        $html_output['categories_list'] = '';

        if (count($category_list) > 0)
        {
            $customer_group_db = 0;
            if(! is_null($item_id))
            {
                $this->db->select('customer_group');
                $this->db->where(array('id' => $item_id));
                $current_item = $this->db->get('email')->row();
				$customer_group_db = $current_item->customer_group;
            }
            foreach ($category_list as $row)
            {
                $html_output['categories_list'] .= '<option value="'.$row->id.'" '.set_select('customer_group', $row->id, ($row->id == $customer_group_db ? true : false)).'>'.$row->title.'</option>';
            }
        }

        if ($html_output['categories_list'] == '')
        {
            $html_output['categories_list'] = "<option value=''>".lang ('no_category')."</option>";
        }

        $page_name = 'send_email';
        $data['page_name'] = $page_name;
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
        $data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/email/add', $data);
        $this->load->view('template/footer');

    }
}

