<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Articles extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->session->set_userdata('last_activity', time());
	}

    /**
     * Dar in safhe Articles ra modiriat (ADD, DELETE, EDIT) mikonim
     */
    public function index()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'لیست محتوا');
        $main_db_name = "articles";     
        $html_output = array();
        
        $task = $this->input->post('task');
        $list_items = $this->input->post('list_items[]');

        if (isset($task) and isset($list_items))
        {
            if ($task == 'edit')
            {
                redirect(base_url("articles/add/".$list_items[0]), 'location');
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
                $this->load->model('articles/content');
				$files_db_query = $this->db->get('files')->result();
				$article_query = $this->db->get('articles')->result();
                foreach ($list_items as $value)
                {
					//delete article files
					foreach($article_query as $article_row)
					{
						if($article_row->id == $value)
						{
							$files_id_array = json_decode($article_row->files_id);
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
							break;
						}
					}
					//delete article
					$this->content->delete(array('id' => $value));
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
            $this->db->or_like('title_alias_url' , $search);
            $this->db->or_like('intro' , $search);
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

		$this->load->model('articles/categories');
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
            	$parent_row = $row->parent;
				$parent_title = $this->categories->find_parent(array('id'=>$parent_row));

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

				$this->db->where(array('id' => $row->author_id));
				$this->db->select('first_name,last_name');
				$temp_user_query = $this->db->get('users')->result();
				$temp_first_name = '';
				$temp_last_name = '';
				foreach($temp_user_query as $us_row)
				{
					$temp_first_name = $us_row->first_name;
					$temp_last_name = $us_row->last_name;
				}

				$temp_html .= "<tr>";
                $temp_html .= '<td scope="row"><input type="checkbox" value="'.$row->id.'" name="list_items[]"></td>';
                $temp_html .= '<td><a href="'.base_url("articles/add/".$row->id).'">'. $row->title .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("articles/add_category/".$row->parent).'">'. $parent_title .'</a></td>';
				$temp_html .= '<td><a href="'.base_url("users/add/".$row->author_id).'">'.$temp_first_name.' '.$temp_last_name.'</a></td>';
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

        $data['page_name'] = 'list_article';
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

        $main_db_name = "articles";
        $html_output = array();
        $this->load->model('articles/categories');
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

            $this->form_validation->set_rules('parent', lang('parent'),'required|'. $this->categories->get_inlist_string());

            $this->form_validation->set_rules('intro', lang('intro'), 'trim');

            $this->form_validation->set_rules('full_content', lang('full_content'), 'trim');

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
            	$files_id = $this->input->post('files[]');
				$dadeh = array
                (
                	'title' => $this->input->post('title'),
                    'title_alias_url' => $title_alias,
                    'parent' => $this->input->post('parent'),
                    'intro' => $this->input->post('intro'),
                    'full_content' => $this->input->post('full_content'),
                    'meta_tag_title' => $this->input->post('meta_tag_title'),
                    'meta_tag_keywords' => $this->input->post('meta_tag_keywords'),
                    'meta_tag_description' => $this->input->post('meta_tag_description'),
                    'publish' => $this->input->post('publish'),
					'files_id' => json_encode($files_id)
                );

                $this->load->model('articles/content');
                if ($item_id)
                {
                    //we should update this $edit_id into the database
					$dadeh['modify_date'] = time();
					$dadeh['author_id'] = $this->session->userdata('id');
                    $this->content->update($item_id, $dadeh);
                }
                else
                {
                    //this is new Item
					$dadeh['author_id'] = $this->session->userdata('id');
					$dadeh['modify_date'] = time();
					$dadeh['insert_date'] = time();
                    $item_id = $this->content->insert($dadeh);
                }

                if ($task == "save" || $task == "save_and_new")
                {
                    //set success message
                    $this->mylib->set_success(lang('success_msg'));
                }
                else if ($task == "save_and_close")
                {
                    //set success message
                    $this->mylib->set_success(lang('success_msg'), 'articlesindexsuccess_msg');
                }

                if ($task == "save")
                {
                    //Go to Paretn Page
                    redirect(base_url("articles/add/".$item_id));
                }

                else if ($task == "save_and_close")
                {
                    //Go to Paretn Page
                    redirect(base_url("articles/index"));
                }
                else if ($task == "save_and_new")
                {
                    //Refresh current page
                    redirect(base_url("articles/add"));
                }
            }
        }


        //It's Edit state, then we need to simulate edit for user
        if ($item_id)
        {
        	$this->session->set_userdata('page_title', 'ویرایش محتوی');
            $page_name = 'edit_content';
            $this->db->where('id', $item_id);
            $query = $this->db->get($main_db_name);
            $html_output['item_data'] = $query->row_array();
            $files_id_array = json_decode($html_output['item_data']['files_id']);
        }
        else
        {
			$this->session->set_userdata('page_title', 'افزودن محتوا');
        	$page_name = 'add_article';
            //Default Item Data
            $html_output['item_data'] = array(
                "id" => '',
                "title" => '',
                "title_alias_url" => '',
                "parent" => '',
                "intro" => '',
                "full_content" => '',
                "meta_tag_title" => '',
                "meta_tag_keywords" => '',
                "meta_tag_description" => '',
                "publish" => 'yes'
            );
			$files_id_array = $this->input->post('files[]');
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
					$html_output['item_data']['files_view'] .= '<div class="upload_module_holder input-group offset-sm-2 col-sm-10 mb-1 uploaded"><div class="loading_holder"> <div class="content"> <div class="loader_spin"></div> <span>'.lang("please_wait").'</span> </div> </div> <div class="upload_module"> <div class="message_holder"></div> <input class="w3-border" type="file"> <div class="btn btn-success btn-sm" onclick="upload_file(this, \'article\');">'.lang("upload").' </div><button type="button" class="close ml-2 mt-1" onclick="delete_parent(this,\'upload_module_holder\');"> <span aria-hidden="true">×</span> </button> </div> <div class="uploaded_module"> <div class="message_holder">فايل «'.$files_name.'» با موفقيت ذخيره شد.</div> <div class="btn btn-danger btn-sm remove_btn" onclick="remove_uploaded_file(this,\'article\')">'.lang("delete_file").'</div> <input class="uploaded_file" name="files[]" type="hidden" value="'.$fi_row.'" file_name="'.$files_name.'"> <div onclick="copy_to_clipboard(\''.$file_src.'\'); var this_node = this; this_node.innerHTML = \'آدرس در حافظه کپی شد\'; setTimeout(function(){ this_node.innerHTML = \'کپی آدرس فایل در حافظه\'; }, 3000);" class="btn btn-success btn-sm copy_to_clipboard">'.lang("copy_file_address_in_memory").'</div> </div> </div>';
				}
			}
		}

        ////////////////////////////////////////////////////
        // Create A list (HTML Select list) of categories //
        ////////////////////////////////////////////////////
		$this->db->where(array('publish' => 'yes'));
        $category_list = $this->db->get('article_categories')->result();
		$html_output['categories_list'] = "<option value=''>".lang ('please_select')."</option>";

        if (count($category_list) > 0)
        { 
            $parent = 0;
            if(! is_null($item_id))
            { 
                $this->load->model('articles/content');
                $current_item = $this->content->get_where(array('id'=>$item_id))->row();
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
        $this->load->view($this->uri->segment(1) . '/add', $data);
        $this->load->view('template/footer');
    }

    public function categories()
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

		$this->session->set_userdata('page_title', 'دسته‌بندی‌ محتوی');
        //////////////////////
        // Define Variables //
        //////////////////////
        $main_db_table = "article_categories";
        $html_output = array();
        
        $task = $this->input->post('task');
        $list_items = $this->input->post('list_items[]');

        if (isset($task) and isset($list_items))
        {
            if ($task == 'edit')
            {
                redirect(base_url("articles/add_category/".$list_items[0]), 'location');
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
                $this->load->model('articles/categories');
				$b = 1;
				$this->db->select('parent');
				foreach ($list_items as $value_1)
				{
					if($b == 1)
					{
						$this->db->where(array('parent' => $value_1));
					}
					else
					{
						$this->db->or_where(array('parent' => $value_1));
					}
					$b++;
				}
				$category_p_list = $this->db->get('articles')->result();

				$t = 0;
				if(isset($category_p_list))
				{
					if($category_p_list != '' && $category_p_list != null && $category_p_list != array())
					{
						$this->db->select('title,id');
						foreach($category_p_list as $cat_id)
						{
							if($t == 0)
							{
								$this->db->where(array('id' => $cat_id->parent));
							}
							else
							{
								$this->db->or_where(array('id' => $cat_id->parent));
							}
							$t++;
						}
						$category_p_title_list = $this->db->get('article_categories')->result();
					}
				}

                foreach ($list_items as $value)
                {
                    $delete_current_item = true;

                    //get all categories that has this item as their parent
                    $children_list = $this->categories->get_where(array('parent'=>$value));
                    $current_item = $this->categories->get_where(array('id'=>$value))->row();

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

					if(isset($category_p_list))
					{
						if($category_p_list != '' && $category_p_list != null && $category_p_list != array())
						{
							$del_cat = 'yes';
							foreach($category_p_list as $cat_id_2)
							{
								if($value == $cat_id_2->parent)
								{
									$del_cat = 'no';
									break;
								}
							}
							if($del_cat == 'no' && isset($category_p_title_list))
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
								$error_msg = "<div>تعدادی محتوا, در دسته‌بندی <<$cat_title>> وجود دارد. لطفا ابتدا محتواهای موجود را حذف نمایید.</div>";
								$this->mylib->set_error($error_msg);
							}
						}
					}

					if ($delete_current_item)
                    {
                        //delete item
                        $this->categories->delete(array('id' => $value));
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
                $temp_html .= '<td><a href="'.base_url("articles/add_category/".$row->id).'">'. $row->title .'</a></td>';
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

    public function add_category($item_id = null)
    {
		if (! $this->mylib->is_login())
		{
			$this->mylib->is_login(true);
		}

        $main_db_name = "article_categories";
        $html_output = array();
        $this->load->model('articles/categories');
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

            $this->form_validation->set_rules('parent', lang('parent'), $this->categories->get_inlist_string($item_id));

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
                    $this->categories->update($item_id, $dadeh);
                }
                else
                {
                    $item_id = $this->categories->insert($dadeh);
                }

                if ($task == "save" || $task == "save_and_new")
                {
                    //set success message
                    $this->mylib->set_success(lang('success_msg'));
                }
                else if ($task == "save_and_close")
                {
                    //set success message
                    $this->mylib->set_success(lang('success_msg'), 'articlescategoriessuccess_msg');
                }

                if ($task == "save")
                {
                    //Go to Paretn Page
                    redirect(base_url("articles/add_category/".$item_id));
                }

                else if ($task == "save_and_close")
                {
                    //Go to Paretn Page
                    redirect(base_url("articles/categories"));
                }
                else if ($task == "save_and_new")
                {
                    //Refresh current page
                    redirect(base_url("articles/add_category"));
                }
            }
        }

        if ($item_id)
        {
			$this->session->set_userdata('page_title', 'ویرایش دسته‌بندی');
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
                "publish" => 'yes'
            );
        }

        ////////////////////////////////////////////////////
        // Create A list (HTML Select list) of categories //
        ////////////////////////////////////////////////////
		$this->db->where(array('publish' => 'yes'));
		$category_list = $this->db->get('article_categories')->result();
		$html_output['categories_list'] = "<option value=''>".lang ('without_parent')."</option>";

        if (count($category_list) > 0)
        {
            $parent = 0;
            if(! is_null($item_id))
            { 
                $current_item = $this->categories->get_where(array('id'=>$item_id))->row();
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
       // $html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
        $html_output['sys_msg'] = $this->load->view('template/sys_msg', $data, TRUE);
        $data['html_output'] = $html_output;
        $this->load->view('template/header', $data);
        $this->load->view('menu', $data);
        $this->load->view($this->uri->segment(1) . '/category/add', $data);
        $this->load->view('template/footer');
    }
}
