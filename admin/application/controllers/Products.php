<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

	/**
	 * Dar in safhe products ra modiriat (ADD, DELETE, EDIT) mikonim
	 */
	public function index()
	{
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->load->library('site_map_lib');
		$this->session->set_userdata('page_title', 'لیست محصولات');
		$main_db_name = "add_products";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("products/add/".$list_items[0]), 'location');
			}
			elseif ($task == 'publish')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('publish', 'yes');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}
				$this->site_map_lib->create_link_site_map();
			}
			elseif ($task == 'unpublish')
			{
				foreach ($list_items as $value)
				{
					$this->db->set('publish', 'no');
					$this->db->where('id', $value);
					$this->db->update($main_db_name);
				}
				$this->site_map_lib->create_link_site_map();
			}
			elseif ($task == 'delete')
			{
				$this->load->model('products/add_products_model');

				$del_1 = 0;
				foreach ($list_items as $value)
				{
					$del_1 = 1;
				}

				if($del_1 == 1)
				{
					$i = 1;
					$this->db->select('primary_pic');
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
					$del_pic_query = $this->db->get($main_db_name);
					foreach($del_pic_query->result() as $del_pic_row)
					{
						$jason_pic = json_decode($del_pic_row->primary_pic);
						if(is_array($jason_pic))
						{
							foreach($jason_pic as $pic_row)
							{
								if(isset($pic_row->file_name))
								{
									$temp_pic_del_src = '../content/products/'.$pic_row->file_name;
									if (file_exists($temp_pic_del_src))
									{
										unlink($temp_pic_del_src);
									}
									$temp_pic_thumb_del_src = '../content/products/thumb/'.$pic_row->file_name;
									if (file_exists($temp_pic_thumb_del_src))
									{
										unlink($temp_pic_thumb_del_src);
									}
								}
							}
						}
					}
					//////////////////////////////////////////
					/////////////delete field file///////////
					////////////////////////////////////////
					$bn = 1;
					foreach ($list_items as $value)
					{
						if($bn == 1)
						{
							$this->db->where(array('id' => $value));
						}
						else
						{
							$this->db->or_where(array('id' => $value));
						}
						$bn++;
					}
					$del_pr_query = $this->db->get($main_db_name);

					$xc = 1;
					foreach($del_pr_query->result() as $del_pr_row)
					{
						if($xc == 1)
						{
							$this->db->where(array('category' => $del_pr_row->category));
						}
						else
						{
							$this->db->or_where(array('category' => $del_pr_row->category));
						}
						$xc++;
					}
					if($xc > 1)
					{
						$del_field_qu = $this->db->get('add_field');
					}

					if(isset($del_field_qu))
					{
						foreach($del_pr_query->result() as $del_pr_row)
						{
							foreach($del_field_qu->result() as $de_field_row)
							{
								if($del_pr_row->category == $de_field_row->category)
								{
									$json_del_file = json_decode($del_pr_row->fields);
									foreach($json_del_file as $del_in => $val_in)
									{
										if($del_in == $de_field_row->id && isset($val_in->file_name))
										{
											$temp_field_del_src = '../content/file/'.$val_in->file_name;
											if (file_exists($temp_field_del_src))
											{
												unlink($temp_field_del_src);
											}
										}
									}
								}
							}
						}
					}
					///////////////////////////////////////////
					/////////////delete option file///////////
					/////////////////////////////////////////
					$xl = 1;
					foreach($del_pr_query->result() as $del_pr_row)
					{
						if($xl == 1)
						{
							$this->db->where(array('category' => $del_pr_row->category));
						}
						else
						{
							$this->db->or_where(array('category' => $del_pr_row->category));
						}
						$xl++;
					}
					if($xl > 1)
					{
						$del_option_qu = $this->db->get('options');
					}

					if(isset($del_option_qu))
					{
						foreach($del_pr_query->result() as $del_pr_row)
						{
							foreach($del_option_qu->result() as $de_option_row)
							{
								if($del_pr_row->category == $de_option_row->category)
								{
									$json_del_file = json_decode($del_pr_row->options);
									foreach($json_del_file as $del_in => $val_in)
									{
										if($del_in == $de_option_row->id && isset($val_in->file_name))
										{
											$temp_field_del_src = '../content/option_file/'.$val_in->file_name;
											if (file_exists($temp_field_del_src))
											{
												unlink($temp_field_del_src);
											}
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
					$this->add_products_model->delete(array('id' => $value));
					//set success message
					$this->mylib->set_success(lang('deleted_successfully'));
				}
				$this->site_map_lib->create_link_site_map();
			}
		}

		//Customize by user search keyword
		$search = $this->input->post('search');
		//Search query
		if($search != "")
		{
			$this->db->group_start();
			$this->db->like('title' , $search);
			$this->db->or_like('title_alias_url' , $search);
			$this->db->or_like('type_of_category' , $search);
			$this->db->or_like('meta_tag_title' , $search);
			$this->db->or_like('meta_tag_keywords' , $search);
			$this->db->or_like('meta_tag_description' , $search);
			$this->db->group_end();
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
		//Get Items from Database
		$page = ($this->uri->segment(2));

		//////////////////////////////////////////
		///////sort products by order_by/////////
		////////////////////////////////////////
		$sort = $this->input->post('sort');
		$html_output['sort'] = $sort;
		if($sort == 'newest_editing')
		{
			$this->db->order_by('modify_date', 'DESC');
		}
		if($sort == 'oldest_editing')
		{
			$this->db->order_by('modify_date', 'ASC');
		}
		elseif($sort == 'lowest_price')
		{
			$this->db->order_by('price', 'ASC');
		}
		elseif($sort == 'highest_price')
		{
			$this->db->order_by('price', 'DESC');
		}
		elseif($sort == 'lowest_number')
		{
			$this->db->order_by('number', 'ASC');
		}
		elseif($sort == 'highest_number')
		{
			$this->db->order_by('number', 'DESC');
		}
		elseif($sort == 'published')
		{
			$this->db->group_start();
			$this->db->where(array('publish' => 'yes'));
			$this->db->group_end();
		}
		elseif($sort == 'unpublished')
		{
			$this->db->group_start();
			$this->db->where(array('publish' => 'no'));
			$this->db->group_end();
		}
		else
		{
			$this->db->order_by('modify_date', 'DESC');
		}
		$this->db->limit($per_page, $page);
		$query = $this->db->get($main_db_name);

		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$category_row = $row->category;
				$category_title = $this->products_category_model->find_parent(array('id'=>$category_row));

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
				$temp_html .= '<td><a href="'.base_url("products/add/".$row->id).'">'. $row->title .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("products/products_category/".$row->category).'">'. $category_title .'</a></td>';
				$temp_html .= '<td>'. $row->price .'</td>';
				$temp_html .= '<td>'. $row->number .'</td>';
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

		$data['page_name'] = 'list_products';
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
    	/*echo "<pre>";
		var_dump($_POST);
    	echo "</pre>";*/
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->load->library('site_map_lib');
		$main_db_name = "add_products";
		$html_output = array();
		$fields = '';
		$options = '';

		$this->load->model('products/add_products_model');
		$this->load->model('products/products_category_model');
		$this->load->model('products/brand_model');

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
			$this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[3]|max_length[200]');

			$this->form_validation->set_rules('title_alias_url', lang('title_alias_url'), 'trim|min_length[1]|max_length[200]'.($item_id==null || $posted_title_alias_url!=$item_id_title_alias_url ? "|is_unique[$main_db_name.title_alias_url]" :""));

			$this->form_validation->set_rules('description', lang('description'), 'trim');

			$this->form_validation->set_rules('type_of_category', lang('type_of_category'),'trim|required|in_list[physical,virtual]', array('in_list' => 'فیلد نوع محصول باید یکی از موارد <<فیزیکی, مجازی>> باشد.'));

			$this->form_validation->set_rules('weight', lang('weight'), 'trim|is_natural');
			$this->form_validation->set_rules('length', lang('length'), 'trim|is_natural');
			$this->form_validation->set_rules('width', lang('width'), 'trim|is_natural');
			$this->form_validation->set_rules('height', lang('height'), 'trim|is_natural');

			$this->form_validation->set_rules('number', lang('number'), 'trim|is_natural'.($this->input->post('type_of_category') == 'physical' ? "|required" :""));

			$this->form_validation->set_rules('min_number', lang('min_number'), 'trim|is_natural');

			$this->form_validation->set_rules('finish', lang('finish'), 'trim|required|in_list[1,2,3]');

			$this->form_validation->set_rules('price', lang('price'), 'trim|is_natural|required');

		    $this->form_validation->set_rules('points_buy', lang('points_buy'), 'trim');

			$this->form_validation->set_rules('type_of_discount', lang('type_of_discount'), 'trim|in_list[percentage,static_value]', array('in_list' => 'فیلد نوع تخفیف باید یکی از موارد <<درصدی, مقدار ثابت>> باشد.'));

			$max_of_percentage_discount = '';
			if($this->input->post('type_of_discount') == 'percentage')
			{
				$max_of_percentage_discount = '|less_than_equal_to[100]';
			}
			$this->form_validation->set_rules('discount_amount', lang('discount_amount'), 'trim|greater_than[0]|is_natural'.$max_of_percentage_discount);

			$this->form_validation->set_rules('meta_tag_title', lang('meta_tag_title'), 'trim|min_length[2]|max_length[200]');

			$this->form_validation->set_rules('meta_tag_keywords', lang('meta_tag_keywords'), 'trim|min_length[2]|max_length[1000]');

			$this->form_validation->set_rules('meta_tag_description', lang('meta_tag_description'), 'trim|max_length[300]');

			$this->form_validation->set_rules('category', lang('category'),'required|'.$this->products_category_model->get_inlist_string());

			$this->form_validation->set_rules('brand', lang('brand'),'required|'.$this->brand_model->get_inlist_string());

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');
			/////////////////////////////////////////////////////////////////////////////////////////////
			//////////options/////////////////////
			/////////////////////////////////////
			$category = $this->input->post('category');

			////////////////////////////
			///removed options file////
			///////////////////////////
			$this->db->where(array('category' => $category));
			$query_options_r = $this->db->get('options');

			if($item_id)
			{
				foreach ($query_options_r->result() as $row_r)
				{
					$delete_options_file = $this->input->post('delete_options_file_'.$row_r->id);
					if(isset($delete_options_file) && $row_r->type == 'file')
					{
						## REMOVE REQUESTED FILES ##
						$temp_options_file = '../content/option_file/'.$delete_options_file;
						if (file_exists($temp_options_file))
						{
							//فایل بر روی سرور وجود دارد
							//میتوانیم آنرا حذف کنیم
							if (unlink($temp_options_file))
							{
								//فایل حذف شد
								$this->db->where(array('id' => $item_id));
								$this->db->select('options');
								$query_opt = $this->db->get('add_products');

								foreach ($query_opt->result() as $row_opt)
								{
									$temp_options = json_decode($row_opt->options, TRUE);

									if (!empty($temp_options))
									{
										if (isset($temp_options[$row_r->id]))
										{
											$temp_options[$row_r->id] = '';
										}
									}

									//Update Database
									$this->add_products_model->set_update($item_id, array('options'=>json_encode($temp_options)));
								}
							}
						}
					}
				}
			}

			$list_options = $this->input->post('list_options[]');
			$temp_options_array = array();

			if($list_options != '' && $list_options != null){
			$this->db->where(array('category' => $category));
			$this->db->order_by('sort', 'ASK');
			$query_options = $this->db->get('options');

			foreach ($query_options->result() as $row_options)
			{
				/*if ($row_options->required == 'yes')
				{
					$this->form_validation->set_rules($row_options->id, $row_options->title, 'required');
				}*/
				if(in_array($row_options->id, $list_options))
				{
					if($row_options->type == 'select' || $row_options->type == 'checkbox'){
					if($row_options->insert_value != '' && $row_options->insert_value != null)
					{
						$insert_value = str_replace("\r","",$row_options->insert_value);
						$insert_value = explode("\n",$insert_value);

						foreach ($insert_value as $value)
						{
							///$value = iconv(mb_detect_encoding($value, mb_detect_order(), true), "UTF-8", $value);
							/////echo $_POST['product_quantity_with_option_'.$row_options->id.'_'.$value];
							$value = explode("|", $value);

							if (count($value) == 1)
							{
								//there wasn't any | character
								$value[1] = $value[0];
							}
							//echo "<br/>".$value[1] . "=>" . $_POST['product_quantity_with_option_'.$row_options->id.'_'.$value[1]];
							//echo '<br/>ssssss<br/>';
							//echo 'product_quantity_with_option_'.$row_options->id.'_'.$value[1];
							//print_r($this->input->post('product_quantity_with_option_'.$row_options->id.'_'.$value[1]));
							$sub_item_options = $this->input->post('sub_list_options['.$row_options->id.'_'.$value[1].']');
							if($sub_item_options != '' && $sub_item_options != null)
							{
								$temp_value = str_replace(" ","_",$value[1]);
								$temp_value = str_replace(".","_",$temp_value);
								$temp_options_array[$row_options->id][$value[1]]['reduce_total_inventory'] = $this->input->post('reduce_total_inventory_'.$row_options->id.'_'.$temp_value);
								$temp_options_array[$row_options->id][$value[1]]['product_quantity_with_option'] = $this->input->post('product_quantity_with_option_'.$row_options->id.'_'.$temp_value);
								$temp_options_array[$row_options->id][$value[1]]['option_price_situation'] = $this->input->post('option_price_situation_'.$row_options->id.'_'.$temp_value);
								$temp_options_array[$row_options->id][$value[1]]['option_price'] = $this->input->post('option_price_'.$row_options->id.'_'.$temp_value);
								$temp_options_array[$row_options->id][$value[1]]['option_point_situation'] = $this->input->post('option_point_situation_'.$row_options->id.'_'.$temp_value);
								$temp_options_array[$row_options->id][$value[1]]['option_point'] = $this->input->post('option_point_'.$row_options->id.'_'.$temp_value);
								$temp_options_array[$row_options->id][$value[1]]['option_weight_situation'] = $this->input->post('option_weight_situation_'.$row_options->id.'_'.$temp_value);
								$temp_options_array[$row_options->id][$value[1]]['option_weight'] = $this->input->post('option_weight_'.$row_options->id.'_'.$temp_value);
								$temp_options_array[$row_options->id][$value[1]]['is_option_required'] = $this->input->post('is_option_required_'.$row_options->id.'_'.$temp_value);
								$temp_options_array[$row_options->id][$value[1]]['can_option_be_purchased_separately'] = $this->input->post('can_option_be_purchased_separately_'.$row_options->id.'_'.$temp_value);
							}
						}
					}}
					elseif($row_options->type == 'file')
					{
						if (isset($_FILES["option_file_".$row_options->id]['name']) && $_FILES["option_file_".$row_options->id]['name'] != "")
						{
							//فایل ارسال شده است
							$uploadPath = '../content/option_file';
							$upload_config['upload_path'] = $uploadPath;
							$upload_config['allowed_types'] = '*';
							$upload_config['file_ext_tolower'] = true;//change finelanem.Jpg or .JPG to filename.jpg
							$upload_config['encrypt_name'] = true;
							$upload_config['max_size'] = 102400;//100 MB

							$file_uploadData = array();

							if ($this->form_validation->run() == TRUE)
							{
								//فایل از طرف کاربر آپلود شده است
								$this->load->library('upload');
								$this->upload->initialize($upload_config);
								if ($this->upload->do_upload("option_file_".$row_options->id))
								{
									$file_uploadData = $this->upload->data();
								}
								else
								{
									$error = array('error' => $this->upload->display_errors());
									$this->mylib->set_error($error);
								}
							}

							if (empty($file_uploadData))
							{
								//فایلی ذخیره نشده است
							}
							else
							{
								//فایل جدید شده است
								if($this->input->post('option_file_name_'.$row_options->id) != '' && $this->input->post('option_file_name_'.$row_options->id) != null)
								{
									$file_uploadData['orig_name'] = $this->input->post('option_file_name_'.$row_options->id);
								}
								$file_uploadData['image_size_str'] = "";//Save Json format in Database will have error if it is not empty string
								$temp_options_array[$row_options->id] = $file_uploadData;

								$temp_options_array[$row_options->id]['reduce_total_inventory'] = $this->input->post('reduce_total_inventory_'.$row_options->id);
								$temp_options_array[$row_options->id]['product_quantity_with_option'] = $this->input->post('product_quantity_with_option_'.$row_options->id);
								$temp_options_array[$row_options->id]['option_price_situation'] = $this->input->post('option_price_situation_'.$row_options->id);
								$temp_options_array[$row_options->id]['option_price'] = $this->input->post('option_price_'.$row_options->id);

								$temp_options_array[$row_options->id]['option_point_situation'] = $this->input->post('option_point_situation_'.$row_options->id);
								$temp_options_array[$row_options->id]['option_point'] = $this->input->post('option_point_'.$row_options->id);

								$temp_options_array[$row_options->id]['option_weight_situation'] = $this->input->post('option_weight_situation_'.$row_options->id);
								$temp_options_array[$row_options->id]['option_weight'] = $this->input->post('option_weight_'.$row_options->id);

								$temp_options_array[$row_options->id]['is_option_required'] = $this->input->post('is_option_required_'.$row_options->id);

								$temp_options_array[$row_options->id]['can_option_be_purchased_separately'] = $this->input->post('can_option_be_purchased_separately_'.$row_options->id);
							}
						}
						else
						{
							if($item_id)
							{
								$this->db->where(array('id' => $item_id));
								$this->db->select('options');
								$query_en_op = $this->db->get('add_products');

								foreach ($query_en_op->result() as $field_en_op)
								{
									$temp_options_en_op = json_decode($field_en_op->options, TRUE);

									if (!empty($temp_options_en_op))
									{
										if (isset($temp_options_en_op[$row_options->id]))
										{
											$temp_options_array[$row_options->id] = $temp_options_en_op[$row_options->id];
											if($temp_options_array[$row_options->id] != '')
											{
												$temp_options_array[$row_options->id]['reduce_total_inventory'] = $this->input->post('reduce_total_inventory_'.$row_options->id);
												$temp_options_array[$row_options->id]['product_quantity_with_option'] = $this->input->post('product_quantity_with_option_'.$row_options->id);
												$temp_options_array[$row_options->id]['option_price_situation'] = $this->input->post('option_price_situation_'.$row_options->id);
												$temp_options_array[$row_options->id]['option_price'] = $this->input->post('option_price_'.$row_options->id);

												$temp_options_array[$row_options->id]['option_point_situation'] = $this->input->post('option_point_situation_'.$row_options->id);
												$temp_options_array[$row_options->id]['option_point'] = $this->input->post('option_point_'.$row_options->id);

												$temp_options_array[$row_options->id]['option_weight_situation'] = $this->input->post('option_weight_situation_'.$row_options->id);
												$temp_options_array[$row_options->id]['option_weight'] = $this->input->post('option_weight_'.$row_options->id);

												$temp_options_array[$row_options->id]['is_option_required'] = $this->input->post('is_option_required_'.$row_options->id);

												$temp_options_array[$row_options->id]['can_option_be_purchased_separately'] = $this->input->post('can_option_be_purchased_separately_'.$row_options->id);
											}
										}
									}
								}
							}
						}
					}
					else
					{
						$temp_options_array[$row_options->id]['reduce_total_inventory'] = $this->input->post('reduce_total_inventory_'.$row_options->id);
						$temp_options_array[$row_options->id]['product_quantity_with_option'] = $this->input->post('product_quantity_with_option_'.$row_options->id);
						$temp_options_array[$row_options->id]['option_price_situation'] = $this->input->post('option_price_situation_'.$row_options->id);
						$temp_options_array[$row_options->id]['option_price'] = $this->input->post('option_price_'.$row_options->id);

						$temp_options_array[$row_options->id]['option_point_situation'] = $this->input->post('option_point_situation_'.$row_options->id);
						$temp_options_array[$row_options->id]['option_point'] = $this->input->post('option_point_'.$row_options->id);

						$temp_options_array[$row_options->id]['option_weight_situation'] = $this->input->post('option_weight_situation_'.$row_options->id);
						$temp_options_array[$row_options->id]['option_weight'] = $this->input->post('option_weight_'.$row_options->id);

						$temp_options_array[$row_options->id]['is_option_required'] = $this->input->post('is_option_required_'.$row_options->id);

						$temp_options_array[$row_options->id]['can_option_be_purchased_separately'] = $this->input->post('can_option_be_purchased_separately_'.$row_options->id);
					}
				}
			}}
			$options = json_encode($temp_options_array, JSON_UNESCAPED_UNICODE );
			////////////////////////////////////////////////////////////////////////////////////////
			//set rules baraye fieldhaye yek category

            $category = $this->input->post('category');

			$this->db->where(array('category' => $category));
			$query = $this->db->get('add_field');

			$temp_fields_array = array();
			foreach ($query->result() as $row)
			{
				if ($row->type == 'single_case')
				{
					if($row->required == 'yes')
					{
						$this->form_validation->set_rules("single_case_".$row->id, $row->title,'required');
					}

					$field = $this->input->post("single_case_".$row->id);
				}
				else if($row->type == 'multiple_case')
				{
					if($row->required == 'yes')
					{
						$this->form_validation->set_rules("multiple_case_".$row->id, $row->title,'required');
					}

					$field = $this->input->post("multiple_case_".$row->id);
					$field = str_replace("\r\n","::new_line::",$field);
				}
				else if($row->type == 'checkbox')
				{
					if($row->required == 'yes')
					{
						$this->form_validation->set_rules("checkbox_".$row->id."[]", $row->title,'required');
					}

					$field = $this->input->post("checkbox_".$row->id."[]");
					if($field != null)
					{
						$field = implode("~||~",$field);
					}
					$field = str_replace("\n","::new_line::",$field);
				}
				else if($row->type == 'textarea')
				{
					if($row->required == 'yes')
					{
						$this->form_validation->set_rules("textarea_".$row->id, $row->title,'required');
					}

					$field = $this->input->post("textarea_".$row->id);
					$field = str_replace("\n","::new_line::",$field);
				}
				else if($row->type == 'select')
				{
					if($row->required == 'yes')
					{
						$this->form_validation->set_rules("select_".$row->id, $row->title,'required');
					}

					$field = $this->input->post("select_".$row->id);
					$field = str_replace("\n","::new_line::",$field);
				}

				if($row->type != 'file')
				{
					$temp_fields_array[$row->id] = $field;
				}
				else
				{
					$uploaded_fields_id_array = $this->input->post('uploaded_fields_id[]');
					if (isset($_FILES["file_".$row->id]['name']) && $_FILES["file_".$row->id]['name'] != "")
					{
						//do nothing
					}
					else if($row->required == 'yes' && is_array($uploaded_fields_id_array))
					{
						if(in_array($row->id, $uploaded_fields_id_array))
						{
							//do nothing
						}
						else
						{
							$this->form_validation->set_rules("file_".$row->id, $row->title,'required');
						}
					}
					else if($row->required == 'yes' && !is_array($uploaded_fields_id_array))
					{
						$this->form_validation->set_rules("file_".$row->id, $row->title,'required');
					}
				}
			}
			foreach ($query->result() as $row)
			{
			    if($row->type == 'file')
				{
					$field = '';
				////////////////////////////
				///////removed file/////////
				///////////////////////////
				$delete_file = $this->input->post('delete_file_'.$row->id);
				if($item_id && isset($delete_file))
				{
					## REMOVE REQUESTED FILES ##
					$temp_file = '../content/file/'.$delete_file;
					if (file_exists($temp_file))
					{
						if (unlink($temp_file))
						{
							$this->db->where(array('id' => $item_id));
							$this->db->select('fields');
							$query_r_f = $this->db->get('add_products');

							foreach ($query_r_f->result() as $field_r_f)
							{
								$temp_fields_r = json_decode($field_r_f->fields, TRUE);

								if (!empty($temp_fields_r))
								{
									if (isset($temp_fields_r[$row->id]))
									{
										$temp_fields_r[$row->id] = '';
									}
								}

								//Update Database
								$this->add_products_model->set_update($item_id, array('fields'=>json_encode($temp_fields_r)));
								$field = '';
							}
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

				$uploaded_fields_id_array = $this->input->post('uploaded_fields_id[]');
				if (isset($_FILES["file_".$row->id]['name']) && $_FILES["file_".$row->id]['name'] != "")
				{
					//فایل ارسال شده است
					$uploadPath = '../content/file';
					$upload_config['upload_path'] = $uploadPath;
					$upload_config['allowed_types'] = '*';
					$upload_config['file_ext_tolower'] = true;//change finelanem.Jpg or .JPG to filename.jpg
					$upload_config['encrypt_name'] = true;
					$upload_config['max_size'] = 102400;//100 MB

					$file_uploadData = array();

					if ($this->form_validation->run() == TRUE)
					{
						//فایل از طرف کاربر آپلود شده است

						$this->load->library('upload');
						$this->upload->initialize($upload_config);
						if ($this->upload->do_upload("file_".$row->id))
						{
							$file_uploadData = $this->upload->data();
						}
						else
						{
							$error = array('error' => $this->upload->display_errors());
							$this->mylib->set_error($error);
						}
					}

					if (empty($file_uploadData))
					{
						//فایلی ذخیره نشده است
					}
					else
					{
						//فایل جدید شده است
						if($this->input->post('file_name_'.$row->id) != '' && $this->input->post('file_name_'.$row->id) != null)
						{
							$file_uploadData['orig_name'] = $this->input->post('file_name_'.$row->id);
						}
						$file_uploadData['image_size_str'] = "";//Save Json format in Database will have error if it is not empty string
						$field = $file_uploadData;
					}
				}
				else if($row->required == 'yes' && is_array($uploaded_fields_id_array))
				{
					if(in_array($row->id, $uploaded_fields_id_array))
					{
						//do nothing
					}
					else
					{
						$this->form_validation->set_rules("file_".$row->id, $row->title,'required');
					}
				}
				else if($row->required == 'yes' && !is_array($uploaded_fields_id_array))
				{
					$this->form_validation->set_rules("file_".$row->id, $row->title,'required');
				}
				if(!isset($delete_file) && !isset($_FILES["file_".$row->id]['name']))
				{
					if($item_id)
					{
						$this->db->where(array('id' => $item_id));
						$this->db->select('fields');
						$query_en = $this->db->get('add_products');

						foreach ($query_en->result() as $field_en)
						{
							$temp_fields_en = json_decode($field_en->fields, TRUE);

							if (!empty($temp_fields_en))
							{
								if (isset($temp_fields_en[$row->id]))
								{
									$field = $temp_fields_en[$row->id];
								}
							}
						}
					}
				}
					$temp_fields_array[$row->id] = $field;
		 		}
			}

			$fields = json_encode( $temp_fields_array, JSON_UNESCAPED_UNICODE );
			//$fields = json_encode($temp_fields_array);

			if ($this->form_validation->run() == TRUE)
			{
				############################
				## Upload Submitted files ##
				############################
				$uploadPath = '../content/products';
				$upload_config['upload_path'] = $uploadPath;
				$upload_config['allowed_types'] = 'gif|jpg|png';
				$upload_config['file_ext_tolower'] = true;//change finelanem.Jpg or .JPG to filename.jpg
				$upload_config['encrypt_name'] = true;
				$upload_config['max_size'] = 102400;//100 MB

				$primary_pic_uploadData = array();

				$this->db->where(array('id' => 1));
				$size_query = $this->db->get('setting');
				foreach ($size_query->result() as $size_row)
				{
					$img_width = $size_row->img_width;
					$img_height = $size_row->img_height;
					$thumb_width = $size_row->thumb_width;
					$thumb_height = $size_row->thumb_height;
				}

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
							$primary_pic_uploadData[$i] = $this->upload->data();

							$this->load->library('image_lib');

							//resize original uploaded photo
							$config['image_library'] = 'gd2';
							$config['source_image'] = '../content/products/'.$primary_pic_uploadData[$i]['file_name'];
							$config['maintain_ratio'] = TRUE;
							$config['width']         = $img_width;
							$config['height']       = $img_height;
							$this->image_lib->clear();
							$this->image_lib->initialize($config);
							$this->image_lib->resize();

							//create thumbnails
							$thumb_config['image_library'] = 'gd2';
							$thumb_config['source_image'] = '../content/products/'.$primary_pic_uploadData[$i]['file_name'];
							$thumb_config['maintain_ratio'] = TRUE;
							$thumb_config['width']         = $thumb_width;
							$thumb_config['height']       = $thumb_height;
							$thumb_config['new_image'] = '../content/products/thumb/'.$primary_pic_uploadData[$i]['file_name'];
							$this->image_lib->clear();
							$this->image_lib->initialize($thumb_config);
							$this->image_lib->resize();
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
						$this->db->select('primary_pic');
						$query = $this->db->get('add_products');

						foreach ($query->result() as $row)
						{
							//$row->primary_pic is something like this:
							//[{etelaeate file 1},{etelaeate file 2},...,{etelaeate file n}]
							$temp_primary_pic_data = json_decode($row->primary_pic);
							if (!empty($temp_primary_pic_data))
							{
								foreach ($temp_primary_pic_data as $uploaded_file_data)
								{
									array_push($primary_pic_uploadData, $uploaded_file_data);
								}
							}
						}
					}
				}

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
					'description' => $this->input->post('description'),
					'type_of_category' => $this->input->post('type_of_category'),
					'separate_buy' => $this->input->post('separate_buy'),
					'weight' => $this->input->post('weight'),
					'length' => $this->input->post('length'),
					'width' => $this->input->post('width'),
					'height' => $this->input->post('height'),
					'number' => $this->input->post('number'),
					'min_number' => $this->input->post('min_number'),
					'finish' => $this->input->post('finish'),
					'price' => $this->input->post('price'),
					'points_buy' => $this->input->post('points_buy'),
					'type_of_discount' => $this->input->post('type_of_discount'),
					'discount_amount' => $this->input->post('discount_amount'),
					'meta_tag_title' => $this->input->post('meta_tag_title'),
					'meta_tag_keywords' => $this->input->post('meta_tag_keywords'),
					'meta_tag_description' => $this->input->post('meta_tag_description'),
					'category' => $this->input->post('category'),
					'brand' => $this->input->post('brand'),
					'the_comment_registration_section_is_enabled' => $this->input->post('the_comment_registration_section_is_enabled'),
					'there_is_a_possibility_to_register_new_comments_for_the_user' => $this->input->post('there_is_a_possibility_to_register_new_comments_for_the_user'),
					'the_questions_and_answers_registration_section_is_active' => $this->input->post('the_questions_and_answers_registration_section_is_active'),
					'possibility_to_register_new_questions_and_answers_for_the_user' => $this->input->post('possibility_to_register_new_questions_and_answers_for_the_user'),
					'publish' => $this->input->post('publish'),
					'fields' => $fields,
					'options' => $options
				);


				//مقدار $primary_pic_uploadData یه چیزی شبیه اینه:
				/*
                Array ( [0] => Array ( [file_name] => Chrysanthemum.jpg [file_type] => image/jpeg [file_path] => /home/amya/public_html/demo/keshavarz/content/products/ [full_path] => /home/amya/public_html/demo/keshavarz/content/products/Chrysanthemum.jpg [raw_name] => Chrysanthemum [orig_name] => Chrysanthemum.jpg [client_name] => Chrysanthemum.jpg [file_ext] => .jpg [file_size] => 858.78 [is_image] => 1 [image_width] => 1024 [image_height] => 768 [image_type] => jpeg [image_size_str] => width="1024" height="768" ) [1] => Array ( [file_name] => Hydrangeas.jpg [file_type] => image/jpeg [file_path] => /home/amya/public_html/demo/keshavarz/content/products/ [full_path] => /home/amya/public_html/demo/keshavarz/content/products/Hydrangeas.jpg [raw_name] => Hydrangeas [orig_name] => Hydrangeas.jpg [client_name] => Hydrangeas.jpg [file_ext] => .jpg [file_size] => 581.33 [is_image] => 1 [image_width] => 1024 [image_height] => 768 [image_type] => jpeg [image_size_str] => width="1024" height="768" ) )
                 */
				if (!empty($primary_pic_uploadData))
				{
					//فایلهای ارسالی کاربر با موفقیت در سرور ذخیره شدند
					//جهت دسترسی در آینده، مشخصات فایل ذخیره شده را در پایگاه داده ثبت میکنیم
					$dadeh['primary_pic'] = json_encode($primary_pic_uploadData);
				}
				else
				{
					//برای primary_pic فایلی آپلود نشده است
				}


				//echo "<pre>"; print_r($fields) ; echo "</pre>";
				//die();
				if ($item_id)
				{
					//we should update this $edit_id into the database
					$dadeh['modify_date'] = time();
					$this->add_products_model->update($item_id, $dadeh);
					$this->site_map_lib->create_link_site_map();
				}
				else
				{
					//this is new Item
					$dadeh['insert_date'] = time();
					$dadeh['modify_date'] = time();
					$item_id = $this->add_products_model->insert($dadeh);
					$this->site_map_lib->create_link_site_map();
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'productsindexsuccess_msg');
				}

				if ($task == "save" && is_null($item_id))
				{
					//Go to Paretn Page
					redirect(base_url("products/add/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("products/index"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("products/add"));
				}
			}
		}

		//It's Edit state, then we need to simulate edit for user
		if ($item_id)
		{
			$this->session->set_userdata('page_title', 'ویرایش محصول');
			$page_name = 'edit_product';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$saved_info_in_database = $query->row_array();
			if ($fields != '')
			{
				//فیلدها قبلا سابمیت شده اند و ما میبایست برای set_value از آنها استفاده کنیم
				$saved_info_in_database['fields'] = $fields;
			}
			$html_output['item_data'] = $saved_info_in_database;
			if ($options != '')
			{
				//فیلدها قبلا سابمیت شده اند و ما میبایست برای set_value از آنها استفاده کنیم
				$saved_info_in_database['options'] = $options;
			}
			$html_output['item_data'] = $saved_info_in_database;
		}
		else
		{
			$page_name = 'add_product';
			//Default Item Data
			$this->session->set_userdata('page_title', 'افزودن محصول');
			if ($fields != null)
			{
				//فیلدها قبلا سابمیت شده اند و ما میبایست برای set_value از آنها استفاده کنیم
				//$fields = $fields;
			}
			else
			{
				$fields = '';
			}
			if ($options != null)
			{
				//فیلدها قبلا سابمیت شده اند و ما میبایست برای set_value از آنها استفاده کنیم
				//$fields = $fields;
			}
			else
			{
				$options = '';
			}

			$html_output['item_data'] = array(
				"id" => '',
				"title" => '',
				"title_alias_url" => '',
				"description" => '',
				"type_of_category" => '',
				"separate_buy" => '',
				"weight" => '',
				"length" => '',
				"width" => '',
				"height" => '',
				"number" => '',
				"min_number" => '',
				"finish" => '',
				"price" => '',
				"points_buy" => '',
				"type_of_discount" => '',
				"discount_amount" => '',
				"meta_tag_title" => '',
				"meta_tag_keywords" => '',
				"meta_tag_description" => '',
				"category" => '',
				"brand" => '',
				"the_comment_registration_section_is_enabled" => '',
				"there_is_a_possibility_to_register_new_comments_for_the_user" => '',
				"the_questions_and_answers_registration_section_is_active" => '',
				"possibility_to_register_new_questions_and_answers_for_the_user" => '',
				"publish" => 'yes',
				'fields' => $fields,
				'options' => $options
			);
		}


		///////////////////////////////////////////////////////////////////////

		if($item_id)
		{
			## REMOVE REQUESTED FILES ##
			$remove_primary_pic = $this->input->post('remove_primary_pic[]');

			if (isset($remove_primary_pic))
			{
				foreach ($remove_primary_pic as $value1)
				{
					$temp_file = '../content/products/'.$value1;
					$temp_thumb_pic = '../content/products/thumb/'.$value1;

					if (file_exists($temp_file))
					{
						//فایل بر روی سرور وجود دارد
						//میتوانیم آنرا حذف کنیم
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

					if (file_exists($temp_thumb_pic))
					{
						//فایل بر روی سرور وجود دارد
						//میتوانیم آنرا حذف کنیم
						if (unlink($temp_thumb_pic))
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
				$this->db->select('primary_pic');
				$query = $this->db->get('add_products');

				$temp_uploaded_files = array();
				foreach ($query->result() as $row)
				{
					$temp_primary_pic_data = json_decode($row->primary_pic, true);

					if (!empty($temp_primary_pic_data))
					{
						foreach ($temp_primary_pic_data as $index => $uploaded_file_data)
						{
							if (in_array($uploaded_file_data['file_name'], $remove_primary_pic))
							{
								//ردیف مورد نظر که باید حذف شود را پیدا کردیم
								//این ردیف را به پایگاه داده اضافه نمیکنیم
							}
							else
							{
								//اطلاعات فایلی که قبلا آپلود شده بود، میبایست همچنان در پایگاه داده بماند.
								array_push($temp_uploaded_files, $uploaded_file_data);
							}
						}
					}
				}

				//لیست جدید که فایلی که اخیرا حذف شده است در آن موجود نیست، را بروزرسانی میکنیم
				$dadeh['primary_pic'] = json_encode($temp_uploaded_files);
				$this->add_products_model->update($item_id, $dadeh);
			}

			## SHOW UPLOADED FILES TO EDIT ##
			$html_output['picture'] = '';
			$this->db->where(array('id' => $item_id));
			$query = $this->db->get('add_products');

			foreach ($query->result() as $row)
			{
				$temp_json = json_decode($row->primary_pic);
				if($temp_json != null)
				{
					foreach ($temp_json as $json_row)
					{
						$pic_name = $json_row->file_name;
						$pic_src = base_url('content/products/'.$pic_name);
						$pic_src = str_replace('/admin','',$pic_src);
						$html_output['picture'] .=
							'<div class="uploaded_pictures_holder">
							<label>
								<img src="'.$pic_src.'" height="100" width="100">
								<input type="checkbox" value='.$pic_name.' name="remove_primary_pic[]">
							</label>
						</div>';
					}
				}
			}
		}
		else
		{
			$html_output['picture'] = '';
		}



		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of categories //
		////////////////////////////////////////////////////
		$category_list = $this->products_category_model->get_where(array('publish' => 'yes'))->result();
		$html_output['categories_list'] = "<option value=''>".lang ('please_select')."</option>";

		if (count($category_list) > 0)
		{
			$category = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->add_products_model->get_where(array('id'=>$item_id))->row();
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
		// Create A list (HTML Select list) of brand //
		////////////////////////////////////////////////////
		$brand_list = $this->brand_model->get_where(array('publish' => 'yes'))->result();
		$html_output['brand_list'] = "<option value=''>".lang ('please_select')."</option>";

		if (count($brand_list) > 0)
		{
			$brand = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->add_products_model->get_where(array('id'=>$item_id))->row();
				$brand = $current_item->brand;
			}

			foreach ($brand_list as $row)
			{
				$html_output['brand_list'] .= '<option value="'.$row->id.'" '.set_select('brand', $row->id, ($row->id == $brand ? true : false)).'>'.$row->title.'</option>';
			}
		}

		if ($html_output['brand_list'] == '')
		{
			$html_output['brand_list'] = "<option value=''>".lang ('no_category')."</option>";
		}


		$data['page_name'] = $page_name;
        $data['option_file_link'] = base_url('download/file');
        $data['field_file_link'] = str_replace('/admin','', base_url('content/file'));

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

    public function products_category($item_id = null)
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$main_db_name = "products_category";
		$html_output = array();
		$this->load->model('products/products_category_model');
		$task = $this->input->post('task');

		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			//Are we editing current_item
			$posted_title= $this->input->post('title');
			$posted_title_alias_url = $this->input->post('title_alias_url');
			$query = $this->db->get($main_db_name);
			foreach ($query->result() as $row)
			{
				if($item_id==$row->id)
				{
					$item_id_title = $row->title;
					$item_id_title_alias_url = $row->title_alias_url;

					break;
				}
			}

			//Set Form Rules:
			$this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[2]|max_length[100]'.($item_id==null || $posted_title!=$item_id_title ? "|is_unique[$main_db_name.title]" :""));

			$this->form_validation->set_rules('title_alias_url', lang('title_alias_url'), 'trim|min_length[2]|max_length[200]'.($item_id==null || $posted_title_alias_url!=$item_id_title_alias_url ? "|is_unique[$main_db_name.title_alias_url]" :""));

			$this->form_validation->set_rules('parent', lang('parent'), $this->products_category_model->get_inlist_string($item_id));

			$this->form_validation->set_rules('description', lang('description'), 'trim');

			$this->form_validation->set_rules('template', lang('template'), 'trim');

			$this->form_validation->set_rules('meta_tag_title', lang('meta_tag_title'), 'trim|min_length[2]|max_length[200]');

			$this->form_validation->set_rules('meta_tag_keywords', lang('meta_tag_keywords'), 'trim|min_length[3]|max_length[1000]');

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
					'parent' => $this->input->post('parent'),
					'description' => $this->input->post('description'),
					'meta_tag_title' => $this->input->post('meta_tag_title'),
					'meta_tag_keywords' => $this->input->post('meta_tag_keywords'),
					'meta_tag_description' => $this->input->post('meta_tag_description'),
					'customized_template' => $this->input->post('customized_template'),
					'template' => $this->input->post('template'),
					'publish' => $this->input->post('publish')
				);

				if ($item_id)
				{
					//we should update this $edit_id into the database
					$this->products_category_model->update($item_id, $dadeh);
				}
				else
				{
					$item_id = $this->products_category_model->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'productsproducts_category_listsuccess_msg');
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("products/products_category/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("products/products_category_list"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("products/products_category"));
				}
			}
		}

		if ($item_id)
		{
			$this->session->set_userdata('page_title', 'ویرایش دسته‌بندی‌ محصول');
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
				"title_alias_url" => '',
				"parent" => '',
				"description" => '',
				"meta_tag_title" => '',
				"meta_tag_keywords" => '',
				"meta_tag_description" => '',
				"customized_template" => '',
				"template" => '',
				"publish" => 'yes'
			);
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of categories //
		////////////////////////////////////////////////////
		$category_list = $this->products_category_model->get_where(array('publish' => 'yes'))->result();
		$html_output['categories_list'] = "<option value=''>".lang ('without_parent')."</option>";

		if (count($category_list) > 0)
		{
			$parent = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->products_category_model->get_where(array('id'=>$item_id))->row();
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

    public function products_category_list()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'دسته‌بندی‌ محصول');
		//////////////////////
		// Define Variables //
		//////////////////////
		$main_db_table = "products_category";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("products/products_category/".$list_items[0]), 'location');
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
				$this->load->model('products/products_category_model');

				$this->db->select('products_category.id AS cat_id,brands.id AS brand_id,add_field.id AS field_id,add_products.id AS product_id,attribute_groups.id AS attribute_group_id,options.id AS option_id');
				$this->db->from('products_category');
				$this->db->join('options', 'products_category.id=options.category', 'left');
				$this->db->join('attribute_groups', 'products_category.id=attribute_groups.product_category', 'left');
				$this->db->join('brands', 'products_category.id=brands.parent', 'left');
				$this->db->join('add_field', 'products_category.id=add_field.category', 'left');
				$this->db->join('add_products', 'products_category.id=add_products.category', 'left');
				$query_join = $this->db->get()->result();

				$this->db->select('title,id');
				$category_p_title_list = $this->db->get('products_category')->result();

				foreach ($list_items as $value)
				{
					$delete_current_item = true;

					//get all categories that has this item as their parent
					$children_list = $this->products_category_model->get_where(array('parent'=>$value));
					$current_item = $this->products_category_model->get_where(array('id'=>$value))->row();

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

					$option_er = '';
					$attribute_group_er = '';
					$brand_er = '';
					$field_er = '';
					$product_er = '';
					$option_des = '';
					$attribute_group_des = '';
					$brand_des = '';
					$field_des = '';
					$product_des = '';
					foreach($query_join as $join_row)
					{
						if($join_row->cat_id == $value)
						{
							if($join_row->option_id != '' && $join_row->option_id != null)
							{
								if($join_row->attribute_group_id != null || $join_row->brand_id != null || $join_row->field_id != null || $join_row->product_id != null)
								{
									$option_er = 'تعدادی خصوصیت و';
									$option_des = 'خصوصیتها و';
								}
								else
								{
									$option_er = 'تعدادی خصوصیت';
									$option_des = 'خصوصیتهای';
								}
							}
							if($join_row->attribute_group_id != '' && $join_row->attribute_group_id != null)
							{
								if($join_row->brand_id != null || $join_row->field_id != null || $join_row->product_id != null)
								{
									$attribute_group_er = 'تعدادی گروه ویژگی و';
									$attribute_group_des = 'گروه ویژگیها و';
								}
								else
								{
									$attribute_group_er = 'تعدادی گروه ویژگی';
									$attribute_group_des = 'گروه ویژگیهای';
								}
							}
							if($join_row->brand_id != '' && $join_row->brand_id != null)
							{
								if($join_row->field_id != null || $join_row->product_id != null)
								{
									$brand_er = 'تعدادی برند و';
									$brand_des = 'برندها و';
								}
								else
								{
									$brand_er = 'تعدادی برند';
									$brand_des = 'برندهای';
								}
							}
							if($join_row->field_id != '' && $join_row->field_id != null)
							{
								if($join_row->product_id != null)
								{
									$field_er = 'تعدادی فیلد و';
									$field_des = 'فیلدها و';
								}
								else
								{
									$field_er = 'تعدادی فیلد';
									$field_des = 'فیلدهای';
								}
							}
							if($join_row->product_id != '' && $join_row->product_id != null)
							{
								$product_er = 'تعدادی محصول';
								$product_des = 'محصولات';
							}
						}
					}
					if($option_er != '' || $attribute_group_er != '' || $brand_er != '' || $field_er != '' || $product_er != '')
					{
						$cat_title = '';
						foreach($category_p_title_list as $cat_tit)
						{
							if($cat_tit->id == $value)
							{
								$cat_title = $cat_tit->title;
								break;
							}
						}
						$delete_current_item = false;
						$error_msg = "<div>$option_er $attribute_group_er $brand_er $field_er $product_er , در دسته‌بندی <<$cat_title>> وجود دارد. لطفا ابتدا, $option_des $attribute_group_des $brand_des $field_des $product_des  موجود را حذف نمایید.</div>";
						$this->mylib->set_error($error_msg);
					}

					if ($delete_current_item)
					{
						//delete item
						$this->products_category_model->delete(array('id' => $value));
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
			$this->db->or_like('title_alias_url' , $search);
			$this->db->or_like('description' , $search);
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
		$query = $this->db->get($main_db_table);

		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("products/products_category/".$row->id).'">'. $row->title .'</a></td>';
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
			'page_name' => 'categories',
			'main_db_table' => $main_db_table,
			'html_output' => $html_output
		);

		$this->load->view('template/header', $data);
		$this->load->view('menu', $data);
		$data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
		$this->load->view($this->uri->segment(1) . '/category/list', $data);
		$this->load->view('template/footer');

    }

    public function brands()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'برندها');
		//////////////////////
		// Define Variables //
		//////////////////////
		$main_db_table = "brands";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("products/add_brand/".$list_items[0]), 'location');
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
				$this->load->model('products/brand_model');

				$this->db->select('brands.id AS brand_id,add_products.id AS product_id');
				$this->db->from('brands');
				$this->db->join('add_products', 'brands.id=add_products.brand', 'left');
				$query_join = $this->db->get()->result();

				$this->db->select('title,id');
				$brands_p_title_list = $this->db->get('brands')->result();

				foreach ($list_items as $value)
				{
					$delete_current_item = true;
					$product_er = '';
					$product_des = '';
					foreach($query_join as $join_row)
					{
						if($join_row->brand_id == $value)
						{
							if($join_row->product_id != '' && $join_row->product_id != null)
							{
								$product_er = 'تعدادی محصول';
								$product_des = 'محصولات';
							}
						}
					}

					if($product_er != '')
					{
						$bra_title = '';
						foreach($brands_p_title_list as $bra_tit)
						{
							if($bra_tit->id == $value)
							{
								$bra_title = $bra_tit->title;
								break;
							}
						}
						$delete_current_item = false;
						$error_msg = "<div>$product_er , در برند <<$bra_title>> وجود دارد. لطفا ابتدا, $product_des  موجود را حذف نمایید.</div>";
						$this->mylib->set_error($error_msg);
					}

					if ($delete_current_item)
					{
						//delete item
						$this->brand_model->delete(array('id' => $value));
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
			$this->db->or_like('title_alias_url' , $search);
			$this->db->or_like('description' , $search);
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
		$query = $this->db->get($main_db_table);

		$temp_html = "";
		if ($query->num_rows() > 0 )
		{
			foreach ($query->result() as $row)
			{
				$temp_html .= "<tr>";
				$temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
				$temp_html .= '<td><a href="'.base_url("products/add_brand/".$row->id).'">'. $row->title .'</a></td>';
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
			'page_name' => 'brands',
			'main_db_table' => $main_db_table,
			'html_output' => $html_output
		);

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
		$data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
        $this->load->view($this->uri->segment(1) . '/brand/list', $data);
        $this->load->view('template/footer');
    }

    public function add_brand($item_id = null)
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$main_db_name = "brands";
		$html_output = array();
		$this->load->model('products/brand_model');
		$this->load->model('products/products_category_model');
		$task = $this->input->post('task');

		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			//Are we editing current_item
			$posted_title= $this->input->post('title');
			$posted_title_alias_url = $this->input->post('title_alias_url');
			$query = $this->db->get($main_db_name);
			foreach ($query->result() as $row)
			{
				if($item_id==$row->id)
				{
					$item_id_title = $row->title;
					$item_id_title_alias_url = $row->title_alias_url;

					break;
				}
			}

			//Set Form Rules:
			$this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[2]|max_length[100]'.($item_id==null || $posted_title!=$item_id_title ? "|is_unique[$main_db_name.title]" :""));

			$this->form_validation->set_rules('title_alias_url', lang('title_alias_url'), 'trim|min_length[2]|max_length[200]'.($item_id==null || $posted_title_alias_url!=$item_id_title_alias_url ? "|is_unique[$main_db_name.title_alias_url]" :""));

			$this->form_validation->set_rules('parent', lang('category'), 'required|'.$this->products_category_model->get_inlist_string());

			$this->form_validation->set_rules('description', lang('description'), 'trim');

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
					'parent' => $this->input->post('parent'),
					'description' => $this->input->post('description'),
					'meta_tag_title' => $this->input->post('meta_tag_title'),
					'meta_tag_keywords' => $this->input->post('meta_tag_keywords'),
					'meta_tag_description' => $this->input->post('meta_tag_description'),
					'publish' => $this->input->post('publish')
				);

				if ($item_id)
				{
					//we should update this $edit_id into the database
					$this->brand_model->update($item_id, $dadeh);
				}
				else
				{
					$item_id = $this->brand_model->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'productsbrandssuccess_msg');
				}

				if ($task == "save")
				{
					//Go to Paretn Page
					redirect(base_url("products/add_brand/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("products/brands"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("products/add_brand"));
				}
			}
		}

		if ($item_id)
		{
			$this->session->set_userdata('page_title', 'ویرایش برند');
			$page_name = 'edit_brand';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
		}
		else
		{
			$this->session->set_userdata('page_title', 'افزودن برند');
			$page_name = 'add_brand';
			//Default Item Data
			$html_output['item_data'] = array(
				"id" => '',
				"title" => '',
				"title_alias_url" => '',
				"parent" => '',
				"description" => '',
				"meta_tag_title" => '',
				"meta_tag_keywords" => '',
				"meta_tag_description" => '',
				"publish" => 'yes'
			);
		}

		////////////////////////////////////////////////////
		// Create A list (HTML Select list) of categories //
		////////////////////////////////////////////////////
		$category_list = $this->products_category_model->get_where(array('publish' => 'yes'))->result();
		$html_output['categories_list'] = "<option value=''>".lang ('please_select')."</option>";

		if (count($category_list) > 0)
		{
			$parent = 0;
			if(! is_null($item_id))
			{
				$current_item = $this->brand_model->get_where(array('id'=>$item_id))->row();
				$parent = $current_item->parent;
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
        $this->load->view($this->uri->segment(1) . '/brand/add', $data);
        $this->load->view('template/footer');

    }

    public function packages()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		//////////////////////
		// Define Variables //
		//////////////////////
		$main_db_table = "package";
		$html_output = array();

		$task = $this->input->post('task');
		$list_items = $this->input->post('list_items[]');

		if (isset($task) and isset($list_items))
		{
			if ($task == 'edit')
			{
				redirect(base_url("products/add_package/".$list_items[0]), 'location');
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
				$this->load->model('products/package_model');

				foreach ($list_items as $value)
				{
						//delete item
						$this->package_model->delete(array('id' => $value));
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
			$this->db->like('title' , $search);
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
				$temp_html .= '<td><a href="'.base_url("products/add_package/".$row->id).'">'. $row->title .'</a></td>';
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
			'page_name' => 'list_packages',
			'main_db_table' => $main_db_table,
			'html_output' => $html_output
		);

        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
		$data['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
        $this->load->view($this->uri->segment(1) . '/package/list', $data);
        $this->load->view('template/footer');
    }

    public function add_package($item_id = null)
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$main_db_name = "package";
		$html_output = array();
		$html_output['products'] = '';
		$view_product_item = '';
		$submited_products = array();
		$view_product = array();
		$product_id = $this->input->post('product_id[]');

		//give title and price foreach product id
		if($product_id != null)
		{
			//در حالت سابمیت هستیم
			foreach ($product_id as $index => $value) {
				$this->db->where(array('id' => $this->input->post("product_id[$index]")));
				$query = $this->db->get('add_products');
				$temp_title = '';
				$temp_price = '';

				array_push($submited_products, array(
					'product_id' => $this->input->post("product_id[$index]"),
					'type_of_discount' => $this->input->post("type_of_discount[$index]"),
					'discounted_price' => $this->input->post("discounted_price[$index]")
				));

				if ($query->num_rows() > 0)
				{
					//محصول در پایگاه داده وجود دارد
					$temp_row = $query->row();
					$temp_title = $temp_row->title;
					$temp_price = $temp_row->price;

					$temp1 = array(
						'product_id' => $this->input->post("product_id[$index]"),
						'type_of_discount' => $this->input->post("type_of_discount[$index]"),
						'discounted_price' => $this->input->post("discounted_price[$index]"),
						'title' => $temp_title,
						'price' => $temp_price
					);
					array_push($view_product, $temp1);

					/*
					if ($item_id and $temp_title != '') {

					}
					 */
				}
				else
				{
					//محصول از پایگاه داده حذف شده است
					//بنابراین نیازی نیست این محصول را مجدد در لیست نمایش دهیم.
				}
			}
			$view_product_item = json_encode($view_product);
		}
		else if(! is_null($item_id))
		{
			//در حالت ویرایش هستیم
			$this->db->where(array('id' => $item_id));
			$query = $this->db->get($main_db_name);
			foreach ($query->result() as $row) {
				$temp_json = json_decode($row->products);
				foreach ($temp_json as $json_row) {
					$this->db->where(array('id' => $json_row->product_id));
					$query2 = $this->db->get('add_products');
					$temp_title = '';
					$temp_price = '';

					if ($query2->num_rows() > 0)
					{
						//محصول در پایگاه داده وجود دارد
						$temp_row = $query2->row();
						$temp_title = $temp_row->title;
						$temp_price = $temp_row->price;

						$temp1 = array(
							'product_id' => $json_row->product_id,
							'type_of_discount' => $json_row->type_of_discount,
							'discounted_price' => $json_row->discounted_price,
							'title' => $temp_title,
							'price' => $temp_price
						);
						array_push($view_product, $temp1);
					}
					else
					{
						//محصول از پایگاه داده حذف شده است
						//بنابراین نیازی نیست این محصول را مجدد در لیست نمایش دهیم.
					}
				}
			}
			$view_product_item = json_encode($view_product);
		}

		$this->load->model('products/package_model');

		$task = $this->input->post('task');

		if($task == 'save' || $task == 'save_and_new' || $task == 'save_and_close')
		{
			//Set Form Rules:
			$this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[3]|max_length[200]');

			$this->form_validation->set_rules('description_package', lang('description_package'), 'trim');

			$this->form_validation->set_rules('how_to_apply_discounts', lang('how_to_apply_discounts'), 'required|in_list[discounted_components,package_discount]');

			if($this->input->post('how_to_apply_discounts') == 'package_discount')
			{
				$this->form_validation->set_rules('type_of_discount_on_whole_of_package', lang('type_of_discount_on_whole_of_package'), 'required|in_list[percentage,static_value]');

				$this->form_validation->set_rules('discounted_price_on_whole_of_package', lang('discounted_price_on_whole_of_package'), 'trim|required|is_natural|greater_than[0]');
			}

			$this->form_validation->set_rules('publish', lang('publish'), 'required|in_list[yes,no]');

			$product_id = $this->input->post('product_id[]');
			if(! isset($product_id))
			{
				//محصولی در این بسته قرار داده نشده است
				//یک روول ایجاد میکنیم که خطا بدهد که فرم بدون درج محصول نمیتواند ذخیره شود
				$this->form_validation->set_rules('product', lang('product'), 'required', array('required' => lang('you_should_add_one_product_at_minimum')));
			}
			else
			{
				//محصولی سابمیت شده است. اما باید چک کنیم که آیا واقعا مقداری در محصولات سابمیت شده درج شده است یا خیر؟
				$is_anything_submited = false;

				foreach ($product_id as $index => $value) {
					if (intval($this->input->post("product_id[$index]")) > 0)
					{
						//یک محصول پیدا شد که مقداری برای آن تعیین شده است.
						//یعنی کاربر حداقل یک محصول را سابمیت کرده است
						$is_anything_submited = true;
					}
				}

				if (! $is_anything_submited)
				{
					//میبایست خطا بدهیم که حداقل یک محصول باید اضافه شده باشد.
					$this->form_validation->set_rules('product', lang('product'), 'required', array('required' => lang('you_didnt_choose_any_product')));
				}
			}

			if ($this->form_validation->run() == TRUE)
			{
				//$view_product_item = json_encode($view_product);

				$dadeh = array
				(
					'title' => $this->input->post('title'),
					'description_package' => $this->input->post('description_package'),
					'publish' => $this->input->post('publish'),
					'how_to_apply_discounts' => $this->input->post('how_to_apply_discounts'),
					'type_of_discount_on_whole_of_package' => $this->input->post('type_of_discount_on_whole_of_package'),
					'discounted_price_on_whole_of_package' => $this->input->post('discounted_price_on_whole_of_package'),
					'products' => json_encode($submited_products)
				);

				if ($item_id)
				{
					//we should update this $edit_id into the database
					$this->package_model->update($item_id, $dadeh);
				}
				else
				{
					//this is new Item
					$item_id = $this->package_model->insert($dadeh);
				}

				if ($task == "save" || $task == "save_and_new")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'));
				}
				else if ($task == "save_and_close")
				{
					//set success message
					$this->mylib->set_success(lang('success_msg'), 'productspackagessuccess_msg');
				}

				if ($task == "save" && ! is_null($item_id))
				{
					//Go to Paretn Page
					redirect(base_url("products/add_package/".$item_id));
				}

				else if ($task == "save_and_close")
				{
					//Go to Paretn Page
					redirect(base_url("products/packages"));
				}
				else if ($task == "save_and_new")
				{
					//Refresh current page
					redirect(base_url("products/add_package"));
				}
			}

		}

		//It's Edit state, then we need to simulate edit for user
		if ($item_id)
		{
			$page_name = 'edit_package';
			$this->db->where('id', $item_id);
			$query = $this->db->get($main_db_name);
			$html_output['item_data'] = $query->row_array();
			$html_output['item_data']['view_product_item'] = $view_product_item;
		}
		else
		{
			$page_name = 'add_package';
			//Default Item Data

			$html_output['item_data'] = array(
				"id" => '',
				"title" => '',
				"description_package" => '',
				"how_to_apply_discounts" => '',
				"type_of_discount_on_whole_of_package" => '',
				"discounted_price_on_whole_of_package" => '',
				"publish" => 'yes',
				"view_product_item" => $view_product_item
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
        $this->load->view($this->uri->segment(1) . '/package/add', $data);
        $this->load->view('template/footer');
    }
}
