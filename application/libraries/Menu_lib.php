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

	public function get_category_menus ($category_id, $active_id)
	{
		$this->CI->load->model('menu/menu_model');
		$this->CI->db->order_by('sort', 'ASC');
		$this->CI->db->where(array('category_id' => $category_id, 'parent_id' => '', 'publish' => 'yes'));
		$query = $this->CI->db->get('menu');
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
		$this->CI->db->order_by('sort', 'ASC');
		$this->CI->db->where(array('parent_id' => $current_menu_id, 'publish' => 'yes'));
		$query = $this->CI->db->get('menu');
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
