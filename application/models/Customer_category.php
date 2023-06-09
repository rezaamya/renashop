<?php
class Customer_category extends CI_Model {
	private $db_table_name = 'customer_category';

	public function get_inlist_string ($item_id = null)
	{
		//$item_id is the ID of the item that we are editing
		$this->db->where(array('publish' => 'yes'));
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

}?>
