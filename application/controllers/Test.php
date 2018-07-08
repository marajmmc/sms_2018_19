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
        echo base_url('maraj');
        echo "<br />";
        echo site_url('maraj');
    }
    public function get_child_ids_designation($designation_id)
    {
        $CI=& get_instance();
        $CI->db->from($CI->config->item('table_login_setup_designation'));
        $CI->db->order_by('ordering');
        $results=$CI->db->get()->result_array();

        $child_ids[0]=0;
        $parents=array();
        foreach($results as $result)
        {
            $parents[$result['parent']][]=$result;
        }
        $this->get_sub_child_ids_designation($designation_id, $parents,$child_ids);
        echo "<pre>";
        print_r($child_ids);
        echo "</pre>";

        return $child_ids;
    }

    public function get_sub_child_ids_designation($id, $parents, &$child_ids)
    {
        if(isset($parents[$id]))
        {
            foreach($parents[$id] as $child)
            {
                $child_ids[$child['id']]=$child['id'];
                if(isset($parents[$child['id']]) && sizeof($parents[$child['id']])>0 )
                {
                    $this->get_sub_child_ids_designation($child['id'], $parents,$child_ids);
                }
            }
        }
    }


    public function all_child_test($id, $parents)
    {
        /*echo "<pre>";
        print_r($parents[$id]);
        echo "</pre>";*/
        if(isset($parents[$id]))
        {
            foreach($parents[$id] as $child)
            {
                /*echo "<pre>";
                print_r($child);
                echo "</pre>";*/
                echo $child['name'].'-'.$child['id'].'-'.$child['parent'].'<br />';
                if(isset($parents[$child['id']]) && sizeof($parents[$child['id']])>0)
                {
                    //for($i=0; $i<sizeof($parents[$child['id']]);$i++)
                    foreach($parents[$child['id']] as $c)
                    {
                        echo ' &nbsp;&nbsp; -- '.$c['name'].'-'.$c['id'].'-'.$c['parent'].'<br />';

                    }
                }

            }
        }
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
    public function sqlProcedure()
    {
        $query = $this->db->query("call TransferOrder(1, 1522727377,5,1522727377,6,1522727377,1522727377,5.5)");
        $result = $query->result();
        echo "<pre>";
        print_r($result);
        echo "</pre>";

    }
}
