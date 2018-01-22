<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_controller extends Root_Controller
{
    private  $message;
    public $permissions;
    public function __construct()
    {
        parent::__construct();
        $this->message='';
    }
    public function index()
    {
        die();
    }
    /*public function get_dropdown_armvarieties_by_croptypeid()
    {
        $crop_type_id = $this->input->post('crop_type_id');
        $html_container_id='#variety_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_login_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$crop_type_id,'status ="'.$this->config->item('system_status_active').'"','whose ="ARM"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->json_return($ajax);
    }*/
    public function get_current_stock()
    {
        $warehouse_id = $this->input->post('warehouse_id');
        $pack_size_id = $this->input->post('pack_size_id');
        $variety_id = $this->input->post('variety_id');
        $html_container_id='#current_stock_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $result=System_helper::get_variety_stock(array($variety_id));
        $stock_current=0;
        if(isset($result[$variety_id][$pack_size_id][$warehouse_id]))
        {
            $stock_current=$result[$variety_id][$pack_size_id][$warehouse_id]['current_stock'];
        }
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$stock_current);
        $this->json_return($ajax);
    }

    public function preference_save()
    {
        $preference=$this->input->post('preference');
        $controller_name=isset($preference['controller_name'])?$preference['controller_name']:'';
        $method=isset($preference['method_name'])?$preference['method_name']:'list';
        $user = User_helper::get_user();
        $this->permissions=User_helper::get_permission($controller_name);
        if(!(isset($this->permissions['action6']) && ($this->permissions['action6']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
            die();
        }
        else
        {
            if($this->input->post('item'))
            {
                $items=$this->input->post('item');
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_PLEASE_SELECT_ANY_ONE");
                $this->json_return($ajax);
                die();
            }

            $time=time();
            $this->db->trans_start();  //DB Transaction Handle START

            $result=Query_helper::get_info($this->config->item('table_sms_setup_user_preference'),'*',array('user_id ='.$user->user_id,'controller ="' .$controller_name.'"','method ="'.$method.'"'),1);
            if($result)
            {
                $data['user_updated']=$user->user_id;
                $data['date_updated']=$time;
                $data['preferences']=json_encode($items);
                Query_helper::update($this->config->item('table_sms_setup_user_preference'),$data,array('id='.$result['id']),false);
            }
            else
            {
                $data['user_id']=$user->user_id;
                $data['controller']=$controller_name;
                $data['method']="$method";
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                $data['preferences']=json_encode($items);
                Query_helper::add($this->config->item('table_sms_setup_user_preference'),$data,false);
            }

            $this->db->trans_complete();   //DB Transaction Handle END
            $ajax['status']=true;
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $ajax['system_page_url']=redirect($controller_name.'/index/'.$method);
                $this->json_return($ajax);
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
        }
    }
}
