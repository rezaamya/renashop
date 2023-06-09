<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

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

		$this->session->set_userdata('page_title', 'لیست فیلدها');
		$main_db_name = "add_field";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("setting/add/".$list_items[0]), 'location');
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
				$this->load->model('setting/add_field_model');

				foreach ($list_items as $value)
				{
					//delete item
					$this->add_field_model->delete(array('id' => $value));
					//set success message
					$this->mylib->set_success(lang('deleted_successfully'));
				}
			}
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of categories //
		////////////////////////////////////////////////////
		$this->load->model('products/products_category_model');

		$category_list = $this->products_category_model->get_all();
		$html_output['categories_list'] = "<option value=''>".lang ('all_category')."</option>";
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

		$product_category_title = $this->products_category_model->find_parent(array('id'=> $category));

		////////////
		// Search //
		////////////
		//Customize by user search keyword
		$search = $this->input->post('search');
		//Search query
		if($search != "" || $product_category_title != ".lang ('all_category').")
		{
			$this->load->model('setting/add_field_model');

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

		$this->load->model('products/products_category_model');
		$this->load->model('setting/attribute_groups_model');
		//Get Items from Database
		$page = ($this->uri->segment(2));
		$this->db->order_by('sort', 'ASC');
		$this->db->limit($per_page, $page);
		$query = $this->db->get($main_db_name);

		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$category_row = $row->category;
				$attribute_groups_row = $row->attribute_groups;
				$category_title = $this->products_category_model->find_parent(array('id'=>$category_row));
				$attribute_groups_title = $this->attribute_groups_model->find_attribute_groups(array('id'=>$attribute_groups_row));

				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("setting/add/".$row->id).'">'. $row->title .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("products/products_category/".$row->category).'">'. $category_title .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("setting/add_attribute_groups/".$row->attribute_groups).'">'. $attribute_groups_title .'</a></td>';
				$temp_html .= '<td>'. lang($row->type) .'</td>';
				$temp_html .= '<td>'. $row->sort .'</td>';
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

		$data['page_name'] = 'list_fields';
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

		$main_db_name = "add_field";
		$html_output = array();
		$this->load->model('setting/add_field_model');
		$this->load->model('products/products_category_model');
		$this->load->model('setting/attribute_groups_model');
		$task = $this->input->post('task');
		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			//Are we editing current_item
			$posted_sort_field = $this->input->post('sort');
			$query = $this->db->get($main_db_name);
			foreach ($query->result() as $row)
			{
				if($item_id==$row->id)
				{
					$item_id_sort_field = $row->sort;

					break;
				}
			}

			//Set Form Rules:
			$this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[3]|max_length[200]');

			$this->form_validation->set_rules('category', lang('category'),'required|'. $this->products_category_model->get_inlist_string());

			$this->form_validation->set_rules('attribute_groups', lang('attribute_groups'),'required|'. $this->attribute_groups_model->get_inlist_string());

            $this->form_validation->set_rules('sort', lang('sort'), 'trim|is_natural|max_length[4]');
			/*$this->form_validation->set_rules('sort', lang('sort'), 'trim|max_length[15]'.($item_id==null || $posted_sort_field!=$item_id_sort_field ? "" :"|is_unique[$main_db_name.sort]"));*/

			$this->form_validation->set_rules('type', lang('type'), 'required');

			#################################
			## Set rules related to fields ##
            #################################
            $field_type = $this->input->post('type');
            if (isset($field_type) && ($field_type == 'select' or $field_type == 'checkbox'))
            {
                //insert_value is required for select/checkbox type of fields
                $this->form_validation->set_rules('insert_value', lang('insert_value'), 'trim|required');
            }

			$this->form_validation->set_rules('location', lang('location'), 'required|in_list[descriptive_movie,position1,position2,position3,position4,position5,position6,position7,position8,position9,position10,position11,position12,position13,position14,position15,position16,position17,position18,position19,position20]');

			$this->form_validation->set_rules('required', lang('required'), 'required|in_list[yes,no]');

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'title' => $this->input->post('title'),
					'category' => $this->input->post('category'),
					'attribute_groups' => $this->input->post('attribute_groups'),
					'sort' => $this->input->post('sort'),
					'type' => $this->input->post('type'),
					'insert_value' => $this->input->post('insert_value'),
					'show_list' => $this->input->post('show_list'),
					'show_not_check' => $this->input->post('show_not_check'),
					'location' => $this->input->post('location'),
					'required' => $this->input->post('required'),
					'publish' => $this->input->post('publish'),
					'special_characteristic' => $this->input->post('special_characteristic'),
					'comparability' => $this->input->post('comparability'),
					'linkable' => $this->input->post('linkable'),
					'searchable' => $this->input->post('searchable'),
				);

				if ($item_id)
				{
					//we should update this $edit_id into the database
					$this->add_field_model->update($item_id, $dadeh);
				}
				else
				{
					//this is new Item
					$item_id = $this->add_field_model->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'settingindexsuccess_msg');
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("setting/add/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("setting/index"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("setting/add"));
				}
			}
		}


		//It's Edit state, then we need to simulate edit for user
		if ($item_id)
		{
			$this->session->set_userdata('page_title', 'ویرایش فیلد');
			$page_name = 'edit_fields';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
		}
		else
		{
			$this->session->set_userdata('page_title', 'افزودن فیلد');
			$page_name = 'add_fields';
			//Default Item Data
			$html_output['item_data'] = array(
				"id" => '',
				"title" => '',
				"category" => '',
				"attribute_groups" => '',
				"sort" => '',
				"type" => '',
				"insert_value" => '',
				"show_list" => '',
				"show_not_check" => '',
				"location" => '',
				"required" => 'yes',
				"publish" => 'yes',
				"special_characteristic" => '',
		    	"comparability" => '',
			    "linkable" => '',
			    "searchable" => ''
			);
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of location //
		////////////////////////////////////////////////////
		$html_output['location_list'] = '';
		$location = 0;
		if(! is_null($item_id))
		{
			$current_item = $this->add_field_model->get_where(array('id'=>$item_id))->row();
			$location = $current_item->location;
		}

		//Add Descriptive movie to the position list
        $html_output['location_list'] .= '<option value="descriptive_movie" '.set_select('location', 'descriptive_movie', ('descriptive_movie' == $location ? true : false)).'>Descriptive Movie</option>';

		for ($i = 1; $i <=23; $i++)
		{
			$html_output['location_list'] .= '<option value="position'.$i.'" '.set_select('location', 'position'.$i, ('position'.$i == $location ? true : false)).'>موقعیت'.$i.'</option>';
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of categories //
		////////////////////////////////////////////////////
		$category_list = $this->products_category_model->get_where(array('publish' => 'yes'))->result();
		$html_output['categories_list'] = "<option value=''>".lang ('without_category')."</option>";

		if (count($category_list) > 0)
		{
			$category = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->add_field_model->get_where(array('id'=>$item_id))->row();
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
		// Create A list (HTML Select list) of attribute_groups //
		////////////////////////////////////////////////////
		$attribute_groups = $this->input->post('attribute_groups');
		if (isset($attribute_groups))
		{
			$html_output['attribute_groups'] = $attribute_groups;
		}
		else if (isset($item_id))
		{
			$html_output['attribute_groups'] = $html_output['item_data']['attribute_groups'];
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

    public function attribute()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

        $data = array('page_name' => 'attributes');

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/attribute/list');
        $this->load->view('template/footer');
    }

    public function add_attribute()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

        $data = array('page_name' => 'add_attribute');

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/attribute/add');
        $this->load->view('template/footer');

    }

    public function attribute_groups()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'گروه ویژگی');
		//////////////////////
		// Define Variables //
		//////////////////////
		$main_db_table = "attribute_groups";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("setting/add_attribute_groups/".$list_items[0]), 'location');
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
				foreach ($list_items as $value) {
					$this->db->set('publish', 'no');
					$this->db->where('id', $value);
					$this->db->update($main_db_table);
				}
			}
			elseif ($task == 'delete')
			{
				$this->load->model('setting/attribute_groups_model');

				$this->db->select('attribute_groups.id AS attribute_groups_id,add_field.id AS field_id');
				$this->db->from('attribute_groups');
				$this->db->join('add_field', 'attribute_groups.id=add_field.attribute_groups', 'left');
				$query_join = $this->db->get()->result();

				$this->db->select('attribute_groups_name,id');
				$attribute_groups_p_title_list = $this->db->get('attribute_groups')->result();

				foreach ($list_items as $value)
				{
					$delete_current_item = true;
					$field_er = '';
					$field_des = '';
					foreach($query_join as $join_row)
					{
						if($join_row->attribute_groups_id == $value)
						{
							if($join_row->field_id != '' && $join_row->field_id != null)
							{
								$field_er = 'تعدادی فیلد';
								$field_des = 'فیلدهای';
							}
						}
					}

					if($field_er != '')
					{
						$attr_title = '';
						foreach($attribute_groups_p_title_list as $attr_tit)
						{
							if($attr_tit->id == $value)
							{
								$attr_title = $attr_tit->attribute_groups_name;
								break;
							}
						}
						$delete_current_item = false;
						$error_msg = "<div>$field_er , در گروه ویژگی <<$attr_title>> وجود دارد. لطفا ابتدا, $field_des  موجود را حذف نمایید.</div>";
						$this->mylib->set_error($error_msg);
					}

					if ($delete_current_item)
					{
						//delete item
						$this->attribute_groups_model->delete(array('id' => $value));
						//set success message
						$this->mylib->set_success(lang('deleted_successfully'));
					}
				}
			}
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of categories //
		////////////////////////////////////////////////////
		$this->load->model('products/products_category_model');

		$category_list = $this->products_category_model->get_all();
		$html_output['categories_list'] = "<option value=''>".lang ('all_category')."</option>";
		$product_category = $this->input->post('category');

		if (count($category_list) > 0)
		{
			foreach ($category_list as $row)
			{
				$html_output['categories_list'] .= '<option value="'.$row->id.'" '.set_select('product_category', $row->id, ($row->id == $product_category ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['categories_list'] == '')
		{
			$html_output['categories_list'] = "<option value=''>".lang ('no_category')."</option>";
		}

		$product_category_title = $this->products_category_model->find_parent(array('id'=> $product_category));

		////////////
		// Search //
		////////////
		//Customize by user search keyword
		$search = $this->input->post('search');
		//Search query
		if($search != "" || $product_category_title != ".lang ('all_category').")
		{
			$this->load->model('setting/attribute_groups_model');

			if($product_category != null)
			{
				$this->db->where('product_category', $product_category);
			}

			$this->db->like('attribute_groups_name' , $search);
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
				$product_category_row = $row->product_category;
				$product_category_title = $this->products_category_model->find_parent(array('id'=> $row->product_category));

				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("setting/add_attribute_groups/".$row->id).'">'. $row->attribute_groups_name .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("products/products_category/".$row->product_category).'">'. $product_category_title .'</a></td>';
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
			'page_name' => 'attribute_groups',
			'main_db_table' => $main_db_table,
			'html_output' => $html_output
		);

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
        $this->load->view($this->uri->segment(1) . '/attribute_groups/list', $data);
        $this->load->view('template/footer');
    }

    public function add_attribute_groups($item_id = null)
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$main_db_name = "attribute_groups";
		$html_output = array();
		$this->load->model('setting/attribute_groups_model');
		$this->load->model('products/products_category_model');
		$task = $this->input->post('task');

		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			//Set Form Rules:
			$this->form_validation->set_rules('attribute_groups_name', lang('attribute_groups_name'), 'trim|required|min_length[2]|max_length[50]');

			$this->form_validation->set_rules('product_category', 'دسته‌بندی‌','required|'. $this->products_category_model->get_inlist_string());

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'attribute_groups_name' => $this->input->post('attribute_groups_name'),
					'product_category' => $this->input->post('product_category'),
					'publish' => $this->input->post('publish')
				);

				if ($item_id)
				{
					//we should update this $edit_id into the database
					$this->attribute_groups_model->update($item_id, $dadeh);
				}
				else
				{
					$item_id = $this->attribute_groups_model->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'settingattribute_groupssuccess_msg');
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("setting/add_attribute_groups/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("setting/attribute_groups"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("setting/add_attribute_groups"));
				}
			}
		}

		if ($item_id)
		{
			$page_name = 'edit_attribute_groups';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
			$this->session->set_userdata('page_title', 'ویرایش گروه ویژگی');
		}
		else
		{
			$page_name = 'add_attribute_groups';
			//Default Item Data
			$html_output['item_data'] = array(
				"id" => '',
				"attribute_groups_name" => '',
				"product_category" => '',
				"publish" => 'yes'
			);
			$this->session->set_userdata('page_title', 'افزودن گروه ویژگی');
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of categories //
		////////////////////////////////////////////////////
		$category_list = $this->products_category_model->get_where(array('publish' => 'yes'))->result();
		$html_output['categories_list'] = "<option value=''>".lang ('without_category')."</option>";

		if (count($category_list) > 0)
		{
			$product_category = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->attribute_groups_model->get_where(array('id'=>$item_id))->row();
				$product_category = $current_item->product_category;
			}

			foreach ($category_list as $row)
			{
				$html_output['categories_list'] .= '<option value="'.$row->id.'" '.set_select('parent', $row->id, ($row->id == $product_category ? true : false)).'>'.$row->title.'</option>';
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
        $this->load->view($this->uri->segment(1) . '/attribute_groups/add', $data);
        $this->load->view('template/footer');

    }

    public function shipping()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'حمل و نقل');
		//////////////////////
		// Define Variables //
		//////////////////////
		$main_db_table = "shipping";
		$html_output = array();
		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');
		$this->load->model('setting/shipping_model');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("setting/add_shipping/".$list_items[0]), 'location');
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
				foreach ($list_items as $value)
				{
					//delete item
					$this->shipping_model->delete(array('id' => $value));
					//set success message
					$this->mylib->set_success(lang('deleted_successfully'));
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
			$this->db->like('delivery_type' , $search);
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
				$temp_html .= '<td><a href="'.base_url("setting/add_shipping/".$row->id).'">'. lang($row->delivery_type) .'</a></td>';
				$temp_html .= '<td>'. $row->sort .'</td>';
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
			'page_name' => 'shipping',
			'html_output' => $html_output
		);

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
		$data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
        $this->load->view($this->uri->segment(1) . '/shipping/list', $data);
        $this->load->view('template/footer');

    }

    public function add_shipping($item_id = null)
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$main_db_name = "shipping";
		$html_output = array();
		$this->load->model('setting/shipping_model');
		$task = $this->input->post('task');
		$state_of_origin_send_array = array("آذربایجان شرقی", "آذربایجان غربی", "اردبیل", "اصفهان", "البرز", "ایلام", "بوشهر", "تهران", "چهارمحال و بختیاری", "خراسان جنوبی", "خراسان رضوی", "خراسان شمالی", "خوزستان", "زنجان", "سمنان", "سیستان و بلوچستان", "فارس", "قزوین", "قم", "کردستان", "کرمان", "کرمانشاه", "کهگیلویه وبویراحمد", "گلستان", "گیلان", "لرستان", "مازندران", "مرکزی", "هرمزگان", "همدان", "یزد");

		if($task == 'save' || $task == 'save_and_close')
		{
			$item_id_delivery_type = '';
			$posted_delivery_type = $this->input->post('delivery_type');
			$query = $this->db->get($main_db_name);
			foreach ($query->result() as $row)
			{
				if($item_id==$row->id)
				{
					$item_id_delivery_type = $row->delivery_type;
					break;
				}
			}

			//Set Form Rules:
			$this->form_validation->set_rules('delivery_type', lang('delivery_type'), 'trim|required|in_list[express_post,registered_post,peyk_delivery]'.($item_id==null || $posted_delivery_type!=$item_id_delivery_type ? "|is_unique[$main_db_name.delivery_type]" :""), array('is_unique' => 'این نوع تحویل قبلا یکبار ثبت شده است.'));

			$this->form_validation->set_rules('sort', lang('sort'), 'trim|is_natural|max_length[4]');

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			$state_of_origin_in_list = '';
			if (count($state_of_origin_send_array) > 0)
			{
				$state_of_origin_in_list = 'in_list['.implode(",",$state_of_origin_send_array).']|';
			}
			$this->form_validation->set_rules('state_of_origin_send[]', lang('state_of_origin_send'), $state_of_origin_in_list.'required');

			if($this->input->post('delivery_type') == 'express_post' || $this->input->post('delivery_type') == 'registered_post')
			{
				$this->form_validation->set_rules('from_weight[]', lang('from_weight'), 'trim|is_natural');

				$this->form_validation->set_rules('to_weight[]', lang('to_weight'), 'trim|is_natural|greater_than[0]|required');

				$this->form_validation->set_rules('within_the_province[]', lang('within_the_province'), 'trim|is_natural|max_length[8]');

				$this->form_validation->set_rules('out_of_the_province[]', lang('out_of_the_province'), 'trim|is_natural|max_length[8]');

				$this->form_validation->set_rules('tax_within[]', lang('tax'), 'trim|is_natural|max_length[8]');

				$this->form_validation->set_rules('insurance_within[]', lang('insurance'), 'trim|is_natural|max_length[8]');

				$this->form_validation->set_rules('other_costs_within[]', lang('other_costs'), 'trim|is_natural|max_length[8]');

				$this->form_validation->set_rules('tax_out_of[]', lang('tax'), 'trim|is_natural|max_length[8]');

				$this->form_validation->set_rules('insurance_out_of[]', lang('insurance'), 'trim|is_natural|max_length[8]');

				$this->form_validation->set_rules('other_costs_out_of[]', lang('other_costs'), 'trim|is_natural|max_length[8]');
			}

			$peyk_required = '';
			if($this->input->post('delivery_type') == 'peyk_delivery')
			{
				$peyk_required = '|required';
			}
			$this->form_validation->set_rules('state[]', lang('state'), $state_of_origin_in_list.'trim'.$peyk_required);

			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$state = $this->input->post('state[]');
			if(count($state) > 0)
			{
				if($this->input->post('delivery_type') == 'peyk_delivery')
				{
					if(is_array($state))
					{
						foreach($state as $in_state => $val_state)
						{
							$region = $this->input->post("region_name[$val_state][]");
							$this->form_validation->set_rules('region_name['.$val_state.'][]', lang('region_name'), 'trim|min_length[2]|max_length[100]'.$peyk_required);
							if(is_array($region))
							{
								foreach($region as $in_region => $val_region)
								{
									$this->form_validation->set_rules('from_weight['.$val_state.']['.$val_region.']', lang('from_weight'), 'trim|is_natural');
									$this->form_validation->set_rules('to_weight['.$val_state.']['.$val_region.']', lang('to_weight'), 'trim|is_natural|required|greater_than[0]');

									$this->form_validation->set_rules('delivery_cost['.$val_state.']['.$val_region.']', lang('delivery_cost'), 'trim|is_natural|max_length[8]');
									$this->form_validation->set_rules('other_costs['.$val_state.']['.$val_region.']', lang('other_costs'), 'trim|is_natural|max_length[8]');
								}
							}
						}
					}
				}
			}
			////////////////////////////////////////////////////////////////////////////////////////////////////////////

			if ($this->form_validation->run() == TRUE)
			{
				$box_information = array();
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$within_the_province = $this->input->post('within_the_province[]');
				if(count($within_the_province) > 0)
				{
					if($this->input->post('delivery_type') == 'express_post' || $this->input->post('delivery_type') == 'registered_post')
					{
						if(is_array($within_the_province))
						{
							$b = 0;
							foreach($within_the_province as $fr_row)
							{
								if($this->input->post("from_weight[$b]") == '' || $this->input->post("from_weight[$b]") == null)
								{
									$box_information[$b]['from_weight'] = 0;
								}
								else
								{
									$box_information[$b]['from_weight'] = $this->input->post("from_weight[$b]");
								}

								if($this->input->post("to_weight[$b]") == '' || $this->input->post("to_weight[$b]") == null)
								{
									$box_information[$b]['to_weight'] = 0;
								}
								else
								{
									$box_information[$b]['to_weight'] = $this->input->post("to_weight[$b]");
								}

								if($this->input->post("within_the_province[$b]") == '' || $this->input->post("within_the_province[$b]") == null)
								{
									$box_information[$b]['within_the_province'] = 0;
								}
								else
								{
									$box_information[$b]['within_the_province'] = $this->input->post("within_the_province[$b]");
								}

								if($this->input->post("out_of_the_province[$b]") == '' || $this->input->post("out_of_the_province[$b]") == null)
								{
									$box_information[$b]['out_of_the_province'] = 0;
								}
								else
								{
									$box_information[$b]['out_of_the_province'] = $this->input->post("out_of_the_province[$b]");
								}

								if($this->input->post("tax_within[$b]") == '' || $this->input->post("tax_within[$b]") == null)
								{
									$box_information[$b]['tax_within'] = 0;
								}
								else
								{
									$box_information[$b]['tax_within'] = $this->input->post("tax_within[$b]");
								}

								if($this->input->post("insurance_within[$b]") == '' || $this->input->post("insurance_within[$b]") == null)
								{
									$box_information[$b]['insurance_within'] = 0;
								}
                                else
								{
									$box_information[$b]['insurance_within'] = $this->input->post("insurance_within[$b]");
								}

								if($this->input->post("other_costs_within[$b]") == '' || $this->input->post("other_costs_within[$b]") == null)
								{
									$box_information[$b]['other_costs_within'] = 0;
								}
								else
								{
									$box_information[$b]['other_costs_within'] = $this->input->post("other_costs_within[$b]");
								}

								if($this->input->post("tax_out_of[$b]") == '' || $this->input->post("tax_out_of[$b]") == null)
								{
									$box_information[$b]['tax_out_of'] = 0;
								}
								else
								{
									$box_information[$b]['tax_out_of'] = $this->input->post("tax_out_of[$b]");
								}

								if($this->input->post("insurance_out_of[$b]") == '' || $this->input->post("insurance_out_of[$b]") == null)
								{
									$box_information[$b]['insurance_out_of'] = 0;
								}
								else
								{
									$box_information[$b]['insurance_out_of'] = $this->input->post("insurance_out_of[$b]");
								}

								if($this->input->post("other_costs_out_of[$b]") == '' || $this->input->post("other_costs_out_of[$b]") == null)
								{
									$box_information[$b]['other_costs_out_of'] = 0;
								}
								else
								{
									$box_information[$b]['other_costs_out_of'] = $this->input->post("other_costs_out_of[$b]");
								}

								$b++;
							}
						}
					}
				}
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$state = $this->input->post('state[]');
				if(count($state) > 0)
				{
					if($this->input->post('delivery_type') == 'peyk_delivery')
					{
						if(is_array($state))
						{
							foreach($state as $in_state => $val_state)
							{
								$region = $this->input->post("region_name[$val_state][]");
								if(is_array($region))
								{
									foreach($region as $in_region => $val_region)
									{
										$from_weight_array = $this->input->post('from_weight['.$val_state.']['.$val_region.']');
										if(is_array($from_weight_array) && $from_weight_array != array())
										{
											$r = 0;
											foreach($from_weight_array as $from_wei_val)
											{
												if($from_wei_val == '')
												{
													$box_information[$val_state][$val_region]['from_weight'][$r] = 0;
												}
												else
												{
													$box_information[$val_state][$val_region]['from_weight'][$r] = $from_wei_val;
												}
												$r++;
											}
										}

										$to_weight_array = $this->input->post('to_weight['.$val_state.']['.$val_region.']');
										if(is_array($to_weight_array) && $to_weight_array != array())
										{
											$y = 0;
											foreach($to_weight_array as $to_wei_val)
											{
												if($to_wei_val == '')
												{
													$box_information[$val_state][$val_region]['to_weight'][$y] = 0;
												}
												else
												{
													$box_information[$val_state][$val_region]['to_weight'][$y] = $to_wei_val;
												}
												$y++;
											}
										}

										$delivery_cost_array = $this->input->post('delivery_cost['.$val_state.']['.$val_region.']');
										if(is_array($delivery_cost_array) && $delivery_cost_array != array())
										{
											$a = 0;
											foreach($delivery_cost_array as $deli_val)
											{
												if($deli_val == '')
												{
													$box_information[$val_state][$val_region]['delivery_cost'][$a] = 0;
												}
												else
												{
													$box_information[$val_state][$val_region]['delivery_cost'][$a] = $deli_val;
												}
												$a++;
											}
										}

										$other_costs_array = $this->input->post('other_costs['.$val_state.']['.$val_region.']');
										if(is_array($other_costs_array) && $other_costs_array != array())
										{
											$s = 0;
											foreach($other_costs_array as $cost_val)
											{
												if($cost_val == '')
												{
													$box_information[$val_state][$val_region]['other_costs'][$s] = 0;
												}
												else
												{
													$box_information[$val_state][$val_region]['other_costs'][$s] = $cost_val;
												}
												$s++;
											}
										}
									}
								}
							}
						}
					}
				}
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$dadeh = array
				(
					'delivery_type' => $this->input->post('delivery_type'),
					'sort' => $this->input->post('sort'),
					'publish' => $this->input->post('publish'),
					'state_of_origin_send' => json_encode($this->input->post('state_of_origin_send[]')),
					'box_information' => json_encode($box_information)
				);
				if ($item_id)
				{
					$this->shipping_model->update($item_id, $dadeh);
				}
				else
				{
					$item_id = $this->shipping_model->insert($dadeh);
				}
				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'settingshippingsuccess_msg');
				}
				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("setting/add_shipping/".$item_id));
				}
				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("setting/shipping"));
				}
			}
		}

		///////////////////////////////////////////////////////////////
		// Create A list (HTML Select list) of state_of_origin_send //
		/////////////////////////////////////////////////////////////
		$html_output['state_of_origin_send_list'] = '';
		if (count($state_of_origin_send_array) > 0)
		{
			if(! is_null($item_id))
			{
				$this->db->select('state_of_origin_send');
				$this->db->where(array('id' => $item_id));
				$current_item = $this->db->get('shipping')->row();
				$state_of_origin_send_db = json_decode($current_item->state_of_origin_send);
			}

			$set_value_state = '';
			foreach ($state_of_origin_send_array as $value_or)
			{
				if(isset($state_of_origin_send_db))
				{
					if(is_array($state_of_origin_send_db) && $state_of_origin_send_db != array())
					{
						$set_value_state = (in_array($value_or, $state_of_origin_send_db) ? "checked" : "");
					}
					else
					{
						$set_value_state = '';
					}
				}
				$html_output['state_of_origin_send_list'] .= '<label><input class="w3-check shahre_mabda" type="checkbox" name="state_of_origin_send[]" value="'.$value_or.'" '.$set_value_state.' onchange="send_to_box(this);">'.$value_or.' </label> ';
			}
		}

		//It's Edit state, then we need to simulate edit for user
		$to_weight_1 = $this->input->post('to_weight[]');
		$state_1 = $this->input->post('state[]');
		if ($item_id && !isset($to_weight_1) && !isset($state_1))
		{
			$page_name = 'edit_shipping';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
			$html_output['peyk_box'] = '';
			$html_output['post_box'] = '';
			if($html_output['item_data']['delivery_type'] == 'express_post' || $html_output['item_data']['delivery_type'] == 'registered_post')
			{
				$box_information_jason = json_decode($html_output['item_data']['box_information']);
				if(is_array($box_information_jason))
				{
				    $f = 0;
					foreach($box_information_jason as $json_b_row)
					{
						$from_weight_p = $this->input->post('from_weight['.$f.']');
						$to_weight_p = $this->input->post('to_weight['.$f.']');
						$within_the_province_p = $this->input->post('within_the_province['.$f.']');
						$tax_within_p = $this->input->post('tax_within['.$f.']');
						$insurance_within_p = $this->input->post('insurance_within['.$f.']');
						$other_costs_within_p = $this->input->post('other_costs_within['.$f.']');
						$out_of_the_province_p = $this->input->post('out_of_the_province['.$f.']');
						$tax_out_of_p = $this->input->post('tax_out_of['.$f.']');
						$insurance_out_of_p = $this->input->post('insurance_out_of['.$f.']');
						$other_costs_out_of_p = $this->input->post('other_costs_out_of['.$f.']');

						$f++;
						if(isset($json_b_row->from_weight) && isset($json_b_row->to_weight) && isset($json_b_row->tax_within) && isset($json_b_row->within_the_province) && isset($json_b_row->insurance_within) && isset($json_b_row->other_costs_within) && isset($json_b_row->out_of_the_province) && isset($json_b_row->tax_out_of) && isset($json_b_row->insurance_out_of) && isset($json_b_row->other_costs_out_of))
						{
							$html_output['post_box'] .= 	'<div class="post_box form-group tariff_box">
                        <div class="tariff_box_tool_bar">
                        <a href="#" onclick="this.parentElement.parentElement.parentElement.removeChild(this.parentElement.parentElement); return false;"><i class="fas fa-window-close fa-1x text-danger"></i></a>
                        </div>
                        <div class="container-fluid">
                        <div class="row">
                        <div class="form-group col-md-2">
                        <label>'.lang("from_weight").'</label>
                        <input type="text" class="form-control" name="from_weight[]" placeholder="'.lang("from_weight").'" value="'.(isset($from_weight_p) ? $from_weight_p : $json_b_row->from_weight).'">
                        </div>
                        <div class="form-group col-md-2">
                        <label>'.lang("to_weight").'</label>
                        <input type="text" class="form-control" name="to_weight[]" placeholder="'.lang("to_weight").'" value="'.(isset($to_weight_p) ? $to_weight_p : $json_b_row->to_weight).'">
                        </div>
                        <div class="form-group col-md-8 tariff_box_2">
                        <div class="container-fluid">
                        <div class="row">
                        <div class="form-group col-md-3">
                        <label>'.lang("within_the_province").'</label>
                        <input type="text" class="form-control" name="within_the_province[]" placeholder="'.lang("within_the_province").'" value="'.(isset($within_the_province_p) ? $within_the_province_p : $json_b_row->within_the_province).'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("tax").'</label>
                        <input type="text" class="form-control" name="tax_within[]" placeholder="'.lang("tax").'" value="'.(isset($tax_within_p) ? $tax_within_p : $json_b_row->tax_within).'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("insurance").'</label>
                        <input type="text" class="form-control" name="insurance_within[]" placeholder="'.lang("insurance").'" value="'.(isset($insurance_within_p) ? $insurance_within_p : $json_b_row->insurance_within).'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("other_costs").'</label>
                        <input type="text" class="form-control" name="other_costs_within[]" placeholder="'.lang("other_costs").'" value="'.(isset($other_costs_within_p) ? $other_costs_within_p : $json_b_row->other_costs_within).'">
                        </div>
                        </div>
                        </div>
                        </div>
                        <div class="form-group col-md-8 offset-md-4 tariff_box_2">
                        <div class="container-fluid">
                        <div class="row">
                        <div class="form-group col-md-3">
                        <label>'.lang("out_of_the_province").'</label>
                        <input type="text" class="form-control" name="out_of_the_province[]" placeholder="'.lang("out_of_the_province").'" value="'.(isset($out_of_the_province_p) ? $out_of_the_province_p : $json_b_row->out_of_the_province).'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("tax").'</label>
                        <input type="text" class="form-control" name="tax_out_of[]" placeholder="'.lang("tax").'" value="'.(isset($tax_out_of_p) ? $tax_out_of_p : $json_b_row->tax_out_of).'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("insurance").'</label>
                        <input type="text" class="form-control" name="insurance_out_of[]" placeholder="'.lang("insurance").'" value="'.(isset($insurance_out_of_p) ? $insurance_out_of_p : $json_b_row->insurance_out_of).'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("other_costs").'</label>
                        <input type="text" class="form-control" name="other_costs_out_of[]" placeholder="'.lang("other_costs").'" value="'.(isset($other_costs_out_of_p) ? $other_costs_out_of_p : $json_b_row->other_costs_out_of).'">
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>';
						}
					}
				}
			}
			elseif($html_output['item_data']['delivery_type'] == 'peyk_delivery')
			{
				$box_information_jason = json_decode($html_output['item_data']['box_information']);
				$t = 0;
				foreach ($box_information_jason as $index_b => $json_b_row)
				{
					$state_posted = $this->input->post('state['.$t.']');
					if(isset($state_posted))
					{
						$state_temp = $state_posted;
					}
					else
					{
						$state_temp = $index_b;
					}
					$t++;

					$state_name = '';
					if(is_array($state_of_origin_send_array) && isset($state_of_origin_send_db))
					{
						if(is_array($state_of_origin_send_db) && $state_of_origin_send_db != array())
						{
							foreach ($state_of_origin_send_array as $value_state)
							{
								if(in_array($value_state, $state_of_origin_send_db))
								{
									$state_name .= '<option value="'.$value_state.'" '.($value_state == $state_temp ? 'selected' : '').'>'.$value_state.'</option>';
								}
							}
						}
					}

					$u = 0;
					foreach ($json_b_row as $index_in => $value_in)
					{
						$region_temp = $this->input->post("region_name[$index_b][$u]");
						$u++;

						$html_output['peyk_box'] .= '<div class="peyk_box form-group tariff_box mb-3">
				<div class="tariff_box_tool_bar">
				<a href="javascript:void(0)" onclick="peyk_tariff_box(this); refresh_fields_names(this);"><i class="fas fa-plus-square fa-1x text-success"></i></a> 
				<a href="javascript:void(0)" onclick="this.parentElement.parentElement.parentElement.removeChild(this.parentElement.parentElement);"><i class="fas fa-window-close fa-1x text-danger"></i></a>
				</div>
				<div class="container-fluid">
				<div class="row">
				<div class="form-group col-md-2">
				<label>' . lang("state") . '</label>
				<select name="state[]" class="ostan_e_box form-control" onchange="refresh_fields_names(this);">
				'.$state_name.'
				</select>
				</div>
				<div class="form-group col-md-2">
				<label>' . lang("region_name") . '</label>
				<input type="text" class="region_name form-control" name="region_name['.$index_b.'][]" oninput="refresh_fields_names(this);" placeholder="' . lang("region_name") . '" value="'.(isset($region_temp) ? $region_temp : $index_in).'">
				</div>
				<div class="form-group col-md-8 peyk_tariff_box">';
							foreach ($value_in as $valintar)
						{
							$p = 0;
							foreach($valintar as $valintar_tar)
							{
								$from_weight = $this->input->post('from_weight['.$index_b.']['.$index_in.']['.$p.']');
								$to_weight_temp = $this->input->post('to_weight['.$index_b.']['.$index_in.']['.$p.']');
								$delivery_cost = $this->input->post('delivery_cost['.$index_b.']['.$index_in.']['.$p.']');
								$other_costs = $this->input->post('other_costs['.$index_b.']['.$index_in.']['.$p.']');

								$html_output['peyk_box'] .= ' <div class="row tariff_box_2 mb-3">
				<div class="col">
				<div class="row">
				<div class="col">
				<a href="#" class="close mb-3" data-dismiss="alert" aria-label="close" onclick="this.parentElement.parentElement.parentElement.parentElement.parentElement.removeChild(this.parentElement.parentElement.parentElement.parentElement);">×</a>
				</div>
				</div>
				<div class="row">
				<div class="col-md-3">
				<label>' . lang("from_weight") . '</label>
				<input type="text" class="from_weight form-control" name="from_weight['.$index_b.']['.$index_in.'][]" placeholder="' . lang("from_weight") . '" value="'.(isset($from_weight) ? $from_weight : $value_in->from_weight[$p]).'">
				</div>
				<div class="col-md-3">
				<label>' . lang("to_weight") . '</label>
				<input type="text" class="to_weight form-control" name="to_weight['.$index_b.']['.$index_in.'][]" placeholder="' . lang("to_weight") . '" value="'.(isset($to_weight_temp) ? $to_weight_temp : $value_in->to_weight[$p]).'">
				</div> <div class="col-md-3">
				<label>' . lang("delivery_cost") . '</label>
				<input type="text" class="delivery_cost form-control" name="delivery_cost['.$index_b.']['.$index_in.'][]" placeholder="' . lang("delivery_cost") . '" value="'.(isset($delivery_cost) ? $delivery_cost : $value_in->delivery_cost[$p]).'">
				</div> <div class="col-md-3"> <label>' . lang("other_costs") . '</label>
				<input type="text" class="other_costs form-control" name="other_costs['.$index_b.']['.$index_in.'][]" placeholder="' . lang("other_costs") . '" value="'.(isset($other_costs) ? $other_costs : $value_in->other_costs[$p]).'">
				</div>
				</div>
				</div>
				</div>';
								$p++;
							}
						break;
						}
					$html_output['peyk_box'] .= '</div>
									</div>
									</div>
									</div>';
					}
				}
			}
			$this->session->set_userdata('page_title', 'ویرایش روش ارسال');
		}
		else
		{
			$this->session->set_userdata('page_title', 'افزودن روش ارسال');
			$page_name = 'add_shipping';
			//Default Item Data
			$id_temp = '';
			if($item_id)
			{
				$id_temp = $item_id;
				$page_name = 'edit_shipping';
			}

			$html_output['item_data'] = array(
				"id" => $id_temp,
				"delivery_type" => '',
				"sort" => '',
				"publish" => 'yes',
				'state_of_origin_send' => ''
			);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$box_information = array();
			$within_the_province = $this->input->post('within_the_province[]');
			if(count($within_the_province) > 0)
			{
				if($this->input->post('delivery_type') == 'express_post' || $this->input->post('delivery_type') == 'registered_post')
				{
					if(is_array($within_the_province))
					{
						$b = 0;
						foreach($within_the_province as $fr_row)
						{
							if($this->input->post("from_weight[$b]") == '' || $this->input->post("from_weight[$b]") == null)
							{
								$box_information[$b]['from_weight'] = 0;
							}
							else
							{
								$box_information[$b]['from_weight'] = $this->input->post("from_weight[$b]");
							}

							if($this->input->post("to_weight[$b]") == '' || $this->input->post("to_weight[$b]") == null)
							{
								$box_information[$b]['to_weight'] = 0;
							}
							else
							{
								$box_information[$b]['to_weight'] = $this->input->post("to_weight[$b]");
							}

							if($this->input->post("within_the_province[$b]") == '' || $this->input->post("within_the_province[$b]") == null)
							{
								$box_information[$b]['within_the_province'] = 0;
							}
							else
							{
								$box_information[$b]['within_the_province'] = $this->input->post("within_the_province[$b]");
							}

							if($this->input->post("out_of_the_province[$b]") == '' || $this->input->post("out_of_the_province[$b]") == null)
							{
								$box_information[$b]['out_of_the_province'] = 0;
							}
							else
							{
								$box_information[$b]['out_of_the_province'] = $this->input->post("out_of_the_province[$b]");
							}

							if($this->input->post("tax_within[$b]") == '' || $this->input->post("tax_within[$b]") == null)
							{
								$box_information[$b]['tax_within'] = 0;
							}
							else
							{
								$box_information[$b]['tax_within'] = $this->input->post("tax_within[$b]");
							}

							if($this->input->post("insurance_within[$b]") == '' || $this->input->post("insurance_within[$b]") == null)
							{
								$box_information[$b]['insurance_within'] = 0;
							}
							else
							{
								$box_information[$b]['insurance_within'] = $this->input->post("insurance_within[$b]");
							}

							if($this->input->post("other_costs_within[$b]") == '' || $this->input->post("other_costs_within[$b]") == null)
							{
								$box_information[$b]['other_costs_within'] = 0;
							}
							else
							{
								$box_information[$b]['other_costs_within'] = $this->input->post("other_costs_within[$b]");
							}

							if($this->input->post("tax_out_of[$b]") == '' || $this->input->post("tax_out_of[$b]") == null)
							{
								$box_information[$b]['tax_out_of'] = 0;
							}
							else
							{
								$box_information[$b]['tax_out_of'] = $this->input->post("tax_out_of[$b]");
							}

							if($this->input->post("insurance_out_of[$b]") == '' || $this->input->post("insurance_out_of[$b]") == null)
							{
								$box_information[$b]['insurance_out_of'] = 0;
							}
							else
							{
								$box_information[$b]['insurance_out_of'] = $this->input->post("insurance_out_of[$b]");
							}

							if($this->input->post("other_costs_out_of[$b]") == '' || $this->input->post("other_costs_out_of[$b]") == null)
							{
								$box_information[$b]['other_costs_out_of'] = 0;
							}
							else
							{
								$box_information[$b]['other_costs_out_of'] = $this->input->post("other_costs_out_of[$b]");
							}

							$b++;
						}
					}
				}
			}
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$state = $this->input->post('state[]');
			if(count($state) > 0)
			{
				if($this->input->post('delivery_type') == 'peyk_delivery')
				{
					if(is_array($state))
					{
						foreach($state as $in_state => $val_state)
						{
							$region = $this->input->post("region_name[$val_state][]");
							if(is_array($region))
							{
								foreach($region as $in_region => $val_region)
								{
									$from_weight_array = $this->input->post('from_weight['.$val_state.']['.$val_region.']');
									if(is_array($from_weight_array) && $from_weight_array != array())
									{
										$r = 0;
										foreach($from_weight_array as $from_wei_val)
										{
											if($from_wei_val == '')
											{
												$box_information[$val_state][$val_region]['from_weight'][$r] = 0;
											}
											else
											{
												$box_information[$val_state][$val_region]['from_weight'][$r] = $from_wei_val;
											}
											$r++;
										}
									}

									$to_weight_array = $this->input->post('to_weight['.$val_state.']['.$val_region.']');
									if(is_array($to_weight_array) && $to_weight_array != array())
									{
										$y = 0;
										foreach($to_weight_array as $to_wei_val)
										{
											if($to_wei_val == '')
											{
												$box_information[$val_state][$val_region]['to_weight'][$y] = 0;
											}
											else
											{
												$box_information[$val_state][$val_region]['to_weight'][$y] = $to_wei_val;
											}
											$y++;
										}
									}

									$delivery_cost_array = $this->input->post('delivery_cost['.$val_state.']['.$val_region.']');
									if(is_array($delivery_cost_array) && $delivery_cost_array != array())
									{
										$a = 0;
										foreach($delivery_cost_array as $deli_val)
										{
											if($deli_val == '')
											{
												$box_information[$val_state][$val_region]['delivery_cost'][$a] = 0;
											}
											else
											{
												$box_information[$val_state][$val_region]['delivery_cost'][$a] = $deli_val;
											}
											$a++;
										}
									}

									$other_costs_array = $this->input->post('other_costs['.$val_state.']['.$val_region.']');
									if(is_array($other_costs_array) && $other_costs_array != array())
									{
										$s = 0;
										foreach($other_costs_array as $cost_val)
										{
											if($cost_val == '')
											{
												$box_information[$val_state][$val_region]['other_costs'][$s] = 0;
											}
											else
											{
												$box_information[$val_state][$val_region]['other_costs'][$s] = $cost_val;
											}
											$s++;
										}
									}
								}
							}
						}
					}
				}
			}
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$html_output['peyk_box'] = '';
			$html_output['post_box'] = '';
			if($this->input->post('delivery_type') == 'express_post' || $this->input->post('delivery_type') == 'registered_post')
			{
				$box_information_jason = $box_information;
				if(is_array($box_information_jason))
				{
					foreach($box_information_jason as $json_b_row)
					{
						if(isset($json_b_row['from_weight']) && isset($json_b_row['to_weight']) && isset($json_b_row['tax_within']) && isset($json_b_row['within_the_province']) && isset($json_b_row['insurance_within']) && isset($json_b_row['other_costs_within']) && isset($json_b_row['out_of_the_province']) && isset($json_b_row['tax_out_of']) && isset($json_b_row['insurance_out_of']) && isset($json_b_row['other_costs_out_of']))
						{
							$html_output['post_box'] .= 	'<div class="post_box form-group tariff_box">
                        <div class="tariff_box_tool_bar">
                        <a href="#" onclick="this.parentElement.parentElement.parentElement.removeChild(this.parentElement.parentElement); return false;"><i class="fas fa-window-close fa-1x text-danger"></i></a>
                        </div>
                        <div class="container-fluid">
                        <div class="row">
                        <div class="form-group col-md-2">
                        <label>'.lang("from_weight").'</label>
                        <input type="text" class="form-control" name="from_weight[]" placeholder="'.lang("from_weight").'" value="'.$json_b_row['from_weight'].'">
                        </div>
                        <div class="form-group col-md-2">
                        <label>'.lang("to_weight").'</label>
                        <input type="text" class="form-control" name="to_weight[]" placeholder="'.lang("to_weight").'" value="'.$json_b_row['to_weight'].'">
                        </div>
                        <div class="form-group col-md-8 tariff_box_2">
                        <div class="container-fluid">
                        <div class="row">
                        <div class="form-group col-md-3">
                        <label>'.lang("within_the_province").'</label>
                        <input type="text" class="form-control" name="within_the_province[]" placeholder="'.lang("within_the_province").'" value="'.$json_b_row['within_the_province'].'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("tax").'</label>
                        <input type="text" class="form-control" name="tax_within[]" placeholder="'.lang("tax").'" value="'.$json_b_row['tax_within'].'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("insurance").'</label>
                        <input type="text" class="form-control" name="insurance_within[]" placeholder="'.lang("insurance").'" value="'.$json_b_row['insurance_within'] .'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("other_costs").'</label>
                        <input type="text" class="form-control" name="other_costs_within[]" placeholder="'.lang("other_costs").'" value="'.$json_b_row['other_costs_within'].'">
                        </div>
                        </div>
                        </div>
                        </div>
                        <div class="form-group col-md-8 offset-md-4 tariff_box_2">
                        <div class="container-fluid">
                        <div class="row">
                        <div class="form-group col-md-3">
                        <label>'.lang("out_of_the_province").'</label>
                        <input type="text" class="form-control" name="out_of_the_province[]" placeholder="'.lang("out_of_the_province").'" value="'.$json_b_row['out_of_the_province'].'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("tax").'</label>
                        <input type="text" class="form-control" name="tax_out_of[]" placeholder="'.lang("tax").'" value="'.$json_b_row['tax_out_of'].'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("insurance").'</label>
                        <input type="text" class="form-control" name="insurance_out_of[]" placeholder="'.lang("insurance").'" value="'.$json_b_row['insurance_out_of'] .'">
                        </div>
                        <div class="form-group col-md-3">
                        <label>'.lang("other_costs").'</label>
                        <input type="text" class="form-control" name="other_costs_out_of[]" placeholder="'.lang("other_costs").'" value="'.$json_b_row['other_costs_out_of'].'">
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>';
						}
					}
				}
			}
			elseif($this->input->post('delivery_type') == 'peyk_delivery')
			{
				$box_information_jason = $box_information;
				$t = 0;
				foreach ($box_information_jason as $index_b => $json_b_row)
				{
					$state_posted = $this->input->post('state['.$t.']');
					$t++;

					$state_name = '';
					if(is_array($state_of_origin_send_array))
					{
						foreach ($state_of_origin_send_array as $value_state)
						{
							$state_name .= '<option value="'.$value_state.'" '.($value_state == $state_posted ? 'selected' : '').'>'.$value_state.'</option>';
						}
					}

					$u = 0;
					foreach ($json_b_row as $index_in => $value_in)
					{
						$region_temp = $this->input->post("region_name[$index_b][$u]");
						$u++;

						$html_output['peyk_box'] .= '<div class="peyk_box form-group tariff_box mb-3">
				<div class="tariff_box_tool_bar">
				<a href="javascript:void(0)" onclick="peyk_tariff_box(this); refresh_fields_names(this);"><i class="fas fa-plus-square fa-1x text-success"></i></a> 
				<a href="javascript:void(0)" onclick="this.parentElement.parentElement.parentElement.removeChild(this.parentElement.parentElement);"><i class="fas fa-window-close fa-1x text-danger"></i></a>
				</div>
				<div class="container-fluid">
				<div class="row">
				<div class="form-group col-md-2">
				<label>' . lang("state") . '</label>
				<select name="state[]" class="ostan_e_box form-control" onchange="refresh_fields_names(this);">
				'.$state_name.'
				</select>
				</div>
				<div class="form-group col-md-2">
				<label>' . lang("region_name") . '</label>
				<input type="text" class="region_name form-control" name="region_name['.$index_b.'][]" oninput="refresh_fields_names(this);" placeholder="' . lang("region_name") . '" value="'.$region_temp.'">
				</div>
				<div class="form-group col-md-8 peyk_tariff_box">';
						foreach ($value_in as $valintar)
						{
							$p = 0;
							if($valintar != array())
							{
								foreach($valintar as $valintar_tar)
								{
									$from_weight = $this->input->post('from_weight['.$index_b.']['.$index_in.']['.$p.']');
									$to_weight_temp = $this->input->post('to_weight['.$index_b.']['.$index_in.']['.$p.']');
									$delivery_cost = $this->input->post('delivery_cost['.$index_b.']['.$index_in.']['.$p.']');
									$other_costs = $this->input->post('other_costs['.$index_b.']['.$index_in.']['.$p.']');

									if(!is_array($from_weight) && !is_array($to_weight_temp) && !is_array($delivery_cost) && !is_array($other_costs))
									{
										$html_output['peyk_box'] .= ' <div class="row tariff_box_2 mb-3">
				<div class="col">
				<div class="row">
				<div class="col">
				<a href="#" class="close mb-3" data-dismiss="alert" aria-label="close" onclick="this.parentElement.parentElement.parentElement.parentElement.parentElement.removeChild(this.parentElement.parentElement.parentElement.parentElement);">×</a>
				</div>
				</div>
				<div class="row">
				<div class="col-md-3">
				<label>' . lang("from_weight") . '</label>
				<input type="text" class="from_weight form-control" name="from_weight['.$index_b.']['.$index_in.'][]" placeholder="' . lang("from_weight") . '" value="'.$from_weight.'">
				</div>
				<div class="col-md-3">
				<label>' . lang("to_weight") . '</label>
				<input type="text" class="to_weight form-control" name="to_weight['.$index_b.']['.$index_in.'][]" placeholder="' . lang("to_weight") . '" value="'.$to_weight_temp.'">
				</div> <div class="col-md-3">
				<label>' . lang("delivery_cost") . '</label>
				<input type="text" class="delivery_cost form-control" name="delivery_cost['.$index_b.']['.$index_in.'][]" placeholder="' . lang("delivery_cost") . '" value="'.$delivery_cost.'">
				</div> <div class="col-md-3"> <label>' . lang("other_costs") . '</label>
				<input type="text" class="other_costs form-control" name="other_costs['.$index_b.']['.$index_in.'][]" placeholder="' . lang("other_costs") . '" value="'.$other_costs.'">
				</div>
				</div>
				</div>
				</div>';
									}
									$p++;
								}
							}
							break;
						}
						$html_output['peyk_box'] .= '</div>
									</div>
									</div>
									</div>';
					}
				}
			}
		}

		$data['page_name'] = $page_name;
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/shipping/add', $data);
        $this->load->view('template/footer');

    }

    public function bike_delivery()
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

        $data = array('page_name' => 'peyk_delivery_edit');

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/shipping/bike_delivery');
        $this->load->view('template/footer');

    }

    public function express_post()
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

        $data = array('page_name' => 'express_post_delivery_edit');

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/shipping/express_post');
        $this->load->view('template/footer');

    }

    public function registered_post()
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

        $data = array('page_name' => 'registered_post_delivery_edit');

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/shipping/registered_post');
        $this->load->view('template/footer');

    }

    public function status_order()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$main_db_name = "status_order";
		$html_output = array();
		$this->session->set_userdata('page_title', 'وضعیت سفارش');
		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("setting/add_status_order/".$list_items[0]), 'location');
			}
			elseif ($task == 'publish')
			{
				foreach ($list_items as $value)
				{
					if($value == 1)
					{
						$error_msg = "<div>امکان ویرایش وضعیت سفارش <<پرداخت نشده>> وجود ندارد.</div>";
						$this->mylib->set_error($error_msg);
					}
					elseif($value == 2)
					{
						$error_msg = "<div>امکان ویرایش وضعیت سفارش <<لغو شده توسط مشتری>> وجود ندارد.</div>";
						$this->mylib->set_error($error_msg);
					}
					elseif($value == 3)
					{
						$error_msg = "<div>امکان ویرایش وضعیت سفارش <<پرداخت شده>> وجود ندارد.</div>";
						$this->mylib->set_error($error_msg);
					}
					else
					{
						$this->db->set('publish', 'yes');
						$this->db->where('id', $value);
						$this->db->update($main_db_name);
					}
				}
			}
			elseif ($task == 'unpublish')
			{
				foreach ($list_items as $value)
				{
					if($value == 1)
					{
						$error_msg = "<div>امکان ویرایش وضعیت سفارش <<پرداخت نشده>> وجود ندارد.</div>";
						$this->mylib->set_error($error_msg);
					}
					elseif($value == 2)
					{
						$error_msg = "<div>امکان ویرایش وضعیت سفارش <<لغو شده توسط مشتری>> وجود ندارد.</div>";
						$this->mylib->set_error($error_msg);
					}
					elseif($value == 3)
					{
						$error_msg = "<div>امکان ویرایش وضعیت سفارش <<پرداخت شده>> وجود ندارد.</div>";
						$this->mylib->set_error($error_msg);
					}
					else
					{
						$this->db->set('publish', 'no');
						$this->db->where('id', $value);
						$this->db->update($main_db_name);
					}
				}

			}
			elseif ($task == 'delete')
			{
				$this->load->model('setting/status_order_model');

				$this->db->where(array('order_will_be_finished_in_this_status' => 'yes', 'publish' => 'yes'));
				$this->db->select('id');
				$query_4 = $this->db->get($main_db_name);

				$delete_current_item = false;
				foreach ($query_4->result() as $row)
				{
					if(!in_array($row->id, $list_items))
					{
						foreach ($list_items as $value)
						{
							//delete item
							if($value == 1)
							{
								$error_msg = "<div>امکان حذف وضعیت سفارش <<پرداخت نشده>> وجود ندارد.</div>";
								$this->mylib->set_error($error_msg);
							}
							elseif($value == 2)
							{
								$error_msg = "<div>امکان حذف وضعیت سفارش <<لغو شده توسط مشتری>> وجود ندارد.</div>";
								$this->mylib->set_error($error_msg);
							}
							elseif($value == 3)
							{
								$error_msg = "<div>امکان حذف وضعیت سفارش <<پرداخت شده>> وجود ندارد.</div>";
								$this->mylib->set_error($error_msg);
							}
							else
							{
								$this->status_order_model->delete(array('id' => $value));
								//set success message
								$this->mylib->set_success(lang('deleted_successfully'));
							}
							$delete_current_item = true;
						}
						break;
					}
				}

				if($delete_current_item != true)
				{
					$error_msg = "<div>".lang('at_least_one_status_should_point_to_finish')."</div>";
					$this->mylib->set_error($error_msg);
				}
			}
		}

		//Customize by user search keyword
		$search = $this->input->post('search');
		//Search query
		if($search != "")
		{
			$this->db->like('status_order' , $search);
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
				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("setting/add_status_order/".$row->id).'">'. $row->status_order .'</a></td>';
				$temp_html .= '<td>'. lang($row->order_will_be_finished_in_this_status) .'</td>';
                $temp_html .= '<td>'. lang($row->can_customer_cancel_the_order_in_this_situation) .'</td>';
                $temp_html .= '<td>'. lang($row->are_virtual_products_accessible_by_customers_in_this_status) .'</td>';
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

		$data = array
		(
			'page_name' => 'status_order',
			'main_db_name' => $main_db_name,
			'html_output' => $html_output
		);

		$this->load->view('template/header', $data);
		$this->load->view('menu', $data);
		$data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$this->load->view($this->uri->segment(1) . '/status_order/list', $data);
		$this->load->view('template/footer');

    }

    public function add_status_order($item_id = null)
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$main_db_name = "status_order";
		$html_output = array();

		$this->load->model('setting/status_order_model');
		$task = $this->input->post('task');

		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			//Are we editing current_item
			$posted_status_order = $this->input->post('status_order');
			$query = $this->db->get($main_db_name);
			foreach ($query->result() as $row)
			{
				if($item_id==$row->id)
				{
					$item_id_status_order = $row->status_order;

					break;
				}
			}

			//Set Form Rules:
			$this->form_validation->set_rules('status_order', lang('status_order'), 'trim|required|min_length[2]|max_length[100]'.($item_id==null || $posted_status_order!=$item_id_status_order ? "|is_unique[$main_db_name.status_order]" :""));

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			$this->form_validation->set_rules('order_will_be_finished_in_this_status', lang('order_will_be_finished_in_this_status'), 'required|in_list[yes,no]');

            $this->form_validation->set_rules('are_virtual_products_accessible_by_customers_in_this_status', lang('are_virtual_products_accessible_by_customers_in_this_status'), 'required|in_list[yes,no]');

			$this->form_validation->set_rules('can_customer_cancel_the_order_in_this_situation', lang('can_customer_cancel_the_order_in_this_situation'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'status_order' => $this->input->post('status_order'),
					'order_will_be_finished_in_this_status' => $this->input->post('order_will_be_finished_in_this_status'),
                    'are_virtual_products_accessible_by_customers_in_this_status' => $this->input->post('are_virtual_products_accessible_by_customers_in_this_status'),
					'can_customer_cancel_the_order_in_this_situation' => $this->input->post('can_customer_cancel_the_order_in_this_situation'),
					'publish' => $this->input->post('publish')
				);

				if ($item_id)
				{
					if($item_id == 1)
					{
						$error_msg = "<div>امکان ویرایش وضعیت سفارش <<پرداخت نشده>> وجود ندارد.</div>";
						$this->mylib->set_error($error_msg);
					}
					elseif($item_id == 2)
					{
						$error_msg = "<div>امکان ویرایش وضعیت سفارش <<لغو شده توسط مشتری>> وجود ندارد.</div>";
						$this->mylib->set_error($error_msg);
					}
					elseif($item_id == 3)
					{
						$error_msg = "<div>امکان ویرایش وضعیت سفارش <<پرداخت شده>> وجود ندارد.</div>";
						$this->mylib->set_error($error_msg);
					}
					else
					{
						$this->status_order_model->update($item_id, $dadeh);
					}
				}
				else
				{
					$item_id = $this->status_order_model->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					if($item_id != 1 && $item_id != 2 && $item_id != 3)
					{
						$this->mylib->set_success(lang('success_msg'));
					}
				}
				else if ($task == "save_and_close")
				{
					//set success message
					if($item_id != 1 && $item_id != 2 && $item_id != 3)
					{
						$this->mylib->set_success(lang('success_msg'), 'settingstatus_ordersuccess_msg');
					}
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("setting/add_status_order/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("setting/status_order"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("setting/add_status_order"));
				}
			}
		}


		//It's Edit state, then we need to simulate edit for user
		if ($item_id)
		{
			$this->session->set_userdata('page_title', 'ویرایش وضعیت سفارش');
			$page_name = 'edit_status_order';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
		}
		else
		{
			$this->session->set_userdata('page_title', 'افزودن وضعیت سفارش');
			$page_name = 'add_status_order';
			//Default Item Data
			$html_output['item_data'] = array(
				"id" => '',
				"status_order" => '',
				"order_will_be_finished_in_this_status" => 'yes',
				"are_virtual_products_accessible_by_customers_in_this_status" => 'yes',
				"can_customer_cancel_the_order_in_this_situation" => 'yes',
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
        $this->load->view($this->uri->segment(1) . '/status_order/add', $data);
        $this->load->view('template/footer');
    }

    public function main_settings($item_id = 1)
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$main_db_name = "setting";
		$html_output = array();
		$task = $this->input->post('task');
		$this->load->model('setting/main_settings_model');
		if($task == 'save')
		{
			//Set Form Rules:
			$this->form_validation->set_rules('store_name', lang('store_name'), 'trim|required|min_length[2]|max_length[100]');

			$this->form_validation->set_rules('img_width', lang('img_width'), 'trim|required|max_length[5]|is_natural|greater_than[0]');

			$this->form_validation->set_rules('img_height', lang('img_height'), 'trim|required|max_length[5]|is_natural|greater_than[0]');

			$this->form_validation->set_rules('thumb_width', lang('thumb_width'), 'trim|required|max_length[5]|is_natural|greater_than[0]');

			$this->form_validation->set_rules('thumb_height', lang('thumb_height'), 'trim|required|max_length[5]|is_natural|greater_than[0]');

			$this->form_validation->set_rules('admin_email', lang('admin_email'), 'trim|valid_email|required|min_length[6]|max_length[100]');

			$this->form_validation->set_rules('system_email', lang('system_email'), 'trim|valid_email|required|min_length[6]|max_length[100]');

            $flag_array = json_encode(array('subject_for_confirmation_code_email', '{{usage_title}}'));
            $this->form_validation->set_rules('subject_for_confirmation_code_email', lang('subject_for_confirmation_code_email'), 'trim|required|max_length[100]|callback__check_flag_exist['.$flag_array.']');

            $flag_array = json_encode(array('message_text_for_confirmation_code_email', '{{usage_title}}', '{{code}}'));
            $this->form_validation->set_rules('message_text_for_confirmation_code_email', lang('message_text_for_confirmation_code_email'), 'trim|required|callback__check_flag_exist['.$flag_array.']');

            $flag_array = json_encode(array('message_text_for_confirmation_code_sms', '{{usage_title}}', '{{code}}'));
            $this->form_validation->set_rules('message_text_for_confirmation_code_sms', lang('message_text_for_confirmation_code_sms'), 'trim|required|max_length[90]|callback__check_flag_exist['.$flag_array.']');

            $flag_array = json_encode(array('subject_for_order_status_email', '{{order_code}}'));
            $this->form_validation->set_rules('subject_for_order_status_email', lang('subject_for_order_status_email'), 'trim|required|max_length[100]|callback__check_flag_exist['.$flag_array.']');

            $flag_array = json_encode(array('message_text_for_order_status_email', '{{name}}', '{{order_code}}', '{{time}}', '{{condition_name}}', '{{description}}'));
            $this->form_validation->set_rules('message_text_for_order_status_email', lang('message_text_for_order_status_email'), 'trim|required|callback__check_flag_exist['.$flag_array.']');

			if ($this->form_validation->run() == TRUE)
			{
				$dadeh = array
				(
					'store_name' => $this->input->post('store_name'),
					'img_width' => $this->input->post('img_width'),
					'img_height' => $this->input->post('img_height'),
					'thumb_width' => $this->input->post('thumb_width'),
					'thumb_height' => $this->input->post('thumb_height'),
					'admin_email' => $this->input->post('admin_email'),
					'system_email' => $this->input->post('system_email'),
                    'subject_for_confirmation_code_email' => $this->input->post('subject_for_confirmation_code_email'),
                    'message_text_for_confirmation_code_email' => $this->input->post('message_text_for_confirmation_code_email'),
                    'message_text_for_confirmation_code_sms' => $this->input->post('message_text_for_confirmation_code_sms'),
                    'subject_for_order_status_email' => $this->input->post('subject_for_order_status_email'),
                    'message_text_for_order_status_email' => $this->input->post('message_text_for_order_status_email')
				);
				$this->main_settings_model->update($item_id, $dadeh);

				if ($task == "save")
				{
					$this->mylib->set_success(lang('success_msg'));
					redirect(base_url("setting/main_settings/".$item_id));
				}
			}
		}

		$this->session->set_userdata('page_title', 'تنطیمات اصلی');
		$this->db->where('id', $item_id);
		$query = $this->db->get($main_db_name);
		$html_output['item_data'] = $query->row_array();

		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$data['page_name'] = 'main_settings';
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/main_settings/main_settings', $data);
        $this->load->view('template/footer');
    }

    function _check_flag_exist($str = null, $flag_array = null)
    {
        $flag_array = json_decode($flag_array, true);
        if($str != null && $str != '' && is_array($flag_array))
        {
            $flag_str = '';
            foreach($flag_array as $index => $value)
            {
                if($index == 0)
                {
                    $input_name = lang($value);
                }
                else
                {
                    $flag_position = strpos($str, $value);
                    if(!is_numeric($flag_position))
                    {
                        $flag_str .= " $value";
                    }
                }
            }
            if(isset($input_name) && $flag_str != '')
            {
                $this->form_validation->set_message('_check_flag_exist', "فیلد $input_name باید شامل موارد $flag_str باشد.");
                return false;
            }
            else
            {
                return true;
            }
        }
    }
}
