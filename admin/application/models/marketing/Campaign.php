<?php
class Campaign extends CI_Model {

    public function get_all()
    {
        $query = $this->db->get('campaignes');
        return $query->result();
    }

	public function get_where($condition_array)
	{
		$this->db->where($condition_array);
		$query = $this->db->get('campaignes');
		return $query;
	}

    public function insert($dadeh)
    {
    	$this->db->insert('campaignes', $dadeh);
    	if ($this->db->insert_id())
    	{
    		return $this->db->insert_id();
    	}
    	else
    	{
    		return False;
    	}
    }

      public function update($item_id, $dadeh)
      {
             $this->db->set('campaign_name', $this->input->post('campaign_name'));
             $this->db->set('campaign_description', $this->input->post('campaign_description'));
             $this->db->set('tracking_code', $this->input->post('tracking_code'));
             $this->db->set('example', $this->input->post('example'));
             $this->db->set('publish', $this->input->post('publish'));
             $this->db->where('id',$item_id);
             $this->db->update('campaignes');
      }

	public function delete ($where_array)
	{
		$this->db->delete('campaignes', $where_array);
	}

}?>
