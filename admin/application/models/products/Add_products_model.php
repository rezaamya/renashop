<?php
class Add_products_model extends CI_Model {
	private $db_table_name = 'add_products';

	public function get_all()
	{
		$query = $this->db->get($this->db_table_name);
		return $query->result();
	}

	public function get_where($condition_array)
	{
		$this->db->where($condition_array);
		$query = $this->db->get($this->db_table_name);
		return $query;
	}

	public function get_inlist_string ($item_id = null)
	{
		//$item_id is the ID of the item that we are editing
		$this->db->group_start();
		$this->db->where(array('publish' => 'yes'));
		$this->db->group_end();
		$this->db->group_start();
		$this->db->where(array('type_of_category' => 'virtual'));
		$this->db->or_where(array('number >' => 0));
		$this->db->or_where(array('finish' => 2));
		$this->db->or_where(array('finish' => 3));
		$this->db->group_end();
		$query = $this->db->get($this->db_table_name);
		$categories_id = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row) {
				if ($item_id == $row->id)
				{
					//do nothing.
					//this item can't be a parent of itself
				}
				else
				{
					array_push($categories_id, $row->id);
				}
			}

			if (count($categories_id) > 0)
			{
				return 'in_list['.implode(",",$categories_id).']';
			}
		}

		//There is no result to return
		return '';
	}

	public function insert($dadeh)
	{
		$this->db->insert($this->db_table_name, $dadeh);
		if ($this->db->insert_id())
		{
			return $this->db->insert_id();
		}
		else
		{
			return False;
		}
	}
	public function delete ($where_array)
	{
		$this->db->delete($this->db_table_name, $where_array);
	}

	public function update($item_id, $dadeh)
	{
		$this->db->where('id',$item_id);
		$this->db->update($this->db_table_name, $dadeh);
	}

	public function set_update ($item_id, $dadeh)
	{
		$this->db->set($dadeh);
		$this->db->where('id', $item_id);
		$this->db->update($this->db_table_name);
	}

}?>
