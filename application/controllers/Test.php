<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    public function index()
    {
        //$this->user_order();
    }
	private function user_order()
	{
        $user = User_helper::get_user();

        $this->db->from($this->config->item('table_setup_user').' user');
        $this->db->select('user.employee_id,user.user_name');
        $this->db->select('user_info.user_id,user_info.id');
        $this->db->join($this->config->item('table_setup_user_info').' user_info','user.id = user_info.user_id','INNER');

        $this->db->where('user_info.revision',1);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $data=array();
            $data['ordering']=intval($result['employee_id']);
            $this->db->where('id',$result['id']);
            $this->db->update($this->config->item('table_setup_user_info'), $data);
            echo '<PRE>';
            print_r($data);
            print_r($result['employee_id']);
            echo '</PRE>';
        }

	}
}
