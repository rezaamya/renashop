<?php
class Menu_model extends CI_Model {
	private $db_table_name = 'menu';


	public function get_where($condition_array)
	{
		$this->db->where($condition_array);
		$query = $this->db->get($this->db_table_name);
		return $query;
	}

	public function create_menu_item ($temp_menu_array)
	{
		$temp_menu_html = "<ul>";

		foreach ($temp_menu_array as $row)
		{
			$children = "";
			if (! empty($row['children']))
			{
				$children = $this->create_menu_item ($row['children']);
			}

			$href = '#';
			if ($row['type'] == "home")
			{
				$href = base_url('');
			}

			else if($row['type'] == "login" || $row['type'] == "profile" || $row['type'] == "logout" || $row['type'] == 'register')
			{
				$href = base_url($row['type']);
			}

			else if($row['type'] == "link")
			{
				$link_attr = json_decode($row['attribute']);
				if(isset($link_attr->link_address))
				{
					$href = $link_attr->link_address;
				}
			}

			else
			{
				if($row['target_id'] != '')
				{
					$href = base_url('pages/'.$row['type'].'/'.$row['target_id']."/".$row['id']);
				}
				else
				{
					$href = base_url('pages/'.$row['type'].'/'.$row['id']);
				}
			}

			####################
			## GET Attributes ##
			####################

			$temp_title = $row['title'];
			$temp_class = '';

			$attributes = $row['attribute'];
			if ($attributes != '')
			{
                $temp_json = json_decode($attributes);
				if(isset($temp_json->icon) && isset($temp_json->icon_position))
				{
					if($temp_json->icon_position == 'left')
					{
						$temp_title = $temp_title.$temp_json->icon;
					}
					else
					{
						$temp_title = $temp_json->icon.$temp_title;
					}
				}

				if(isset($temp_json->class))
				{
					$temp_class = $temp_json->class;
				}
			}

			if ($row['is_active'] == 'yes')
			{
				$temp_class .= " active";
			}

           $has_children = $this->get_where(array('parent_id' => $row['id'], 'publish' => 'yes'))->result();

			$show = 'no';
			if(isset($temp_json))
			{
				if(isset($temp_json->access))
				{
					if($temp_json->access == 'registered' && $this->session->has_userdata('id'))
					{
						$show = 'yes';
					}
					elseif($temp_json->access == 'guest' && !$this->session->has_userdata('id'))
					{
						$show = 'yes';
					}
					elseif($temp_json->access == 'all')
					{
						$show = 'yes';
					}
				}
			}

			///////////////////////////////////////
			/////////open page in new tab/////////
			/////////////////////////////////////
			$page_open_type = '';
			if(isset($temp_json))
			{
				if(isset($temp_json->page_open_type))
				{
					if($temp_json->page_open_type == 'open_in_new_window' && $row['type'] == "link")
					{
						$page_open_type = 'target="_blank"';
					}
				}
			}

              ////////////////////////////////////////
			 ////////created link for menu///////////
			////////////////////////////////////////
           if(!isset($temp_json->access) || $show == 'yes' || $attributes == '' || $attributes == null)
		   {
			   if($row['publish'] == 'yes')
			   {
				   if($row['parent_id'] == '' && $has_children != array())
				   {
					   $temp_menu_html .= "<li class='has_children $temp_class'><a href='$href' $page_open_type>".$temp_title." <i class='fas fa-caret-down'></i></a>$children</li>";
				   }
				   else if($row['parent_id'] != '' && $has_children != array())
				   {
					   $temp_menu_html .= "<li class='has_children $temp_class'><a href='$href' $page_open_type>".$temp_title." <i class='fas fa-caret-left'></i></a>$children</li>";
				   }
				   else
				   {
					   $temp_menu_html .= "<li class='$temp_class'><a class='' href='$href' $page_open_type>".$temp_title."</a>$children</li>";
				   }
			   }
		   }
		}

		$temp_menu_html .= "</ul>";

		return $temp_menu_html;
	}

}?>
