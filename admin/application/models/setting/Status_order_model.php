<?php
class Status_order_model extends CI_Model {
	private $db_table_name = 'status_order';

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

	public function get_inlist_string ()
	{
		//$item_id is the ID of the item that we are editing
		$this->db->where(array('publish' => 'yes'));
		$query = $this->db->get($this->db_table_name);
		$categories_id = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				array_push($categories_id, $row->id);
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

}?>
