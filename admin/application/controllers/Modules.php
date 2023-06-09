<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modules extends CI_Controller {

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

		$this->session->set_userdata('page_title', 'لیست ماژول‌ها');
		$main_db_name = "modules";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("modules/add/".$list_items[0]), 'location');
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
				$this->load->model('modules_model');

				$del_1 = 0;
				foreach ($list_items as $value)
				{
					$del_1 = 1;
				}

				if($del_1 == 1)
				{
					$i = 1;
					$this->db->select('target,type,files_id');
					foreach ($list_items as $value)
					{
						if($i == 1)
						{
							$this->db->where(array('id' => $value));
						}
						else
						{
							$this->db->or_where(array('id' => $value));
						}
						$i++;
					}
					$del_pic_query = $this->db->get('modules');
					$files_db_query = $this->db->get('files')->result();
					foreach($del_pic_query->result() as $del_pic_row)
					{
						//delete custom html file
						if($del_pic_row->type == 'custom_html')
						{
							$files_id_array = json_decode($del_pic_row->files_id);
							if(is_array($files_id_array))
							{
								foreach($files_id_array as $fi_row)
								{
									foreach($files_db_query as $files_db_row)
									{
										if($files_db_row->id == $fi_row)
										{
											//delete file
											$temp_delete_file_src = '../content/'.$files_db_row->target.'/'.$files_db_row->name;
											if (file_exists($temp_delete_file_src))
											{
												unlink($temp_delete_file_src);
											}
											break;
										}
									}
									//delete file from database
									$this->db->delete('files', array('id' => $fi_row));
								}
							}
						}
						//delete slide files
						elseif($del_pic_row->type == 'slide')
						{
							$jason_slides = json_decode($del_pic_row->target);
							if(isset($jason_slides->slides))
							{
								foreach($jason_slides->slides as $slides_row)
								{
									if(isset($slides_row->pic))
									{
										$temp_pic_del_src = '../content/products/'.$slides_row->pic;
										if (file_exists($temp_pic_del_src))
										{
											unlink($temp_pic_del_src);
										}
									}
								}
							}
						}
					}
				}

				foreach ($list_items as $value)
				{
					//delete item
					$this->modules_model->delete(array('id' => $value));
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


				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("modules/add/".$row->id).'">'. $row->title .'</a></td>';
				$temp_html .= '<td>'. $insert_date .'</td>';
				$temp_html .= '<td>'. $modify_date .'</td>';
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

		$data['page_name'] = 'modules_list';
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

		$main_db_name = "modules";
		$html_output = array();
		$this->load->model('modules_model');
		$this->load->model('menu/menu_category');
		$this->load->model('articles/categories');
		$task = $this->input->post('task');

		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			//Set Form Rules:
			$this->form_validation->set_rules('sort', lang('sort'), 'trim|is_natural|max_length[4]');

			$this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[2]|max_length[200]');

			$this->form_validation->set_rules('type', lang('type'), 'required|in_list[custom_html,menu,slide,latest,weekly_discount,selected_items,cart,simple_search,map,selected_article_category,most_popular,best_sales]');

			$slide_config_required = '';
			if($this->input->post('type') == 'latest' || $this->input->post('type') == 'weekly_discount' || $this->input->post('type') == 'selected_items' || $this->input->post('type') == 'most_popular' || $this->input->post('type') == 'best_sales')
			{
				$slide_config_required = 'required|';
			}
			$this->form_validation->set_rules('view_in_1024', lang('view_in_1024'), $slide_config_required.'trim|is_natural|greater_than[0]');
			$this->form_validation->set_rules('view_in_768', lang('view_in_768'), $slide_config_required.'trim|is_natural|greater_than[0]');
			$this->form_validation->set_rules('view_in_640', lang('view_in_640'), $slide_config_required.'trim|is_natural|greater_than[0]');
			$this->form_validation->set_rules('view_in_320', lang('view_in_320'), $slide_config_required.'trim|is_natural|greater_than[0]');
			$this->form_validation->set_rules('group_in_1024', lang('group_in_1024'), $slide_config_required.'trim|is_natural|greater_than[0]');
			$this->form_validation->set_rules('group_in_768', lang('group_in_768'), $slide_config_required.'trim|is_natural|greater_than[0]');
			$this->form_validation->set_rules('group_in_640', lang('group_in_640'), $slide_config_required.'trim|is_natural|greater_than[0]');
			$this->form_validation->set_rules('group_in_320', lang('group_in_320'), $slide_config_required.'trim|is_natural|greater_than[0]');
			$this->form_validation->set_rules('space_between', lang('space_between'), $slide_config_required.'trim|is_natural');
			$this->form_validation->set_rules('loop', lang('loop'), $slide_config_required.'trim|in_list[true,false]');
			$this->form_validation->set_rules('loop_fill_group_with_blank', lang('loop_fill_group_with_blank'), $slide_config_required.'trim|in_list[true,false]');

			$content_required = '';
			if($this->input->post('type') == 'custom_html')
			{
				$content_required = 'required|';
			}
			$this->form_validation->set_rules('content', lang('content'), $content_required.'trim');

			$menu_categories_required = '';
			if($this->input->post('type') == 'menu')
			{
				$menu_categories_required = 'required|';
			}
			$this->form_validation->set_rules('menu_categories', lang('menu_categories'), $menu_categories_required. $this->menu_category->get_inlist_string());

			$slide_required = '';
			if($this->input->post('type') == 'slide')
			{
				$slide_required = 'required|';
			}
			$this->form_validation->set_rules('slide_width', lang('slide_width'), $slide_required.'trim|is_natural');
			$this->form_validation->set_rules('slide_height', lang('slide_height'), $slide_required.'trim|is_natural');

			$selected_item_required = '';
			if($this->input->post('type') == 'selected_items')
			{
				$selected_item_required = 'required|';
			}
			$this->form_validation->set_rules('selected_product_item[]', 'انتخاب محصول', $selected_item_required.'trim');

			$number_of_items_required = '';
			if($this->input->post('type') == 'latest' || $this->input->post('type') == 'weekly_discount' || $this->input->post('type') == 'most_popular' || $this->input->post('type') == 'best_sales')
			{
				$number_of_items_required = 'required|';
			}
			$this->form_validation->set_rules('number_of_items', 'تعداد کل آیتمها', $number_of_items_required.'trim|is_natural|greater_than[1]');

			$number_of_items_per_page_required = '';
			if($this->input->post('type') == 'selected_article_category')
			{
				$number_of_items_per_page_required = 'required|';
			}
			$this->form_validation->set_rules('number_of_items_per_page', lang('number_of_items_per_page'), $number_of_items_per_page_required.'trim|is_natural|greater_than[0]|less_than[11]');

			$show_type_required = '';
			if($this->input->post('type') == 'selected_items')
			{
				$show_type_required = 'required|';
			}
			$this->form_validation->set_rules('show_type', lang('show_type'), $show_type_required.'trim|in_list[slide,descriptive,pargar]');

			$map_required = '';
			if($this->input->post('type') == 'map')
			{
				$map_required = 'required|';
			}
            $this->form_validation->set_rules('longitude', lang('longitude'), $map_required.'trim|numeric');
            $this->form_validation->set_rules('latitude', lang('latitude'), $map_required.'trim|numeric');

            $this->form_validation->set_rules('map_height', lang('map_height'), $map_required.'trim');
            $this->form_validation->set_rules('map_width', lang('map_width'), $map_required.'trim');

			$this->form_validation->set_rules('zoom', lang('zoom'), $map_required.'trim|in_list[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]');

			$this->form_validation->set_rules('map_type', lang('map_type'), $map_required.'in_list[ROADMAP,SATELLITE,HYBRID,TERRAIN]');

			$this->form_validation->set_rules('api_key', lang('api_key'), $map_required.'trim');

			$this->form_validation->set_rules('marker', lang('marker'), 'in_list[yes,no]');

			$this->form_validation->set_rules('marker_title', lang('marker_title'), 'trim|min_length[2]|max_length[200]');

			$this->form_validation->set_rules('marker_description', lang('marker_description'), 'trim|min_length[2]|max_length[500]');

			$article_category_required = '';
			if($this->input->post('type') == 'selected_article_category')
			{
				$article_category_required = 'required|';
			}
			$this->form_validation->set_rules('article_category_id[]', lang('article_category'), $article_category_required.$this->categories->get_inlist_string());

			$this->form_validation->set_rules('menu_assignment', lang('display'), 'required|in_list[all,selected_pages,all_except_selected]');

			$selected_pages_required = '';
			if($this->input->post('menu_assignment') == 'selected_pages' || $this->input->post('menu_assignment') == 'all_except_selected')
			{
				$selected_pages_required = 'required|';
			}
			$this->load->model('menu/menu_model');
			$this->form_validation->set_rules('selected_pages[]', lang('selected_pages'), $selected_pages_required.'trim|'.$this->menu_model->get_inlist_string());

			/*$position_in_list = '';
			$position_array = $this->modules_model->position_array();
			if (count($position_array) > 0)
			{
				$position_in_list = 'in_list['.implode(",",$position_array).']|';
			}*/
			$this->form_validation->set_rules('position', lang('position'), 'trim|required');

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			if ($this->form_validation->run() == TRUE)
			{
				if($item_id)
				{
					$this->db->where(array('id' => $item_id));
					$this->db->select('target');
					$query_p_temp = $this->db->get($main_db_name)->row();
					$temp_attribute = $query_p_temp->target;
				}
				else
				{
					$temp_attribute = '';
				}

				if($this->input->post('type') == 'custom_html')
				{
					$temp_attribute = $this->input->post('content');
					$files_id = $this->input->post('files[]');
				}
				elseif($this->input->post('type') == 'menu')
				{
					$temp_attribute = $this->input->post('menu_categories');
				}
				elseif($this->input->post('type') == 'slide')
				{
					$content = $this->input->post('content[]');

					$uploadPath = '../content/products';
					$upload_config['upload_path'] = $uploadPath;
					$upload_config['allowed_types'] = 'gif|jpg|png';
					$upload_config['file_ext_tolower'] = true;//change finelanem.Jpg or .JPG to filename.jpg
					$upload_config['encrypt_name'] = true;
					$upload_config['max_size'] = 1024;//1 MB

					$primary_pic_uploadData = array();
					$primary_pic_uploadData_1 = array();
					if (isset($_FILES['primary_pic']['name']))
					{
						$filesCount = count($_FILES['primary_pic']['name']);
						for ($i = 0; $i < $filesCount; $i++)
						{
							$_FILES['gallery_file']['name'] = $_FILES['primary_pic']['name'][$i];
							$_FILES['gallery_file']['type'] = $_FILES['primary_pic']['type'][$i];
							$_FILES['gallery_file']['tmp_name'] = $_FILES['primary_pic']['tmp_name'][$i];
							$_FILES['gallery_file']['error'] = $_FILES['primary_pic']['error'][$i];
							$_FILES['gallery_file']['size'] = $_FILES['primary_pic']['size'][$i];

							$this->load->library('upload');
							$this->upload->initialize($upload_config);
							if ($this->upload->do_upload('gallery_file'))
							{
								$primary_pic_uploadData_1[$i] = $this->upload->data();
								$this->load->library('image_lib');
								$primary_pic_uploadData[$i]['pic'] = $primary_pic_uploadData_1[$i]['file_name'];
								if($content != '' && $content != null && is_array($content) && $content != array())
								{
									if(isset($content[$i]))
									{
										$primary_pic_uploadData[$i]['content'] = $content[$i];
									}
								}
							}
							else
							{
								$error = array('error' => $this->upload->display_errors());
								$this->mylib->set_error($error);
							}
						}

						if($item_id)
						{
							$this->db->where(array('id' => $item_id));
							$this->db->select('target');
							$query_pic = $this->db->get('modules');

							foreach ($query_pic->result() as $row_pic)
							{
								$temp_primary_pic_data = json_decode($row_pic->target);
								if (!empty($temp_primary_pic_data->slides))
								{
									foreach ($temp_primary_pic_data->slides as $uploaded_file_data)
									{
										array_push($primary_pic_uploadData, $uploaded_file_data);
									}
								}
							}
						}
						if (!empty($primary_pic_uploadData))
						{
							$temp_attribute = array();
							$temp_attribute['slides'] = $primary_pic_uploadData;
							$temp_attribute['script'] = 'var slide = new Swiper(\'.{{slide_name}}\', {centeredSlides: true, loop: true, autoplay: {delay: 40000, disableOnInteraction: false, }, pagination: {el: \'.swiper-pagination\', clickable: true, }, navigation: {nextEl: \'.swiper-button-next\', prevEl: \'.swiper-button-prev\', }, });';
							$temp_attribute['slide_width'] = $this->input->post('slide_width');
							$temp_attribute['slide_height'] = $this->input->post('slide_height');
							$temp_attribute = json_encode($temp_attribute);
						}
					}
					else
					{
						if($item_id)
						{
							$primary_pic_uploadData = array();
							$this->db->where(array('id' => $item_id));
							$this->db->select('target');
							$query_cont = $this->db->get('modules');

							foreach ($query_cont->result() as $row_cont)
							{
								$temp_target_data = json_decode($row_cont->target);
								if(isset($temp_target_data->slides))
								{
									$slides_2 = $temp_target_data->slides;
									if(is_array($slides_2))
									{
										$p = 0;
										foreach($slides_2 as $slides_row_2)
										{
											if(isset($content[$p]))
											{
												$primary_pic_uploadData[$p]['content'] = $content[$p];
											}
											if(isset($slides_row_2->pic))
											{
												$primary_pic_uploadData[$p]['pic'] = $slides_row_2->pic;
											}
											$p++;
										}
									}
								}
							}
							$temp_attribute = array();
							$temp_attribute['slides'] = $primary_pic_uploadData;
							$temp_attribute['script'] = 'var slide = new Swiper(\'.{{slide_name}}\', {centeredSlides: true, loop: true, autoplay: {delay: 40000, disableOnInteraction: false, }, pagination: {el: \'.swiper-pagination\', clickable: true, }, navigation: {nextEl: \'.swiper-button-next\', prevEl: \'.swiper-button-prev\', }, });';
							$temp_attribute['slide_width'] = $this->input->post('slide_width');
							$temp_attribute['slide_height'] = $this->input->post('slide_height');
							$temp_attribute = json_encode($temp_attribute);
						}
					}
				}
				elseif($this->input->post('type') == 'latest' || $this->input->post('type') == 'weekly_discount' || $this->input->post('type') == 'most_popular' || $this->input->post('type') == 'best_sales')
				{
					$temp_attribute = array();
					$temp_attribute['number_of_items'] = $this->input->post('number_of_items');
					$temp_attribute['slide_config']['view_in_1024'] = $this->input->post('view_in_1024');
					$temp_attribute['slide_config']['view_in_768'] = $this->input->post('view_in_768');
					$temp_attribute['slide_config']['view_in_640'] = $this->input->post('view_in_640');
					$temp_attribute['slide_config']['view_in_320'] = $this->input->post('view_in_320');
					$temp_attribute['slide_config']['group_in_1024'] = $this->input->post('group_in_1024');
					$temp_attribute['slide_config']['group_in_768'] = $this->input->post('group_in_768');
					$temp_attribute['slide_config']['group_in_640'] = $this->input->post('group_in_640');
					$temp_attribute['slide_config']['group_in_320'] = $this->input->post('group_in_320');
					$temp_attribute['slide_config']['space_between'] = $this->input->post('space_between');
					$temp_attribute['slide_config']['loop'] = $this->input->post('loop');
					$temp_attribute['slide_config']['loop_fill_group_with_blank'] = $this->input->post('loop_fill_group_with_blank');
					$temp_attribute = json_encode($temp_attribute);
				}
				elseif($this->input->post('type') == 'selected_items')
				{
					$temp_attribute = array();
					$temp_attribute['items_id'] = $this->input->post('selected_product_item[]');
					$temp_attribute['show_type'] = $this->input->post('show_type');
					$temp_attribute['slide_config']['view_in_1024'] = $this->input->post('view_in_1024');
					$temp_attribute['slide_config']['view_in_768'] = $this->input->post('view_in_768');
					$temp_attribute['slide_config']['view_in_640'] = $this->input->post('view_in_640');
					$temp_attribute['slide_config']['view_in_320'] = $this->input->post('view_in_320');
					$temp_attribute['slide_config']['group_in_1024'] = $this->input->post('group_in_1024');
					$temp_attribute['slide_config']['group_in_768'] = $this->input->post('group_in_768');
					$temp_attribute['slide_config']['group_in_640'] = $this->input->post('group_in_640');
					$temp_attribute['slide_config']['group_in_320'] = $this->input->post('group_in_320');
					$temp_attribute['slide_config']['space_between'] = $this->input->post('space_between');
					$temp_attribute['slide_config']['loop'] = $this->input->post('loop');
					$temp_attribute['slide_config']['loop_fill_group_with_blank'] = $this->input->post('loop_fill_group_with_blank');
					$temp_attribute = json_encode($temp_attribute);
				}
				elseif($this->input->post('type') == 'map')
				{
					$temp_attribute = array();
                    $temp_attribute['longitude'] = $this->input->post('longitude');
                    $temp_attribute['latitude'] = $this->input->post('latitude');
                    $temp_attribute['map_height'] = $this->input->post('map_height');
                    $temp_attribute['map_width'] = $this->input->post('map_width');
					$temp_attribute['zoom'] = $this->input->post('zoom');
					$temp_attribute['map_type'] = $this->input->post('map_type');
					$temp_attribute['api_key'] = $this->input->post('api_key');
					$temp_attribute['marker'] = $this->input->post('marker');
					$temp_attribute['marker_title'] = $this->input->post('marker_title');
					$temp_attribute['marker_description'] = $this->input->post('marker_description');
					$temp_attribute = json_encode($temp_attribute);
				}
				elseif($this->input->post('type') == 'selected_article_category')
				{
					$temp_attribute = array();
					$temp_attribute['article_category_id'] = $this->input->post('article_category_id[]');
					$temp_attribute['number_of_items_per_page'] = $this->input->post('number_of_items_per_page');
					$temp_attribute = json_encode($temp_attribute);
				}

				$selected_pages_1 = array();
				$selected_pages_1['pages_id'] = $this->input->post('selected_pages[]');
				$selected_pages_2 = json_encode($selected_pages_1);
				$dadeh = array
				(
					'title' => $this->input->post('title'),
					'sort' => $this->input->post('sort'),
					'type' => $this->input->post('type'),
					'menu_assignment' => $this->input->post('menu_assignment'),
					'position' => $this->input->post('position'),
					'target' => $temp_attribute,
					'selected_pages' => $selected_pages_2,
					'publish' => $this->input->post('publish')
				);
				if(isset($files_id))
				{
					$dadeh['files_id'] = json_encode($files_id);
				}

				if ($item_id)
				{
					$dadeh['modify_date'] = time();
					$this->modules_model->update($item_id, $dadeh);
				}
				else
				{
					$dadeh['modify_date'] = time();
					$dadeh['insert_date'] = time();
					$item_id = $this->modules_model->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'modulesindexsuccess_msg');
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					if ($item_id)
					{
						//we are currently in the page
						//then, do nothing
					}
					else
					{
						redirect(base_url("modules/add/".$item_id));
					}
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("modules/index"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("modules/add"));
				}
			}
		}

		//It's Edit state, then we need to simulate edit for user
		if ($item_id)
		{
			$this->session->set_userdata('page_title', 'ویرایش ماژول');
			$page_name = 'edit_module';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
			$temp_attribute_query = json_decode($html_output['item_data']['target']);
			$files_id_array = json_decode($html_output['item_data']['files_id']);

			if(isset($html_output['item_data']['type']) && isset($html_output['item_data']['target']))
			{
				if($html_output['item_data']['type'] == 'custom_html')
				{
					$html_output['item_data']['content'] = $html_output['item_data']['target'];
				}
				else
				{
					$html_output['item_data']['content'] = '';
				}
			}
			if(isset($temp_attribute_query->slide_width))
			{
				$html_output['item_data']['slide_width'] = $temp_attribute_query->slide_width;
			}
			else
			{
				$html_output['item_data']['slide_width'] = '';
			}
			if(isset($temp_attribute_query->slide_height))
			{
				$html_output['item_data']['slide_height'] = $temp_attribute_query->slide_height;
			}
			else
			{
				$html_output['item_data']['slide_height'] = '';
			}
			if(isset($temp_attribute_query->number_of_items))
			{
				$html_output['item_data']['number_of_items'] = $temp_attribute_query->number_of_items;
			}
			else
			{
				$html_output['item_data']['number_of_items'] = '';
			}
			if(isset($temp_attribute_query->number_of_items_per_page))
			{
				$html_output['item_data']['number_of_items_per_page'] = $temp_attribute_query->number_of_items_per_page;
			}
			else
			{
				$html_output['item_data']['number_of_items_per_page'] = '';
			}
			if(isset($temp_attribute_query->items_id))
			{
				$html_output['item_data']['items_id'] = '';
				$product_selected = $temp_attribute_query->items_id;
				if(is_array($product_selected))
				{
					$pr = 0;
					foreach($product_selected as $pr_se_row)
					{
						$pr = 1;
					}
					if($pr == 1)
					{
						$o = 1;
						$this->db->select('id,title');
						foreach($product_selected as $pr_se_row)
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

					foreach($product_selected as $pr_se_row)
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
			else
			{
				$html_output['item_data']['items_id'] = '';
			}
			if(isset($temp_attribute_query->show_type))
			{
				$html_output['item_data']['show_type'] = $temp_attribute_query->show_type;
			}
			else
			{
				$html_output['item_data']['show_type'] = '';
			}
			if(isset($temp_attribute_query->longitude))
			{
				$html_output['item_data']['longitude'] = $temp_attribute_query->longitude;
			}
			else
			{
				$html_output['item_data']['longitude'] = '';
			}
            if(isset($temp_attribute_query->latitude))
            {
                $html_output['item_data']['latitude'] = $temp_attribute_query->latitude;
            }
            else
            {
                $html_output['item_data']['latitude'] = '';
            }

            $html_output['item_data']['map_height'] = isset($temp_attribute_query->map_height) ? $temp_attribute_query->map_height : "";
            $html_output['item_data']['map_width'] = isset($temp_attribute_query->map_width) ? $temp_attribute_query->map_width : "";

			if(isset($temp_attribute_query->zoom))
			{
				$html_output['item_data']['zoom'] = $temp_attribute_query->zoom;
			}
			else
			{
				$html_output['item_data']['zoom'] = '';
			}
			if(isset($temp_attribute_query->map_type))
			{
				$html_output['item_data']['map_type'] = $temp_attribute_query->map_type;
			}
			else
			{
				$html_output['item_data']['map_type'] = '';
			}
			if(isset($temp_attribute_query->api_key))
			{
				$html_output['item_data']['api_key'] = $temp_attribute_query->api_key;
			}
			else
			{
				$html_output['item_data']['api_key'] = '';
			}
			if(isset($temp_attribute_query->marker))
			{
				$html_output['item_data']['marker'] = $temp_attribute_query->marker;
			}
			else
			{
				$html_output['item_data']['marker'] = '';
			}
			if(isset($temp_attribute_query->marker_title))
			{
				$html_output['item_data']['marker_title'] = $temp_attribute_query->marker_title;
			}
			else
			{
				$html_output['item_data']['marker_title'] = '';
			}
			if(isset($temp_attribute_query->marker_description))
			{
				$html_output['item_data']['marker_description'] = $temp_attribute_query->marker_description;
			}
			else
			{
				$html_output['item_data']['marker_description'] = '';
			}
			if(isset($temp_attribute_query->article_category_id))
			{
				$html_output['item_data']['article_category_id'] = $temp_attribute_query->article_category_id;
			}
			else
			{
				$html_output['item_data']['article_category_id'] = '';
			}
			if(isset($temp_attribute_query->how_many_item))
			{
				$html_output['item_data']['how_many_item'] = $temp_attribute_query->how_many_item;
			}
			else
			{
				$html_output['item_data']['how_many_item'] = '';
			}
			if(isset($temp_attribute_query->slide_config->view_in_1024))
			{
				$html_output['item_data']['view_in_1024'] = $temp_attribute_query->slide_config->view_in_1024;
			}
			else
			{
				$html_output['item_data']['view_in_1024'] = '';
			}
			if(isset($temp_attribute_query->slide_config->view_in_768))
			{
				$html_output['item_data']['view_in_768'] = $temp_attribute_query->slide_config->view_in_768;
			}
			else
			{
				$html_output['item_data']['view_in_768'] = '';
			}
			if(isset($temp_attribute_query->slide_config->view_in_640))
			{
				$html_output['item_data']['view_in_640'] = $temp_attribute_query->slide_config->view_in_640;
			}
			else
			{
				$html_output['item_data']['view_in_640'] = '';
			}
			if(isset($temp_attribute_query->slide_config->view_in_320))
			{
				$html_output['item_data']['view_in_320'] = $temp_attribute_query->slide_config->view_in_320;
			}
			else
			{
				$html_output['item_data']['view_in_320'] = '';
			}
			if(isset($temp_attribute_query->slide_config->group_in_1024))
			{
				$html_output['item_data']['group_in_1024'] = $temp_attribute_query->slide_config->group_in_1024;
			}
			else
			{
				$html_output['item_data']['group_in_1024'] = '';
			}
			if(isset($temp_attribute_query->slide_config->group_in_768))
			{
				$html_output['item_data']['group_in_768'] = $temp_attribute_query->slide_config->group_in_768;
			}
			else
			{
				$html_output['item_data']['group_in_768'] = '';
			}
			if(isset($temp_attribute_query->slide_config->group_in_640))
			{
				$html_output['item_data']['group_in_640'] = $temp_attribute_query->slide_config->group_in_640;
			}
			else
			{
				$html_output['item_data']['group_in_640'] = '';
			}
			if(isset($temp_attribute_query->slide_config->group_in_320))
			{
				$html_output['item_data']['group_in_320'] = $temp_attribute_query->slide_config->group_in_320;
			}
			else
			{
				$html_output['item_data']['group_in_320'] = '';
			}
			if(isset($temp_attribute_query->slide_config->space_between))
			{
				$html_output['item_data']['space_between'] = $temp_attribute_query->slide_config->space_between;
			}
			else
			{
				$html_output['item_data']['space_between'] = '';
			}
			if(isset($temp_attribute_query->slide_config->loop))
			{
				$html_output['item_data']['loop'] = $temp_attribute_query->slide_config->loop;
			}
			else
			{
				$html_output['item_data']['loop'] = '';
			}
			if(isset($temp_attribute_query->slide_config->loop_fill_group_with_blank))
			{
				$html_output['item_data']['loop_fill_group_with_blank'] = $temp_attribute_query->slide_config->loop_fill_group_with_blank;
			}
			else
			{
				$html_output['item_data']['loop_fill_group_with_blank'] = '';
			}
		}
		else
		{
			$files_id_array = $this->input->post('files[]');
			$this->session->set_userdata('page_title', 'افزودن ماژول');
			$page_name = 'add_module';
			$html_output['item_data'] = array(
				"id" => '',
				"title" => '',
				"sort" => '',
				"type" => '',
				"menu_assignment" => '',
				"position" => '',
				"target" => '',
				"selected_pages" => '',
				"publish" => 'yes',
				"number_of_items" => '',
				"number_of_items_per_page" => '',
				"show_type" => '',
                "longitude" => '',
                "latitude" => '',
                "map_height" => '',
                "map_width" => '',
				"zoom" => '',
				"map_type" => '',
				"api_key" => '',
				"marker" => '',
				"marker_title" => '',
				"marker_description" => '',
				"article_category_id" => '',
				"how_many_item" => '',
				'content' => '',
				'slide_width' => '',
				'slide_height' => '',
				'view_in_1024' => '',
				'view_in_768' => '',
				'view_in_640' => '',
				'view_in_320' => '',
				'group_in_1024' => '',
				'group_in_768' => '',
				'group_in_640' => '',
				'group_in_320' => '',
				'space_between' => '',
				'loop' => '',
				'loop_fill_group_with_blank' => ''
			);
			$html_output['picture'] = '';

			$html_output['item_data']['items_id'] = '';
			$product_selected = $this->input->post('selected_product_item[]');
			if(is_array($product_selected))
			{
				$pr = 0;
				foreach($product_selected as $pr_se_row)
				{
					$pr = 1;
				}
				if($pr == 1)
				{
					$o = 1;
					$this->db->select('id,title');
					foreach($product_selected as $pr_se_row)
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

				foreach($product_selected as $pr_se_row)
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

		///////////////////////////////////////////////////
		/////////////create view for files////////////////
		/////////////////////////////////////////////////
		$files_db_query = $this->db->get('files');
		$html_output['item_data']['files_view'] = '';
		if(is_array($files_id_array))
		{
			foreach($files_id_array as $fi_row)
			{
				$files_name = '';
				$file_src = '';
				foreach($files_db_query->result() as $files_db_row)
				{
					if($files_db_row->id == $fi_row)
					{
						$files_name = $files_db_row->name;
						$file_src = $files_db_row->directory;
						break;
					}
				}
				if($files_name != '' && $files_name != null)
				{
					$html_output['item_data']['files_view'] .= '<div class="upload_module_holder input-group offset-sm-2 col-sm-10 mb-1 uploaded"><div class="loading_holder"> <div class="content"> <div class="loader_spin"></div> <span>'.lang("please_wait").'</span> </div> </div> <div class="upload_module"> <div class="message_holder"></div> <input class="w3-border" type="file"> <div class="btn btn-success btn-sm" onclick="upload_file(this, \'module\');">'.lang("upload").' </div><button type="button" class="close ml-2 mt-1" onclick="delete_parent(this,\'upload_module_holder\');"> <span aria-hidden="true">×</span> </button> </div> <div class="uploaded_module"> <div class="message_holder">فايل «'.$files_name.'» با موفقيت ذخيره شد.</div> <div class="btn btn-danger btn-sm remove_btn" onclick="remove_uploaded_file(this,\'module\')">'.lang("delete_file").'</div> <input class="uploaded_file" name="files[]" type="hidden" value="'.$fi_row.'" file_name="'.$files_name.'"> <div onclick="copy_to_clipboard(\''.$file_src.'\'); var this_node = this; this_node.innerHTML = \'آدرس در حافظه کپی شد\'; setTimeout(function(){ this_node.innerHTML = \'کپی آدرس فایل در حافظه\'; }, 3000);" class="btn btn-success btn-sm copy_to_clipboard">'.lang("copy_file_address_in_memory").'</div> </div> </div>';
				}
			}
		}

		if($item_id)
		{
			////////////////////////////////////////////////////////////////////////////
			## REMOVE REQUESTED FILES ##
			$remove_primary_pic = $this->input->post('remove_primary_pic[]');
			if (isset($remove_primary_pic))
			{
				foreach ($remove_primary_pic as $value1)
				{
					$temp_file = '../content/products/'.$value1;
					if (file_exists($temp_file))
					{
						if (unlink($temp_file))
						{
							//فایل حذف شد
						}
						else
						{
							//فایل حذف نشده است
						}
					}
					else
					{
						//فایل وجود ندارد
					}
				}

				//موارد ارسالی از پایگاه داده نیز باید حذف شود
				$this->db->where(array('id' => $item_id));
				$this->db->select('target');
				$query_p = $this->db->get($main_db_name)->result();

				$temp_uploaded_files = array();
				$temp_uploaded_files_2 = array();
				foreach ($query_p as $row_p)
				{
					$temp_primary_pic_data = json_decode($row_p->target);

					if (!empty($temp_primary_pic_data->slides))
					{
						foreach ($temp_primary_pic_data->slides as $index => $uploaded_file_data)
						{
							if (in_array($uploaded_file_data->pic, $remove_primary_pic))
							{
								//ردیف مورد نظر که باید حذف شود را پیدا کردیم
								//این ردیف را به پایگاه داده اضافه نمیکنیم
							}
							else
							{
								//اطلاعات فایلی که قبلا آپلود شده بود، میبایست همچنان در پایگاه داده بماند.
								array_push($temp_uploaded_files_2, $uploaded_file_data);
							}
						}
					}
					$temp_uploaded_files['slides'] = $temp_uploaded_files_2;
					$temp_uploaded_files['script'] = $temp_primary_pic_data->script;
					$temp_uploaded_files['slide_width'] = $temp_primary_pic_data->slide_width;
					$temp_uploaded_files['slide_height'] = $temp_primary_pic_data->slide_height;
				}
				//لیست جدید که فایلی که اخیرا حذف شده است در آن موجود نیست، را بروزرسانی میکنیم
				$dadeh['target'] = json_encode($temp_uploaded_files);
				$this->modules_model->update($item_id, $dadeh);
			}

			## SHOW UPLOADED FILES TO EDIT ##
			$html_output['picture'] = '';
			$this->db->where(array('id' => $item_id));
			$query_p_2 = $this->db->get('modules');

			foreach ($query_p_2->result() as $row_p_2)
			{
				$temp_json = json_decode($row_p_2->target);
				if(isset($temp_json->slides))
				{
					foreach ($temp_json->slides as $json_row)
					{
						if(isset($json_row->pic))
						{
							$pic_name = $json_row->pic;
							$pic_src = base_url('content/products/'.$pic_name);
							$pic_src = str_replace('/admin','',$pic_src);
							$html_output['picture'] .=
								'<div class="uploaded_pictures_holder row">
							<label class="col-sm-2">
								<img src="'.$pic_src.'" height="100" width="100">
								<input type="checkbox" value='.$pic_name.' name="remove_primary_pic[]">
							</label>
							<div class="col-sm-10"><textarea class="form-control mb-111" name="content[]" rows="5">'.$json_row->content.'</textarea></div>
						</div>';
						}
					}
				}
			}
		}
		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of type ////////
		////////////////////////////////////////////////////
		$html_output['type_list'] = "";
		$type = '';
		if(! is_null($item_id))
		{
			$current_item = $this->modules_model->get_where(array('id'=>$item_id))->row();
			$type = $current_item->type;
		}

		$html_output['type_list'] .=
			'<option value="custom_html" '.set_select('type', 'custom_html', ('custom_html' == $type ? true : false)).'>'.lang('custom_html').'</option>'
			.'<option value="menu" '.set_select('type', 'menu', ('menu' == $type ? true : false)).'>'.lang('menu').'</option>'
			.'<option value="slide" '.set_select('type', 'slide', ('slide' == $type ? true : false)).'>'.lang('slide').'</option>'
			.'<option value="latest" '.set_select('type', 'latest', ('latest' == $type ? true : false)).'>'.lang('latest').'</option>'
            .'<option value="best_sales" '.set_select('type', 'best_sales', ('best_sales' == $type ? true : false)).'>'.lang('best_sales').'</option>'
			.'<option value="most_popular" '.set_select('type', 'most_popular', ('most_popular' == $type ? true : false)).'>'.lang('most_popular').'</option>'
            .'<option value="weekly_discount" '.set_select('type', 'weekly_discount', ('weekly_discount' == $type ? true : false)).'>'.lang('weekly_discount').'</option>'
			.'<option value="selected_items" '.set_select('type', 'selected_items', ('selected_items' == $type ? true : false)).'>'.lang('selected_items').'</option>'
			.'<option value="cart" '.set_select('type', 'cart', ('cart' == $type ? true : false)).'>'.lang('cart').'</option>'
			.'<option value="simple_search" '.set_select('type', 'simple_search', ('simple_search' == $type ? true : false)).'>'.lang('simple_search').'</option>'
			.'<option value="map" '.set_select('type', 'map', ('map' == $type ? true : false)).'>'.lang('map').'</option>'
			.'<option value="selected_article_category" '.set_select('type', 'selected_article_category', ('selected_article_category' == $type ? true : false)).'>'.lang('selected_article_category').'</option>';

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
				$this->db->select('target,type');
				$this->db->where(array('id' => $item_id));
				$menu_category_query = $this->db->get('modules')->result();
				foreach($menu_category_query as $cat_row)
				{
					$type_1 = $cat_row->type;
					if($type_1 == 'menu')
					{
						$menu_category = $cat_row->target;
					}
				}
			}
			foreach ($menu_category_list as $row)
			{
				$html_output['menu_category_list'] .= '<option value="'.$row->id.'" '.set_select('menu_categories', $row->id, ($row->id == $menu_category ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['menu_category_list'] == '')
		{
			$html_output['menu_category_list'] = "<option value=''>".lang ('no_category')."</option>";
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of zoom ////////
		////////////////////////////////////////////////////
		$html_output['zoom_list'] = "";
		$zoom = '';
		if(! is_null($item_id))
		{
			$this->db->select('target,type');
			$this->db->where(array('id' => $item_id));
			$zoom_query = $this->db->get('modules')->result();
			foreach($zoom_query as $zoom_row)
			{
				$type_2 = $zoom_row->type;
				if($type_2 == 'map')
				{
					$temp_zoom = json_decode($zoom_row->target);
					if(isset($temp_zoom->zoom))
					{
						$zoom = $temp_zoom->zoom;
					}
				}
			}
		}
		$html_output['zoom_list'] .=
			'<option value="1" '.set_select('zoom', '1', ('1' == $zoom ? true : false)).'>1</option>'
			.'<option value="2" '.set_select('zoom', '2', ('2' == $zoom ? true : false)).'>2</option>'
			.'<option value="3" '.set_select('zoom', '3', ('3' == $zoom ? true : false)).'>3</option>'
			.'<option value="4" '.set_select('zoom', '4', ('4' == $zoom ? true : false)).'>4</option>'
			.'<option value="5" '.set_select('zoom', '5', ('5' == $zoom ? true : false)).'>5</option>'
			.'<option value="6" '.set_select('zoom', '6', ('6' == $zoom ? true : false)).'>6</option>'
			.'<option value="7" '.set_select('zoom', '7', ('7' == $zoom ? true : false)).'>7</option>'
			.'<option value="8" '.set_select('zoom', '8', ('8' == $zoom ? true : false)).'>8</option>'
			.'<option value="9" '.set_select('zoom', '9', ('9' == $zoom ? true : false)).'>9</option>'
			.'<option value="10" '.set_select('zoom', '10', ('10' == $zoom ? true : false)).'>10</option>'
			.'<option value="11" '.set_select('zoom', '11', ('11' == $zoom ? true : false)).'>11</option>'
			.'<option value="12" '.set_select('zoom', '12', ('12' == $zoom ? true : false)).'>12</option>'
			.'<option value="13" '.set_select('zoom', '13', ('13' == $zoom ? true : false)).'>13</option>'
			.'<option value="14" '.set_select('zoom', '14', ('14' == $zoom ? true : false)).'>14</option>'
			.'<option value="15" '.set_select('zoom', '15', ('15' == $zoom ? true : false)).'>15</option>'
			.'<option value="16" '.set_select('zoom', '16', ('16' == $zoom ? true : false)).'>16</option>'
			.'<option value="17" '.set_select('zoom', '17', ('17' == $zoom ? true : false)).'>17</option>'
			.'<option value="18" '.set_select('zoom', '18', ('18' == $zoom ? true : false)).'>18</option>'
			.'<option value="19" '.set_select('zoom', '19', ('19' == $zoom ? true : false)).'>19</option>'
			.'<option value="20" '.set_select('zoom', '20', ('20' == $zoom ? true : false)).'>20</option>';

		////////////////////////////////////////////////////////////
		// Create A list (HTML Select list) of article categories //
		///////////////////////////////////////////////////////////
		$category_list = $this->categories->get_where(array('publish' => 'yes'))->result();
		$html_output['categories_list'] = "";

		if (count($category_list) > 0)
		{
			$article_category = 0;
			if(! is_null($item_id))
			{
				$this->db->select('target,type');
				$this->db->where(array('id' => $item_id));
				$article_cat_query = $this->db->get('modules')->result();
				foreach($article_cat_query as $art_cat_row)
				{
					$type_3 = $art_cat_row->type;
					if($type_3 == 'selected_article_category')
					{
						$temp_art_cat = json_decode($art_cat_row->target);
						if(isset($temp_art_cat->article_category_id))
						{
							$article_category = $temp_art_cat->article_category_id;
						}
					}
				}
			}

			foreach ($category_list as $row)
			{
				if(is_array($article_category))
				{
					$temp_article_cat = (in_array($row->id, $article_category) ? "checked" : "");
				}
				else
				{
					$temp_article_cat = '';
				}
				$html_output['categories_list'] .= '<li>
                            <input value="'.$row->id.'" name="article_category_id[]" type="checkbox" '.$temp_article_cat.'>
                            <label>'.$row->title.'</label>
                        </li>';
			}
		}
		if ($html_output['categories_list'] == '')
		{
			$html_output['categories_list'] = "<option value=''>".lang ('no_category')."</option>";
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of position ////
		////////////////////////////////////////////////////
	/*	$html_output['position_list'] = "";
		$position = '';
		if(! is_null($item_id))
		{
			$this->db->select('position');
			$this->db->where(array('id' => $item_id));
			$pos_query = $this->db->get('modules')->result();
			foreach($pos_query as $p_row)
			{
				$position = $p_row->position;
			}
		}
		$position_array = $this->modules_model->position_array();
		if(is_array($position_array))
		{
			foreach($position_array as $pos_row)
			{
				$html_output['position_list'] .= '<option value="'.$pos_row.'" '.set_select('position', $pos_row, ($pos_row == $position ? true : false)).'>'.$pos_row.'</option>';
			}
		}*/

		////////////////////////////////////////////////////////////////////
		// Create A list (HTML Select list) of selected pages list ////////
		//////////////////////////////////////////////////////////////////
		$html_output['selected_pages_list'] = "";
		$pages_id = '';
		if(! is_null($item_id))
		{
			$current_item = $this->modules_model->get_where(array('id'=>$item_id))->row();
			if(isset($current_item->selected_pages))
			{
				$pages_id = json_decode($current_item->selected_pages);
				if(isset($pages_id->pages_id))
				{
					$pages_id = $pages_id->pages_id;
				}
			}
		}

		$this->db->select('id,title');
		$this->db->where(array('publish' => 'yes'));
		$menu_cat_query = $this->db->get('menu_category');

		$this->db->select('id,category_id,title');
		$this->db->where(array('publish' => 'yes'));
		$menu_query = $this->db->get('menu');

		foreach($menu_cat_query->result() as $menu_cat_row)
		{
			$html_output['selected_pages_list'] .='<div>'.$menu_cat_row->title.'</div>';
			foreach($menu_query->result() as $menu_row)
			{
				if($menu_row->category_id == $menu_cat_row->id)
				{
					if(is_array($pages_id))
					{
						$temp_pages_id = (in_array($menu_row->id, $pages_id) ? "checked" : "");
					}
					else
					{
						$temp_pages_id = '';
					}
					$html_output['selected_pages_list'] .= '<li>
                            <input value="'.$menu_row->id.'" name="selected_pages[]" type="checkbox" '.$temp_pages_id.'>
                            <label>'.$menu_row->title.'</label>
                        </li>';
				}
			}
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		///////////////////////////////////////
		// Create Error and Success Messages //
		///////////////////////////////////////
		$data['page_name'] = $page_name;
	    $html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$data['html_output'] = $html_output;

		$this->load->view('template/header', $data);
		$this->load->view('menu', $data);
		$this->load->view($this->uri->segment(1) . '/add', $data);
		$this->load->view('template/footer');
    }
}
