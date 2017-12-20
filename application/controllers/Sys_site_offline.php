<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sys_site_offline extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Sys_site_offline');
        $this->controller_url='sys_site_offline';
    }

    public function index($action="add",$id=0)
    {
        if($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_add($id);
        }
    }
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="Site Offline";
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sys_site_offline/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save()
    {
            $user=User_helper::get_user();
            $data=$this->input->post('site');
            $this->db->trans_start();  //DB Transaction Handle START
            $data['user_created'] = $user->user_id;
            $data['date_created'] = time();
            Query_helper::add($this->config->item('table_system_site_offline'),$data);
            $this->db->trans_complete();   //DB Transaction Handle END

            if ($this->db->trans_status() === TRUE)
            {
                $this->dashboard_page();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->json_return($ajax);
            }
    }
}
