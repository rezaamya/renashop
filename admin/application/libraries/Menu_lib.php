<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_lib
{
	protected $CI;

	public function __construct()
	{
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$this->CI->load->helper('url');
	}

	/*public function find_childe_lib($menu_category_id)
	{
		$this->CI->load->model('menu/menu_model');
		$this->CI->load->model('menu/menu_category');

		$current_item = $this->CI->menu_category->get_where(array('id' => $menu_category_id))->row();
		$temp1 = $current_item->target_id;

		if($temp1 != null)
		{
			$this->CI->menu_model->find_childe_menu($temp1);
		}
	}*/

	/*public function menu_lib_info($menu_model_id, $menu)
	{
		$this->CI->load->model('menu/menu_model');
		$this->CI->load->model('menu/menu_category');

		$current_item = $this->CI->menu_model->get_where(array('id' => $menu_model_id))->row();

		$menu = array(
			'title' => $current_item->title,
			'id' => $current_item->id,
			'active' => 'true'
		);

		if($current_item->childe_id != null)
		{
			$childe = json_decode($current_item->childe_id);

			foreach($childe as $index => $value)
			{
				$menu['children'][$index] = $this->CI->menu_lib->menu_lib_info($value, array());
			}
			print_r($menu);
		}

	}*/

	public function get_category_menus ($category_id, $active_id)
	{
		$this->CI->load->model('menu/menu_model');

		$query = $this->CI->menu_model->get_where(array('category_id' => $category_id, 'parent_id' => null));

		$menu = array();
		foreach ($query->result_array() as $row)
		{
			$result = $this->get_children_for ($row['id'], $active_id);
			$row['children'] = $result['items'];
			$row['is_active'] = $result['is_active'];
			array_push($menu, $row);
		}

		return $menu;
	}

	public function get_children_for ($current_menu_id, $active_id)
	{
		$this->CI->load->model('menu/menu_model');

		$query = $this->CI->menu_model->get_where(array('parent_id' => $current_menu_id));

		$item_list = array();
		$is_active = 'no';
		foreach ($query->result_array() as $row)
		{
			$result = $this->get_children_for ($row['id'], $active_id);
			$row['children'] = $result['items'];
			$row['is_active'] = $result['is_active'];
			$is_active = $result['is_active'];
			array_push($item_list, $row);
		}

		if ($current_menu_id == $active_id)
		{
			$is_active = 'yes';
		}

		return array('items' => $item_list, 'is_active' => $is_active);
	}

}
