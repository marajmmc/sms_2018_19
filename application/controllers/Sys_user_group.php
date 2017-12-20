<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sys_user_group extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=User_helper::get_permission('Sys_user_group');
        $this->controller_url='sys_user_group';
        $user = User_helper::get_user();
        if($user->user_group==1)
        {
            $this->permissions['action0']=1;
            $this->permissions['action2']=1;
        }
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
        elseif($action=='add')
        {
            $this->system_add();
        }
        elseif($action=='edit')
        {
            $this->system_edit($id);
        }
        elseif($action=='assign_group_role')
        {
            $this->system_assign_group_role($id);
        }
        elseif($action=='save')
        {
            $this->system_save();
        }
        elseif($action=='save_assign_group_role')
        {
            $this->system_save_assign_group_role();
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
            $data['title']='User Groups';
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
        if($user->user_group==1)
        {
            $user_groups=Query_helper::get_info($this->config->item('table_system_user_group'),array('id','name','status','ordering'),array('status!="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
        }
        else
        {
            $user_groups=Query_helper::get_info($this->config->item('table_system_user_group'),array('id','name','status','ordering'),array('id!=1','status!="'.$this->config->item('system_status_delete').'"'),0,0,array('ordering ASC'));
        }

        $this->db->from($this->config->item('table_system_user_group_role'));
        $this->db->select('COUNT(id) total_task',false);
        $this->db->select('user_group_id');
        $this->db->where('revision',1);
        $this->db->where('action0',1);
        $this->db->group_by('user_group_id');
        $results=$this->db->get()->result_array();

        $total_roles=array();
        foreach($results as $result)
        {
            $total_roles[$result['user_group_id']]['total_task']=$result['total_task'];
        }
        foreach($user_groups as &$groups)
        {
            if(isset($total_roles[$groups['id']]['total_task']))
            {
                $groups['total_task']=$total_roles[$groups['id']]['total_task'];
            }
            else
            {
                $groups['total_task']=0;
            }
        }
        $this->json_return($user_groups);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1']) && ($this->permissions['action1']==1))
        {
            $data['title']='Create New User Group';
            $data['item']=array
            (
                'id'=>0,
                'name'=>'',
                'type'=>'',
                'ordering'=>99,
                'status'=>$this->config->item('system_status_active')
            );
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add');
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/add_edit',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }
    private function system_edit($id)
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
            $data['item']=Query_helper::get_info($this->config->item('table_system_user_group'),'*',array('id ='.$item_id),1);
            $data['title']='Edit User Group ('.$data['item']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/add_edit',$data,true));
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
    private function system_assign_group_role($id)
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
            $data['modules_tasks']=Task_helper::get_modules_tasks_table_tree();
            $data['role_status']=$this->get_role_status($item_id);
            $data['title']="Edit User Role";
            $data['item_id']=$item_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url.'/assign_group_role',$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/assign_group_role/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
    }
    private function get_role_status($user_group_id)
    {
        $this->db->from($this->config->item('table_system_user_group_role'));
        $this->db->select('*');
        $this->db->where('user_group_id',$user_group_id);
        $this->db->where('revision',1);
        $results=$this->db->get()->result_array();

        $roles=array();
        for($i=0;$i<$this->config->item('system_max_actions');$i++)
        {
            $roles['action'.$i]=array();
        }
        foreach($results as $result)
        {
            for($i=0;$i<$this->config->item('system_max_actions');$i++)
            {
                if($result['action'.$i])
                {
                    $roles['action'.$i][]=$result['task_id'];
                }
            }
        }
        return $roles;
    }

    private function system_save()
    {
        $id=$this->input->post('id');
        $user=User_helper::get_user();
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
                $this->json_return($ajax);
            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }
        else
        {
            $data=$this->input->post('item');
            $this->db->trans_start(); //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated']=$user->user_id;
                $data['date_updated']=time();
                Query_helper::update($this->config->item('table_system_user_group'),$data,array('id='.$id));
            }
            else
            {
                $data['user_created']=$user->user_id;
                $data['date_created']=time();
                Query_helper::add($this->config->item('table_system_user_group'),$data);
            }
            $this->db->trans_complete(); //DB Transaction Handle END
            if ($this->db->trans_status()===true)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line('MSG_SAVED_SUCCESS');
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
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
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[name]',$this->lang->line('LABEL_NAME'),'required');
        if($this->form_validation->run()==false)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function system_save_assign_group_role()
    {
        $item_id=$this->input->post('id');
        $user=User_helper::get_user();
        if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            $this->json_return($ajax);
        }
        $tasks=$this->input->post('tasks');
        $time=time();

        $this->db->trans_start(); //DB Transaction Handle START

        $revision_history_data=array();
        $revision_history_data['date_updated']=$time;
        $revision_history_data['user_updated']=$user->user_id;
        Query_helper::update($this->config->item('table_system_user_group_role'),$revision_history_data,array('revision=1','user_group_id='.$item_id));

        $this->db->where('user_group_id',$item_id);
        $this->db->set('revision','revision+1',false);
        $this->db->update($this->config->item('table_system_user_group_role'));
        if(is_array($tasks))
        {
            foreach($tasks as $task_id=>$task)
            {
                $data=array();
                for($i=0;$i<$this->config->item('system_max_actions');$i++)
                {
                    if(isset($task['action'.$i]) && ($task['action'.$i]==1))
                    {
                        $data['action'.$i]=1;
                    }
                    else
                    {
                        $data['action'.$i]=0;
                    }
                }
                for($i=0;$i<$this->config->item('system_max_actions');$i++)
                {
                    if($data['action'.$i])
                    {
                        $data['action0']=1;
                        break;
                    }
                }
                $data['task_id']=$task_id;
                $data['user_group_id']=$item_id;
                $data['user_created']=$user->user_id;
                $data['date_created']=$time;
                $data['revision']=1;
                Query_helper::add($this->config->item('table_system_user_group_role'),$data);
            }
        }
        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status()===true)
        {
            $this->message=$this->lang->line('MSG_ROLE_ASSIGN_SUCCESS');
            $this->system_list();
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_ROLE_ASSIGN_FAIL');
            $this->json_return($ajax);
        }
    }
}
