<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sys_users extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Sys_users');
        $this->controller_url='sys_users';
    }
    public function index($action='list',$id=0)
    {
        if($action=='list')
        {
            $this->system_list();
        }
        elseif($action=='get_items')
        {
            $this->system_get_items();
        }
        elseif($action=='assign_user_group')
        {
            $this->system_assign_user_group($id);
        }
        elseif($action=='save_assign_user_group')
        {
            $this->system_save_assign_user_group();
        }
        else
        {
            $this->system_list();
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['action0']) && ($this->permissions['action0']==1))
        {
            $data['title']='List of Users';
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/list',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        $user=User_helper::get_user();
        $this->db->from($this->config->item('table_login_setup_user').' u');
        $this->db->select('u.id,u.employee_id,u.user_name,u.status');
        $this->db->select('ui.name,ui.ordering');
        $this->db->select('d.name designation_name');
        $this->db->join($this->config->item('table_login_setup_user_info').' ui','u.id=ui.user_id','inner');
        $this->db->join($this->config->item('table_login_setup_users_other_sites').' uos','uos.user_id=u.id','inner');
        $this->db->join($this->config->item('table_login_system_other_sites').' os','os.id=uos.site_id','inner');
        $this->db->join($this->config->item('table_login_setup_designation').' d','d.id=ui.designation','left');
        $this->db->where('ui.revision',1);
        $this->db->where('uos.revision',1);
        $this->db->where('os.short_name',$this->config->item('system_site_short_name'));
        $this->db->order_by('ui.ordering','ASC');
        if($user->user_group!=1)
        {
            $this->db->where('ui.user_group!=',1);
        }
        $items=$this->db->get()->result_array();

        $this->db->from($this->config->item('table_system_assigned_group').' ag');
        $this->db->select('ag.user_id');
        $this->db->select('ug.name group_name');
        $this->db->join($this->config->item('table_system_user_group').' ug','ug.id=ag.user_group','inner');
        $this->db->where('ag.revision',1);
        $results=$this->db->get()->result_array();

        $groups=array();
        foreach($results as $result)
        {
            $groups[$result['user_id']]['group_name']=$result['group_name'];
        }
        foreach($items as &$item)
        {
            if(isset($groups[$item['id']]['group_name']))
            {
                $item['group_name']=$groups[$item['id']]['group_name'];
            }
            else
            {
                $item['group_name']='Not Assigned';
            }
            if($item['designation_name']==null)
            {
                $item['designation_name']='Not Assigned';
            }
        }
        $this->json_return($items);
    }
    private function system_assign_user_group($id)
    {
        if(isset($this->permissions['action2']) && ($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $this->db->from($this->config->item('table_login_setup_user_info'));
            $this->db->select('name,user_id');
            $this->db->where('revision',1);
            $this->db->where('user_id',$item_id);
            $data['item']=$this->db->get()->row_array();

            $data['title']='Assign User('.$data['item']['name'].') to a Group';
            $data['item']['user_group']=0;
            $group_info=Query_helper::get_info($this->config->item('table_system_assigned_group'),array('user_group'),array('revision=1','user_id='.$item_id),1);
            if($group_info)
            {
                $data['item']['user_group']=$group_info['user_group'];
            }
            $data['user_groups']=Query_helper::get_info($this->config->item('table_system_user_group'),array('id value','name text'),array('status="'.$this->config->item('system_status_active').'"','id!=1'));
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/assign_user_group',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }
    private function system_save_assign_user_group()
    {
        $id=$this->input->post('id');
        $user=User_helper::get_user();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $time=time();

            $this->db->trans_start(); //DB Transaction Handle START
            
            $revision_history_data=array();
            $revision_history_data['date_updated']=$time;
            $revision_history_data['user_updated']=$user->user_id;
            Query_helper::update($this->config->item('table_system_assigned_group'),$revision_history_data,array('revision=1','user_id='.$id));

            $this->db->where('user_id',$id);
            $this->db->set('revision','revision+1',false);
            $this->db->update($this->config->item('table_system_assigned_group'));

            $data['user_id']=$id;
            $data['user_group']=$this->input->post('user_group');
            $data['user_created']=$user->user_id;
            $data['date_created']=$time;
            $data['revision']=1;

            if($data['user_group']!=0)
            {
                Query_helper::add($this->config->item('table_system_assigned_group'),$data);
            }
            $this->db->trans_complete(); //DB Transaction Handle END
            if($this->db->trans_status()===true)
            {
                $this->message=$this->lang->line('MSG_SAVED_SUCCESS');
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('MSG_SAVED_FAIL');
                $this->json_return($ajax);
            }
        }
    }
    private function check_validation()
    {
        $user_group=$this->input->post('user_group');
        if($user_group==1)
        {
            $this->message='Try again';
            return false;
        }
        return true;
    }
}
