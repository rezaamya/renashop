<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

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

		$this->session->set_userdata('page_title', 'مشتریان');
		$main_db_name = "customer";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("customer/edit/".$list_items[0]), 'location');
			}
			elseif ($task == 'active')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('condition', 'active');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}
			}
			elseif ($task == 'inactive')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('condition', 'inactive');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}

			}
			elseif ($task == 'delete')
			{
				$this->load->model('customer/customer_model');

				foreach ($list_items as $value)
				{
						//delete item
						$this->customer_model->delete(array('id' => $value));
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
			$this->db->like('first_name' , $search);
			$this->db->or_like('last_name' , $search);
			$this->db->or_like('username' , $search);
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

		$this->load->model('customer/customer_category');
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
				$parent_row = $row->customer_group;
				$parent_title = $this->customer_category->find_parent(array('id'=>$parent_row));

				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit/".$row->id).'">'. $row->username .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit/".$row->id).'">'. $row->first_name .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit/".$row->id).'">'. $row->last_name .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit/".$row->id).'">'. $row->email .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/add_category/".$row->customer_group).'">'. $parent_title .'</a></td>';
				$temp_html .= '<td class="text-center"><i class="fa fa-'.($row->condition =="inactive" ? "lock" : "unlock").'"></i></td>';
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

		$data['page_name'] ='customers';
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

    public function edit($item_id = null)
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$main_db_name = "customer";
		$html_output = array();
		$html_output['addresses'] = '';

		$this->load->model('customer/customer_model');
		$this->load->model('localization/regions');

		//create list for date of birthday
		$html_output['day'] = '';
		$html_output['month'] = '';
	    $html_output['year'] = '';

		$day = 0;
		$month = 0;
		$year = 0;

		$list_day = array();
		$list_month = array();
		$list_year = array();

		if(! is_null($item_id))
		{
			$current_item = $this->customer_model->get_where(array('id'=>$item_id))->row();
			$day = $current_item->day;
			$month = $current_item->month;
			$year = $current_item->year;
		}

		//create list for day of birthday
		for($i = 1; $i <= 31; $i++)
		{
			$html_output['day'] .= '<option value="'.$i.'" '.set_select('day', $i, ($i == $day ? true : false)).'>'.$i.'</option>';
			array_push($list_day, $i);
		}
		$get_inlist_day = 'in_list['.implode(",",$list_day).']';

		//create list for month of birthday
		$month_array = array('1' => 'فروردین', '2' => 'اردیبهشت', '3' => 'خرداد', '4' => 'تیر', '5' => 'مرداد', '6' => 'شهریور', '7' => 'مهر', '8' => 'ابان', '9' => 'اذر', '10' => 'دی', '11' => 'بهمن', '12' => 'اسفند');

		for($i = 1; $i <= 12; $i++)
		{
			$html_output['month'] .= '<option value="'.$i.'" '.set_select('month', $i, ($i == $month ? true : false)).'>'.$month_array[$i].'</option>';
			array_push($list_month, $i);
		}
		$get_inlist_month = 'in_list['.implode(",",$list_month).']';

		//create list for year of birthday
		$time = time();

		if($year != null)
		{
			$this_year = $year;
		}
		else
		{
			$this_year = $this->date_shamsi->jdate('o', $time,'','Asia/Tehran', 'en');
		}

		for($i = $this_year; $i > 1300; $i--)
		{
			$html_output['year'] .= '<option value="'.$i.'" '.set_select('year', $i, ($i == $year ? true : false)).'>'.$i.'</option>';
			array_push($list_year, $i);
		}
		$get_inlist_year = 'in_list['.implode(",",$list_year).']';

		###############################
		## Create EMPTY ADDRESS FORM ##
		###############################
		//this empty form will be used when a user want to add new address
		$html_output['empty_address_html_form'] = '<div class="row address_holder"><div class="col"><div><div class="container-fluid"><div class="row"><button type="button" onclick="remove_address(this);" class="btn btn-danger btn-sm col-auto">'.lang('delete').'</button><div class="address_title h5 mr-1 col">'.lang('address_title').'</div></div></div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('address_title').'</label><div class="col-sm-10"><input name="address_title[]" type="text" class="form-control" placeholder="'.lang('address_title').'" oninput="refresh_title(this);"></div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('first_name').'</label><div class="col-sm-10"><input name="address_first_name[]" type="text" class="form-control" placeholder="'.lang('first_name').'"></div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('last_name').'</label><div class="col-sm-10"><input name="address_last_name[]" type="text" class="form-control" placeholder="'.lang('last_name').'"></div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('country').'</label><div class="col-sm-10"><select name="address_country[]" class="form-control">'.$this->regions->get_countries_as_html_option().'</select></div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('state').'</label><div class="col-sm-10"><select onchange="refresh_cities();" name="address_state[]" class="form-control">'.$this->regions->get_states_as_html_option().'</select></div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('city').'</label><div class="col-sm-10"><select name="address_city[]" class="form-control">'.$this->regions->get_cities_as_html_option().'</select></div></div><div class="form-group row"><label class="col-sm-2">'.lang('address').'</label><div class="col-sm-10"><textarea name="address[]" class="form-control" rows="5"></textarea></div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('postcode').'</label><div class="col-sm-10"><input name="address_postcode[]" type="text" class="form-control" placeholder="'.lang('postcode').'"></div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('mobile').'</label><div class="col-sm-10"><input name="address_mobile[]" type="text" class="form-control" placeholder="'.lang('mobile').'"></div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('tel').'</label><div class="col-sm-10"><input name="address_tel[]" type="text" class="form-control" placeholder="'.lang('tel').'"></div></div></div></div>';

		$this->load->model('customer/customer_model');
		$this->load->model('customer/customer_category');
		$task = $this->input->post('task');

		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			#################
			## SUBMIT MODE ##
			#################
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

			######################
			## Create ADDRESSES ##
			######################
			$all_addreses = array();
			$all_addresses_title = $this->input->post('address_title[]');

			if($all_addresses_title != null){
			foreach ($all_addresses_title as $index => $value)
			{
				$temp_array = array (
					'address_title' => $this->input->post("address_title[$index]"),
					'address_first_name' => $this->input->post("address_first_name[$index]"),
					'address_last_name' => $this->input->post("address_last_name[$index]"),
					'address_country' => $this->input->post("address_country[$index]"),
					'address_state' => $this->input->post("address_state[$index]"),
					'address_city' => $this->input->post("address_city[$index]"),
					'address' => ($this->input->post("address[$index]")),
					'address_postcode' => $this->input->post("address_postcode[$index]"),
					'address_mobile' => $this->input->post("address_mobile[$index]"),
					'address_tel' => $this->input->post("address_tel[$index]")
				);
				array_push($all_addreses, $temp_array);

				######################
				## Create ADDRESSES ##
				######################
				$html_output['addresses'] .= '<div class="row address_holder"><div class="col"><div><div class="container-fluid">
<div class="row"><button type="button" onclick="remove_address(this);" class="btn btn-danger btn-sm col-auto">'.lang('delete').'</button>
<div class="address_title h5 mr-1 col">'.$temp_array['address_title'].'</div></div></div></div><div class="form-group row">
<label class="col-sm-2 col-form-label">'.lang('address_title').'</label><div class="col-sm-10">
<input name="address_title[]" value="'.$temp_array['address_title'].'"type="text" class="form-control" placeholder="'.lang('address_title').'" oninput="refresh_title(this);">
</div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('first_name').'</label><div class="col-sm-10">
<input name="address_first_name[]" value="'.$temp_array['address_first_name'].'" type="text" class="form-control" placeholder="'.lang('first_name').'">
</div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('last_name').'</label><div class="col-sm-10">
<input name="address_last_name[]" value="'.$temp_array['address_last_name'].'" type="text" class="form-control" placeholder="'.lang('last_name').'">
</div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('country').'</label><div class="col-sm-10">
<select name="address_country[]" class="form-control">'.$this->regions->get_countries_as_html_option($temp_array['address_country']).'</select>
</div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('state').'</label><div class="col-sm-10">
<select onchange="refresh_cities();" name="address_state[]" class="form-control">'.$this->regions->get_states_as_html_option($temp_array['address_state']).'</select>
</div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('city').'</label><div class="col-sm-10">
<select name="address_city[]" class="form-control">'.$this->regions->get_cities_as_html_option($temp_array['address_city']).'</select>
</div></div><div class="form-group row"><label class="col-sm-2">'.lang('address').'</label><div class="col-sm-10">
<textarea name="address[]" class="form-control" rows="5">'.$temp_array['address'].'</textarea></div></div>
<div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('postcode').'</label><div class="col-sm-10">
<input name="address_postcode[]" value="'.$temp_array['address_postcode'].'" type="text" class="form-control" placeholder="'.lang('postcode').'">
</div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('mobile').'</label><div class="col-sm-10">
<input name="address_mobile[]" value="'.$temp_array['address_mobile'].'" type="text" class="form-control" placeholder="'.lang('mobile').'">
</div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('tel').'</label><div class="col-sm-10">
<input name="address_tel[]" value="'.$temp_array['address_tel'].'" type="text" class="form-control" placeholder="'.lang('tel').'"></div></div></div></div>';
			}}

			//Set Form Rules:
			$this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|min_length[2]|max_length[50]');

			$this->form_validation->set_rules('last_name', lang('last_name'), 'trim|required|min_length[3]|max_length[50]');

			$this->form_validation->set_rules('username', lang('username'), 'trim|required|callback__username_check|min_length[2]|max_length[50]'.($item_id==null || $posted_username!=$item_id_username ? "|is_unique[$main_db_name.username]" :""));
			$this->form_validation->set_message('_username_check', '«{field}» میتواند تنها شامل حروف و اعداد انگلیسی و علامت های @ و . و _ باشد. همچنین درج فاصله (Space) مجاز نمیباشد.');

			$this->form_validation->set_rules('day', lang('day'), 'required|'.$get_inlist_day);

			$this->form_validation->set_rules('month', lang('month'), 'required|'.$get_inlist_month);

			$this->form_validation->set_rules('year', lang('year'), 'required|'.$get_inlist_year);

			$this->form_validation->set_rules('sex', lang('sex'), 'required|in_list[man,others,female]');

			$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[50]'.($item_id==null || $posted_email!=$item_id_email ? "|is_unique[$main_db_name.email]" :""));

			$this->form_validation->set_rules('mobile', lang('mobile'),'trim|required|exact_length[11]|is_natural');

			$this->form_validation->set_rules('customer_group', lang('customer_group'),'required|'. $this->customer_category->get_inlist_string());

			$password_required = '';
			if(!$item_id)
			{
				$password_required = '|required';
			}
			$this->form_validation->set_rules('password', lang('password'), 'trim|min_length[5]|max_length[50]'.$password_required);

			$this->form_validation->set_rules('confirm_password', lang('confirm_password'), 'trim|matches[password]|min_length[5]|max_length[50]'.$password_required);

			$this->form_validation->set_rules('condition', lang('condition'), 'required|in_list[active,inactive]');

			//Aya Addressi ra ersal karde ast ya na?
			if (count($all_addresses_title) > 0)
			{
				//Hadeaghal yek Address ersal shode ast
				$this->form_validation->set_rules('address_title[]', lang('title'),'trim|required|min_length[3]|max_length[50]');

				$this->form_validation->set_rules('address_first_name[]', lang('first_name'),'trim|required|min_length[2]|max_length[50]');

				$this->form_validation->set_rules('address_last_name[]', lang('last_name'),'trim|required|min_length[3]|max_length[50]');

				$this->form_validation->set_rules('address_country[]', lang('country'),'required|'. $this->regions->get_countries_inlist_string());
				$this->form_validation->set_rules('address_state[]', lang('state'),'required|'. $this->regions->get_state_inlist_string());

				$this->form_validation->set_rules('address_city[]', lang('city'),'required|max_length[50]|'. $this->regions->get_city_inlist_string());

				$this->form_validation->set_rules('address[]', lang('address'),'trim|required|min_length[5]|max_length[1000]');

				$this->form_validation->set_rules('address_postcode[]', lang('postcode'),'trim|exact_length[10]|is_natural');

				$this->form_validation->set_rules('address_mobile[]', lang('mobile'),'trim|required|exact_length[11]|is_natural');

				$this->form_validation->set_rules('address_tel[]', lang('tel'),'trim|min_length[3]|max_length[12]|is_natural');
			}


			if ($this->form_validation->run() == TRUE)
			{
				$html_output['item'] = array("address_title" => '');
				$address_item_id = json_encode($all_addreses);
				$dadeh = array
				(
					'first_name' => $this->input->post('first_name'),
					'last_name' => $this->input->post('last_name'),
					'username' => $this->input->post('username'),
					'day' => $this->input->post('day'),
					'month' => $this->input->post('month'),
					'year' => $this->input->post('year'),
					'sex' => $this->input->post('sex'),
					'email' => $this->input->post('email'),
					'mobile' => $this->input->post('mobile'),
					'customer_group' => $this->input->post('customer_group'),
					'condition' => $this->input->post('condition'),
					'address' => $address_item_id
				);

				$password_temp = $this->input->post('password');
				if(isset($password_temp) && $password_temp != null && $password_temp != '')
				{
					$dadeh['password'] = md5($this->input->post('password'));
				}

				if ($item_id)
				{
					$this->customer_model->update($item_id, $dadeh);
				}
				else
				{
					$item_id = $this->customer_model->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'customerindexsuccess_msg');
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("customer/edit/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("customer/index"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("customer/edit"));
				}
			}
		}
		else if(! is_null($item_id))
		{
			###############
			## EDIT MODE ##
			###############
			//We are in edit mode.
			//then we should create a list of addresses in HTML Form,
			//that user submitted before, into the database
			$this->load->model('customer/customer_model');
			$country = $this->customer_model->get_where(array('id' => $item_id));
			foreach ($country->result() as $row)
			{
				$address = $row->address;
				$temp = json_decode($address);
				if(isset($temp) && $temp != '' && $temp != null){
				foreach ($temp as $json_row)
				{
					######################
					## Create ADDRESSES ##
					######################
					$html_output['addresses'] .= '<div class="row address_holder"><div class="col"><div><div class="container-fluid">
<div class="row"><button type="button" onclick="remove_address(this);" class="btn btn-danger btn-sm col-auto">'.lang('delete').'</button>
<div class="address_title h5 mr-1 col">'.$json_row->address_title.'</div></div></div></div><div class="form-group row">
<label class="col-sm-2 col-form-label">'.lang('address_title').'</label><div class="col-sm-10">
<input name="address_title[]" value="'.$json_row->address_title.'"type="text" class="form-control" placeholder="'.lang('address_title').'" oninput="refresh_title(this);"></div></div>
<div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('first_name').'</label><div class="col-sm-10">
<input name="address_first_name[]" value="'.$json_row->address_first_name.'" type="text" class="form-control" placeholder="'.lang('first_name').'"></div></div>
<div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('last_name').'</label><div class="col-sm-10">
<input name="address_last_name[]" value="'.$json_row->address_last_name.'" type="text" class="form-control" placeholder="'.lang('last_name').'"></div></div>
<div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('country').'</label><div class="col-sm-10">
<select name="address_country[]" class="form-control">'.$this->regions->get_countries_as_html_option($json_row->address_country).'</select>
</div></div><div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('state').'</label><div class="col-sm-10">
<select onchange="refresh_cities();" name="address_state[]" class="form-control">'.$this->regions->get_states_as_html_option($json_row->address_state).'</select></div></div>
<div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('city').'</label><div class="col-sm-10">
<select name="address_city[]" class="form-control">'.$this->regions->get_cities_as_html_option($json_row->address_city).'</select></div></div>
<div class="form-group row"><label class="col-sm-2">'.lang('address').'</label><div class="col-sm-10">
<textarea name="address[]" class="form-control" rows="5">'.$json_row->address.'</textarea></div></div><div class="form-group row">
<label class="col-sm-2 col-form-label">'.lang('postcode').'</label><div class="col-sm-10">
<input name="address_postcode[]" value="'.$json_row->address_postcode.'" type="text" class="form-control" placeholder="'.lang('postcode').'"></div></div>
<div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('mobile').'</label><div class="col-sm-10">
<input name="address_mobile[]" value="'.$json_row->address_mobile.'" type="text" class="form-control" placeholder="'.lang('mobile').'"></div></div>
<div class="form-group row"><label class="col-sm-2 col-form-label">'.lang('tel').'</label><div class="col-sm-10">
<input name="address_tel[]" value="'.$json_row->address_tel.'" type="text" class="form-control" placeholder="'.lang('tel').'"></div></div></div></div>';
				}}
			}
		}

		//It's Edit state, then we need to simulate edit for user
		if ($item_id)
		{
			$this->session->set_userdata('page_title', 'ویرایش مشخصات مشتری');
			$page_name = 'edit_customer';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
		}
		else
		{
			$this->session->set_userdata('page_title', 'افزودن مشتری');
			$page_name = 'add_customer';
			//Default Item Data
			$html_output['item_data'] = array(
				"id" => '',
				"first_name" => '',
				"last_name" => '',
				"username" => '',
				"day" => '',
				"month" => '',
				"year" => '',
				"sex" => '',
				"email" => '',
				"mobile" => '',
				"customer_group" => '',
				"condition" => ''
			);
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of categories //
		////////////////////////////////////////////////////
		$this->db->where(array('publish' => 'yes'));
		$category_list = $this->db->get('customer_category')->result();
		$html_output['categories_list'] = '';

		if (count($category_list) > 0)
		{
			$parent = 0;
			if(! is_null($item_id))
			{
				$this->load->model('customer/customer_model');
				$current_item = $this->customer_model->get_where(array('id'=>$item_id))->row();
				$parent = $current_item->customer_group;
			}
			foreach ($category_list as $row)
			{
				$html_output['categories_list'] .= '<option value="'.$row->id.'" '.set_select('parent', $row->id, ($row->id == $parent ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['categories_list'] == '')
		{
			$html_output['categories_list'] = "<option value=''>".lang ('no_category')."</option>";
		}


		$data['page_name'] = $page_name;

		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

		$this->load->view('template/header', $data);
		$this->load->view('menu', $data);
		$this->load->view($this->uri->segment(1) . '/edit', $data);
		$this->load->view('template/footer');
	}

    public function categories()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'دسته‌بندی‌ مشتری');
		$main_db_table = "customer_category";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("customer/add_category/".$list_items[0]), 'location');
			}
			elseif ($task == 'publish')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('publish', 'yes');
					$this->db->where('id', $value);
					$this->db->update($main_db_table);
				}
			}
			elseif ($task == 'unpublish')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('publish', 'no');
					$this->db->where('id', $value);
					$this->db->update($main_db_table);
				}

			}
			elseif ($task == 'delete')
			{
				$this->load->model('customer/customer_category');

				$this->db->select('customer_category.id AS customer_category_id,customer.id AS customer_id');
				$this->db->from('customer_category');
				$this->db->join('customer', 'customer_category.id=customer.customer_group', 'left');
				$query_join = $this->db->get()->result();

				$this->db->select('title,id');
				$customer_category_p_title_list = $this->db->get('customer_category')->result();

				foreach ($list_items as $value)
				{
					$delete_current_item = true;

					//get all categories that has this item as their parent
					$children_list = $this->customer_category->get_where(array('parent'=>$value));
					$current_item = $this->customer_category->get_where(array('id'=>$value))->row();

					//we need to check database items with list_items
					foreach ($children_list->result() as $row)
					{
						if (in_array($row->id, $list_items))
						{
							//this sub_item should delete also, then don't show error
						}
						else
						{
							//we can't delete this item because it is a parent of another item right now
							$delete_current_item = false;
							$error_msg = "<div>".str_replace(array("{{item_title}}", "{{sub_item_title}}"), array($current_item->title, $row->title), lang('can not delete {{item_title}} because it is parent for {{sub_item_title}}'))."</div>";
							$this->mylib->set_error($error_msg);
						}
					}

					$customer_er = '';
					foreach($query_join as $join_row)
					{
						if($join_row->customer_category_id == $value)
						{
							if($join_row->customer_id != '' && $join_row->customer_id != null)
							{
								$customer_er = 'تعدادی مشتری';
							}
						}
					}

					if($customer_er != '')
					{
						$cat_title = '';
						foreach($customer_category_p_title_list as $cat_tit)
						{
							if($cat_tit->id == $value)
							{
								$cat_title = $cat_tit->title;
								break;
							}
						}
						$delete_current_item = false;
						$error_msg = "<div>امکان حذف دسته‌بندی <<$cat_title>> وجود ندارد, مشتریانی وجود دارند که دسته‌بندی آنها<< $cat_title>> است.</div>";
						$this->mylib->set_error($error_msg);
					}

					if ($delete_current_item)
					{
						//delete item
						$this->customer_category->delete(array('id' => $value));
						//set success message
						$this->mylib->set_success(lang('deleted_successfully'));
					}
				}
			}
		}

		////////////
		// Search //
		////////////
		//Customize by user search keyword
		$search = $this->input->post('search');
		//Search query
		if($search != "")
		{
			$this->db->like('title' , $search);
			$this->db->or_like('description' , $search);
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
		$this->db->order_by('id', 'DESC');
		$this->db->limit($per_page, $page);
		$query = $this->db->get($main_db_table);

		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("customer/add_category/".$row->id).'">'. $row->title .'</a></td>';
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
		$html_output['pagination'] = $this->mylib->get_pagination ($this->uri->segment(1).'/'.$this->uri->segment(2), $main_db_table, $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'per_page'));

		$data = array
		(
			'page_name' => 'category_customer',
			'main_db_table' => $main_db_table,
			'html_output' => $html_output
		);

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
		$data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
        $this->load->view($this->uri->segment(1) . '/category/list', $data);
        $this->load->view('template/footer');
    }

    public function add_category($item_id = null)
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$main_db_name = "customer_category";
		$html_output = array();
		$this->load->model('customer/customer_category');
		$task = $this->input->post('task');

		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			//Are we editing current_item
			$posted_title= $this->input->post('title');
			$query = $this->db->get($main_db_name);
			foreach ($query->result() as $row)
			{
				if($item_id==$row->id)
				{
					$item_id_title = $row->title;

					break;
				}
			}

			//Set Form Rules:
			$this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[3]|max_length[50]'.($item_id==null || $posted_title!=$item_id_title ? "|is_unique[$main_db_name.title]" :""));

			$this->form_validation->set_rules('parent', lang('parent'), $this->customer_category->get_inlist_string($item_id));

			$this->form_validation->set_rules('description', lang('description'), 'trim');

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'title' => $this->input->post('title'),
					'parent' => $this->input->post('parent'),
					'description' => $this->input->post('description'),
					'publish' => $this->input->post('publish')
				);

				if ($item_id)
				{
					//we should update this $edit_id into the database
					$this->customer_category->update($item_id, $dadeh);
				}
				else
				{
					$item_id = $this->customer_category->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'customercategoriessuccess_msg');
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("customer/add_category/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("customer/categories"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("customer/add_category"));
				}
			}
		}

		if ($item_id)
		{
			$this->session->set_userdata('page_title', 'ویرایش دسته‌بندی‌ مشتری');
			$page_name = 'edit_category';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
		}
		else
		{
			$this->session->set_userdata('page_title', 'افزودن دسته‌بندی جدید');
			$page_name = 'add_category';
			//Default Item Data
			$html_output['item_data'] = array(
				"id" => '',
				"title" => '',
				"parent" => '',
				"description" => '',
				"publish" => 'yes'
			);
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of categories //
		////////////////////////////////////////////////////
		$this->db->where(array('publish' => 'yes'));
		$category_list = $this->db->get('customer_category')->result();
		$html_output['categories_list'] = "<option value=''>".lang ('without_parent')."</option>";

		if (count($category_list) > 0)
		{
			$parent = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->customer_category->get_where(array('id'=>$item_id))->row();
				$parent = $current_item->parent;
			}

			foreach ($category_list as $row)
			{
				if ($item_id == $row->id)
				{
					//do nothing.
					//we are editing this item currently, then he should not be able to select himself
				}
				else
				{
					$html_output['categories_list'] .= '<option value="'.$row->id.'" '.set_select('parent', $row->id, ($row->id == $parent ? true : false)).'>'.$row->title.'</option>';

				}
			}
		}

		if ($html_output['categories_list'] == '')
		{
			$html_output['categories_list'] = "<option value=''>".lang ('no_category')."</option>";
		}


		$data['page_name'] = $page_name;

		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/category/add', $data);
        $this->load->view('template/footer');
    }

    public function comments_list()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'لیست نظرات');
		$main_db_name = "comment";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit_comment')
			{
				redirect(base_url("customer/edit_comment/".$list_items[0]), 'location');
			}
			elseif ($task == 'marked')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('marked', 'yes');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}
			}
			elseif ($task == 'unmarked')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('marked', 'no');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}

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
				foreach ($list_items as $value)
				{
					//delete item
					$this->db->delete($main_db_name, array('id' => $value));
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
			$this->db->like('first_name' , $search);
			$this->db->or_like('email' , $search);
			$this->db->or_like('full_comment' , $search);
			$this->db->or_like('answer' , $search);
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
		$this->db->order_by('id', 'DESC');
		$this->db->limit($per_page, $page);
		$query = $this->db->get($main_db_name);

		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$count_full_comment = count(explode(' ', $row->full_comment));
				$temp_full_comment = implode(' ', array_slice(explode(' ', $row->full_comment), 0, 10));
				if($count_full_comment > 10)
				{
					$temp_full_comment = explode(' ', $temp_full_comment);
					array_push($temp_full_comment, '...');
					$temp_full_comment = implode(' ', $temp_full_comment);
				}

				$count_answer = count(explode(' ', $row->answer));
				$temp_answer = implode(' ', array_slice(explode(' ', $row->answer), 0, 10));
				if($count_answer > 10)
				{
					$temp_answer = explode(' ', $temp_answer);
					array_push($temp_answer, '...');
					$temp_answer = implode(' ', $temp_answer);
				}

				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_comment/".$row->id).'">'. $row->first_name .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_comment/".$row->id).'">'. $temp_full_comment .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_comment/".$row->id).'">'. $temp_answer .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_comment/".$row->id).'">'. $row->email .'</a></td>';
				$temp_html .= '<td class="text-center"><i class="'.($row->marked =="yes" ? "fas" : "far").' fa-star"></i></td>';
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
		$html_output['pagination'] = $this->mylib->get_pagination ($this->uri->segment(1).'/'.$this->uri->segment(2), $main_db_name, $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'per_page'));

		$data['page_name'] = 'comments_list';
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/comment/list', $data);
        $this->load->view('template/footer');
    }

    public function edit_comment($item_id = null)
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'مشاهده نظر');
		//Default Item Data
		$html_output['item_data'] = array(
			"id" => '',
			"first_name" => '',
			"email" => '',
			"full_comment" => '',
			"answer" => '',
			"marked" => '',
			"publish" => ''
		);

		$task = $this->input->post('task');
		if($task == 'save' || $task == 'save_and_close')
		{
			$this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|min_length[2]|max_length[50]');

			$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[100]');

			$this->form_validation->set_rules('full_comment', lang('full_comment'), 'trim');

			$this->form_validation->set_rules('answer', lang('answer'), 'trim');

			$this->form_validation->set_rules('marked', lang('marked'), 'required|in_list[yes,no]');

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'first_name' => $this->input->post('first_name'),
					'email' => $this->input->post('email'),
					'full_comment' => $this->input->post('full_comment'),
					'answer' => $this->input->post('answer'),
					'marked' => $this->input->post('marked'),
					'publish' => $this->input->post('publish')
				);

				$this->db->where('id', $item_id);
				$this->db->update('comment', $dadeh);

				if ($task == "save")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
					//Stay in current page
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'customercomments_listsuccess_msg');
					//Go to Parent Page
					redirect(base_url("customer/comments_list"));
				}
			}
		}

		$this->db->where('id', $item_id);
		$query = $this->db->get('comment');
		$html_output['item_data'] = $query->row_array();

		$data['page_name'] = 'view_comment';

		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/comment/edit', $data);
        $this->load->view('template/footer');
    }

    public function messages_list()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'لیست پیام‌ها');
		$main_db_name = "messages";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit_message')
			{
				redirect(base_url("customer/edit_message/".$list_items[0]), 'location');
			}
			elseif ($task == 'marked')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('marked', 'yes');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}
			}
			elseif ($task == 'unmarked')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('marked', 'no');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}

			}
			elseif ($task == 'delete')
			{
				foreach ($list_items as $value)
				{
					//delete item
					$this->db->delete($main_db_name, array('id' => $value));
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
			$this->db->like('name' , $search);
			$this->db->or_like('email' , $search);
			$this->db->or_like('message' , $search);
			$this->db->or_like('answer' , $search);
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
		$this->db->order_by('id', 'DESC');
		$this->db->limit($per_page, $page);
		$query = $this->db->get($main_db_name);

		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$count_full_comment = count(explode(' ', $row->message));
				$temp_full_comment = implode(' ', array_slice(explode(' ', $row->message), 0, 10));
				if($count_full_comment > 10)
				{
					$temp_full_comment = explode(' ', $temp_full_comment);
					array_push($temp_full_comment, '...');
					$temp_full_comment = implode(' ', $temp_full_comment);
				}

				$count_answer = count(explode(' ', $row->answer));
				$temp_answer = implode(' ', array_slice(explode(' ', $row->answer), 0, 10));
				if($count_answer > 10)
				{
					$temp_answer = explode(' ', $temp_answer);
					array_push($temp_answer, '...');
					$temp_answer = implode(' ', $temp_answer);
				}
				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_message/".$row->id).'">'. $row->name .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_message/".$row->id).'">'. $temp_full_comment .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_message/".$row->id).'">'. $temp_answer .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_message/".$row->id).'">'. $row->email .'</a></td>';
				$temp_html .= '<td class="text-center"><i class="'.($row->marked =="yes" ? "fas" : "far").' fa-star"></i></td>';
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

		$data['page_name'] = 'messages_list';
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/message/list', $data);
        $this->load->view('template/footer');
    }

    public function edit_message($item_id = null)
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'مشاهده پیام');
		//Default Item Data
		$html_output['item_data'] = array(
			"id" => '',
			"name" => '',
			"email" => '',
			"message" => '',
			"answer" => '',
			"marked" => ''
		);

		$task = $this->input->post('task');
		if($task == 'save' || $task == 'save_and_close')
		{
			$this->form_validation->set_rules('name', lang('name'), 'trim|required|min_length[2]|max_length[50]');

			$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[100]');

			$this->form_validation->set_rules('message', lang('message'), 'trim');

			$this->form_validation->set_rules('answer', lang('answer'), 'trim');

			$this->form_validation->set_rules('marked', lang('marked'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'name' => $this->input->post('name'),
					'email' => $this->input->post('email'),
					'message' => $this->input->post('message'),
					'answer' => $this->input->post('answer'),
					'marked' => $this->input->post('marked')
				);

				$this->db->where('id', $item_id);
				$this->db->update('messages', $dadeh);

				//send email
				$answer = $this->input->post('answer');
				$name = $this->input->post('name');
				$message = $this->input->post('message');
				if($answer != '' && $answer != null)
				{
					$answer = "<div dir='rtl' style='text-align: right;'>با سلام و احترام<br/>$name عزیز،<br/>پیام شما به شرح زیر دریافت شد.<br/>پیام: $message<br/>پاسخ پیام شما:<br/>$answer<br/>با سپاس<br/>فروشگاه اینترنتی کشاورز</div>";
					$email = $this->input->post('email');
					$this->load->library('email');
					$mail_config['mailtype'] = "html";
					$this->email->initialize($mail_config);
					$this->email->from('noreply@amya.ir', 'noreply@amya.ir');
					$this->email->to($email);
					$this->email->subject('پاسخ به پیام | فروشگاه اینترنتی کتاب پرگار');
					$this->email->message($answer);
					$this->email->send();
				}

				if ($task == "save")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
					//Stay in current page
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'customermessages_listsuccess_msg');
					//Go to Parent Page
					redirect(base_url("customer/messages_list"));
				}
			}
		}

		$this->db->where('id', $item_id);
		$query = $this->db->get('messages');
		$html_output['item_data'] = $query->row_array();

		$data['page_name'] = 'view_message';

		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/message/edit', $data);
        $this->load->view('template/footer');
    }

	function _username_check($str)
	{
		$Valid_character_list = array('.', '_', '@');

		if(!ctype_alnum(str_replace($Valid_character_list, '', $str))) {
			return FALSE;
		}

		return true;
	}

    public function question_and_answer()
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$this->session->set_userdata('page_title', 'پرسش و پاسخ');
		$main_db_name = "question_and_answer";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit_question_and_answer')
			{
				redirect(base_url("customer/edit_question_and_answer/".$list_items[0]), 'location');
			}
			elseif ($task == 'marked')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('marked', 'yes');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}
			}
			elseif ($task == 'unmarked')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('marked', 'no');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}

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
				foreach ($list_items as $value)
				{
					//delete item
					$this->db->delete($main_db_name, array('id' => $value));
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
			$this->db->like('first_name' , $search);
			$this->db->or_like('email' , $search);
			$this->db->or_like('question' , $search);
			$this->db->or_like('answer' , $search);
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
		$this->db->order_by('id', 'DESC');
		$this->db->limit($per_page, $page);
		$query = $this->db->get($main_db_name);

		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$count_full_comment = count(explode(' ', $row->question));
				$temp_full_comment = implode(' ', array_slice(explode(' ', $row->question), 0, 10));
				if($count_full_comment > 10)
				{
					$temp_full_comment = explode(' ', $temp_full_comment);
					array_push($temp_full_comment, '...');
					$temp_full_comment = implode(' ', $temp_full_comment);
				}

				$count_answer = count(explode(' ', $row->answer));
				$temp_answer = implode(' ', array_slice(explode(' ', $row->answer), 0, 10));
				if($count_answer > 10)
				{
					$temp_answer = explode(' ', $temp_answer);
					array_push($temp_answer, '...');
					$temp_answer = implode(' ', $temp_answer);
				}
				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_question_and_answer/".$row->id).'">'. $row->first_name .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_question_and_answer/".$row->id).'">'. $temp_full_comment .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_question_and_answer/".$row->id).'">'. $temp_answer .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_question_and_answer/".$row->id).'">'. $row->email .'</a></td>';
				$temp_html .= '<td class="text-center"><i class="'.($row->marked =="yes" ? "fas" : "far").' fa-star"></i></td>';
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
		$html_output['pagination'] = $this->mylib->get_pagination ($this->uri->segment(1).'/'.$this->uri->segment(2), $main_db_name, $this->session->userdata($this->uri->segment(1).$this->uri->segment(2).'per_page'));

		$data['page_name'] = 'question_and_answer';
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/question_and_answer/list', $data);
        $this->load->view('template/footer');

    }

    public function edit_question_and_answer($item_id = null)
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$this->session->set_userdata('page_title', 'ویرایش پرسش و پاسخ');
		//Default Item Data
		$html_output['item_data'] = array(
			"id" => '',
			"first_name" => '',
			"email" => '',
			"question" => '',
			"answer" => '',
			"marked" => '',
			"publish" => ''
		);

		$task = $this->input->post('task');
		if($task == 'save' || $task == 'save_and_close')
		{
			$this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|min_length[2]|max_length[50]');

			$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[100]');

			$this->form_validation->set_rules('question', lang('question'), 'trim');

			$this->form_validation->set_rules('answer', lang('answer'), 'trim');

			$this->form_validation->set_rules('marked', lang('marked'), 'required|in_list[yes,no]');

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'first_name' => $this->input->post('first_name'),
					'email' => $this->input->post('email'),
					'question' => $this->input->post('question'),
					'answer' => $this->input->post('answer'),
					'marked' => $this->input->post('marked'),
					'publish' => $this->input->post('publish')
				);

				$this->db->where('id', $item_id);
				$this->db->update('question_and_answer', $dadeh);

				//send email
				$answer = $this->input->post('answer');
				$name = $this->input->post('first_name');
				$question = $this->input->post('question');
				if($answer != '' && $answer != null)
				{
					$answer = "<div dir='rtl' style='text-align: right;'>با سلام و احترام<br/>$name عزیز،<br/>پرسش شما به شرح زیر دریافت شد و توسط مسئول مربوطه مورد بررسی قرار گرفته است.<br/>پرسش: $question<br/>پاسخ پرسش شما:<br/>$answer<br/>با سپاس<br/>فروشگاه اینترنتی کشاورز</div>";
					$email = $this->input->post('email');
					$this->load->library('email');
					$mail_config['mailtype'] = "html";
					$this->email->initialize($mail_config);
					$this->email->from('noreply@amya.ir', 'noreply@amya.ir');
					$this->email->to($email);
					$this->email->subject('پاسخ به پرسش | فروشگاه اینترنتی کتاب پرگار');
					$this->email->message($answer);
					$this->email->send();
				}

				if ($task == "save")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
					//Stay in current page
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'customercomments_listsuccess_msg');
					//Go to Parent Page
					redirect(base_url("customer/question_and_answer"));
				}
			}
		}

		$this->db->where('id', $item_id);
		$query = $this->db->get('question_and_answer');
		$html_output['item_data'] = $query->row_array();

		$data['page_name'] = 'edit_question_and_answer';

		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/question_and_answer/edit', $data);
        $this->load->view('template/footer');

    }

    public function complaint_list()
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$this->session->set_userdata('page_title', 'لیست شکایات');
		$main_db_name = "complaint";
		$html_output = array();
		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit_complaint')
			{
				redirect(base_url("customer/edit_complaint/".$list_items[0]), 'location');
			}
			elseif ($task == 'marked')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('marked', 'yes');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}
			}
			elseif ($task == 'unmarked')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('marked', 'no');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}

			}
			elseif ($task == 'delete')
			{
				foreach ($list_items as $value)
				{
					//delete item
					$this->db->delete($main_db_name, array('id' => $value));
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
			$this->db->like('name' , $search);
			$this->db->or_like('email' , $search);
			$this->db->or_like('complaint' , $search);
			$this->db->or_like('answer' , $search);
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
		$this->db->order_by('id', 'DESC');
		$this->db->limit($per_page, $page);
		$query = $this->db->get($main_db_name);

		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$count_full_comment = count(explode(' ', $row->complaint));
				$temp_full_comment = implode(' ', array_slice(explode(' ', $row->complaint), 0, 10));
				if($count_full_comment > 10)
				{
					$temp_full_comment = explode(' ', $temp_full_comment);
					array_push($temp_full_comment, '...');
					$temp_full_comment = implode(' ', $temp_full_comment);
				}

				$count_answer = count(explode(' ', $row->answer));
				$temp_answer = implode(' ', array_slice(explode(' ', $row->answer), 0, 10));
				if($count_answer > 10)
				{
					$temp_answer = explode(' ', $temp_answer);
					array_push($temp_answer, '...');
					$temp_answer = implode(' ', $temp_answer);
				}
				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_complaint/".$row->id).'">'. $row->name .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_complaint/".$row->id).'">'. $temp_full_comment .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_complaint/".$row->id).'">'. $temp_answer .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_complaint/".$row->id).'">'. $row->email .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("customer/edit_complaint/".$row->id).'">'. $row->phone_number .'</a></td>';
				$temp_html .= '<td class="text-center"><i class="'.($row->marked =="yes" ? "fas" : "far").' fa-star"></i></td>';
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
		$data['page_name'] = 'complaint_list';
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/complaint/list', $data);
        $this->load->view('template/footer');

    }

    public function edit_complaint($item_id = null)
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$this->session->set_userdata('page_title', 'ویرایش شکایت');
		//Default Item Data
		$html_output['item_data'] = array(
			"id" => '',
			"name" => '',
			"email" => '',
			"phone_number" => '',
			"complaint" => '',
			"answer" => '',
			"marked" => ''
		);

		$task = $this->input->post('task');
		if($task == 'save' || $task == 'save_and_close')
		{
			$this->form_validation->set_rules('name', lang('name'), 'trim|required|min_length[2]|max_length[50]');

			$this->form_validation->set_rules('email', lang('email'), 'trim|valid_email|required|min_length[6]|max_length[100]');

			$this->form_validation->set_rules('complaint', lang('complaint'), 'trim');

			$this->form_validation->set_rules('phone_number', lang('phone_number'),'trim|required|exact_length[11]|is_natural');

			$this->form_validation->set_rules('answer', lang('answer'), 'trim');

			$this->form_validation->set_rules('marked', lang('marked'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'name' => $this->input->post('name'),
					'email' => $this->input->post('email'),
					'complaint' => $this->input->post('complaint'),
					'phone_number' => $this->input->post('phone_number'),
					'answer' => $this->input->post('answer'),
					'marked' => $this->input->post('marked')
				);

				$this->db->where('id', $item_id);
				$this->db->update('complaint', $dadeh);

				//send email
				$answer = $this->input->post('answer');
				$name = $this->input->post('name');
				$complaint = $this->input->post('complaint');
				if($answer != '' && $answer != null)
				{
					$answer = "<div dir='rtl' style='text-align: right;'>با سلام و احترام<br/>$name عزیز،<br/>شکایت شما به شرح زیر دریافت شد و توسط مسئول مربوطه مورد بررسی قرار گرفته است.<br/>شکایت: $complaint<br/>پاسخ شکایت شما:<br/>$answer<br/>با سپاس<br/>فروشگاه اینترنتی کشاورز</div>";
					$email = $this->input->post('email');
					$this->load->library('email');
					$mail_config['mailtype'] = "html";
					$this->email->initialize($mail_config);
					$this->email->from('noreply@amya.ir', 'noreply@amya.ir');
					$this->email->to($email);
					$this->email->subject('پاسخ به شکایت | فروشگاه اینترنتی کتاب پرگار');
					$this->email->message($answer);
					$this->email->send();
				}

				if ($task == "save")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
					//Stay in current page
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'customercomplaint_listsuccess_msg');
					//Go to Parent Page
					redirect(base_url("customer/complaint_list"));
				}
			}
		}

		$this->db->where('id', $item_id);
		$query = $this->db->get('complaint');
		$html_output['item_data'] = $query->row_array();

		$data['page_name'] = 'complaint_edit';
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/complaint/edit', $data);
        $this->load->view('template/footer');

    }
}
