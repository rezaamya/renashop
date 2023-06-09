<?php
class Customer_model extends CI_Model {
	private $db_table_name = 'customer';

	public function update($item_id, $dadeh)
	{
		$this->db->where('id',$item_id);
		$this->db->update($this->db_table_name, $dadeh);
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

}?>
