<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

	/**
	 * Dar in safhe menu ra modiriat (ADD, DELETE, EDIT) mikonim
	 */
    public function index()
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$this->session->set_userdata('page_title', 'منو');
        $main_db_name = "menu";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("menu/add/".$list_items[0]), 'location');
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
				$this->load->model('menu/menu_model');

				foreach ($list_items as $value)
				{
					$delete_current_item = true;

					//get all categories that has this item as their parent
					$children_list = $this->menu_model->get_where(array('parent_id'=>$value));
					$current_item = $this->menu_model->get_where(array('id'=>$value))->row();

					if($current_item->type == 'home')
					{
						$delete_current_item = false;
						$error_msg = '<div>منویی که نوع آن home است نباید حذف شود.</div>';
						$this->mylib->set_error($error_msg);
					}
					else
					{
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
					}

					if ($delete_current_item)
					{
						//delete item
						$this->menu_model->delete(array('id' => $value));
						//set success message
						$this->mylib->set_success(lang('deleted_successfully'));
					}
				}
			}
		}

		//Customize by user search keyword
		$search = $this->input->post('search');
		//Search query
		if($search != "")
		{
			$this->db->like('title' , $search);
			$this->db->or_like('title_alias_url' , $search);
			$this->db->or_like('type' , $search);
			$this->db->or_like('meta_tag_title' , $search);
			$this->db->or_like('meta_tag_keywords' , $search);
			$this->db->or_like('meta_tag_description' , $search);
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
		$this->db->order_by('modify_date', 'DESC');
		$this->db->limit($per_page, $page);
		$query = $this->db->get($main_db_name);

		$this->load->model('menu/menu_category');
		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$parent_row = $row->category_id;
				$parent_title = $this->menu_category->find_parent(array('id'=>$parent_row));

				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("menu/add/".$row->id).'">'. $row->title .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("menu/add_categories/".$row->category_id).'">'. $parent_title .'</a></td>';
				$temp_html .= '<td>'.lang($row->type).'</td>';
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

		$data['page_name'] = 'menu';
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

		$main_db_name = "menu";
		$html_output = array();
		$target = '';

		$this->load->model('menu/menu_model');
		$this->load->model('menu/menu_category');
		$this->load->model('articles/content');
		$this->load->model('articles/categories');
		$this->load->model('products/add_products_model');
		$this->load->model('products/products_category_model');
		$task = $this->input->post('task');

		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			//Are we editing current_item
			$posted_title_alias_url = $this->input->post('title_alias_url');
			$query = $this->db->get($main_db_name);
			foreach ($query->result() as $row)
			{
				if($item_id==$row->id)
				{
					$item_id_title_alias_url = $row->title_alias_url;

					break;
				}
			}

			//Set Form Rules:
			$this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[2]|max_length[200]');

			$this->form_validation->set_rules('title_alias_url', lang('title_alias_url'), 'trim|min_length[1]|max_length[200]'.($item_id==null || $posted_title_alias_url!=$item_id_title_alias_url ? "|is_unique[$main_db_name.title_alias_url]" :""));

			$this->form_validation->set_rules('sort', lang('sort'), 'trim|is_natural|max_length[4]');

			$this->form_validation->set_rules('parent_id', lang('parent'),$this->menu_model->get_inlist_string($item_id));

			$this->form_validation->set_rules('category_id', lang('menu_category'),'required|'. $this->menu_category->get_inlist_string());

			$callback_is_home = '';
			if($this->input->post('type') == 'home')
			{
				$this->db->where(array('type' => 'home'));
				$this->db->select('id');
				$temp_ins = $this->db->get('menu')->row();
				if(isset($temp_ins))
				{
					if(count($temp_ins) == 1 && isset($item_id))
					{
						if($item_id == $temp_ins->id)
						{
							//do_nothing;
						}
						else
						{
							$callback_is_home = '|callback__is_home';
						}
					}
					elseif(count($temp_ins) == 1 && !isset($item_id))
					{
						$callback_is_home = '|callback__is_home';
					}
					elseif(count($temp_ins) > 1)
					{
						$callback_is_home = '|callback__is_home';
					}
				}
			}

			$this->form_validation->set_rules('type', lang('type'),'required|in_list[logout,login,profile,register,single_page,home,single_category,site_map,single_product,single_product_category,link]'.$callback_is_home);

			$required_list_pages = '';
			if($this->input->post('type') == 'single_page')
			{
				$required_list_pages = 'required|';
				$target = $this->input->post('list_pages');
			}
			$this->form_validation->set_rules('list_pages', lang('list_pages'),$required_list_pages . $this->content->get_inlist_string());

			$required_list_categories = '';
			if($this->input->post('type') == 'single_category')
			{
				$required_list_categories = 'required|';
				$target = $this->input->post('list_categories');
			}
			$this->form_validation->set_rules('list_categories', lang('list_categories'),$required_list_categories . $this->categories->get_inlist_string());

			$required_list_products = '';
			if($this->input->post('type') == 'single_product')
			{
				$required_list_products = 'required|';
				$target = $this->input->post('list_products');
			}
			$this->form_validation->set_rules('list_products', lang('list_products'),$required_list_products. $this->add_products_model->get_inlist_string());

			$required_products_categories = '';
			if($this->input->post('type') == 'single_product_category')
			{
				$required_products_categories = 'required|';
				$target = $this->input->post('products_categories');
			}
			$this->form_validation->set_rules('products_categories', lang('products_categories'),$required_products_categories. $this->products_category_model->get_inlist_string());

			$required_link_address = '';
			$callback_check_valid_url = '';
			if($this->input->post('type') == 'link')
			{
				$required_link_address = '|required';
				$callback_check_valid_url = '|callback__check_valid_url';
			}
			$this->form_validation->set_rules('link_address', lang('link_address'), 'trim'.$required_link_address.$callback_check_valid_url);

			$this->form_validation->set_rules('page_open_type', lang('page_open_type'), 'trim|in_list[open_in_new_window,open_in_this_window]'.$required_link_address);

			$this->form_validation->set_rules('access', lang('access_level'), 'trim|required|in_list[all,guest,registered]');

			$this->form_validation->set_rules('class', lang('add_class'), 'trim');

			$this->form_validation->set_rules('icon', lang('add_icon'), 'trim');

			$required_position_icon = '';
			if($this->input->post('icon') != '' && $this->input->post('icon') != null)
			{
				$required_position_icon = '|required';
			}
			$this->form_validation->set_rules('icon_position', lang('position_icon'), 'trim|in_list[left,right]'.$required_position_icon);

			$this->form_validation->set_rules('meta_tag_title', lang('meta_tag_title'), 'trim|min_length[2]|max_length[200]');

			$this->form_validation->set_rules('meta_tag_keywords', lang('meta_tag_keywords'), 'trim|min_length[2]|max_length[1000]');

			$this->form_validation->set_rules('meta_tag_description', lang('meta_tag_description'), 'trim|max_length[300]');

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$temp_attribute = array();
				$temp_attribute['access'] = $this->input->post('access');
				$temp_attribute['class'] = $this->input->post('class');
				$temp_attribute['icon'] = $this->input->post('icon');
				$temp_attribute['icon_position'] = $this->input->post('icon_position');
				$temp_attribute['link_address'] = $this->input->post('link_address');
				$temp_attribute['page_open_type'] = $this->input->post('page_open_type');
				$temp_attribute = json_encode($temp_attribute);

				$title_alias = $this->input->post('title_alias_url');
				if(isset($title_alias) && $title_alias != '' && $title_alias != null)
				{
					$title_alias = preg_replace('/(\s|[^\p{Arabic}\w0-9\-])+/u', '-', $title_alias);
				}
				else
				{
					$title_alias = $this->input->post('title');
					$title_alias = preg_replace('/(\s|[^\p{Arabic}\w0-9\-])+/u', '-', $title_alias);
				}
				$dadeh = array
				(
					'title' => $this->input->post('title'),
					'title_alias_url' => $title_alias,
					'sort' => $this->input->post('sort'),
					'parent_id' => $this->input->post('parent_id'),
					'category_id' => $this->input->post('category_id'),
					'type' => $this->input->post('type'),
					'list_pages' => $this->input->post('list_pages'),
					'list_categories' => $this->input->post('list_categories'),
					'list_products' => $this->input->post('list_products'),
					'products_categories' => $this->input->post('products_categories'),
					'target_id' => $target,
					'meta_tag_title' => $this->input->post('meta_tag_title'),
					'meta_tag_keywords' => $this->input->post('meta_tag_keywords'),
					'meta_tag_description' => $this->input->post('meta_tag_description'),
					'attribute' => $temp_attribute,
					'publish' => $this->input->post('publish')
				);

				if ($item_id)
				{
					$dadeh['modify_date'] = time();
					$this->menu_model->update($item_id, $dadeh);
				}
				else
				{
					$dadeh['modify_date'] = time();
					$dadeh['insert_date'] = time();
					$item_id = $this->menu_model->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'menuindexsuccess_msg');
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("menu/add/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("menu/index"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("menu/add"));
				}
			}
		}


		//It's Edit state, then we need to simulate edit for user
		if ($item_id)
		{
			$this->session->set_userdata('page_title', 'ویرایش منو');
			$page_name = 'edit_menu';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();

			$temp_attribute_query = json_decode($html_output['item_data']['attribute']);
			if(isset($temp_attribute_query->access))
			{
				$html_output['item_data']['access'] = $temp_attribute_query->access;
			}
			else
			{
				$html_output['item_data']['access'] = '';
			}
			if(isset($temp_attribute_query->class))
			{
				$html_output['item_data']['class'] = $temp_attribute_query->class;
			}
			else
			{
				$html_output['item_data']['class'] = '';
			}
			if(isset($temp_attribute_query->icon))
			{
				$html_output['item_data']['icon'] = $temp_attribute_query->icon;
			}
			else
			{
				$html_output['item_data']['icon'] = '';
			}
			if(isset($temp_attribute_query->icon_position))
			{
				$html_output['item_data']['icon_position'] = $temp_attribute_query->icon_position;
			}
			else
			{
				$html_output['item_data']['icon_position'] = '';
			}
			if(isset($temp_attribute_query->link_address))
			{
				$html_output['item_data']['link_address'] = $temp_attribute_query->link_address;
			}
			else
			{
				$html_output['item_data']['link_address'] = '';
			}
			if(isset($temp_attribute_query->page_open_type))
			{
				$html_output['item_data']['page_open_type'] = $temp_attribute_query->page_open_type;
			}
			else
			{
				$html_output['item_data']['page_open_type'] = '';
			}
		}
		else
		{
			$this->session->set_userdata('page_title', 'افزودن منو');
			$page_name = 'add_menu';
			//Default Item Data
			$html_output['item_data'] = array(
				"id" => '',
				"title" => '',
				"title_alias_url" => '',
				"sort" => '',
				"parent_id" => '',
				"category_id" => '',
				"type" => '',
				"list_pages" => '',
				"list_categories" => '',
				"list_products" => '',
				"products_categories" => '',
				"target_id" => '',
				"link_address" => '',
				"page_open_type" => '',
				"access" => '',
				"class" => '',
				"icon" => '',
				"icon_position" => '',
				"meta_tag_title" => '',
				"meta_tag_keywords" => '',
				"meta_tag_description" => '',
				"publish" => 'yes'
			);
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of parent /////
		////////////////////////////////////////////////////
		$this->db->where(array('publish' => 'yes'));
		$parent_list = $this->db->get('menu')->result();
		$html_output['parent_list'] = "<option value=''>".lang ('without_parent')."</option>";

		if (count($parent_list) > 0)
		{
			$parent = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->menu_model->get_where(array('id'=>$item_id))->row();
				$parent = $current_item->parent_id;
			}

			foreach ($parent_list as $row)
			{
				$html_output['parent_list'] .= '<option value="'.$row->id.'" '.set_select('parent_id', $row->id, ($row->id == $parent ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['parent_list'] == '')
		{
			$html_output['parent_list'] = "<option value=''>".lang ('no_category')."</option>";
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of menu_category
		////////////////////////////////////////////////////
		$this->db->where(array('publish' => 'yes'));
		$menu_category_list = $this->db->get('menu_category')->result();
		$html_output['menu_category_list'] = "<option value=''>".lang ('without_category')."</option>";

		if (count($menu_category_list) > 0)
		{
			$menu_category = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->menu_model->get_where(array('id'=>$item_id))->row();
				$menu_category = $current_item->category_id;
			}

			foreach ($menu_category_list as $row)
			{
				$html_output['menu_category_list'] .= '<option value="'.$row->id.'" '.set_select('category_id', $row->id, ($row->id == $menu_category ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['menu_category_list'] == '')
		{
			$html_output['menu_category_list'] = "<option value=''>".lang ('no_category')."</option>";
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of type ////////
		////////////////////////////////////////////////////
		$html_output['type_list'] = "<option value=''>".lang ('please_select')."</option>";

		$type = '';
		if(! is_null($item_id))
		{
			$current_item = $this->menu_model->get_where(array('id'=>$item_id))->row();
			$type = $current_item->type;
		}

		$html_output['type_list'] .=
			'<option value="logout" '.set_select('type', 'logout', ('logout' == $type ? true : false)).'>'.lang("logout").'</option>'
			.'<option value="login" '.set_select('type', 'login', ('login' == $type ? true : false)).'>'.lang("login").'</option>'
			.'<option value="profile" '.set_select('type', 'profile', ('profile' == $type ? true : false)).'>'.lang("profile").'</option>'
			.'<option value="register" '.set_select('type', 'register', ('register' == $type ? true : false)).'>'.lang("register").'</option>'
			.'<option value="single_page" '.set_select('type', 'single_page', ('single_page' == $type ? true : false)).'>'.lang("single_page").'</option>'
			.'<option value="home" '.set_select('type', 'home', ('home' == $type ? true : false)).'>'.lang("home").'</option>'
			.'<option value="single_category" '.set_select('type', 'single_category', ('single_category' == $type ? true : false)).'>'.lang("single_category").'</option>'
			.'<option value="site_map" '.set_select('type', 'site_map', ('site_map' == $type ? true : false)).'>'.lang("site_map").'</option>'
			.'<option value="single_product" '.set_select('type', 'single_product', ('single_product' == $type ? true : false)).'>'.lang("single_product").'</option>'
			.'<option value="single_product_category" '.set_select('type', 'single_product_category', ('single_product_category' == $type ? true : false)).'>'.lang("single_product_category").'</option>'
			.'<option value="link" '.set_select('type', 'link', ('link' == $type ? true : false)).'>'.lang("link").'</option>';

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of list_pages //
		////////////////////////////////////////////////////
		$this->db->where(array('publish' => 'yes'));
		$pages_list = $this->db->get('articles')->result();
		$html_output['pages_list'] = "<option value=''>".lang ('without_category')."</option>";

		if (count($pages_list) > 0)
		{
			$list_pages = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->menu_model->get_where(array('id'=>$item_id))->row();
				$list_pages = $current_item->list_pages;
			}

			foreach ($pages_list as $row)
			{
				$html_output['pages_list'] .= '<option value="'.$row->id.'" '.set_select('list_pages', $row->id, ($row->id == $list_pages ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['pages_list'] == '')
		{
			$html_output['pages_list'] = "<option value=''>".lang ('no_category')."</option>";
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of list_categories
		////////////////////////////////////////////////////
		$this->db->where(array('publish' => 'yes'));
		$category_list = $this->db->get('article_categories')->result();
		$html_output['categories_list'] = "<option value=''>".lang ('without_category')."</option>";

		if (count($category_list) > 0)
		{
			$list_categories = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->menu_model->get_where(array('id'=>$item_id))->row();
				$list_categories = $current_item->list_categories;
			}

			foreach ($category_list as $row)
			{
				$html_output['categories_list'] .= '<option value="'.$row->id.'" '.set_select('list_categories', $row->id, ($row->id == $list_categories ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['categories_list'] == '')
		{
			$html_output['categories_list'] = "<option value=''>".lang ('no_category')."</option>";
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of list_products
		////////////////////////////////////////////////////
		$this->db->group_start();
		$this->db->where(array('publish' => 'yes'));
		$this->db->group_end();
		$this->db->group_start();
		$this->db->where(array('type_of_category' => 'virtual'));
		$this->db->or_where(array('number >' => 0));
		$this->db->or_where(array('finish' => 2));
		$this->db->or_where(array('finish' => 3));
		$this->db->group_end();
		$products_list = $this->db->get('add_products')->result();
		$html_output['products_list'] = "<option value=''>".'لطفا محصول مورد نظر را انتخاب کنید'."</option>";

		if (count($products_list) > 0)
		{
			$list_products = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->menu_model->get_where(array('id'=>$item_id))->row();
				$list_products = $current_item->list_products;
			}

			foreach ($products_list as $row)
			{
				$html_output['products_list'] .= '<option value="'.$row->id.'" '.set_select('list_products', $row->id, ($row->id == $list_products ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['products_list'] == '')
		{
			$html_output['products_list'] = "<option value=''>".lang ('no_category')."</option>";
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of products_categories
		////////////////////////////////////////////////////
		$this->db->where(array('publish' => 'yes'));
		$products_categories_list = $this->db->get('products_category')->result();
		$html_output['products_categories_list'] = "<option value=''>".lang ('without_category')."</option>";

		if (count($products_categories_list) > 0)
		{
			$products_categories = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->menu_model->get_where(array('id'=>$item_id))->row();
				$products_categories = $current_item->products_categories;
			}

			foreach ($products_categories_list as $row)
			{
				$html_output['products_categories_list'] .= '<option value="'.$row->id.'" '.set_select('products_categories', $row->id, ($row->id == $products_categories ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['products_categories_list'] == '')
		{
			$html_output['products_categories_list'] = "<option value=''>".lang ('no_category')."</option>";
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

    function _check_valid_url($param)
	{
		if($param == '#')
		{
			return TRUE;
		}
		$space_pos = strpos($param,' ');
		if($space_pos != '')
		{
			$this->form_validation->set_message('_check_valid_url', 'لینک آدرس نباید خط فاصله داشته باشد.');
			return FALSE;
		}

		if (!filter_var($param, FILTER_VALIDATE_URL))
		{
			$this->form_validation->set_message('_check_valid_url', 'لینک وارد شده معتبر نمیباشد, ابتدای لینک باید //:https یا //:http باشد و لینک وارد شده نباید حروف فارسی داشته باشد.');
			return FALSE;
		}

		$pos_1 = strpos($param,'.');
		if(isset($pos_1))
		{
			$pos_2 = strpos($param,'.',$pos_1+1);
			if(isset($pos_2))
			{
				$pos_3 = strpos($param,'.',$pos_2+1);
			}
		}
		$pos_w = strpos($param,'w');
		$pos_w_2 = strpos($param,'w',$pos_w+1);
		$pos_w_3 = strpos($param,'w',$pos_w_2+1);

		if($pos_1 != '' && $pos_2 == 0 && $pos_2 == '')
		{
			if($pos_w_2 == $pos_w+1 && $pos_w_3 == $pos_w_2+1)
			{
				$this->form_validation->set_message('_check_valid_url', 'لینک آدرس وارد شده بدون دامنه است.');
				return FALSE;
			}
			else
			{
				return TRUE;
			}

		}
		elseif($pos_1 != '' && isset($pos_2))
		{
			if($pos_2 != '' && $pos_3 == '')
			{
				if($pos_w != '' || $pos_w == 0)
				{
					return TRUE;
				}
				else
				{
					$this->form_validation->set_message('_check_valid_url', 'لینک وارد شده معتبر نمیباشد.');
					return FALSE;
				}

			}
			else
			{
				$this->form_validation->set_message('_check_valid_url', 'لینک آدرس حداکثر میتواند دو نقطه داشته باشد.');
				return FALSE;
			}
		}
		else
		{
			$this->form_validation->set_message('_check_valid_url', 'لینک وارد شده معتبر نمیباشد.');
			return FALSE;
		}
	}

	function _is_home()
	{
		$this->form_validation->set_message('_is_home', 'فقط یک منو میتواند نوع home داشته باشد. در دیتابیس منویی با نوع home وجود دارد.');
		return FALSE;
	}

    public function categories()
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$this->session->set_userdata('page_title', 'دسته‌بندی‌ منو');
		$main_db_name = "menu_category";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("menu/add_categories/".$list_items[0]), 'location');
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
				$this->load->model('menu/menu_category');

				$this->db->select('menu_category.id AS menu_category_id,menu.id AS menu_id');
				$this->db->from('menu_category');
				$this->db->join('menu', 'menu_category.id=menu.category_id', 'left');
				$query_join = $this->db->get()->result();

				$this->db->select('title,id');
				$menu_category_p_title_list = $this->db->get('menu_category')->result();

				foreach ($list_items as $value)
				{
					$delete_current_item = true;
					$menu_er = '';
					$menu_des = '';
					foreach($query_join as $join_row)
					{
						if($join_row->menu_category_id == $value)
						{
							if($join_row->menu_id != '' && $join_row->menu_id != null)
							{
								$menu_er = 'تعدادی منو';
								$menu_des = 'منوهای';
							}
						}
					}

					if($menu_er != '')
					{
						$cat_title = '';
						foreach($menu_category_p_title_list as $cat_tit)
						{
							if($cat_tit->id == $value)
							{
								$cat_title = $cat_tit->title;
								break;
							}
						}
						$delete_current_item = false;
						$error_msg = "<div>$menu_er , در دسته‌بندی <<$cat_title>> وجود دارد. لطفا ابتدا, $menu_des  موجود را حذف نمایید.</div>";
						$this->mylib->set_error($error_msg);
					}

					if ($delete_current_item)
					{
						//delete item
						$this->menu_category->delete(array('id' => $value));
						//set success message
						$this->mylib->set_success(lang('deleted_successfully'));
					}
				}
			}
		}

		//Customize by user search keyword
		$search = $this->input->post('search');
		//Search query
		if($search != "")
		{
			$this->db->like('title' , $search);
			$this->db->or_like('title_alias_url' , $search);
			$this->db->or_like('meta_tag_title' , $search);
			$this->db->or_like('meta_tag_keywords' , $search);
			$this->db->or_like('meta_tag_description' , $search);
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
				$temp_html .= '<td><a href="'.base_url("menu/add_categories/".$row->id).'">'. $row->title .'</a></td>';
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

		$data['page_name'] = 'menu_category';
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/category/list', $data);
        $this->load->view('template/footer');

    }

    public function add_categories($item_id = null)
    {
        if (! $this->mylib->is_login())
        {
            $this->mylib->is_login(true);
        }

		$main_db_name = "menu_category";
		$html_output = array();

		$this->load->model('menu/menu_category');
		$task = $this->input->post('task');

		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			//Are we editing current_item
			$posted_title_alias_url = $this->input->post('title_alias_url');
			$query = $this->db->get($main_db_name);
			foreach ($query->result() as $row)
			{
				if($item_id==$row->id)
				{
					$item_id_title_alias_url = $row->title_alias_url;
					break;
				}
			}

			//Set Form Rules:
			$this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[2]|max_length[200]');

			$this->form_validation->set_rules('title_alias_url', lang('title_alias_url'), 'trim|min_length[1]|max_length[200]'.($item_id==null || $posted_title_alias_url!=$item_id_title_alias_url ? "|is_unique[$main_db_name.title_alias_url]" :""));

			$this->form_validation->set_rules('meta_tag_title', lang('meta_tag_title'), 'trim|min_length[2]|max_length[200]');

			$this->form_validation->set_rules('meta_tag_keywords', lang('meta_tag_keywords'), 'trim|min_length[2]|max_length[1000]');

			$this->form_validation->set_rules('meta_tag_description', lang('meta_tag_description'), 'trim|max_length[300]');

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				$title_alias = $this->input->post('title_alias_url');
				if(isset($title_alias) && $title_alias != '' && $title_alias != null)
				{
					$title_alias = preg_replace('/(\s|[^\p{Arabic}\w0-9\-])+/u', '-', $title_alias);
				}
				else
				{
					$title_alias = $this->input->post('title');
					$title_alias = preg_replace('/(\s|[^\p{Arabic}\w0-9\-])+/u', '-', $title_alias);
				}
				$dadeh = array
				(
					'title' => $this->input->post('title'),
					'title_alias_url' => $title_alias,
					'meta_tag_title' => $this->input->post('meta_tag_title'),
					'meta_tag_keywords' => $this->input->post('meta_tag_keywords'),
					'meta_tag_description' => $this->input->post('meta_tag_description'),
					'publish' => $this->input->post('publish')
				);

				if ($item_id)
				{
					//we should update this $edit_id into the database
					$this->menu_category->update($item_id, $dadeh);
				}
				else
				{
					//this is new Item
					$item_id = $this->menu_category->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'menucategoriessuccess_msg');
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("menu/add_categories/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("menu/categories"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("menu/add_categories"));
				}
			}
		}


		//It's Edit state, then we need to simulate edit for user
		if ($item_id)
		{
			$data['page_name'] = 'edit_category';
			$this->session->set_userdata('page_title', 'ویرایش دسته‌بندی');
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
		}
		else
		{
			$data['page_name'] = 'add_categories';
			$this->session->set_userdata('page_title', 'افزودن دسته‌بندی');
			//Default Item Data
			$html_output['item_data'] = array(
				"id" => '',
				"title" => '',
				"title_alias_url" => '',
				"meta_tag_title" => '',
				"meta_tag_keywords" => '',
				"meta_tag_description" => '',
				"publish" => 'yes'
			);
		}

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
}
