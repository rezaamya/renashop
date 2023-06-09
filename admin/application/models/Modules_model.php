<?php
class Modules_model extends CI_Model {
	private $db_table_name = 'modules';

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

	public function position_array()
	{
		$position_array = array("position_1","position_2","position_3","position_4","position_5","position_6","position_7","position_8","position_9","position_10","position_11","position_12","position_13","position_14","position_15","position_16","position_17","position_18","position_19","position_20");
		return $position_array;
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
