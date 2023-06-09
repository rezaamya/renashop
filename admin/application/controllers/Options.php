<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Options extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

    /**
     * Dar in safhe Setting ra modiriat (ADD, DELETE, EDIT) mikonim
     */
    public function index()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'لیست خصوصیت‌ها');
		$main_db_name = "options";
		$html_output = array();

		$this->load->model('products/products_category_model');

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("options/add/".$list_items[0]), 'location');
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
				$this->load->model('options_model');

				foreach ($list_items as $value)
				{
					//delete item
					$this->options_model->delete(array('id' => $value));
					//set success message
					$this->mylib->set_success(lang('deleted_successfully'));
				}
			}
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of categories //
		////////////////////////////////////////////////////
		$category_list = $this->products_category_model->get_all();
		$html_output['categories_list'] = "<option value=''>".lang ('all_categories')."</option>";
		$category = $this->input->post('category');

		if (count($category_list) > 0)
		{
			foreach ($category_list as $row)
			{
				$html_output['categories_list'] .= '<option value="'.$row->id.'" '.set_select('category', $row->id, ($row->id == $category ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['categories_list'] == '')
		{
			$html_output['categories_list'] = "<option value=''>".lang ('no_category')."</option>";
		}

		////////////
		// Search //
		////////////
		//Customize by user search keyword
		$category = $this->input->post('category');
		$product_category_title = $this->products_category_model->find_parent(array('id'=> $category));
		$search = $this->input->post('search');
		//Search query
		if($search != "" || $product_category_title != ".lang ('all_category').")
		{
			if($category != null)
			{
				$this->db->where('category', $category);
			}

			$this->db->like('title' , $search);
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
				$parent_row = $row->category;
				$parent_title = $this->products_category_model->find_parent(array('id'=>$parent_row));

				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("options/add/".$row->id).'">'. $row->title .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("products/products_category/".$row->category).'">'. $parent_title .'</a></td>';
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

		$data['page_name'] = 'option_list';
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
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

		$main_db_name = "options";
		$html_output = array();
		$this->load->model('options_model');
		$this->load->model('products/products_category_model');
		$task = $this->input->post('task');

		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			//Set Form Rules:
			$this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[2]|max_length[200]');

			$this->form_validation->set_rules('category', lang('category'),'required|'. $this->products_category_model->get_inlist_string());

			$this->form_validation->set_rules('type', lang('type'), 'required|in_list[single_entry,multiple_entry,textarea,select,checkbox,file,upload]');

			$required_insert_value = '';
			if($this->input->post('type') == 'select' || $this->input->post('type') == 'checkbox')
			{
				$required_insert_value = '|required';
			}
			$this->form_validation->set_rules('insert_value', lang('insert_value'), 'trim'.$required_insert_value);

			$this->form_validation->set_rules('sort', lang('sort'), 'trim|is_natural|max_length[4]');

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'title' => $this->input->post('title'),
					'category' => $this->input->post('category'),
					'type' => $this->input->post('type'),
					'insert_value' => $this->input->post('insert_value'),
					'sort' => $this->input->post('sort'),
					'publish' => $this->input->post('publish')
				);

				if ($item_id)
				{
					//we should update this $edit_id into the database
					$this->options_model->update($item_id, $dadeh);
				}
				else
				{
					//this is new Item
					$item_id = $this->options_model->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'optionsindexsuccess_msg');
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("options/add/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("options/index"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("options/add"));
				}
			}
		}

		//It's Edit state, then we need to simulate edit for user
		if ($item_id)
		{
			$this->session->set_userdata('page_title', 'ویرایش خصوصیت');
			$page_name = 'edit_option';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
		}
		else
		{
			$this->session->set_userdata('page_title', 'افزودن خصوصیت');
			$page_name = 'add_option';
			//Default Item Data
			$html_output['item_data'] = array(
				"id" => '',
				"title" => '',
				"category" => '',
				"type" => '',
				"insert_value" => '',
				"sort" => '',
				"publish" => 'yes'
			);
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of categories //
		////////////////////////////////////////////////////
		$category_list = $this->products_category_model->get_where(array('publish' => 'yes'))->result();
		$html_output['categories_list'] = "<option value=''>".lang ('all_categories')."</option>";

		if (count($category_list) > 0)
		{
			$category = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->options_model->get_where(array('id'=>$item_id))->row();
				$category = $current_item->category;
			}

			foreach ($category_list as $row)
			{
				$html_output['categories_list'] .= '<option value="'.$row->id.'" '.set_select('category', $row->id, ($row->id == $category ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['categories_list'] == '')
		{
			$html_output['categories_list'] = "<option value=''>".lang ('no_category')."</option>";
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of type ////////
		////////////////////////////////////////////////////
		$html_output['type_list'] = "<option value=''>".lang ('without_category')."</option>";

		$type = '';
		if(! is_null($item_id))
		{
			$current_item = $this->options_model->get_where(array('id'=>$item_id))->row();
			$type = $current_item->type;
		}

		$html_output['type_list'] .=
			'<option value="single_entry" '.set_select('type', 'single_entry', ('single_entry' == $type ? true : false)).'>'.lang('single_input').'</option>'
			.'<option value="multiple_entry" '.set_select('type', 'multiple_entry', ('multiple_entry' == $type ? true : false)).'>'.lang('multiple_input').'</option>'
			.'<option value="textarea" '.set_select('type', 'textarea', ('textarea' == $type ? true : false)).'>'.lang('paragraph').'</option>'
			.'<option value="select" '.set_select('type', 'select', ('select' == $type ? true : false)).'>'.lang('select').'</option>'
			.'<option value="checkbox" '.set_select('type', 'checkbox', ('checkbox' == $type ? true : false)).'>'.lang('checkbox').'</option>'
			.'<option value="file" '.set_select('type', 'file', ('file' == $type ? true : false)).'>'.lang('file').'</option>'
			.'<option value="upload" '.set_select('type', 'upload', ('upload' == $type ? true : false)).'>'.lang('upload').'</option>';

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
}
