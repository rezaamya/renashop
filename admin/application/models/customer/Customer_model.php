<?php
class Customer_model extends CI_Model {
	private $db_table_name = 'customer';

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

	public function find_parent($condition_array)
	{
		$this->db->where($condition_array);
		$query = $this->db->get($this->db_table_name);
		foreach ($query->result() as $row)
		{
			$parent_title = $row->title;
			return $parent_title;
		}
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

}?>
